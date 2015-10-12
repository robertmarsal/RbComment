<?php
namespace RbComment\Factory;

use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\TransportInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailerFactory implements FactoryInterface
{
    /**
     * Placeholder transport config. Do not use this in production.
     * Replace with SMTP.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return TransportInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Sendmail();
    }
}
