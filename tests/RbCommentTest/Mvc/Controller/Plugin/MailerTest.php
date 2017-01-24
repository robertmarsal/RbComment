<?php
namespace RbCommentTest\Mvc\Controller\Plugin;

use RbComment\Mvc\Controller\Plugin\Mailer;
use PHPUnit_Framework_TestCase;
use Zend\Mail\Transport\Sendmail;
use Zend\View\Helper\ServerUrl;

class MailerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group controllerPlugin
     */
    public function testConstructStoresTheDependencies()
    {
        $serverUrlHelperMock = $this->createMock(ServerUrl::class);
        $mailerServiceMock   = $this->createMock(Sendmail::class);
        $configServiceMock   = [uniqid()];

        $mailer = new Mailer(
            $serverUrlHelperMock,
            $mailerServiceMock,
            $configServiceMock
        );

        // Assertions
        $this->assertAttributeEquals($serverUrlHelperMock, 'serverUrlHelper', $mailer);
        $this->assertAttributeEquals($mailerServiceMock, 'mailerService', $mailer);
        $this->assertAttributeEquals($configServiceMock, 'configService', $mailer);
    }
}
