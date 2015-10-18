<?php
/**
 * Created by PhpStorm.
 * User: mreza
 * Date: 10/9/15
 * Time: 12:40 PM
 */

namespace User\Controller;


use Application\Controller\BaseController;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\Text;
use Ellie\UI\Form;

class RegistrationController extends BaseController{

    protected $registration;

    public function __construct($service)
    {
        $this->registration = $service['registration'];
    }

    public function registerAction()
    {
        if($this->request->isPost()){
            //TODO:: Set Validation That All Input is valid
            //TODO:: Get User From Form
            $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $user = $em->find('Application\Entity\User', 1);
            $this->registration->register($user);

            if($this->request->getPost('companyName') == 'mreza'){
            }
            var_dump($this->getServiceLocator()->get('Ellie\Service\Registration'));die();

            var_dump($this->request->getPost());die();

            return 'U R Registered';
        }

        return $this->getForm();
    }

    public function getForm()
    {
        $form    = new Form(['header' => 'Registration Form', 'action' => '']);
        $name    = new Text([
            'name' => 'companyName',
            'value' => '',
            'label' => 'Company Name'
        ]);
        $email   = new Text([
            'name' => 'email',
            'value' => '',
            'type'  => 'email',
            'placeholder' => 'example@gmail.com',
            'label' => 'Email'
        ]);
        $code    = new Text([
            'name' => 'ecode',
            'value' => '',
            'label' => 'Economic Code'
        ]);
        $address = new Text([
            'name' => 'address',
            'label' => 'Address',
            'placeholder' => 'Address ... ',
            'value' => ''
        ]);
        $phone   = new Text([
            'name' => 'phone',
            'label' => 'Phone'
        ]);

        $form->addChild($name, 'name');
        $form->addChild($email, 'email');
        $form->addChild($code, 'code');
        $form->addChild($address,'address');
        $form->addChild($phone,'phone');
        $form->addChild(new Button(),'submit');
        return $form;


    }

}