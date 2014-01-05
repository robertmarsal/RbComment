<?php

namespace RbCommentTest\Model;

use RbComment\Model\CommentTable;
use RbComment\Controller\CommentController;
use ZendService\Akismet\Akismet;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class CommentControllerTest extends PHPUnit_Framework_TestCase
{
    protected $serviceLocatorMock;

    public function setUp()
    {
        $this->serviceLocatorMock = $this->getMock(
            'Zend\ServiceManager\ServiceLocatorInterface',
            array('get', 'has'),
            array(),
            'ServiceLocatorInterface'
        );

        // Global values
        $_SERVER['HTTP_USER_AGENT'] = 'RbComment Testing Suite';
    }

    /**
     * @dataProvider isSpamDataProvider
     */
    public function testIsSpam($comment, $isSpam)
    {
        $rbCommentConfig = (object) array(
            'akismet' => array(
                'proxy' => array(
                    'use' => false,
                    'trusted' => array(),
                    'header' => '',
                ),
            ),
        );

        $akismetServiceMock = $this->getMock(
            'ZendService\Akismet\Akismet',
            array('isSpam'),
            array(),
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
        return array(
            array(
                // comment
                (object) array(
                    'author' => 'not a spammer',
                    'contact' => 'me@me.com',
                    'content' => 'test',
                ),
                // isSpam
                false,
            ),
            array(
                // comment
                (object) array(
                    'author' => 'spammer',
                    'contact' => 'spam@spamfactory.com',
                    'content' => 'spam',
                ),
                // isSpam
                true,
            ),
        );
    }

    public function testGetCommentTableReturnsAnInstanceOfCommentTable()
    {
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array(),
            array(),
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
