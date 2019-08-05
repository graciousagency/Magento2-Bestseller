<?php

namespace Gracious\Bestseller\Model;

use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\SearchCriteriaInterface;
use Gracious\Bestseller\Api\Data\ProductInterface;
use Gracious\Bestseller\Api\Data\ProductSearchResultsInterface;
use Gracious\Bestseller\Model\ProductSearchCriteria;

/**
 * Class ProductSearchResults
 *
 * @package Gracious\Bestseller\Model
 */
class ProductSearchResults extends AbstractSimpleObject implements ProductSearchResultsInterface
{
    const KEY_ITEMS           = 'items';
    const KEY_SEARCH_CRITERIA = 'search_criteria';
    const KEY_TOTAL_COUNT     = 'total_count';

    /**
     * Function getItems
     *
     * @return ProductInterface[]
     */
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * function setItems
     *
     * @param ProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items)
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }

    /**
     * Function getSearchCriteria
     *
     * @return ProductSearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get(self::KEY_SEARCH_CRITERIA);
    }

    /**
     * Function setSearchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        return $this->setData(self::KEY_SEARCH_CRITERIA, $searchCriteria);
    }

    /**
     * Function getTotalCount
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get(self::KEY_TOTAL_COUNT);
    }

    /**
     * Function setTotalCount
     *
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count)
    {
        return $this->setData(self::KEY_TOTAL_COUNT, $count);
    }
}
