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
namespace Dss\DiscountLimit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ConfigInterface
{
    public const XML_PATH_ENABLED = 'dss_discountlimit/general/enabled';
    public const XML_PATH_DEBUG = 'dss_discountlimit/general/debug';

    /**
     * Config constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @return void
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Flag of config
     *
     * @param mixed $xmlPath
     * @param mixed $storeId
     */
    public function getConfigFlag($xmlPath, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            $xmlPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get config value
     *
     * @param mixed $xmlPath
     * @param mixed $storeId
     */
    public function getConfigValue($xmlPath, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $xmlPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    
    /**
     * Is enable
     *
     * @param mixed $storeId
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        return $this->getConfigFlag(self::XML_PATH_ENABLED, $storeId);
    }
    
    /**
     * Is Debug Enabled
     *
     * @param mixed $storeId
     * @return bool
     */
    public function isDebugEnabled($storeId = null): bool
    {
        return $this->getConfigFlag(self::XML_PATH_DEBUG, $storeId);
    }
}
