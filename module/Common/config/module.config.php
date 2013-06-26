<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Common\Controller\Index' => 'Common\Controller\IndexController',
        ),
    ),

        'router' => array(
                'routes' => array(
                        'common' => array(
                                'type'    => 'segment',
                                'options' => array(
                                        'route'    => '/common[/][:action][/:id]',
                                        'constraints' => array(
                                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'id'     => '[0-9]+',
                                        ),
                                        'defaults' => array(
                                                'controller' => 'Common\Controller\Index',
                                                'action'     => 'index',
                                        ),
                                ),
                        ),
                ),
        ),


    'view_manager' => array(
        'template_path_stack' => array(
            'common' => __DIR__ . '/../view',
        ),
    ),
);