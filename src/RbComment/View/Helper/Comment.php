<?php

namespace RbComment\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Comment  extends AbstractHelper implements ServiceLocatorAwareInterface
{
    private $serviceLocator;

    protected $themes = array(
        'default' => true,
        'uikit'   => true,
    );

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

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

        echo $viewHelperManager->get('partial')->__invoke($invokablePartial, array(
            'comments' => $serviceManager->get('RbComment\Model\CommentTable')
                                         ->fetchAllForThread($thread),
            'form' => new \RbComment\Form\CommentForm($strings),
            'thread' => $thread,
            'validationResults' => count($validationMessages) > 0
                ? json_decode(array_shift($validationMessages))
                : array(),
            'uri' => $uri,
            'strings' => $strings,
        ));
    }
}
