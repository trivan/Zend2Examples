<?php
namespace Common\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Paginator;

class GeoipTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(array('cc' =>'VN'));
        return $resultSet;
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