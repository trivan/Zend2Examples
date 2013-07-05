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
        $this -> initAcl($e);
        $e -> getApplication() -> getEventManager() -> attach('route', array($this, 'checkAcl'));

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function initAcl(MvcEvent $e) {

        $acl = new \Zend\Permissions\Acl\Acl();
        $roles = include __DIR__ . '/config/module.acl.roles.php';
        $allResources = array();
        foreach ($roles as $role => $resources) {
            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl -> addRole($role);

            $allResources = array_merge($resources, $allResources);

            //adding resources
            foreach ($resources as $resource) {
                $acl -> addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
            }
            //adding restrictions
            foreach ($allResources as $resource) {
                $acl -> allow($role, $resource);
            }
        }
        //testing
//         var_dump($acl->isAllowed('guest','listgeoip'));die;

        //setting to view
        $e -> getViewModel() -> acl = $acl;

    }

    public function checkAcl(MvcEvent $e) {
        $route = $e -> getRouteMatch() -> getMatchedRouteName();
        //you set your role
        $userRole = 'guest';

        if (!$e -> getViewModel() -> acl -> isAllowed($userRole, $route)) {
            $response = $e -> getResponse();
            //location to page or what ever
            $response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/404');
            $response -> setStatusCode(303);
        }
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
