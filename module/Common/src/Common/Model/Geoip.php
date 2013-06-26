<?php
namespace Common\Model;

// Add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Geoip
{
    public $start_ip;
    public $end_ip;
    public $start;
    public $end;
    public $cc;
    public $cn;

    public function exchangeArray($data)
    {
        $this->start_ip     = (isset($data['start_ip']))     ? $data['start_ip']     : null;
        $this->end_ip     = (isset($data['end_ip']))     ? $data['end_ip']     : null;
        $this->start     = (isset($data['start']))     ? $data['start']     : null;
        $this->end     = (isset($data['end']))     ? $data['end']     : null;
        $this->cc     = (isset($data['cc']))     ? $data['cc']     : null;
        $this->cn     = (isset($data['cn']))     ? $data['cn']     : null;
    }

    public function getArrayCopy()
    {
    	return get_object_vars($this);
    }
}