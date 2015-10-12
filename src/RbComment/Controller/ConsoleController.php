<?php
namespace RbComment\Controller;

use Zend\Console\Console;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * @var \RbComment\Model\CommentTable
     */
    protected $commentTable;

    public function deleteSpamAction()
    {
        $deleted = $this->getCommentTable()->deleteSpam();

        $console = Console::getInstance();
        $console->writeLine($deleted . ' spam comments removed');
    }

    /**
     * @return \RbComment\Model\CommentTable
     */
    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('RbComment\Model\CommentTable');
        }

        return $this->commentTable;
    }
}
