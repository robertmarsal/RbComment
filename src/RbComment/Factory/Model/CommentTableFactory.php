<?php
namespace RbComment\Factory\Model;

use Interop\Container\ContainerInterface;
use RbComment\Model\CommentTable;
use Zend\ServiceManager\Factory\FactoryInterface;

final class CommentTableFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return object|CommentTable
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new CommentTable(
            $container->get('RbCommentTableGateway')
        );
    }
}
