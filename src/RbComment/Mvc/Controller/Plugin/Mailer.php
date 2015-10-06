<?php

namespace RbComment\Mvc\Controller\Plugin;

use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Mailer extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    private $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($comment)
    {
        $serviceManager = $this->getServiceLocator()->getServiceLocator();
        $viewHelperManager = $serviceManager->get('viewhelpermanager');
        $serverUrlHelper = $viewHelperManager->get('serverUrl');

        $mailerService = $serviceManager->get('RbComment\Mailer');

        $config = $serviceManager->get('Config');
        $mailerConfig = $config['rb_comment']['email'];

        $htmlContent = $comment->content;
        $htmlContent .= '<br><br>';
        $htmlContent .= '<a href="' . $serverUrlHelper() . $comment->uri . '#rbcomment-' . $comment->id . '">' .
                        $mailerConfig['context_link_text'] .
                        '</a>';

        $html = new MimePart($htmlContent);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts([$html]);

        $message = new Message();
        $message->addFrom($mailerConfig['from'])
                ->setSubject($mailerConfig['subject'])
                ->setBody($body);

        foreach ($mailerConfig['to'] as $mConfig) {
            $message->addTo($mConfig);
        }

        $mailerService->send($message);
    }
}
