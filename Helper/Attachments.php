<?php
/**
 * Copyright 2022, Todd Lininger Design, LLC. All rights reserved. * https://toddlininger.com * See LICENSE.txt for details.
 */

namespace ToddLininger\ClassAttachments\Helper;

class Attachments extends \ToddLininger\ClassManager\Helper\Compatibility\Attachments
{
    /** @var \ToddLininger\ClassManager\Api\RegistrationRepositoryInterface  */
    protected $registrationRepository;
    /** @var \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory  */
    protected $attachmentCollectionFactory;
    /** @var \MageWorx\Downloads\Block\Catalog\Product\Link  */
    protected $productLink;

    /** @var Config */
    protected $helperConfig;
    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;

    /**
     * @param \ToddLininger\ClassManager\Api\RegistrationRepositoryInterfaceFactory $registrationRepositoryInterfaceFactory
     * @param \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory
     * @param \MageWorx\Downloads\Block\Catalog\Product\Link $productLink
     * @param Config $helperConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \ToddLininger\ClassManager\Api\RegistrationRepositoryInterfaceFactory $registrationRepositoryInterfaceFactory,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Block\Catalog\Product\Link $productLink,
        \ToddLininger\ClassAttachments\Helper\Config $helperConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->registrationRepository = $registrationRepositoryInterfaceFactory->create();
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->productLink = $productLink;
        $this->helperConfig = $helperConfig;
        $this->logger = $logger;
    }

    /**
     * Global setting of this module
     * @return bool
     */
    protected function isInsertEnable()
    {
        return $this->helperConfig->isIncludeAttachmentEnabled();
    }

    /**
     * Wrap data from MW Downloads into wrapper
     * Depr. Not this is in the templates
     * @param $data
     * @return mixed|string
     */
    /*protected function wrapHtmlContainer($data)
    {
        if (!empty($data)) {
            $data = '<div class="tl_attach_wrap" style="border:2px solid #ecde11; border-radius:6px; padding:0 1em;">' .
                '<h3>' . __('Downloads') . '</h3><div>' . $data . '</div></div>';
        }
        return $data;
    }*/

    /**
     * Get files data from MW
     * @param $vars
     * @return array
     */
    protected function getAttachDataFromMW($vars)
    {
        $result = null;

        $productId = null;
        $customerGroupId = null;
        $storeId = null;
        try {
            if (!empty($vars) && (!empty($vars['product_id'])) && (!empty($vars['registration_id']))) {
                // get productId param
                $productId = (int)$vars['product_id'];

                // get customerGroupId and storeId params
                $registrationId = (int)$vars['registration_id'];
                $_reg = $this->registrationRepository->getById($registrationId);
                if (!empty($_reg)) {
                    /** @var \Magento\Sales\Model\Order | null $_order */
                    $_order = $_reg->getOrderModel();
                    if ($_order) {
                        /** @var int|null $customerGroupId */
                        $customerGroupId = $_order->getCustomerGroupId();
                        /** @var int|null $storeId */
                        $storeId = $_order->getStoreId();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        if (($productId !== null) && ($customerGroupId !== null) && ($storeId !== null)) {
            try {
                $collection = $this->attachmentCollectionFactory->create();
                $collection->getAttachmentsForEmail(
                    $productId,
                    $customerGroupId,
                    $storeId
                );
                if (!empty($collection)) {
                    /** @var  $item */
                    foreach ($collection->getItems() as $item)
                    {
                        $item->setIsInGroup(1);
                        $result[] = $item;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Get Html content of Downloads with MW methods
     * @param $vars
     * @return string|null
     */
    protected function getHtmlDataFromMW($vars)
    {
        $data = null;
        $dataFiles = $this->getAttachDataFromMW($vars);
        if (!empty($dataFiles)) {
            $data = '<ul>';
            foreach ($dataFiles as $attachment) {
                try {
                    $name = $attachment['name'] ?: __('No name');
                    $url = $this->productLink->getAttachmentLink($attachment['attachment_id']);
                    $size = $this->productLink->getPrepareFileSize($attachment['size']);

                    $data .= '<li>';
                    $data .= '<a href="'.$url.'" target="_blank">' . $name . '</a> (' . $size . ')';
                    $data .= '</li>';
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
            $data .= '</ul>';
        }
        return $data;
    }

    /**
     * Prepare attachments data from MW Downloads
     * @param $vars
     * @return mixed|string|null
     */
    public function getData($vars)
    {
        $data = null;
        if ($this->isInsertEnable()) {
            $data = $this->getHtmlDataFromMW($vars);
        }
        return $data;
    }
}
