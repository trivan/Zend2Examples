<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ContactTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function fetchAll()
    {
    	$resultSet = $this->tableGateway->select();
    	return $resultSet;
    }

    public function getContact($id)
    {
    	$id  = (int) $id;
    	$rowset = $this->tableGateway->select(array('id' => $id));
    	$row = $rowset->current();
    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}
    	return $row;
    }

    public function saveContact(Contact $contact)
    {
        $data = array(
            'firstname' => $contact->fname,
            'lastname'  => $contact->lname,
            'companyname'  => $contact->lname,
            'phone'  => $contact->lname,
            'email'  => $contact->lname,
            'enquiry'  => $contact->lname,
        );

        $this->tableGateway->insert($data);
    }
}