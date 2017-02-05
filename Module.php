<?php
namespace RbComment;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements ConsoleUsageProviderInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @param Console $console
     *
     * @return array
     */
    public function getConsoleUsage(Console $console)
    {
        return [
            'delete spam' => 'Delete all comments marked as spam from the database',
        ];
    }
}
