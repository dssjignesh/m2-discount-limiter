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
namespace Dss\DiscountLimit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Dss\DiscountLimit\Logger\Logger as ModuleLogger;
use Dss\DiscountLimit\Model\Config;

class Data extends AbstractHelper
{

    /**
     * Data constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Dss\DiscountLimit\Logger\Logger $moduleLogger
     * @param \Dss\DiscountLimit\Model\Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        protected Context $context,
        protected ModuleLogger $moduleLogger,
        protected Config $config,
        protected StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
    }
    
    /**
     * Get Config Helper
     *
     * @return mixed
     */
    public function getConfigHelper(): mixed
    {
        return $this->config;
    }
    
    /**
     * Get base Url
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_WEB,
            true
        );
    }
    
    /**
     * Is Active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Logging Utility
     *
     * @param mixed $message
     * @param bool|false $useSeparator
     * @return void
     */
    public function log($message, $useSeparator = false)
    {
        if ($this->isActive()
            && $this->config->isDebugEnabled()
        ) {
            if ($useSeparator) {
                $this->moduleLogger->customLog(str_repeat('=', 100));
            }
            $this->moduleLogger->customLog($message);
        }
    }
}
