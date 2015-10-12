<?php
namespace RbComment\View\Helper;

use RbComment\Form\CommentForm;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Comment extends AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $themes = [
        'default'    => true,
        'uikit'      => true,
        'bootstrap3' => true,
    ];

    public function __invoke($theme = 'default')
    {
        // If using a custom theme/partial do not append the prefix
        $invokablePartial = isset($this->themes[$theme])
            ? 'rbcomment/theme/' . $theme
            : $theme;

        $serviceManager = $this->getServiceLocator()->getServiceLocator();
        $viewHelperManager = $serviceManager->get('viewhelpermanager');

        $uri = $serviceManager->get('router')->getRequestUri()->getPath();
        $thread = sha1($uri);
        $validationMessages = $viewHelperManager->get('flashMessenger')
                                                ->getMessagesFromNamespace('RbComment');

        $config = $serviceManager->get('Config');
        $strings = $config['rb_comment']['strings'];

        echo $viewHelperManager->get('partial')->__invoke($invokablePartial, [
            'comments' => $serviceManager->get('RbComment\Model\CommentTable')
                                         ->fetchAllForThread($thread),
            'form' => new CommentForm($strings),
            'thread' => $thread,
            'validationResults' => count($validationMessages) > 0
                ? json_decode(array_shift($validationMessages))
                : [],
            'uri' => $uri,
            'strings' => $strings,
            'zfc_user'=> $config['rb_comment']['zfc_user']['enabled'],
            'gravatar'=> $config['rb_comment']['gravatar']['enabled'],
        ]);
    }
}
