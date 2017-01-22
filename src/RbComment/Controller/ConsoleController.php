<?php
namespace RbComment\Controller;

use RbComment\Model\CommentTable;
use Zend\Console\Console;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    private $commentTable;

    public function __construct(CommentTable $commentTable)
    {
        $this->commentTable = $commentTable;
    }

    public function deleteSpamAction()
    {
        $deleted = $this->commentTable->deleteSpam();

        $console = Console::getInstance();
        $console->writeLine($deleted . ' spam comments removed');
    }
}
