<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Geoip;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Application\Model\phpMo;
use Application\Model\poParser;

class IndexController extends AbstractActionController
{
    protected $geoipTable;
    public function indexAction()
    {

        //create mo file for vi_VN.po
//         $path = __DIR__.'/../../../language/'.'en_US'.".po";
//         $abc = new phpMo();
//         $abc->phpmo_convert($path);

        return new ViewModel();
    }

    public function testAction()
    {
        return new ViewModel();
    }

    public function updateLanguageAction()
    {
        $lang = $_GET["lang"];
        $path = __DIR__.'/../../../language/'.$lang.".po";

        /*----- start read file -----*/
        $poparser = new poParser();
        $entries = $poparser->read($path);
//         echo "<pre>";print_r($entries);echo "</pre>";die;
        /*----- end read file -----*/

        $message = "";
        $request = $this->getRequest();
        if ($request->isPost()) {

            //update Po File
            $dataPost = $_POST;
            $countDataLang = (count($dataPost) - 1)/2;
            for($i=0;$i<$countDataLang;$i++){

                $key = $i + 2;
                $id = "entry$key";
                $translation = 'entrytranslator'.$key;

                if(!empty($dataPost[$id])){
                    $entries = $poparser->update_entry($dataPost[$id],$dataPost[$translation]);
                    $poparser->write($path);
                }
            }

            //update Mo File
            $abc = new phpMo();
            $abc->phpmo_convert($path);
            $message = "change translator language success!";
        }

        return new ViewModel(array(
                'lang' => $lang,
                'message' => $message,
                'entries'=> $entries,
        ));
    }

    public function listGeoipAction()
    {
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
        $this->params()->fromRoute('order_by') : 'cn';

        $order = $this->params()->fromRoute('order') ?
        $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;

        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

        $geoip = $this->getGeoipTable()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 15;

        $geoip->current();
        $paginator = new Paginator(new paginatorIterator($geoip));
        $paginator->setCurrentPageNumber($page)
        ->setItemCountPerPage($itemsPerPage)
        ->setPageRange(7);

        return new ViewModel(array(
                'order_by' => $order_by,
                'order' => $order,
                'page' => $page,
                'paginator' => $paginator,
        ));
    }

    public function locationAction()
    {
        $ip = $this->getRealIP();
        //         $ip_num = sprintf("%u", ip2long($ip));
        $ip_num = 16777218;
        echo"<pre>ip2long(ip) = ";print_r($ip_num);echo"</pre>";

        $result = $this->getGeoipTable()->getGeoipbyIP($ip_num);

        foreach ($result as $result) {}
        echo"<pre>";print_r($result->cc);echo"</pre>";
        echo"<pre>";print_r($result->cn);echo"</pre>";
        echo"<pre>";print_r($result);echo"</pre>";

        // convert object => json
        $json = json_encode($result);
        echo $json;

        // Convert Object to Array
        $array = (array) $result;
        echo"<pre>";print_r($array);echo"</pre>";
        echo json_encode($array);
    }

    public function getGeoipTable()
    {
        if (!$this->geoipTable) {
            $sm = $this->getServiceLocator();
            $this->geoipTable = $sm->get('Application\Model\GeoipTable');
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
