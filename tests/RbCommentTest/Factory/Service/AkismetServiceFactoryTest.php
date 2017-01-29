<?php
namespace RbCommentTest\Factory\Service;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RbComment\Factory\Service\AkismetServiceFactory;
use Zend\View\Helper\ServerUrl;
use ZendService\Akismet\Akismet;

final class AkismetServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group factory
     */
    public function testInvokeWillCreateAnAkismetServiceInstance()
    {
        $containerMock = $this->createMock(ContainerInterface::class);

        $viewHelperManagerMock =
            $this->getMockBuilder('ViewHelperManager')
                 ->setMethods(['get'])
                 ->getMock();

        $serverUrlMock =
            $this->getMockBuilder(ServerUrl::class)
                 ->setMethods(['__invoke'])
                 ->getMock();

        $configMock = [
            'rb_comment' => [
                'akismet' => [
                    'api_key' => uniqid(),
                ],
            ],
        ];

        $factory = new AkismetServiceFactory();

        // Expectations
        $containerMock->expects($this->exactly(2))
                      ->method('get')
                      ->withConsecutive(
                          ['Config'],
                          ['ViewHelperManager']
                      )
                      ->willReturnOnConsecutiveCalls(
                          $configMock,
                          $viewHelperManagerMock
                      );

        $viewHelperManagerMock->expects($this->once())
                              ->method('get')
                              ->with('serverUrl')
                              ->willReturn($serverUrlMock);

        $serverUrlMock->expects($this->once())
                      ->method('__invoke')
                      ->willReturn('http://test.com');

        $table = $factory($containerMock, Akismet::class);

        // Assertions
        $this->assertInstanceOf(Akismet::class, $table);
    }
}
