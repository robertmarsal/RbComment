<?php

namespace RbCommentTest\Controller;

use RbComment\Model\CommentTable;
use RbComment\Controller\CommentController;
use PHPUnit_Framework_TestCase;

class ConsoleControllerTest extends PHPUnit_Framework_TestCase
{
    protected $serviceLocatorMock;

    public function setUp()
    {
        $this->serviceLocatorMock = $this->createMock(ServiceLocatorInterface::class);
    }

    public function testDeleteSpamAction()
    {
        $deletedCount = rand(1, 100);

        $commentTableMock = $this->getMock('RbComment\Model\CommentTable', [
            'deleteSpam'
        ], [], '', false);

        // Expect this to be called once
        $commentTableMock->expects($this->once())
                         ->method('deleteSpam')
                         ->will($this->returnValue($deletedCount));

        // ServiceLocator Mock Setup
        $this->serviceLocatorMock->expects($this->once())
                                 ->method('get')
                                 ->with('RbComment\Model\CommentTable')
                                 ->will($this->returnValue($commentTableMock));

        // CommentController Mock
        $consoleControllerMock = $this->getMock(
            'RbComment\Controller\ConsoleController',
            ['getServiceLocator'],
            [],
            '',
            false
        );

        $consoleControllerMock->expects($this->once())
                              ->method('getServiceLocator')
                              ->will($this->returnValue($this->serviceLocatorMock));

        // Capture output
        ob_start();
        $consoleControllerMock->deleteSpamAction();
        $output = ob_get_clean();

        $this->assertEquals($output, $deletedCount . ' spam comments removed' . PHP_EOL);
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
}
