<?php
namespace Common;

use Common\Model\Geoip;
use Common\Model\GeoipTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Common\Model\GeoipTable' =>  function($sm) {
    						$tableGateway = $sm->get('GeoipTableGateway');
    						$table = new GeoipTable($tableGateway);
    						return $table;
    					},
    					'GeoipTableGateway' => function ($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new Geoip());
    						return new TableGateway('geo_csv', $dbAdapter, null, $resultSetPrototype);
    					},
    			),
    	);
    }
}