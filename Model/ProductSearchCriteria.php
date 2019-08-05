<?php

namespace Gracious\Bestseller\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\InputException;
use Gracious\Bestseller\Api\ProductSearchCriteriaInterface;

/**
 * Class ProductSearchCriteria
 *
 * @package Gracious\Bestseller\Model
 */
class ProductSearchCriteria extends SearchCriteria implements ProductSearchCriteriaInterface
{
    /** @var string */
    protected $period;

    /** @var string */
    protected $ratingCode;

    /**
     * Function getPeriod
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Function setPeriod
     *
     * @param string $period
     * @return $this
     * @throws InputException
     */
    public function setPeriod($period = null)
    {
        $allowed = [static::PERIOD_DAILY, static::PERIOD_MONTHLY, static::PERIOD_YEARLY];

        if (empty($period)) {
            $period = static::PERIOD_DAILY;
        }

        if (!in_array($period, $allowed, true)) {
            $allowed = implode(', ', $allowed);
            $msg     = 'Requested period "%s" doesn\'t exist. Allowed: %s. Default: %s';
            $phrase  = __($msg, $period, $allowed, static::PERIOD_DAILY);

            throw new InputException($phrase);
        }

        $this->period = $period;

        return $this;
    }

    /**
     * Function getRatingCode
     *
     * @return string
     */
    public function getRatingCode()
    {
        return $this->ratingCode;
    }

    /**
     * Function setRatingCode
     *
     * @param string $code
     * @return $this
     */
    public function setRatingCode($code = null)
    {
        $this->ratingCode = $code;

        return $this;
    }
}
