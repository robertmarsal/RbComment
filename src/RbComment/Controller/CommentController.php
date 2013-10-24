<?php

namespace RbComment\Controller;

use RbComment\Model\Comment;
use RbComment\Form\CommentForm;
use Zend\Mvc\Controller\AbstractActionController;

class CommentController extends AbstractActionController
{
    /**
     * @var \RbComment\Model\CommentTable
     */
    protected $commentTable;

    public function addAction()
    {
        $form = new CommentForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = new Comment();
            $form->setInputFilter($comment->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $comment->exchangeArray($form->getData());

                // Set default visibility from config
                $config = $this->getServiceLocator()->get('Config');
                if(isset($config['rb_comment']['default_visibility'])) {
                    $comment->visible = $config['rb_comment']['default_visibility'];
                }

                $this->getCommentTable()->saveComment($comment);

                return $this->redirect()->toUrl($form->get('uri')->getValue());
            } else {
                $this->flashMessenger()->setNamespace('RbComment');
                $this->flashMessenger()->addMessage(json_encode($form->getMessages()));
                return $this->redirect()->toUrl($form->get('uri')->getValue() . '#rbcomment');
            }
        }
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