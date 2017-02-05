<?php
namespace RbComment\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use RbComment\Model\CommentTable;
use RbComment\View\Helper\Comment;
use Zend\ServiceManager\Factory\FactoryInterface;

final class CommentFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Comment
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Comment(
            $container->get('ViewHelperManager'),
            $container->get('Router'),
            $container->get('Config'),
            $container->get(CommentTable::class)
        );
    }
}
