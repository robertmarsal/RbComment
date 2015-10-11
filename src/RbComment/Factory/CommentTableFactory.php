<?php
namespace RbComment\Factory;

use RbComment\Model\Comment as RbComment;
use RbComment\Model\CommentTable as RbCommentTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommentTableFactory implements FactoryInterface
{
    /**
     * Create and configure and instance of the RbCommentTable.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return RbCommentTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $databaseAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new RbComment());

        $tableGateway = new TableGateway(
            'rb_comments',
            $databaseAdapter,
            null,
            $resultSetPrototype
        );

        return new RbCommentTable($tableGateway);
    }
}
