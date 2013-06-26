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
//         if (!session_is_registered("locale")) { //check if the session variable has already been set first
//             $con = mysql_connect('localhost', 'geo_user', 'geo_password');
//             if ($con) {
//                 $ip_num = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
//                 mysql_select_db("geo_ip", $con);
//                 $result = mysql_query( "SELECT '' FROM ch_ip WHERE $ip_num BETWEEN start AND end" );
//                 $num_rows = mysql_num_rows($result);
//                 if ($num_rows > 0) {
//                     $_SESSION['locale'] = "ch";
//                 }
//                 else { $_SESSION['locale'] = "de"; }
//             }
//             else { $_SESSION['locale'] = "de"; //If no db connection can be made then set their locale to German }
//             }
//         }

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