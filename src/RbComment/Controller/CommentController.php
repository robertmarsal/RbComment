<?php

namespace RbComment\Controller;

use RbComment\Model\Comment;
use RbComment\Form\CommentForm;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mvc\Controller\AbstractActionController;

class CommentController extends AbstractActionController
{
    /**
     * @var \RbComment\Model\CommentTable
     */
    protected $commentTable;

    /**
     * @var \ZendService\Akismet\Akismet
     */
    protected $akismetService;

    public function addAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $rbCommentConfig = (object) $config['rb_comment'];

        $form = new CommentForm($rbCommentConfig->strings);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = new Comment();
            $form->setInputFilter($comment->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $comment->exchangeArray($form->getData());

                // Set default visibility from config
                $comment->visible = $rbCommentConfig->default_visibility;

                // If akismet is enabled check for spam
                if(($rbCommentConfig->akismet['enabled'] === true) &&
                    $this->isSpam($comment, $rbCommentConfig)) {
                    $comment->spam = 1;
                    $comment->visible = 0;
                }

                // We need the id for the mailer
                $comment->id = $this->getCommentTable()->saveComment($comment);

                // Send email if active and not spam
                if(($rbCommentConfig->email['notify'] === true) &&
                    ($comment->spam === 0)) {
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
     * Checks if a comment is spam using the akismet service.
     *
     * @param  \RbComment\Model\Comment $comment
     * @param  mixed                    $rbCommentConfig
     * @return boolean
     */
    protected function isSpam($comment, $rbCommentConfig)
    {
        $remote = new RemoteAddress();
        $remote->setUseProxy($rbCommentConfig->akismet['proxy']['use']);
        $remote->setTrustedProxies($rbCommentConfig->akismet['proxy']['trusted']);
        $remote->setProxyHeader($rbCommentConfig->akismet['proxy']['header']);

        return $this->getAkismetService()->isSpam(array(
            'user_ip' => $remote->getIpAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'comment_type' => 'comment',
            'comment_author' => $comment->author,
            'comment_author_email' => $comment->contact,
            'comment_content' => $comment->content,
        ));
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

    /**
     * @return \ZendService\Akismet\Akismet
     */
    public function getAkismetService()
    {
        if (!$this->akismetService) {
            $sm = $this->getServiceLocator();
            $this->akismetService = $sm->get('RbComment\Akismet');
        }

        return $this->akismetService;
    }
}
