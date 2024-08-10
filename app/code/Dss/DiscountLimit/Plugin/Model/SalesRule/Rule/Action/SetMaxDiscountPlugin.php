<?php

declare(strict_types=1);

/**
* Digit Software Solutions.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
*
* @category  Dss
* @package   Dss_DiscountLimit
* @author    Extension Team
* @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
*/
namespace Dss\DiscountLimit\Plugin\Model\SalesRule\Rule\Action;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\SalesRule\Model\Rule\Action\Discount\ByPercent;
use Dss\DiscountLimit\Helper\Data as DiscountLimitHelper;

class SetMaxDiscountPlugin
{
    /**
     * @var array
     */
    public static $maxDiscount = [];

    /**
     * @var array
     */
    private $processedRule = [];
    
    /**
     * MaxDiscount constructor
     *
     * @param \Dss\DiscountLimit\Helper\Data $discountLimitHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @return void
     */
    public function __construct(
        private DiscountLimitHelper $discountLimitHelper,
        private PriceCurrencyInterface $priceCurrency
    ) {
    }
    
    /**
     * Calculate maximum discount
     *
     * @param ByPercent $subject
     * @param callable  $proceed
     * @param mixed     $rule
     * @param mixed     $item
     * @param mixed     $qty
     * @return mixed
     */
    public function aroundCalculate(
        ByPercent $subject,
        callable $proceed,
        $rule,
        $item,
        $qty
    ): mixed {
        $discountData = $proceed($rule, $item, $qty);
        if (! $this->discountLimitHelper->isActive()) {
            return $discountData;
        }

        $store = $item->getQuote()->getStore();
        $itemId = $item->getId();
        $cachedKey = $itemId . '_' . $discountData->getBaseAmount();
        if (! strlen($rule->getMaxDiscountAmount()) || $rule->getMaxDiscountAmount() < 0.0001) {
            return $discountData;
        }

        if (! isset(self::$maxDiscount[$rule->getId()]) || isset($this->processedRule[$rule->getId()][$cachedKey])) {
            self::$maxDiscount[$rule->getId()] = $rule->getMaxDiscountAmount();
            $this->processedRule[$rule->getId()] = null;
        }

        if (self::$maxDiscount[$rule->getId()] - $discountData->getBaseAmount() < 0) {
            $convertedPrice = $this->priceCurrency->convert(self::$maxDiscount[$rule->getId()], $store);
            $discountData->setBaseAmount(self::$maxDiscount[$rule->getId()]);
            $discountData->setAmount($this->priceCurrency->round($convertedPrice));
            $discountData->setBaseOriginalAmount(self::$maxDiscount[$rule->getId()]);
            $discountData->setOriginalAmount($this->priceCurrency->round($convertedPrice));
            self::$maxDiscount[$rule->getId()] = 0;
        } else {
            self::$maxDiscount[$rule->getId()] =
                self::$maxDiscount[$rule->getId()] - $discountData->getBaseAmount();
        }

        $this->processedRule[$rule->getId()][$cachedKey] = true;

        return $discountData;
    }
}
