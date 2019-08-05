<?php

namespace Gracious\Bestseller\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Gracious\Bestseller\Api\Data\ProductInterface;

/**
 * Interface ProductSearchResultsInterface
 *
 * @api
 * @package Gracious\Bestseller\Api\Data
 */
interface ProductSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Function getItems
     *
     * @return ProductInterface[]
     */
    public function getItems();

    /**
     * Function setItems
     *
     * @param ProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
