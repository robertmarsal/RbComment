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
        $config = $this->getServiceLocator()->get('Config');
        $form = new CommentForm($config['rb_comment']['strings']);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = new Comment();
            $form->setInputFilter($comment->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $comment->exchangeArray($form->getData());

                // Set default visibility from config
                $comment->visible = $config['rb_comment']['default_visibility'];

                // We need the id for the mailer
                $comment->id = $this->getCommentTable()->saveComment($comment);

                // Send email if active
                if($config['rb_comment']['email']['notify'] === true) {
                    $this->rbMailer($comment);
                }

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