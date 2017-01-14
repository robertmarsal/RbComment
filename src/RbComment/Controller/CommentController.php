<?php
namespace RbComment\Controller;

use RbComment\Model\Comment;
use RbComment\Form\CommentForm;
use RbComment\Model\CommentTable;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mvc\Controller\AbstractActionController;
use ZendService\Akismet\Akismet;

class CommentController extends AbstractActionController
{
    /**
     * @var CommentTable
     */
    private $commentTable;

    /**
     * @var Akismet
     */
    private $akismetService;

    /**
     * @var array
     */
    private $configService;

    public function __construct(
        array $configService,
        CommentTable $commentTable,
        Akismet $akismetService
    ) {
        $this->configService  = $configService;
        $this->commentTable   = $commentTable;
        $this->akismetService = $akismetService;
    }

    public function addAction()
    {
        $rbCommentConfig = (object) $this->configService['rb_comment'];

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
                if (($rbCommentConfig->akismet['enabled'] === true) &&
                    $this->isSpam($comment, $rbCommentConfig)) {
                    $comment->spam = 1;
                    $comment->visible = 0;
                }

                // We need the id for the mailer
                $comment->id = $this->commentTable->saveComment($comment);

                // Send email if active and not spam
                if (($rbCommentConfig->email['notify'] === true) &&
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

        return $this->akismetService->isSpam([
            'user_ip' => $remote->getIpAddress(),
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'comment_type' => 'comment',
            'comment_author' => $comment->author,
            'comment_author_email' => $comment->contact,
            'comment_content' => $comment->content,
        ]);
    }
}
