<?php
namespace Application\Form;

use Zend\Form\Form;

class ContactForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('contact');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'fname',
            'type' => 'Text',
            'options' => array(
                'label' => 'First Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'lname',
            'type' => 'Text',
            'options' => array(
                'label' => 'Last Name: ',
            ),
        ));
        $this->add(array(
                'name' => 'companyname',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Company Name: ',
                ),
        ));
        $this->add(array(
                'name' => 'phone',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Contact Phone Number: ',
                ),
        ));
        $this->add(array(
                'name' => 'email',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Email: ',
                ),
        ));
        $this->add(array(
                'name' => 'enquiry',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                        'label' => 'Enquiry Details: ',
                ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'SEND',
                'id' => 'submitbutton',
            ),
        ));
    }
}