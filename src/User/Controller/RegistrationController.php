<?php
/**
 * Created by PhpStorm.
 * User: mreza
 * Date: 10/9/15
 * Time: 12:40 PM
 */

namespace User\Controller;


use Application\Controller\BaseController;
use Application\Entity\User;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\Text;
use Ellie\UI\Form;
use User\Helper\Helper;
use User\Validator\RegisterValidator;
use Zend\View\Model\ViewModel;

class RegistrationController extends BaseController{

    protected $registration;
    protected $doctrineService;
    protected $eventHandler;
    public function __construct($service,$eventHandler)
    {
        $this->registration = $service['registration'];
        $this->doctrineService = $service['doctrineService'];
        $this->eventHandler = $eventHandler;
    }

    public function registerAction()
    {
        $messages = [];
        $form = $this->getForm();
        if($this->request->isPost()){
            $validation = RegisterValidator::validate($form,$this->request->getPost()->toArray());
            if(is_null($validation)){
                //TODO:: Get User From Form
                $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $user = new User;
                $user->setusername($this->request->getPost('companyName'));
                $user->setPassword(Helper::passwordGenerator());
                $user->setRole();
                $user->setStatus('disable');
                $role = $this->doctrineService->find('Application\Entity\Role', 3);
                $user->setRole($role);
                $this->registration->register($user);
                $this->eventHandler->registered();
                return 'U R Registered';
            }
            else{
                $messages = $validation;
            }
        }
        $layout = $this->layout();
        $layout->setTemplate('user/welcome');
        $layout->addChild($form,'form');
        $layout->setVariables(['messages' => $messages]);

    }

    public function getForm()
    {
        $form    = new Form(['header' => ' ', 'action' => '']);
        $name    = new Text([
            'name' => 'companyName',
            'value' => '',
            'placeholder' => 'Company Name',
            'label' => ''
        ]);
        $email   = new Text([
            'name' => 'email',
            'value' => '',
            'type'  => 'email',
            'placeholder' => 'example@gmail.com',
            'label' => ''
        ]);
        $code    = new Text([
            'name' => 'mobile',
            'value' => '',
            'placeholder' => 'Mobile',
            'label' => ''
        ]);
        $country = new Text([
            'name' => 'country',
            'label' => '',
            'placeholder' => 'Country',
            'value' => ''
        ]);

        $city = new Text([
            'name' => 'city',
            'label' => '',
            'placeholder' => 'City',
            'value' => ''
        ]);
        $phone   = new Text([
            'name' => 'phone',
            'label' => '',
            'placeholder' => 'Phone'
        ]);

        $form->addChild($name, 'name');
        $form->addChild($email, 'email');
        $form->addChild($country,'country');
        $form->addChild($city,'address');
        $form->addChild($code, 'code');
        $form->addChild($phone,'phone');
        $form->addChild(new Button(['value' => 'ثبت نام']),'submit');
        return $form;


    }

}