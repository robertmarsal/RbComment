<?php
namespace RbComment\View\Helper;

use RbComment\Form\CommentForm;
use RbComment\Model\CommentTable;
use Zend\View\Helper\AbstractHelper;

class Comment extends AbstractHelper
{
    protected $themes = [
        'default'    => true,
        'uikit'      => true,
        'bootstrap3' => true,
    ];

    private $viewHelperManager;
    private $routerService;
    private $configService;
    private $commentTable;

    public function __construct(
        $viewHelperManager,
        $routerService,
        array $configService,
        CommentTable $commentTable
    ) {
        $this->viewHelperManager = $viewHelperManager;
        $this->routerService     = $routerService;
        $this->configService     = $configService;
        $this->commentTable      = $commentTable;
    }

    public function __invoke($theme = 'default')
    {
        // If using a custom theme/partial do not append the prefix
        $invokablePartial = isset($this->themes[$theme])
            ? 'rbcomment/theme/' . $theme
            : $theme;

        $uri = $this->routerService->getRequestUri()->getPath();
        $thread = sha1($uri);
        $validationMessages = $this->viewHelperManager->get('flashMessenger')
                                                      ->getMessagesFromNamespace('RbComment');

        $strings = $this->configService['rb_comment']['strings'];

        echo $this->viewHelperManager->get('partial')->__invoke($invokablePartial, [
            'comments'          => $this->commentTable->fetchAllForThread($thread),
            'form'              => new CommentForm($strings),
            'thread'            => $thread,
            'validationResults' => count($validationMessages) > 0
                ? json_decode(array_shift($validationMessages))
                : [],
            'uri'               => $uri,
            'strings'           => $strings,
            'zfc_user'          => $this->configService['rb_comment']['zfc_user']['enabled'],
            'gravatar'          => $this->configService['rb_comment']['gravatar']['enabled'],
        ]);
    }
}
