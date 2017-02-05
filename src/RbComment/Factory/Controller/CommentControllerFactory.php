<?php
namespace RbComment\Factory\Controller;

use Interop\Container\ContainerInterface;
use RbComment\Controller\CommentController;
use RbComment\Model\CommentTable;
use Zend\ServiceManager\Factory\FactoryInterface;

final class CommentControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return CommentController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new CommentController(
            $container->get('Config'),
            $container->get(CommentTable::class),
            $container->get('RbComment\Akismet')
        );

        return $controller;
    }
}
