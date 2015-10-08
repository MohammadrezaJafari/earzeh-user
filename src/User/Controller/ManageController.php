<?php
namespace User\Controller;
use Application\Controller\BaseController;
use Application\Entity\User;
use Application\Entity\UserDetails;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\CheckBox;
use Ellie\UI\Element\Select;
use Ellie\UI\Element\Select2;
use Ellie\UI\Element\Text;
use Ellie\UI\Element\Textarea;
use Ellie\UI\Form;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\AbstractActionController;// for run in zend's MVC
use Zend\View\Model\ViewModel;


class ManageController extends  BaseController{

    protected $request;

    protected $objectManager;

    public function __construct($services){
        $this->request = $this->getRequest();
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {

        //*****
        $this->base = $this->getRequest()->getBasePath();
        $layout = $this->layout();
        $layout->setTemplate('layout/master');
        $layout->setVariables(['menu' => $this->getServiceLocator()->get('Config')['menu']]);
        return parent::onDispatch($e);
    }

    public function listAction()
    {
        $this->layout()->message = [
            'type' => 'success',
            'text' => 'The User Is Created Successfully'
        ];
        $view = new ViewModel();
        $view->setTemplate('user/datatable');
        return $view;
    }

    public function createAction(){
        if($this->request->isPost()){
            $user = new User;
            $userDetails = new UserDetails();
            $objectManager = $this
                ->getServiceLocator()
                ->get('Doctrine\ORM\EntityManager');
            $user->setUsername($this->request->getPost()['username']);
            $user->setPassword($this->request->getPost()['password']);
            $user->setEmail($this->request->getPost()['email']);
            $user->setCountry($this->request->getPost()['country']);
            $role = $objectManager->find('Application\Entity\Role', $this->request->getPost()['role']);
            $language = $objectManager->find('Application\Entity\Language', $this->request->getPost()['language']);
            $user->setRole($role);
            $user->setDefaultLanguage($language);
            $userDetails->setPhone($this->request->getPost()['phone']);
            $userDetails->setAddress($this->request->getPost()['address']);
            $userDetails->setActive(1);
            $userDetails->setUser($user);
            $objectManager->persist($user);
            $objectManager->persist($userDetails);
            $objectManager->flush();

            return $this->redirect()->toRoute("user",array("controller"=>"manage","action"=>"list"));
        }
        $this->layout()->message = [
            'type' => 'info',
            'text' => 'Fill This Form For Creating New User'
        ];
        return $this->getCreateUserForm();
    }

    public function storeAction()
    {
        $user = new User;
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $user->setUsername($this->request->getPost()['username']);
        $user->setPassword($this->request->getPost()['password']);
        $user->setCountry($this->request->getPost()['country']);
//        $user->setAddress($this->request->getPost()['address']);
//            $user->setDefaultLanguage(1);
        $role = $objectManager->find('Application\Entity\Role', $this->request->getPost()['role']);
        $user->setRole($role);
        $objectManager->persist($user);
        $objectManager->flush();
        return 1;
    }

    public function editAction()
    {
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $user = $objectManager->find('Application\Entity\User', 2);

        if($this->request->isPost()){
            $user->setUsername($this->request->getPost()['username']);
            $user->setPassword($this->request->getPost()['password']);
            $user->setCountry($this->request->getPost()['country']);
//        $user->setAddress($this->request->getPost()['address']);
//            $user->setDefaultLanguage(1);
            $role = $objectManager->find('Application\Entity\Role', $this->request->getPost()['role']);
            $user->setRole($role);
            $objectManager->persist($user);
            $objectManager->flush();
        }


        $uri = $this->url()->getController()->getRequest()->getBaseUrl(). "/user/manage/edit";
        $form = $this->getCreateUserForm();
        $form->setVariables(['action' => $uri]);
        $form->getChildrenByCaptureTo('username')[0]->value = $user->getUsername();
        $form->getChildrenByCaptureTo('password')[0]->value = $user->getPassword();
        $form->getChildrenByCaptureTo('email')[0]->value    = $user->getEmail();
        $form->getChildrenByCaptureTo('country')[0]->value  = $user->getCountry();
        $form->getChildrenByCaptureTo('role')[0]->value     = $user->getRole()->getId();
        return $form;
    }

    public function updateAction()
    {

    }

    public function deleteAction()
    {
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $user = $objectManager->find('Application\Entity\User', 8);

        $objectManager->remove($user);
        $objectManager->flush();
        return $this->redirect()->toRoute("user",array("controller"=>"manage","action"=>"list"));

    }

    public function getCreateUserForm()
    {

        $translator = $this->getServiceLocator()->get('translator');
        $translator->setLocale("fa_IR");
        $config = $this->getServiceLocator()->get('Config')['translator']['translation_file_patterns'];
        var_dump($config);
        $form     = new Form(['header' =>$translator->translate('User Management', __NAMESPACE__) ,'action' => $this->url()->getController()->getRequest()->getBaseUrl(). "/user/manage/create"]);
        $username = new Text([
            'name' => 'username',
            'placeholder' => 'Username',
            'type' => 'text',
            'label' => 'Username',
        ]);
        $phone    = new Text([
            'name' => 'phone',
            'placeholder' => '',
            'type' => 'text',
            'label' => 'Phone',
        ]);
        $address  = new Text([
            'name' => 'address',
            'placeholder' => 'Address ...',
            'type' => 'text',
            'label' => 'Address',
        ]);
        $email    = new Text([
            'name' => 'email',
            'placeholder' => 'email@example.com',
            'type' => 'email',
            'label' => 'Email',
        ]);
        $password = new Text([
            'name' => 'password',
            'placeholder' => '',
            'type' => 'password',
            'label' => 'Password',
        ]);
        $role     = new Select([
            'name'  => 'role',
            'label' => 'Role',
            'options' => [
                '1' => 'Manager',
                '2' => 'Operator',
                '3' => 'Company'
            ]
        ]);
        $language     = new Select([
            'name'  => 'language',
            'label' => 'Default Language',
            'options' => [
                '1' => 'Persian',
                '2' => 'English',
            ]
        ]);

        $country  = new Select2([
            'label' => 'Country',
            'name' => 'country'
        ]);
        $description    = new Textarea([
            'name' => 'description',
            'placeholder' => 'Description ...',
            'label' => 'Description',
        ]);
        $enableCheckbox = new CheckBox(['name' => 'active', 'label' => 'Active']);

        //$upload = new FileUpload(['label' => 'Upload Documents']);

        $form->addChild($username, 'username');
        $form->addChild($password, 'password');
        $form->addChild($email, 'email');
        $form->addChild($phone, 'phone');
        $form->addChild($country, 'country');
        $form->addChild($address, 'address');
        $form->addChild($role, 'role');
        $form->addChild($description, 'description');
        $form->addChild($language, 'language');
        $form->addChild($enableCheckbox);

        $submit = new Button();

        $form->addChild($submit, 'submit');


        return $form;
    }

}