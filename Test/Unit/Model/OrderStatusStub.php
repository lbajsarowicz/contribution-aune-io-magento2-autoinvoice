<?php

namespace Aune\AutoInvoice\Test\Unit\Model;

class OrderStatusStub extends \Magento\Sales\Model\Order\Status
{
    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getState()
    {
        return $this->getData('state');
    }
}
