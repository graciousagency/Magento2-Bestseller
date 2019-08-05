<?php

namespace Gracious\Bestseller\Model\ResourceModel\Rating\Option;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Aggregated
 *
 * @package Gracious\Bestseller\Model\ResourceModel\Rating\Option
 */
class Aggregated extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('rating_option_vote_aggregated', 'primary_id');
    }
}
