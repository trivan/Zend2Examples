<?php
namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Common\Model\Geoip;

class IndexController extends AbstractActionController
{
	protected $geoipTable;
    public function indexAction()
    {
    	return new ViewModel(array(
    			'geoip' => $this->getGeoipTable()->fetchAll(),
    	));
    }

    public function getGeoipTable()
    {
    	if (!$this->geoipTable) {
    		$sm = $this->getServiceLocator();
    		$this->geoipTable = $sm->get('Common\Model\GeoipTable');
    	}
    	return $this->geoipTable;
    }

    private function getRealIP()
    {
    	if (isset($_SERVER["HTTP_CLIENT_IP"])){
    		return $_SERVER["HTTP_CLIENT_IP"];
    	}
    	elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
    		return $_SERVER["HTTP_X_FORWARDED_FOR"];
    	}
    	elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
    		return $_SERVER["HTTP_X_FORWARDED"];
    	}
    	elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
    		return $_SERVER["HTTP_FORWARDED_FOR"];
    	}
    	elseif (isset($_SERVER["HTTP_FORWARDED"])){
    		return $_SERVER["HTTP_FORWARDED"];
    	}
    	else{
    		return $_SERVER["REMOTE_ADDR"];
    	}
    }
}