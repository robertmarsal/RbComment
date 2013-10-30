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
    'view_helpers' => array(
        'invokables' => array(
            'rbComment' => 'RbComment\View\Helper\Comment',
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'rbcomment/theme/uikit'   => __DIR__ . '/../view/theme/uikit.phtml',
            'rbcomment/theme/default' => __DIR__ . '/../view/theme/default.phtml',
        ),
    ),
    'rb_comment' => array(
        'default_visibility' => 1,
        'strings' => array(
            'author' => 'Author',
            'contact' => 'Email',
            'content' => 'Comment',
            'submit' => 'Post',
            'comments' => 'Comments',
            'required' => 'All fields are required. Contact info will not be published.',
        ),
    ),
);