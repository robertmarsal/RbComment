<?php
namespace RbCommentTest\Factory\Model;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Factory\Model\RbCommentTableGatewayFactory;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

final class RbCommentTableGatewayFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateARbCommentTableGatewayInstance()
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $adapterMock   = $this->createMock(Adapter::class);

        $factory = new RbCommentTableGatewayFactory();

        // Expectations
        $containerMock->expects($this->once())
                      ->method('get')
                      ->with(Adapter::class)
                      ->willReturn($adapterMock);

        $table = $factory($containerMock, 'RbCommentTableGateway');

        // Assertions
        $this->assertInstanceOf(TableGateway::class, $table);
    }
}
