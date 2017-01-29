<?php
namespace RbCommentTest\Factory\Controller;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Controller\ConsoleController;
use RbComment\Factory\Controller\ConsoleControllerFactory;
use RbComment\Model\CommentTable;

final class ConsoleControllerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateAConsoleControllerInstance()
    {
        $containerMock    = $this->createMock(ContainerInterface::class);
        $commentTableMock = $this->createMock(CommentTable::class);

        $consoleControllerFactory = new ConsoleControllerFactory();

        // Expectations
        $containerMock->expects($this->once())
                      ->method('get')
                      ->with(CommentTable::class)
                      ->willReturn($commentTableMock);

        $controller = $consoleControllerFactory($containerMock, CommentController::class);

        // Assertions
        $this->assertInstanceOf(ConsoleController::class, $controller);
    }
}
