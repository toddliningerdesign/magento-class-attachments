<?php
/**
 * Copyright 2022, Todd Lininger Design, LLC. All rights reserved. * https://toddlininger.com * See LICENSE.txt for details.
 */

namespace ToddLininger\ClassAttachments\Helper;

class Config
{
    const CONFIG_PREFIX = 'tl_class_attachments';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $scopeConfig;
    /** @var \ToddLininger\ClassManager\Helper\Config  */
    protected $cmConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \ToddLininger\ClassManager\Helper\Config $cmConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \ToddLininger\ClassManager\Helper\Config $cmConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->cmConfig = $cmConfig;
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
     * Use parent setting
     * @param string $scope
     * @return bool
     */
    public function isIncludeAttachmentEnabled($scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
    {
        return $this->cmConfig->isIncludeDownloadableLinksEnabled($scope);
    }
}
