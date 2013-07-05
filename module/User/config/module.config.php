<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'tuser' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/tuser',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'login',
                    ),
                ),
            ),
        		'tlogout' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/tlogout',
        						'defaults' => array(
        								'controller' => 'User\Controller\User',
        								'action'     => 'logout',
        						),
        				),
        		),
        	'profile' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/profile',
        						'defaults' => array(
        								'controller' => 'User\Controller\User',
        								'action'     => 'profile',
        						),
        				),
        	),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
    ),
);