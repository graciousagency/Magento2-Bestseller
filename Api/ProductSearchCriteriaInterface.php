<?php

namespace Gracious\Bestseller\Api;

use Magento\Framework\Api\Search\FilterGroup;

/**
 * Interface ProductSearchCriteriaInterface
 *
 * @package Gracious\Bestseller\Api
 */
interface ProductSearchCriteriaInterface
{
    const PERIOD_YEARLY  = 'yearly';
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_DAILY   = 'daily';

    /**
     * Function getFilterGroups
     *
     * @return FilterGroup[]
     */
    public function getFilterGroups();

    /**
     * Function setFilterGroups
     *
     * @param FilterGroup[] $filterGroups
     * @return $this
     */
    public function setFilterGroups(array $filterGroups = null);

    /**
     * Function getPageSize
     *
     * @return int|null
     */
    public function getPageSize();

    /**
     * Function setPageSize
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize);

    /**
     * Function getCurrentPage
     *
     * @return int|null
     */
    public function getCurrentPage();

    /**
     * Function setCurrentPage
     *
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage);

    /**
     * Function getPeriod
     *
     * @return string
     */
    public function getPeriod();

    /**
     * Function setPeriod
     *
     * @param string $period Type of period
     * @return $this
     */
    public function setPeriod($period = null);

    /**
     * Function getRatingCode
     *
     * @return string
     */
    public function getRatingCode();

    /**
     * Function setRatingCode
     *
     * @param string $code Raging code filter
     * @return $this
     */
    public function setRatingCode($code = null);
}
