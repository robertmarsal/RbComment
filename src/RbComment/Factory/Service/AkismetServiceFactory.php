<?php
namespace RbComment\Factory\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZendService\Akismet\Akismet;

final class AkismetServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Akismet
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configService     = $container->get('Config');
        $viewHelperManager = $container->get('ViewHelperManager');

        $akismetConfig = $configService['rb_comment']['akismet'];

        return new Akismet(
            $akismetConfig['api_key'],
            $viewHelperManager->get('serverUrl')->__invoke()
        );
    }
}
