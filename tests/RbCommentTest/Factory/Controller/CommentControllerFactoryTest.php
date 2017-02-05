<?php
namespace RbCommentTest\Factory\Controller;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Controller\CommentController;
use RbComment\Factory\Controller\CommentControllerFactory;
use RbComment\Model\CommentTable;
use ZendService\Akismet\Akismet;

final class CommentControllerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateACommentControllerInstance()
    {
        $containerMock = $this->createMock(ContainerInterface::class);

        $configServiceMock  = [uniqid()];
        $commentTableMock   = $this->createMock(CommentTable::class);
        $akismetServiceMock = $this->createMock(Akismet::class);

        $commentControllerFactory = new CommentControllerFactory();

        // Expectations
        $containerMock->expects($this->exactly(3))
                      ->method('get')
                      ->withConsecutive(
                          ['Config'],
                          [CommentTable::class],
                          ['RbComment\Akismet']
                      )
                      ->willReturnOnConsecutiveCalls(
                          $configServiceMock,
                          $commentTableMock,
                          $akismetServiceMock
                      );

        $controller = $commentControllerFactory($containerMock, CommentController::class);

        // Assertions
        $this->assertInstanceOf(CommentController::class, $controller);
    }
}
