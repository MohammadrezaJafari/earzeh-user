<?php
namespace User\Controller;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\CheckBox;
use Ellie\UI\Element\Select;
use Ellie\UI\Element\Select2;
use Ellie\UI\Element\Text;
use Ellie\UI\Element\Textarea;
use Ellie\UI\Form;
use User\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;// for run in zend's MVC
use Ellie\Interfaces\ControllerInterface;
use Zend\View\Model\ViewModel;


class ManageController extends  AbstractActionController{

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
//        echo "<pre>";
//        var_dump($this->getServiceLocator()->get('Config')['menu']);die();
        return parent::onDispatch($e);
    }

    public function listAction()
    {
        $view = new ViewModel();
        $view->setTemplate('user/datatable');
        return $view;
    }

    public function createAction(){

        $this->layout()->message = [
            'type' => 'danger',
            'text' => 'Changes has been saved successfully!'
        ];
        return $this->getCreateUserForm();
    }

    public function storeAction()
    {
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        $user = new User();
        $user->setCreatedAt(new \DateTime(date("Y-m-d H:i:s")));
        echo "<pre>";
        var_dump($user->getCreatedAt());die();
        $user->setusername($this->request->getPost()['username']);
        $user->setPassword($this->request->getPost()['username']);
        $user->setCountry($this->request->getPost()['username']);
        $user->setRole($this->request->getPost()['role']);
//        $user->setCreatedAt('2015-09-20 11:19:31');

        $objectManager->persist($user);

        $objectManager->flush();

        return 1;
    }

    public function editAction()
    {

    }

    public function updateAction()
    {

    }

    public function deleteAction()
    {

    }

    public function getCreateUserForm()
    {
        $form     = new Form(['header' => 'User Management','action' => 'user/manage/store']);
        $username = new Text([
            'name' => 'username',
            'placeholder' => 'Username',
            'type' => 'text',
            'label' => 'Username',
        ]);

        $address    = new Text([
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
        $role     = new Select([
            'label' => 'Role',
            'options' => [
                '1' => 'Manager',
                '2' => 'Operator',
                '3' => 'Company'
            ]
        ]);
        $country  = new Select2([
            'label' => 'Country'
        ]);

        $description    = new Textarea([
            'name' => 'description',
            'placeholder' => 'Description ...',
            'label' => 'Description',
        ]);
        $enableCheckbox = new CheckBox(['name' => 'status', 'label' => 'Active']);

        //$upload = new FileUpload(['label' => 'Upload Documents']);

        $form->addChild($username);
        $form->addChild($email);
        $form->addChild($country);
        $form->addChild($address);
        $form->addChild($role);
        $form->addChild($description);
//        $form->addChild($upload);
        $form->addChild($enableCheckbox);

        $submit = new Button();

        $form->addChild($submit, 'submit');


        return $form;
    }

}