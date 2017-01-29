<?php
namespace RbCommentTest\Factory\Model;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Factory\Model\CommentTableFactory;
use RbComment\Model\CommentTable;
use Zend\Db\TableGateway\TableGateway;

final class CommentTableFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateACommentTableInstance()
    {
        $containerMock             = $this->createMock(ContainerInterface::class);
        $rbCommentTableGatewayMock = $this->createMock(TableGateway::class);

        $commentTableFactory = new CommentTableFactory();

        // Expectations
        $containerMock->expects($this->once())
                      ->method('get')
                      ->with('RbCommentTableGateway')
                      ->willReturn($rbCommentTableGatewayMock);

        $table = $commentTableFactory($containerMock, CommentController::class);

        // Assertions
        $this->assertInstanceOf(CommentTable::class, $table);
    }
}
