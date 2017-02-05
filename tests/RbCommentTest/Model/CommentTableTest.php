<?php
namespace RbCommentTest\Model;

use RbComment\Model\Comment;
use RbComment\Model\CommentTable;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway\TableGateway;

class CommentTableTest extends PHPUnit_Framework_TestCase
{
    private $tableGatewayMock;

    public function setUp()
    {
        $this->tableGatewayMock = $this->createMock(TableGateway::class);
    }

    /**
     * @group table
     */
    public function testConstructorStoresDependencies()
    {
        $commentTable = new CommentTable($this->tableGatewayMock);

        // Assertions
        $this->assertAttributeEquals($this->tableGatewayMock, 'tableGateway', $commentTable);
    }

    /**
     * @group table
     */
    public function testFetchAllReturnsAllComments()
    {
        $resultSet = new ResultSet();

        $this->tableGatewayMock->expects($this->once())
                               ->method('select')
                               ->willReturn($resultSet);

        $commentTable = new CommentTable($this->tableGatewayMock);

        $this->assertSame($resultSet, $commentTable->fetchAll());
    }

    /**
     * @group table
     */
    public function testFetchAllForThreadReturnsAllTheCommentsInThread()
    {
        $resultSet = new ResultSet();

        $this->tableGatewayMock->expects($this->once())
                               ->method('selectWith')
                               ->willReturn($resultSet);

        $commentTable = new CommentTable($this->tableGatewayMock);

        $this->assertSame($resultSet, $commentTable->fetchAllForThread('test'));
    }

    /**
     * @group table
     */
    public function testFetchAllPendingForThreadReturnsAllThePendingCommentsInAThread()
    {
        $resultSet = new ResultSet();

        $this->tableGatewayMock->expects($this->once())
                               ->method('selectWith')
                               ->willReturn($resultSet);

        $commentTable = new CommentTable($this->tableGatewayMock);

        $this->assertSame($resultSet, $commentTable->fetchAllPendingForThread('test'));
    }

    /**
     * @group table
     */
    public function testFetchAllUsingSelectUsesTheCustomSelectAndReturnsTheResult()
    {
        $resultSetMock = new ResultSet();
        $selectMock    = new Select();

        $this->tableGatewayMock->expects($this->once())
                               ->method('selectWith')
                               ->with($selectMock)
                               ->willReturn($resultSetMock);

        $commentTableMock = new CommentTable($this->tableGatewayMock);

        $this->assertSame(
            $resultSetMock,
            $commentTableMock->fetchAllUsingSelect($selectMock)
        );
    }

    /**
     * @group table
     */
    public function testCanRetrieveACommentByItsId()
    {
        $commentData = [
            'id'      => 12345,
            'thread'  => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'     => '/test',
            'author'  => 'Robert Boloc',
            'contact' => 'robertboloc@gmail.com',
            'content' => 'Bla bla bla',
            'visible' => 1,
            'spam'    => 0,
        ];

        $comment = new Comment();
        $comment->exchangeArray($commentData);

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new Comment());
        $resultSet->initialize([$commentData]);

        $this->tableGatewayMock->expects($this->once())
                               ->method('select')
                               ->with(['id' => 12345])
                               ->will($this->returnValue($resultSet));

        $commentTable = new CommentTable($this->tableGatewayMock);

        $this->assertEquals($comment, $commentTable->getComment(12345));
    }

    /**
     * @group table
     */
    public function testSaveCommentWillInsertNewCommentIfDoesNotAlreadyHaveAnId()
    {
        $comment = new Comment();
        $commentData = [
            'thread'  => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'     => '/test',
            'author'  => 'Robert Boloc',
            'contact' => 'robertboloc@gmail.com',
            'content' => 'Bla bla bla',
            'visible' => 1,
            'spam'    => 0,
        ];

        $comment->exchangeArray($commentData);

        $this->tableGatewayMock->expects($this->once())
                               ->method('insert')
                               ->with($commentData);

        $commentTable = new CommentTable($this->tableGatewayMock);
        $commentTable->saveComment($comment);
    }

    /**
     * @group table
     */
    public function testSaveCommentWillUpdateExistingCommentIfItAlreadyHasAnId()
    {
        $comment = new Comment();
        $commentData = [
            'id'      => 12345,
            'thread'  => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'     => '/test',
            'author'  => 'Robert Boloc',
            'contact' => 'robertboloc@gmail.com',
            'content' => 'Bla bla bla',
            'visible' => 1,
            'spam'    => 0,
        ];

        $comment->exchangeArray($commentData);

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new Comment());
        $resultSet->initialize([$commentData]);

        $this->tableGatewayMock->expects($this->once())
                               ->method('select')
                               ->with(['id' => 12345])
                               ->will($this->returnValue($resultSet));

        $updateWith = [
            'thread'  => 'f133a4599372cf531bcdbfeb1116b9afe8d09b4f',
            'uri'     => '/test',
            'author'  => 'Robert Boloc',
            'contact' => 'robertboloc@gmail.com',
            'content' => 'Bla bla bla',
            'visible' => 1,
            'spam'    => 0,
        ];

        $this->tableGatewayMock->expects($this->once())
                               ->method('update')
                               ->with($updateWith, ['id' => 12345]);

        $commentTable = new CommentTable($this->tableGatewayMock);
        $commentTable->saveComment($comment);
    }

    /**
     * @group table
     */
    public function testCanDeleteACommentByItsId()
    {
        $this->tableGatewayMock->expects($this->once())
                               ->method('delete')
                               ->with(['id' => 12345]);

        $commentTable = new CommentTable($this->tableGatewayMock);
        $commentTable->deleteComment(12345);
    }

    /**
     * @group table
     */
    public function testCanDeleteSpam()
    {
        $this->tableGatewayMock->expects($this->once())
                               ->method('delete')
                               ->with(['spam' => 1]);

        $commentTable = new CommentTable($this->tableGatewayMock);
        $commentTable->deleteSpam();
    }
}
