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
        $this->initLanguage($e);

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    private function initLanguage(MvcEvent $e){

    	$session = $e->getApplication()->getServiceManager()->get('session');
    	$translator = $e->getApplication()->getServiceManager()->get('translator');

    	if(isset($_GET["lang"]) && !empty($_GET["lang"])){
    		switch ($_GET["lang"]){
    			case "vi": {
    				$translator->setLocale("vi_VN");
    				$session->lang = "VN";break;
    			}
    			case "vi_VN": {
    				$translator->setLocale("vi_VN");
    				$session->lang = "VN";break;
    			}
    			case "en": {
    				$translator->setLocale("en_US");
    				$session->lang = "US";break;
    			}
    		}
    	}
    	else{
    		//condition for no choose from UI
    		if (isset($session->lang)) {
    			if($session->lang == "VN"){
    				$translator->setLocale("vi_VN");
    			}
    			else $translator->setLocale("en_US");
    		}
    	}
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
