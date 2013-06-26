<?php
namespace Common\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;

class GeoipTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
    	$sql = new Sql($this->tableGateway->getAdapter());

    	$select = $sql->select();
    	$select->from('geo_csv')
    	->columns(array('start_ip', 'end_ip', 'start','end','cc','cn'))
    	->limit(20);
    	$resultSet = $this->tableGateway->selectWith($select);
    	return $resultSet;

//         $resultSet = $this->tableGateway->select(array('cc' =>'VN'));
//         return $resultSet;
    }

    public function getGeoipbyIP($ip)
    {
        $ip  = (int) $ip;
        $rowset = $this->tableGateway->select(array('ip' => $$ip));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $ip");
        }
        return $row;
    }
}