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
use Zend\View\Model\ViewModel;

class RegistrationController extends BaseController{

    protected $registration;
    protected $doctrineService;

    public function __construct($service)
    {
        $this->registration = $service['registration'];
        $this->doctrineService = $service['doctrineService'];
    }

    public function registerAction()
    {
        if($this->request->isPost()){
            //TODO:: Set Validation That All Input is valid
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

            return 'U R Registered';
        }
        $layout = $this->layout();
        $layout->setTemplate('user/welcome');
        $layout->addChild($this->getForm(),'form');

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
            'name' => 'ecode',
            'value' => '',
            'placeholder' => 'Economic Code',
            'label' => ''
        ]);
        $address = new Text([
            'name' => 'address',
            'label' => '',
            'placeholder' => 'Address ... ',
            'value' => ''
        ]);
        $phone   = new Text([
            'name' => 'phone',
            'label' => '',
            'placeholder' => 'Phone'
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