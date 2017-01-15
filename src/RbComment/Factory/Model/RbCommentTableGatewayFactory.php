<?php
namespace RbComment\Factory\Model;

use Interop\Container\ContainerInterface;
use RbComment\Model\Comment;
use RbComment\Model\CommentTable;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

final class RbCommentTableGatewayFactory implements FactoryInterface
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
        $databaseAdapter = $container->get(Adapter::class);
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Comment());

        return new TableGateway('rb_comments', $databaseAdapter, null, $resultSetPrototype);
    }
}
