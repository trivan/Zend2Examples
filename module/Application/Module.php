<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\Contact;
use Application\Model\ContactTable;
use Application\Model\Geoip;
use Application\Model\GeoipTable;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
//     	echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];die;
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator
        ->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        ->setFallbackLocale('vi');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
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
    					'Application\Model\ContactTable' =>  function($sm) {
    						$tableGateway = $sm->get('ContactTableGateway');
    						$table = new ContactTable($tableGateway);
    						return $table;
    					},
    					'ContactTableGateway' => function ($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new Contact());
    						return new TableGateway('contact', $dbAdapter, null, $resultSetPrototype);
    					},
    					'Application\Model\GeoipTable' =>  function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    		$table = new GeoipTable($dbAdapter);
                    		return $table;
    					},
    			),
    	);
    }
}
