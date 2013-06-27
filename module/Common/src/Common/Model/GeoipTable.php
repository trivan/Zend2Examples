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
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('geo_csv')
        ->columns(array('start_ip', 'end_ip', 'start','end','cc','cn'))
        ->where("$ip BETWEEN start AND end")
        ->limit(20);

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}