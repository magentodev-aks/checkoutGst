<?php
namespace Custom\GstForm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Captcha\Observer\CaptchaStringResolver;

class SaveGst implements ObserverInterface
{
      public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);



    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create('Magento\Checkout\Model\Session');

		if ($customerSession->getVarValue()) {
		        $event = $observer->getEvent();
		        $quote = $event->getQuote();
		    	$order = $event->getOrder();
		        $order->setData('gstdata', $customerSession->getVarValue());
				$logger->info(' >>>>>> ');
		}
    }

}
