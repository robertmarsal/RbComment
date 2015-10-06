<?php

namespace RbCommentTest\Mvc\Controller\Plugin;

use RbComment\Mvc\Controller\Plugin\Mailer;
use PHPUnit_Framework_TestCase;

class MailerTest extends PHPUnit_Framework_TestCase
{
    public function testSetAndGetServiceLocator()
    {
        $serviceLocatorMock = $this->getMock(
            'Zend\ServiceManager\ServiceLocatorInterface',
            [],
            [],
            'ServiceLocatorInterface'
        );

        $mailer = new Mailer();
        $mailer->setServiceLocator($serviceLocatorMock);

        $this->assertEquals($serviceLocatorMock, $mailer->getServiceLocator());
    }
}
