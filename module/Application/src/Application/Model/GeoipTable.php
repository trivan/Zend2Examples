<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class GeoipTable extends AbstractTableGateway
{
    protected $tableGateway;

    protected $table = 'geo_csv';
    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Geoip());

        $this->initialize();
    }

    public function fetchAll(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from($this->table);
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	return $resultSet;
    }

    public function getGeoipbyIP($ip)
    {
        $select = new Select();
        $select->from('geo_csv')
        ->columns(array('start_ip', 'end_ip', 'start','end','cc','cn'))
        ->where("$ip BETWEEN start AND end")
        ->limit(20);

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
}