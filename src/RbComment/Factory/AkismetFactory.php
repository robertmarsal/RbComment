<?php
namespace RbComment\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendService\Akismet\Akismet;

class AkismetFactory implements FactoryInterface
{
    /**
     * Akismet service instance factory.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return TransportInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config            = $serviceLocator->get('Config');
        $viewHelperManager = $serviceLocator->get('ViewHelperManager');

        $akismetConfig = $config['rb_comment']['akismet'];

        return new Akismet(
            $akismetConfig['api_key'],
            $viewHelperManager->get('serverUrl')->__invoke()
        );
    }
}
