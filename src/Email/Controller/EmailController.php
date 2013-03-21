<?php

namespace Email\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmailController extends AbstractActionController
{
    public function indexAction()
    {
        $email = $this->getServiceLocator()->get('email');
        $email->to('raymond@4mation.com.au')
            ->subject('This is a subject')
            ->text('<strong>This is the body</strong>')
            //->setTemplate('custom')
            ->setType('text')
            ->setVars(array('name' => 'Ray'))
            ->send();
    }
}