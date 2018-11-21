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
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) 2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CommentContent
 * @package Mageplaza\Blog\Ui\Component\Listing\Columns
 */
class Message extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
//                if (isset($item[$this->getData('name')])) {
                    $successMes = $item['success_message'];
                    $errorMes = $item['error_message'];
//					\Zend_Debug::dump($item);
                    $item[$this->getData('name')] = '<p style="color: green">' . $successMes . '</p>';
                    $item[$this->getData('name')] .= '<p style="color: red">' . $errorMes . '</p>';
//                }
            }
        }
        return $dataSource;
    }
}
