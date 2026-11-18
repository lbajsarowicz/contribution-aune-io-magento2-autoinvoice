<?php

namespace Aune\AutoInvoice\Test\Unit\Console;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Sales\Model\Order;
use Aune\AutoInvoice\Api\Data\InvoiceProcessItemInterface;
use Aune\AutoInvoice\Api\InvoiceProcessInterface;
use Aune\AutoInvoice\Api\InvoiceProcessInterfaceFactory;
use Aune\AutoInvoice\Console\ProcessCommand;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ProcessCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var State|\PHPUnit\Framework\MockObject\MockObject
     */
    private $stateMock;
    
    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;
    
    /**
     * @var InvoiceProcessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $invoiceProcessMock;
    
    /**
     * @var ProcessCommand
     */
    private $processCommand;
    
    protected function setUp(): void
    {
        $this->stateMock = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->invoiceProcessMock = $this->createMock(InvoiceProcessInterface::class);
        
        $invoiceProcessFactory = $this->getMockBuilder(InvoiceProcessInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $invoiceProcessFactory->method('create')->willReturn($this->invoiceProcessMock);

        $this->processCommand = new ProcessCommand(
            $this->stateMock,
            $this->loggerMock,
            $invoiceProcessFactory
        );
    }
    
    /**
     * @covers \Aune\AutoInvoice\Console\ProcessCommand::configure
     */
    public function testConfigure()
    {
        $this->assertEquals(
            $this->processCommand->getName(),
            ProcessCommand::COMMAND_NAME
        );
        
        $this->assertEquals(
            $this->processCommand->getDescription(),
            ProcessCommand::COMMAND_DESCRIPTION
        );
    }
    
    /**
     * @covers \Aune\AutoInvoice\Console\ProcessCommand::execute
     */
    public function testExecuteDryRun()
    {
        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $inputMock->expects(self::exactly(1))
            ->method('getOption')
            ->with(ProcessCommand::OPTION_DRY_RUN)
            ->willReturn(true);
        
        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $itemMocks = [];
        for ($i=0; $i<10; $i++) {
            $orderMock = $this->getMockBuilder(Order::class)
                ->disableOriginalConstructor()
                ->getMock();
            
            $itemMock = $this->createMock(InvoiceProcessItemInterface::class);
            $itemMock->method('getOrder')
                ->willReturn($orderMock);
            
            $itemMocks []= $itemMock;
        }
        
        $this->invoiceProcessMock->expects(self::once())
            ->method('getItemsToProcess')
            ->willReturn($itemMocks);
        
        $this->invoiceProcessMock->expects(self::exactly(0))
            ->method('invoice');
        
        $this->processCommand->run($inputMock, $outputMock);
    }
    
    /**
     * @covers \Aune\AutoInvoice\Console\ProcessCommand::execute
     */
    public function testExecute()
    {
        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $inputMock->expects(self::exactly(1))
            ->method('getOption')
            ->with(ProcessCommand::OPTION_DRY_RUN)
            ->willReturn(false);
        
        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $itemMocks = [];
        for ($i=0; $i<10; $i++) {
            $orderMock = $this->getMockBuilder(Order::class)
                ->disableOriginalConstructor()
                ->getMock();
            
            $itemMock = $this->createMock(InvoiceProcessItemInterface::class);
            $itemMock->method('getOrder')
                ->willReturn($orderMock);
            
            $itemMocks []= $itemMock;
        }
        
        $this->invoiceProcessMock->expects(self::once())
            ->method('getItemsToProcess')
            ->willReturn($itemMocks);
        
        $this->invoiceProcessMock->expects(self::exactly(count($itemMocks)))
            ->method('invoice');
        
        $this->processCommand->run($inputMock, $outputMock);
    }
}
