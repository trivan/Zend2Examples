<?php

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module
{
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
            'factories'=>array(
                    'User\Model\MyAuthStorage' => function($sm){
                        return new \User\Model\MyAuthStorage('tauth');
                    },

                    'AuthService' => function($sm) {
                                //My assumption, you've alredy set dbAdapter
                                //and has users table with columns : user_name and pass_word
                                //that password hashed with md5
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
			            $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,'users','username','password','MD5(?)');
			            /*
			             * Advanced Usage By Example
			            *
			            * // The status field value of an account is not equal to "compromised"
			            $dbTableAuthAdapter = new DbTableAuthAdapter($db,
			            		'users',
			            		'username',
			            		'password',
			            		'MD5(?) AND status != "compromised"'
			            );

			            // The active field value of an account is equal to "TRUE"
			            $dbTableAuthAdapter = new DbTableAuthAdapter($db,
			            		'users',
			            		'username',
			            		'password',
			            		'MD5(?) AND active = "TRUE"'
			            );
			            */

			            $authService = new AuthenticationService();
			            $authService->setAdapter($dbTableAuthAdapter);
                        $authService->setStorage($sm->get('User\Model\MyAuthStorage'));

                        return $authService;
                    },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}