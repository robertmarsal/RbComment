<?php
namespace RbCommentTest\Factory\Mvc\Controller\Plugin;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Factory\Mvc\Controller\Plugin\MailerFactory;
use RbComment\Mvc\Controller\Plugin\Mailer;
use Zend\Mail\Transport\Sendmail;

final class MailerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateAMailerInstance()
    {
        $containerMock = $this->createMock(ContainerInterface::class);

        $viewHelperManagerMock =
            $this->getMockBuilder('ViewHelperManager')
                 ->setMethods(['get'])
                 ->getMock();
        $mailerMock            = $this->createMock(Sendmail::class);
        $configMock            = [uniqid()];

        $serverUrlMock = uniqid();

        $factory = new MailerFactory();

        // Expectations
        $containerMock->expects($this->exactly(3))
                      ->method('get')
                      ->withConsecutive(
                          ['ViewHelperManager'],
                          ['RbComment\Mailer'],
                          ['Config']
                      )
                      ->willReturnOnConsecutiveCalls(
                          $viewHelperManagerMock,
                          $mailerMock,
                          $configMock
                      );

        $viewHelperManagerMock->expects($this->once())
                              ->method('get')
                              ->with('serverUrl')
                              ->willReturn($serverUrlMock);

        $table = $factory($containerMock, Mailer::class);

        // Assertions
        $this->assertInstanceOf(Mailer::class, $table);
    }
}
