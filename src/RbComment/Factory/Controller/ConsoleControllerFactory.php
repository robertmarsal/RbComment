<?php
namespace RbComment\Factory\Controller;

use Interop\Container\ContainerInterface;
use RbComment\Controller\ConsoleController;
use RbComment\Model\CommentTable;
use Zend\ServiceManager\Factory\FactoryInterface;

final class ConsoleControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ConsoleController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new ConsoleController(
            $container->get(CommentTable::class)
        );

        return $controller;
    }
}
