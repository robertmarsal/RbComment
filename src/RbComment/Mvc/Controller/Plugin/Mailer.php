<?php
namespace RbComment\Mvc\Controller\Plugin;

use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Mailer extends AbstractPlugin
{
    private $serverUrlHelper;
    private $mailerService;
    private $configService;

    public function __construct(
        $serverUrlHelper,
        $mailerService,
        array $configService
    ) {
        $this->serverUrlHelper = $serverUrlHelper;
        $this->mailerService   = $mailerService;
        $this->configService   = $configService;
    }

    public function __invoke($comment)
    {
        $mailerConfig = $this->configService['rb_comment']['email'];

        $htmlContent = $comment->content;
        $htmlContent .= '<br><br>';
        $htmlContent .= '<a href="' . $this->serverUrlHelper() . $comment->uri . '#rbcomment-' . $comment->id . '">' .
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

        $this->mailerService->send($message);
    }
}
