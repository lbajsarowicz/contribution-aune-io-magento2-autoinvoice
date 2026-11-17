<?php

namespace Aune\AutoInvoice\Test\Unit\Model;

class InvoiceStub extends \Magento\Sales\Model\Order\Invoice
{
    public function setRequestedCaptureCase($captureCase)
    {
        return $this->setData('requested_capture_case', $captureCase);
    }
}
