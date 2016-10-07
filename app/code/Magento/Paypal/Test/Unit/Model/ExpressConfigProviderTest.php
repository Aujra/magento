<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Test\Unit\Model;

use Magento\Paypal\Model\ExpressConfigProvider;

class ExpressConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $localeResolver = $this->getMock(\Magento\Framework\Locale\ResolverInterface::class, [], [], '', false);
        $localeResolver->expects($this->once())->method('getLocale');

        $configFactory = $this->getMock(\Magento\Paypal\Model\ConfigFactory::class, ['create'], [], '', false);

        $currentCustomer = $this->getMock(\Magento\Customer\Helper\Session\CurrentCustomer::class, [], [], '', false);
        $currentCustomer->expects($this->atLeastOnce())->method('getCustomerId')->willReturn(12);

        $paymentHelper= $this->getMock(\Magento\Payment\Helper\Data::class, [], [], '', false);

        $paypalHelper = $this->getMock(\Magento\Paypal\Helper\Data::class, [], [], '', false);
        $paypalHelper->expects($this->atLeastOnce())->method('shouldAskToCreateBillingAgreement')->willReturn(false);

        $config = $this->getMock(\Magento\Paypal\Model\Config::class, [], [], '', false);
        $config->expects($this->once())->method('getPaymentMarkWhatIsPaypalUrl');
        $config->expects($this->once())->method('getPaymentMarkImageUrl');
        $config->expects($this->atLeastOnce())->method('setMethod');

        $configFactory->expects($this->once())->method('create')->willReturn($config);

        $payment = $this->getMock(
            \Magento\Paypal\Model\Payflowpro::class,
            ['isAvailable', 'getCheckoutRedirectUrl'],
            [],
            'paymentInstance',
            false
        );
        $payment->expects($this->atLeastOnce())->method('isAvailable')->willReturn(true);
        $payment->expects($this->atLeastOnce())->method('getCheckoutRedirectUrl')->willReturn('http://redirect.url');
        $paymentHelper->expects($this->atLeastOnce())->method('getMethodInstance')->willReturn($payment);

        /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject $urlBuilderMock */
        $urlBuilderMock = $this->getMock(\Magento\Framework\UrlInterface::class, [], [], '', false);

        $configProvider = new ExpressConfigProvider(
            $configFactory,
            $localeResolver,
            $currentCustomer,
            $paypalHelper,
            $paymentHelper,
            $urlBuilderMock
        );
        $configProvider->getConfig();
    }
}
