<?php

namespace Gracious\Bestseller\Model\Rating\Option;

use Magento\Framework\Model\AbstractModel;
use Gracious\Bestseller\Model\ResourceModel\Rating\Option\Aggregated as AggregatedResourceModel;

/**
 * Class Aggregated
 * - aggregated vote model
 *
 * @api
 * @package Gracious\Bestseller\Model\Rating\Option
 */
class Aggregated extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(AggregatedResourceModel::class);
    }
}
