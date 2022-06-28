<?php
/**
 * Copyright 2022, Todd Lininger Design, LLC. All rights reserved. * https://toddlininger.com * See LICENSE.txt for details.
 */

namespace ToddLininger\ClassAttachments\Helper;

class Config
{
    const CONFIG_PREFIX = 'tl_class_attachments';
    const CONFIG_PATH_GENERAL_INCLUDE_ENABLED = 'general/include_attachments_enabled';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return module config value
     * @param $param
     * @param string $scope
     * @return mixed
     */
    public function getValue($param, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        $paramPath = [
            static::CONFIG_PREFIX,
            $param
        ];

        return $this->scopeConfig->getValue(implode('/', $paramPath), $scope);
    }

    /**
     * @param string $scope
     * @return bool
     */
    public function isIncludeAttachmentEnabled($scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
    {
        $paramPath = [
            static::CONFIG_PREFIX,
            static::CONFIG_PATH_GENERAL_INCLUDE_ENABLED
        ];
        return $this->scopeConfig->isSetFlag(implode('/', $paramPath), $scope);
    }
}
