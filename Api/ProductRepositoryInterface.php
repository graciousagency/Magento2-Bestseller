<?php

namespace Gracious\Bestseller\Api;

use Gracious\Bestseller\Api\ProductSearchCriteriaInterface;
use Gracious\Bestseller\Api\Data\ProductSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ProductRepositoryInterface
 *
 * @api
 * @package Gracious\Bestseller\Api
 */
interface ProductRepositoryInterface
{
    const FILTER_TYPE_TOP_SELLING = 'selling';
    const FILTER_TYPE_TOP_FREE    = 'free';
    const FILTER_TYPE_TOP_RATED   = 'rated';

    /**
     * Function getList
     *
     * @param string $type
     * @param ProductSearchCriteriaInterface $searchCriteria
     * @return ProductSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList($type, ProductSearchCriteriaInterface $searchCriteria = null);
}
