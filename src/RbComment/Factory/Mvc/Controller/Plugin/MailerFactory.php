<?php
namespace RbComment\Factory\Mvc\Controller\Plugin;

use Interop\Container\ContainerInterface;
use RbComment\Mvc\Controller\Plugin\Mailer;
use Zend\ServiceManager\Factory\FactoryInterface;

final class MailerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Mailer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Comment(
            $container->get('ViewHelperManager')->get('serverUrl'),
            $container->get('RbComment\Mailer'),
            $container->get('Config')
        );
    }
}
