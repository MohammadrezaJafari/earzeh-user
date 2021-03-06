<?php
namespace User\Controller;
use Application\Controller\BaseController;
use Zend\View\Model\ViewModel;


class AuthenticationController extends  BaseController
{
    protected $AuthService;
    protected $request;
    protected $message;

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        return parent::onDispatch($e);
    }

    public function __construct($services){
        $this->AuthService = $services["authenticationService"];
        $this->request = $this->getRequest();
    }

    public function loginAction(){

        $this->layout()->setTemplate('layout/layout');
        if($this->AuthService->hasIdentity())
        {
            $user = $this->AuthService->getIdentity();
            $lang = 'en';
            if($user->getDefaultLanguage() != null){
                $lang = $user->getDefaultLanguage()->getName();
            }

            return $this->redirect()->toRoute("home",array("controller"=>"Application\\Controller\\Index","action"=>"dashboard", 'lang' => $lang));
        }

        if($this->request->isPost())
        {
            $data = ['username' => $this->request->getPost('username'),
                'password' =>$this->request->getPost('password')];
            $this->message ="wrong username or pass ...";
            if ($this->AuthService->authenticate($data["username"],$data["password"])) {
                $this->message = "u are logedin :)";
                $user = $this->AuthService->getIdentity();
                $lang = 'en';
                if($user->getDefaultLanguage() != null){
                    $lang = $user->getDefaultLanguage()->getName();
                }
                return $this->redirect()->toRoute("home",array("controller"=>"Application\\Controller\\Index","action"=>"dashboard", 'lang'=> $lang));
            }
        }

        return new ViewModel(array(
            "result"=>$this->message
        ));
    }

    public function logoutAction(){

        $user = $this->AuthService->getIdentity();
//        if($this->request->isPost())
//        {
            $this->AuthService->clearIdentity();
            return $this->redirect()->toRoute("sign-up");
//        }
    }
}