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

    public function locationAction()
    {
        $ip = $this->getRealIP();
//         $ip_num = sprintf("%u", ip2long($ip));
        $ip_num = 16777218;

        $result = $this->getGeoipTable()->getGeoipbyIP($ip_num);

        echo count($result)."<br/>";
        foreach ($result as $result) {}
        echo"<pre>";print_r($result->cc);echo"</pre>";
        echo"<pre>";print_r($result->cn);echo"</pre>";
        echo"<pre>";print_r($result);echo"</pre>";

        // convert object => json
        $json = json_encode($result);
        echo $json;die;

        // Convert Object to Array
        $array = (array) $result;
        echo"<pre>";print_r($array);echo"</pre>";
        echo json_encode($array);die;
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