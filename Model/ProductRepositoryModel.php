<?php

namespace Gracious\Bestseller\Model;

use DateTime;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Rating\Collection as Rating;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as Bestsellers;
use Magento\Store\Model\StoreManager;
use Gracious\Bestseller\Api\ProductRepositoryInterface;
use Gracious\Bestseller\Api\ProductSearchCriteriaInterface;
use Gracious\Bestseller\Api\Data\ProductSearchResultsInterface;
use Gracious\Bestseller\Model\ProductSearchResults;
use Gracious\Bestseller\Model\ResourceModel\Rating\Option\Aggregated\Collection as RatingAggregated;

/**
 * Class Repository
 *
 * @package Gracious\Bestseller\Model
 */
class ProductRepositoryModel implements ProductRepositoryInterface
{
    /** @var Bestsellers */
    protected $bestsellers;

    /** @var ProductRepository */
    protected $products;

    /** @var ProductCollection */
    protected $productCollection;

    /** @var StoreManager */
    protected $storeManager;

    /** @var Rating */
    protected $rating;

    /** @var Rating */
    protected $ratingAggregated;

    /** @var ProductAttributeRepositoryInterface */
    protected $metadataService;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * @param Bestsellers $bestsellers
     * @param ProductRepository $products
     * @param ProductCollection $productCollection
     * @param StoreManager $storeManager
     * @param Rating $rating
     * @param RatingAggregated $ratingAggregated
     * @param ProductAttributeRepositoryInterface $metadataServiceInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Bestsellers $bestsellers,
        ProductRepository $products,
        ProductCollection $productCollection,
        StoreManager $storeManager,
        Rating $rating,
        RatingAggregated $ratingAggregated,
        ProductAttributeRepositoryInterface $metadataServiceInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->bestsellers           = $bestsellers;
        $this->products              = $products;
        $this->productCollection     = $productCollection;
        $this->storeManager          = $storeManager;
        $this->rating                = $rating;
        $this->ratingAggregated      = $ratingAggregated;
        $this->metadataService       = $metadataServiceInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Function getList
     *
     * @api
     * @param string $type
     * @param ProductSearchCriteriaInterface $searchCriteria
     * @return ProductSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList($type, ProductSearchCriteriaInterface $searchCriteria = null)
    {
        $allowed = [static::FILTER_TYPE_TOP_SELLING, static::FILTER_TYPE_TOP_FREE, static::FILTER_TYPE_TOP_RATED];
        $type    = mb_strtolower($type);

        if (empty($searchCriteria)) {
            $searchCriteria = new ProductSearchCriteria();
        }

        switch ($type) {
            case static::FILTER_TYPE_TOP_SELLING:
                $result = $this->getBestsellers('gt', $searchCriteria);
                break;

            case static::FILTER_TYPE_TOP_FREE:
                $result = $this->getBestsellers('eq', $searchCriteria);
                break;

            case static::FILTER_TYPE_TOP_RATED:
                $result = $this->getRatedProducts($searchCriteria);
                break;

            default:
                $allowed = implode(', ', $allowed);
                $phrase  = __('Requested type "%s" doesn\'t exist. Allowed: %s', $type, $allowed);
                throw new InputException($phrase);
        }

        return $result;
    }

    /**
     * Function getBestsellers
     *
     * @param $condition
     * @param ProductSearchCriteriaInterface $searchCriteria
     * @return ProductSearchResults $searchCriteria
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getBestsellers($condition, ProductSearchCriteriaInterface $searchCriteria)
    {
        $allowed = [
            ProductSearchCriteriaInterface::PERIOD_DAILY,
            ProductSearchCriteriaInterface::PERIOD_MONTHLY,
            ProductSearchCriteriaInterface::PERIOD_YEARLY
        ];

        $period = $searchCriteria->getPeriod();
        if (!in_array($period, $allowed, true)) {
            $period = ProductSearchCriteriaInterface::PERIOD_YEARLY;
        }

        $from = (new DateTime())->setTime(0, 0, 0, 0);
        $to   = (new DateTime())->setTime(0, 0, 0, 0);

        switch ($period) {
            case ProductSearchCriteriaInterface::PERIOD_YEARLY:
                $from->setDate($from->format('Y'), 1, 1);

                $to->modify('+1 year');
                $to->setDate($to->format('Y'), 1, 1);
                $to->modify('-1 day');

                $range = [
                    'from' => $from->format('Y-m-d 00:00:00'),
                    'to'   => $to->format('Y-m-d 23:59:59'),
                ];
                break;

            case ProductSearchCriteriaInterface::PERIOD_MONTHLY:
                $from->setDate($from->format('Y'), $from->format('m'), 1);

                $to->setDate($to->format('Y'), $to->format('m'), 1);
                $to->modify('+1 month');
                $to->modify('-1 day');

                $range = [
                    'from' => $from->format('Y-m-d 00:00:00'),
                    'to'   => $to->format('Y-m-d 23:59:59'),
                ];
                break;

            case ProductSearchCriteriaInterface::PERIOD_DAILY:
            default:
                $range = [
                    'from' => $from->format('Y-m-d 00:00:00'),
                    'to'   => $to->format('Y-m-d 23:59:59'),
                ];
                break;
        }

        $storeId = (int)$this->storeManager->getStore()->getId();
        $this->prepareProductCollection($searchCriteria);

        $joinCond = [
            'store_id'      => ['eq' => $storeId],
            'product_price' => [$condition => 0],
        ];

        $table = $this->bestsellers->getTableByAggregationPeriod($period);
        $this->productCollection->joinTable(
            ['b' => $table],
            'product_id = entity_id',
            [
                'product_price' => 'product_price',
                'period'        => 'period',
                'rating_pos'    => 'rating_pos',
            ],
            $joinCond
        );

        $this->productCollection
            ->addFieldToFilter('period', ['gteq' => $range['from']])
            ->addFieldToFilter('period', ['lteq' => $range['to']])
            ->addOrder('rating_pos', Collection::SORT_ORDER_DESC);

        $result = $this->processProductCollection($searchCriteria);

        return $result;
    }

    /**
     * Function getRatedProducts
     *
     * @param ProductSearchCriteriaInterface $searchCriteria
     * @return ProductSearchResults
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getRatedProducts(ProductSearchCriteriaInterface $searchCriteria)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $this->prepareProductCollection($searchCriteria);

        $joinCond = [
            'store_id' => ['eq' => $storeId],
        ];

        $code   = $searchCriteria->getRatingCode();
        $rating = $this->rating->getItemByColumnValue('rating_code', $code);

        if (empty($rating) || $rating->isEmpty()) {
            throw new InputException(__('Rating code "%s" not found.', $code));
        }

        // there is something like we are searching
        $id = $rating->getData('rating_id');
        $joinCond['rating_id'] = ['eq' => $id];

        // $ra - alias for rating table
        $ra = 'r';
        /**
         * To create more reliable order based on number of votes and quality of votes we have to introduce product
         * weight, because product with 1 vote and 5 stars cannot be at first position. Please see link to get more
         * information about this.
         *
         * At current realisation
         * q        - 'vote_count' column
         * P = 0.5
         * Q = 5    - becase we have 5 star system
         * p        - 'vote_value_sum' / 'vote_count'
         *
         * @link https://math.stackexchange.com/questions/942738/algorithm-to-calculate-rating-based-on-multiple-reviews-using-both-review-score
         */
        $q = "`{$ra}`.vote_count";
        $P = 0.5;
        $Q = 5;
        $p = "(`{$ra}`.vote_value_sum / {$q})";
        $weight = "ROUND(({$P} * {$p} + 10 * (1 - {$P}) * (1 -  EXP( -({$q}) / {$Q} ))), 2)";
        $this->productCollection
            ->getSelect()
            ->columns([
                'score' => new \Zend_Db_Expr($weight)
            ]);

        $this->productCollection->joinTable(
            [$ra => $this->ratingAggregated->getMainTable()],
            'entity_pk_value = entity_id',
            [
                'rating_id'      => 'rating_id',
                'vote_count'     => 'vote_count',
                'vote_value_sum' => 'vote_value_sum',
            ],
            $joinCond
        );

        $this->productCollection
            ->getSelect()
            ->order('score ' . Collection::SORT_ORDER_DESC);

        $result = $this->processProductCollection($searchCriteria);

        return $result;
    }

    /**
     * Function prepareProductCollection
     *
     * @param ProductSearchCriteriaInterface $searchCriteria
     * @throws LocalizedException
     */
    protected function prepareProductCollection(ProductSearchCriteriaInterface $searchCriteria)
    {
        $pageSize = (int)$searchCriteria->getPageSize();
        $page     = (int)$searchCriteria->getCurrentPage();

        $this->productCollection
            ->clear()
            ->setPageSize($pageSize)
            ->setCurPage($page);

        $criteria    = $this->searchCriteriaBuilder->create();
        $attributes  = $this->metadataService->getList($criteria)->getItems();
        $allowedAttr = [];

        foreach ($attributes as $attribute) {
            /** @var Attribute $attribute */
            $allowedAttr[$attribute->getAttributeCode()] = $attribute;
        }

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if (in_array($filter->getField(), array_keys($allowedAttr), true)) {
                    // add this attribute to join list and filter
                    $attribute = $allowedAttr[$filter->getField()];
                    $this->productCollection->joinAttribute($filter->getField(), $attribute, 'entity_id', null, 'inner');

                    $this->productCollection->addAttributeToFilter(
                        $filter->getField(),
                        [$filter->getConditionType() => $filter->getValue()]
                    );
                }
            }
        }
    }

    /**
     * Function processProductCollection
     *
     * @param ProductSearchCriteriaInterface $searchCriteria
     * @return ProductSearchResults
     */
    protected function processProductCollection(ProductSearchCriteriaInterface $searchCriteria)
    {
        $items = $this->productCollection->walk(function ($item) {
            /** @var Product $item */
            $productId = $item->getId();
            $product   = $this->products->getById($productId);

            return $product;
        });

        $size = $this->productCollection->getSize();

        $result = new ProductSearchResults();
        $result->setItems($items)
            ->setSearchCriteria($searchCriteria)
            ->setTotalCount($size);

        return $result;
    }
}
