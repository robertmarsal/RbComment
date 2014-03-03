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
    'console' => array(
        'router' => array(
            'routes' => array(
                'delete-spam' => array(
                    'options' => array(
                        'route' => 'delete spam',
                        'defaults' => array(
                            'controller' => 'RbComment\Controller\Console',
                            'action' => 'delete-spam',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'RbComment\Controller\Comment' => 'RbComment\Controller\CommentController',
            'RbComment\Controller\Console' => 'RbComment\Controller\ConsoleController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'rbMailer' => 'RbComment\Mvc\Controller\Plugin\Mailer',
        )
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
        /**
         * Default visibility of the comments.
         */
        'default_visibility' => 1,
        'strings' => array(
            'author' => 'Author',
            'contact' => 'Email',
            'content' => 'Comment',
            'submit' => 'Post',
            'comments' => 'Comments',
            'required' => 'All fields are required. Contact info will not be published.',
            'signout' => 'Sign Out',
            'signin' => 'Sign In',
            'signedinas' => 'You are signed in as',
            'notsignedin' => 'You are not signed in. To be able to comment, please ',
        ),
        'email' => array(
            /**
             * Send email notifications.
             */
            'notify' => false,
            /**
             * Email addresses where to send the notification.
             */
            'to' => array(),
            /**
             * From header. Usually something like noreply@myserver.com
             */
            'from' => '',
            /**
             * Subject of the notification email.
             */
            'subject' => 'New Comment',
            /**
             * Text of the comment link.
             */
            'context_link_text' => 'See this comment in context',
        ),
        'akismet' => array(
            /**
             * If this is true, the comment will be checked for spam.
             */
            'enabled' => false,
            /**
             * Your Akismet api key.
             */
            'api_key' => '',
            /**
             * Akismet uses IP addresses. If you are behind a proxy this SHOULD
             * be configured to avoid false positives.
             * Uses the class \Zend\Http\PhpEnvironment\RemoteAddress
             */
            'proxy' => array(
                /**
                 * Use proxy addresses or not.
                 */
                'use' => false,
                /**
                 * List of trusted proxy IP addresses.
                 */
                'trusted' => array(
                ),
                /**
                 * HTTP header to introspect for proxies.
                 */
                'header' => 'X-Forwarded-For',
            ),
        ),
        'zfc_user' => array(
            /**
             * This enables the ZfcUser integration.
             */
            'enabled' => false,
        ),
        'gravatar' => array(
            /**
             * This enables the Gravatar integration.
             */
            'enabled' => false,
        ),
    ),
);