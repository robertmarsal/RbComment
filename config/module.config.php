<?php

return array(
    'router' => array(
        'routes' => array(
            'rbcomment' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/rbcomment/:action',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'RbComment\Controller\Comment',
                    ),
                ),
            ),
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'RbComment\Controller\Comment' => 'RbComment\Controller\CommentController',
        ),
    ),
    'rb_comment' => array(
        'default_visibility' => 1,
    ),
);