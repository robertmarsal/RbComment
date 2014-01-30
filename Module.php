<?php

namespace RbComment;

use RbComment\Model\Comment as RbComment;
use RbComment\Model\CommentTable as RbCommentTable;
use ZendService\Akismet\Akismet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Mail\Transport\Sendmail;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'RbComment\Model\CommentTable' =>  function($sm) {
                    $tableGateway = $sm->get('RbCommentTableGateway');
                    $table = new RbCommentTable($tableGateway);
                    return $table;
                },
                'RbCommentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RbComment());
                    return new TableGateway('rb_comments', $dbAdapter, null, $resultSetPrototype);
                },
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
            ),
        );
    }
}
