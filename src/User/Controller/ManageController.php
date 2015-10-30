<?php
namespace User\Controller;
use Application\Controller\BaseController;
use Application\Entity\User;
use Application\Entity\UserDetails;
use Ellie\Interfaces\ControllerInterface;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\CheckBox;
use Ellie\UI\Element\Select;
use Ellie\UI\Element\Select2;
use Ellie\UI\Element\Text;
use Ellie\UI\Element\Textarea;
use Ellie\UI\Form;
use Zend\View\Model\ViewModel;


class ManageController extends  BaseController implements ControllerInterface{

    protected $request;

    protected $objectManager;

    public function __construct($services){
        $this->objectManager = $services["doctrine"];
        $this->request = $this->getRequest();
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->base = $this->getRequest()->getBasePath();
        $layout = $this->layout();
        $layout->setTemplate('layout/master');

        return parent::onDispatch($e);
    }

    public function listAction()
    {
        $userType = ucfirst($this->params('id'));
        $type = $this->objectManager->getRepository('Application\Entity\Role')->findOneBy(['name' => $userType]);
        $this->layout()->message = [
            'type' => 'success',
            'text' => 'The User Is Created Successfully'
        ];

        $user = $this->objectManager->getRepository('Application\Entity\User')->findBy(['role' => $type->getId()]);
        $view = new ViewModel();
        $view->setTemplate('user/datatable');
        $view->setVariables(['users' => $user, 'lang' => $this->lang]);
        return $view;
    }

    public function createAction(){
        $translator = $this->getServiceLocator()->get('translator');

        if($this->request->isPost()){
            $user = new User;
            $userDetails = new UserDetails();
            $user->setUsername($this->request->getPost()['username']);
            $user->setPassword($this->request->getPost()['password']);
            $user->setStatus($this->request->getPost()['status']);
            $user->setEmail($this->request->getPost()['email']);
            $user->setCountry($this->request->getPost()['country']);
            $role = $this->objectManager->find('Application\Entity\Role', $this->request->getPost()['role']);
            $language = $this->objectManager->find('Application\Entity\Language', $this->request->getPost()['language']);
            $user->setRole($role);
            $user->setDefaultLanguage($language);
            $userDetails->setPhone($this->request->getPost()['phone']);
            $userDetails->setAddress($this->request->getPost()['address']);
            $userDetails->setActive(1);
            $userDetails->setUser($user);
            $this->objectManager->persist($user);
            $this->objectManager->persist($userDetails);
            $this->objectManager->flush();

            return $this->redirect()->toRoute("user",array('lang' => $this->params('lang'), "controller"=>"manage","action"=>"list", 'id' => lcfirst($user->getRole()->getName())));
        }
        $this->layout()->message = [
            'type' => 'info',
            'text' =>  $translator->translate('Fill This Form For Creating New User')
        ];
        return $this->getCreateUserForm();
    }


    public function editAction()
    {
        $user = $this->objectManager->find('Application\Entity\User', $this->params('id'));
        if($this->request->isPost()){
            $user->setUsername($this->request->getPost()['username']);
            $user->setPassword($this->request->getPost()['password']);
            $user->setCountry($this->request->getPost()['country']);
            $user->setStatus($this->request->getPost()['status']);
            $user->setEmail($this->request->getPost()['email']);
//        $user->setAddress($this->request->getPost()['address']);
//            $user->setDefaultLanguage(1);
            $role = $this->objectManager->find('Application\Entity\Role', $this->request->getPost()['role']);
            $user->setRole($role);
            $this->objectManager->persist($user);
            $this->objectManager->flush();
            return $this->redirect()->toRoute("user",array('lang' => $this->params('lang'), "controller"=>"manage","action"=>"list", 'id' => lcfirst($user->getRole()->getName())));

        }


        $uri = $this->url()->getController()->getRequest()->getBaseUrl(). "/user/manage/edit/" . $this->params('id');
        $form = $this->getCreateUserForm();
        $form->setVariables(['action' => $uri]);
        $form->getChildrenByCaptureTo('username')[0]->value = $user->getUsername();
        $form->getChildrenByCaptureTo('password')[0]->value = $user->getPassword();
        $form->getChildrenByCaptureTo('email')[0]->value    = $user->getEmail();
        $form->getChildrenByCaptureTo('country')[0]->value  = $user->getCountry();
        $form->getChildrenByCaptureTo('role')[0]->value     = $user->getRole()->getId();
        return $form;
    }

    public function deleteAction()
    {
        var_dump(1);die();
        $user = $this->objectManager->find('Application\Entity\User', 8);

        $this->objectManager->remove($user);
        $this->objectManager->flush();
        return $this->redirect()->toRoute("user",array("controller"=>"manage","action"=>"list"));

    }

    public function getCreateUserForm()
    {

        $translator = $this->getServiceLocator()->get('translator');
        $form     = new Form(['header' => $translator->translate('User Management') ,'action' => $this->url()->getController()->getRequest()->getBaseUrl(). "/user/manage/create"]);
        $username = new Text([
            'name' => 'username',
            'placeholder' => $translator->translate('Username'),
            'type' => 'text',
            'label' => $translator->translate('Username'),
        ]);
        $phone    = new Text([
            'name' => 'phone',
            'placeholder' => '',
            'type' => 'text',
            'label' => $translator->translate('Phone'),
        ]);
        $address  = new Text([
            'name' => 'address',
            'placeholder' => $translator->translate('Address') . '...',
            'type' => 'text',
            'label' => $translator->translate('Address'),
        ]);
        $email    = new Text([
            'name' => 'email',
            'placeholder' => 'email@example.com',
            'type' => 'email',
            'label' => $translator->translate('Email'),
        ]);
        $password = new Text([
            'name' => 'password',
            'placeholder' => '',
            'type' => 'password',
            'label' => $translator->translate('Password'),
        ]);
        $role     = new Select([
            'name'  => 'role',
            'label' => $translator->translate('Role'),
            'options' => [
                '1' => 'Manager',
                '2' => 'Operator',
                '3' => 'Company'
            ]
        ]);
        $language     = new Select([
            'name'  => 'language',
            'label' => $translator->translate('Default Language'),
            'options' => [
                '1' => 'Persian',
                '2' => 'English',
            ]
        ]);
        $country  = new Select2([
            'label' => $translator->translate('Country'),
            'name' => 'country'
        ]);
        $description    = new Textarea([
            'name' => 'description',
            'placeholder' => $translator->translate('Description'). '...',
            'label' => $translator->translate('Description'),
        ]);
        $status = new Select(['name' => 'status',
            'label' => 'Status',
            'options' => [
                'enable'  => 'Enable',
                'disable' => 'Disable',
                'unregistered' => 'Unregistered',
            ]
        ]);

        //$upload = new FileUpload(['label' => 'Upload Documents']);

        $form->addChild($username, 'username');
        $form->addChild($password, 'password');
        $form->addChild($email, 'email');
        $form->addChild($phone, 'phone');
        $form->addChild($country, 'country');
        $form->addChild($address, 'address');
        $form->addChild($role, 'role');
        $form->addChild($language, 'language');
        $form->addChild($status);
        $form->addChild($description, 'description');

        $submit = new Button();

        $form->addChild($submit, 'submit');


        return $form;
    }

}