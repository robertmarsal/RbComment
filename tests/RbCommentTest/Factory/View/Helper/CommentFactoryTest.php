<?php
namespace RbCommentTest\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Factory\View\Helper\CommentFactory;
use RbComment\Model\CommentTable;
use RbComment\View\Helper\Comment;

final class CommentFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateACommentInstance()
    {
        $containerMock = $this->createMock(ContainerInterface::class);

        $viewHelperManagerMock =
            $this->getMockBuilder('ViewHelperManager')
                 ->setMethods(['get'])
                 ->getMock();

        $routerMock = $this->getMockBuilder('Router')->getMock();

        $configMock = [uniqid()];

        $commentTableMock = $this->createMock(CommentTable::class);

        $factory = new CommentFactory();

        // Expectations
        $containerMock->expects($this->exactly(4))
                      ->method('get')
                      ->withConsecutive(
                          ['ViewHelperManager'],
                          ['Router'],
                          ['Config'],
                          [CommentTable::class]
                      )
                      ->willReturnOnConsecutiveCalls(
                          $viewHelperManagerMock,
                          $routerMock,
                          $configMock,
                          $commentTableMock
                      );

        $table = $factory($containerMock, Comment::class);

        // Assertions
        $this->assertInstanceOf(Comment::class, $table);
    }
}
