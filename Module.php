<?php
namespace RbComment;

use ZendService\Akismet\Akismet;
use Zend\Mail\Transport\Sendmail;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements ConsoleUsageProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                /**
                 * Placeholder transport config. Do not use this in production.
                 * Replace with smtp.
                 */
                'RbComment\Mailer' => function () {
                    return new Sendmail();
                },
                /**
                 * Akismet service instance factory. Uses the config down below.
                 */
                'RbComment\Akismet' => function ($serviceManager) {

                    $config = $serviceManager->get('Config');
                    $viewHelperManager = $serviceManager->get('viewhelpermanager');

                    $akismetConfig = $config['rb_comment']['akismet'];

                    return new Akismet(
                        $akismetConfig['api_key'],
                        $viewHelperManager->get('serverUrl')->__invoke()
                    );
               }
            ],
        ];
    }

    public function getConsoleUsage(Console $console)
    {
        return [
            'delete spam' => 'Delete all comments marked as spam from the database',
        ];
    }
}
