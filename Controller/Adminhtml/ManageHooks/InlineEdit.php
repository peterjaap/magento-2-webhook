<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Webhook\Controller\Adminhtml\ManageHooks;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Webhook\Model\HookFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\Blog\Controller\Adminhtml\Post
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $jsonFactory;

    /**
     * Post Factory
     *
     * @var \Mageplaza\Webhook\Model\HookFactory
     */
    public $hookFactory;

    /**
     * InlineEdit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Mageplaza\Blog\Model\PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
		HookFactory $hookFactory
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->hookFactory = $hookFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $hookItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($hookItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($hookItems);
        $hookId = !empty($key) ? (int)$key[0] : '';
        /** @var \Mageplaza\Webhook\Model\Hook $post */
        $hook = $this->hookFactory->create()->load($hookId);
        try {
            $hookData = $hookItems[$hookId];
			$hook->addData($hookData);
			$hook->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithHookId($hook, $e->getMessage());
            $error = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithHookId($hook, $e->getMessage());
            $error = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithHookId(
				$hook,
                __('Something went wrong while saving the Post.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Post id to error message
     *
     * @param \Mageplaza\Blog\Model\Post $post
     * @param string $errorText
     * @return string
     */
    public function getErrorWithHookId(\Mageplaza\Webhook\Model\Hook $hook, $errorText)
    {
        return '[Hook ID: ' . $hook->getId() . '] ' . $errorText;
    }
}
