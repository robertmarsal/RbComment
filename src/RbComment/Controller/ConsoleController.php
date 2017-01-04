<?php
namespace RbComment\Controller;

use RbComment\Util\CommentTableAwareTrait;
use Zend\Console\Console;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    use CommentTableAwareTrait;

    public function deleteSpamAction()
    {
        $deleted = $this->getCommentTable()->deleteSpam();

        $console = Console::getInstance();
        $console->writeLine($deleted . ' spam comments removed');
    }
}
