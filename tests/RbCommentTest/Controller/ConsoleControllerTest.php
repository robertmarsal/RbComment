<?php
namespace RbCommentTest\Controller;

use RbComment\Controller\ConsoleController;
use RbComment\Model\CommentTable;
use PHPUnit_Framework_TestCase;

class ConsoleControllerTest extends PHPUnit_Framework_TestCase
{
    protected $commentTableMock;

    public function setUp()
    {
        $this->commentTableMock = $this->createMock(CommentTable::class);
    }

    /**
     * @group controller
     */
    public function testDeleteSpamAction()
    {
        $deletedCount = rand(1, 100);

        // Expect this to be called once
        $this->commentTableMock->expects($this->once())
                               ->method('deleteSpam')
                               ->will($this->returnValue($deletedCount));

        // CommentController Mock
        $consoleControllerMock = new ConsoleController($this->commentTableMock);

        // Capture output
        ob_start();
        $consoleControllerMock->deleteSpamAction();
        $output = ob_get_clean();

        $this->assertEquals($output, $deletedCount . ' spam comments removed' . PHP_EOL);
    }
}
