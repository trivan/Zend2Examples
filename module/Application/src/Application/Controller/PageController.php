<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Contact;
use Application\Form\ContactForm;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PageController extends AbstractActionController
{
	protected $contactTable;
    public function aboutAction()
    {
        return new ViewModel();
    }

    public function contactAction()
    {
        $form = new ContactForm();
        $form->get('submit')->setValue('SEND');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $contact = new Contact();
            $form->setInputFilter($contact->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $contact->exchangeArray($form->getData());
                $this->getContactTable()->saveContact($contact);

                // Redirect to list of contact
                return $this->redirect()->toRoute('contact');
            }

        }
        return array('form' => $form);
    }

    public function getContactTable()
    {
    	if (!$this->contactTable) {
    		$sm = $this->getServiceLocator();
    		$this->contactTable = $sm->get('Application\Model\ContactTable');
    	}
    	return $this->contactTable;
    }
}
