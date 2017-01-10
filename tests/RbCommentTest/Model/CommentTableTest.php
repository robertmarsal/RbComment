<?php

namespace RbCommentTest\Model;

use RbComment\Model\Comment;
use RbComment\Model\CommentTable;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class CommentTableTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorSetsDependencies()
    {
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            [],
            [],
            'TableGateway',
            false
        );

        $commentTable = new CommentTable($tableGatewayMock);

        $reflectedCommentTable = new ReflectionClass($commentTable);
        $tableGatewayReflectionProperty = $reflectedCommentTable->getProperty('tableGateway');
        $tableGatewayReflectionProperty->setAccessible(true);

        $this->assertInstanceOf(
            'Zend\Db\TableGateway\TableGateway',
            $tableGatewayReflectionProperty->getValue($commentTable)
        );
    }

    public function testFetchAllReturnsAllComments()
    {
        $resultSet        = new ResultSet();
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['select'],
            [],
            '',
            false
        );
        $tableGatewayMock->expects($this->once())
                         ->method('select')
                         ->will($this->returnValue($resultSet));

        $commentTable = new CommentTable($tableGatewayMock);

        $this->assertSame($resultSet, $commentTable->fetchAll());
    }

    public function testFetchAllForThreadReturnsAllTheCommentsInThread()
    {
        $resultSet        = new ResultSet();
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['selectWith'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('selectWith')
                         ->will($this->returnValue($resultSet));

        $commentTable = new CommentTable($tableGatewayMock);

        $this->assertSame($resultSet, $commentTable->fetchAllForThread('test'));
    }

    public function testFetchAllPendingForThreadReturnsAllThePendingCommentsInAThread()
    {
        $resultSet        = new ResultSet();
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['selectWith'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('selectWith')
                         ->will($this->returnValue($resultSet));

        $commentTable = new CommentTable($tableGatewayMock);

        $this->assertSame($resultSet, $commentTable->fetchAllPendingForThread('test'));
    }

    public function testFetchAllUsingSelectUsesTheCustomSelectAndReturnsTheResult()
    {
        $tableGatewayMock =
            $this->getMockBuilder('Zend\Db\TableGateway\TableGateway')
                 ->setMethods(['selectWith'])
                 ->disableOriginalConstructor()
                 ->getMock();

        $resultSetMock = new ResultSet();
        $selectMock = new Select();

        $tableGatewayMock->expects($this->once())
                         ->method('selectWith')
                         ->with($selectMock)
                         ->will($this->returnValue($resultSetMock));

        $commentTableMock = new CommentTable($tableGatewayMock);

        $this->assertSame(
            $resultSetMock,
            $commentTableMock->fetchAllUsingSelect($selectMock)
        );
    }

    public function testCanRetrieveACommentByItsId()
    {
        $comment = new Comment();
        $comment->exchangeArray([
            'id'     => 12345,
            'thread' => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'  => '/test',
            'author'  => 'Robert Boloc',
            'contact'  => 'robertboloc@gmail.com',
            'content'  => 'Bla bla bla',
            'visible'  => 1,
            'spam'  => 0,
        ]);

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new Comment());
        $resultSet->initialize([$comment]);

        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['select'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('select')
                         ->with(['id' => 12345])
                         ->will($this->returnValue($resultSet));

        $commentTable = new CommentTable($tableGatewayMock);

        $this->assertSame($comment, $commentTable->getComment(12345));
    }

    public function testSaveCommentWillInsertNewCommentIfDoesNotAlreadyHaveAnId()
    {
        $comment = new Comment();
        $commentData = [
            'thread' => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'  => '/test',
            'author'  => 'Robert Boloc',
            'contact'  => 'robertboloc@gmail.com',
            'content'  => 'Bla bla bla',
            'visible'  => 1,
            'spam'  => 0,
        ];

        $comment->exchangeArray($commentData);

        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['insert'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('insert')
                         ->with($commentData);

        $commentTable = new CommentTable($tableGatewayMock);
        $commentTable->saveComment($comment);
    }

    public function testSaveCommentWillUpdateExistingCommentIfItAlreadyHasAnId()
    {
        $comment = new Comment();
        $commentData = [
            'id' => 12345,
            'thread' => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'  => '/test',
            'author'  => 'Robert Boloc',
            'contact'  => 'robertboloc@gmail.com',
            'content'  => 'Bla bla bla',
            'visible'  => 1,
            'spam'  => 0,
        ];

        $comment->exchangeArray($commentData);

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new Comment());
        $resultSet->initialize([$comment]);

        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['select', 'update'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('select')
                         ->with(['id' => 12345])
                         ->will($this->returnValue($resultSet));
        $tableGatewayMock->expects($this->once())
                         ->method('update')
                         ->with([
            'thread' => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'  => '/test',
            'author'  => 'Robert Boloc',
            'contact'  => 'robertboloc@gmail.com',
            'content'  => 'Bla bla bla',
            'visible'  => 1,
            'spam'  => 0,
                         ], ['id' => 12345]);

        $commentTable = new CommentTable($tableGatewayMock);
        $commentTable->saveComment($comment);
    }

    public function testCanDeleteACommentByItsId()
    {
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['delete'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('delete')
                         ->with(['id' => 12345]);

        $commentTable = new CommentTable($tableGatewayMock);
        $commentTable->deleteComment(12345);
    }

    public function testCanDeleteSpam()
    {
        $tableGatewayMock = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            ['delete'],
            [],
            '',
            false
        );

        $tableGatewayMock->expects($this->once())
                         ->method('delete')
                         ->with(['spam' => 1]);

        $commentTable = new CommentTable($tableGatewayMock);
        $commentTable->deleteSpam();
    }
}
