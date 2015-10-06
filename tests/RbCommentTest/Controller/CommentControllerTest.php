<?php

namespace RbCommentTest\Controller;

use RbComment\Model\CommentTable;
use RbComment\Controller\CommentController;
use ZendService\Akismet\Akismet;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class CommentControllerTest extends PHPUnit_Framework_TestCase
{
    protected $configMock = [
        'rb_comment' => [
            'strings' => [
                'author'  => 'author',
                'contact' => 'contact',
                'content' => 'content',
                'submit'  => 'submit',
            ],
        ],
    ];

    protected $requestMock;

    protected $serviceLocatorMock;

    public function setUp()
    {
        $this->serviceLocatorMock = $this->getMock(
            'Zend\ServiceManager\ServiceLocatorInterface',
            ['get', 'has'],
            [],
            'ServiceLocatorInterface'
        );

        $this->requestMock = $this->getMock(
            'Zend\Http\Request',
            ['isPost', 'getPost'],
            [],
            '',
            false
        );

        // Global values
        $_SERVER['HTTP_USER_AGENT'] = 'RbComment Testing Suite';
    }

    public function testAddActionOnlyWorksWithPostMethod()
    {
        // ServiceLocator Mock Setup
        $this->serviceLocatorMock->expects($this->once())
                                 ->method('get')
                                 ->with('Config')
                                 ->will($this->returnValue($this->configMock));

        // Request Mock Setup
        $this->requestMock->expects($this->once())
                    ->method('isPost')
                    ->will($this->returnValue(false));

        // CommentController Mock
        $commentControllerMock = $this->getMock(
            'RbComment\Controller\CommentController',
            ['getRequest', 'getServiceLocator'],
            [],
            '',
            false
        );

        $commentControllerMock->expects($this->once())
                              ->method('getServiceLocator')
                              ->will($this->returnValue($this->serviceLocatorMock));

        $commentControllerMock->expects($this->once())
                              ->method('getRequest')
                              ->will($this->returnValue($this->requestMock));

        $commentControllerMock->addAction();
    }

    public function testAddActionLogsFormErrorsIntoTheRbCommentNamespace()
    {
        //'contact' key is missing on purpose
        $postMock = [
            'author' => 'Tester',
            'content' => 'test',
            'uri' => '/test',
        ];

        // ServiceLocator Mock Setup
        $this->serviceLocatorMock->expects($this->once())
                                 ->method('get')
                                 ->with('Config')
                                 ->will($this->returnValue($this->configMock));

        // Request Mock Setup
        $this->requestMock->expects($this->once())
                    ->method('isPost')
                    ->will($this->returnValue(true));

        $this->requestMock->expects($this->once())
                    ->method('getPost')
                    ->will($this->returnValue($postMock));

        // FlashMessenger Mock
        $flashMessengerMock = $this->getMock(
            'Zend\Mvc\Controller\Plugin\FlashMessenger',
            ['setNamespace', 'addMessage'],
            [],
            '',
            false
        );

        $flashMessengerMock->expects($this->once())
                           ->method('setNamespace')
                           ->with('RbComment');

        // Redirect Mock
        $redirectMock = $this->getMock(
            'Zend\Mvc\Controller\Plugin\Redirect',
            ['toUrl'],
            [],
            '',
            false
        );

        $redirectMock->expects($this->once())
                     ->method('toUrl')
                     ->with($postMock['uri'] . '#rbcomment');

        // CommentController Mock
        $commentControllerMock = $this->getMock(
            'RbComment\Controller\CommentController',
            ['getRequest', 'getServiceLocator', 'flashMessenger', 'redirect'],
            [],
            '',
            false
        );

        $commentControllerMock->expects($this->once())
                              ->method('getServiceLocator')
                              ->will($this->returnValue($this->serviceLocatorMock));

        $commentControllerMock->expects($this->once())
                              ->method('getRequest')
                              ->will($this->returnValue($this->requestMock));

        $commentControllerMock->expects($this->exactly(2))
                              ->method('flashMessenger')
                              ->will($this->returnValue($flashMessengerMock));

        $commentControllerMock->expects($this->once())
                              ->method('redirect')
                              ->will($this->returnValue($redirectMock));

        $commentControllerMock->addAction();
    }

    /**
     * @dataProvider isSpamDataProvider
     */
    public function testIsSpam($comment, $isSpam)
    {
        $rbCommentConfig = (object) [
            'akismet' => [
                'proxy' => [
                    'use' => false,
                    'trusted' => [],
                    'header' => '',
                ],
            ],
        ];

        $akismetServiceMock = $this->getMock(
            'ZendService\Akismet\Akismet',
            ['isSpam'],
            [],
            '',
            false
        );

        $akismetServiceMock->expects($this->once())
                           ->method('isSpam')
                           ->will($this->returnValue($isSpam));

        $this->serviceLocatorMock->expects($this->once())
                                 ->method('get')
                                 ->with('RbComment\Akismet')
                                 ->will($this->returnValue($akismetServiceMock));

        $commentControllerReflection = new ReflectionClass('RbComment\Controller\CommentController');

        $isSpamReflection = $commentControllerReflection->getMethod('isSpam');
        $isSpamReflection->setAccessible(true);

        $commentController = new CommentController();
        $commentController->setServiceLocator($this->serviceLocatorMock);

        $this->assertEquals($isSpam, $isSpamReflection->invoke($commentController, $comment, $rbCommentConfig));
    }

    public static function isSpamDataProvider()
    {
        return [
            [
                // comment
                (object) [
                    'author' => 'not a spammer',
                    'contact' => 'me@me.com',
                    'content' => 'test',
                ],
                // isSpam
                false,
            ],
            [
                // comment
                (object) [
                    'author' => 'spammer',
                    'contact' => 'spam@spamfactory.com',
                    'content' => 'spam',
                ],
                // isSpam
                true,
            ],
        ];
    }

    public function testGetCommentTableReturnsAnInstanceOfCommentTable()
    {
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            [],
            [],
            '',
            false
        );

        $commentTable = new CommentTable($tableGatewayMock);

        $this->serviceLocatorMock->expects($this->once())
                                 ->method('get')
                                 ->with('RbComment\Model\CommentTable')
                                 ->will($this->returnValue($commentTable));

        $commentController = new CommentController();
        $commentController->setServiceLocator($this->serviceLocatorMock);

        $this->assertEquals($commentTable, $commentController->getCommentTable());
        $this->assertInstanceOf('RbComment\Model\CommentTable', $commentController->getCommentTable());
    }

    public function testGetAkismetServiceReturnsAnInstanceOfAkismet()
    {
        $akismetService = new Akismet('test', 'test');

        $this->serviceLocatorMock->expects($this->once())
                                 ->method('get')
                                 ->with('RbComment\Akismet')
                                 ->will($this->returnValue($akismetService));

        $commentController = new CommentController();
        $commentController->setServiceLocator($this->serviceLocatorMock);

        $this->assertEquals($akismetService, $commentController->getAkismetService());
        $this->assertInstanceOf('ZendService\Akismet\Akismet', $commentController->getAkismetService());
    }
}
