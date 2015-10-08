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
        if($this->AuthService->hasIdentity())
        {
            return $this->redirect()->toRoute("home",array("controller"=>"Application\\Controller\\Index","action"=>"dashboard"));
        }
        if($this->request->isPost())
        {
            $data = array("username"=>"asghar" , "password"=>"123");
            $this->message ="wrong username or pass ...";
            if ($this->AuthService->authenticate($data["username"],$data["password"])) {
                $this->message = "u are logedin :)";
                return $this->redirect()->toRoute("home",array("controller"=>"Application\\Controller\\Index","action"=>"dashboard"));
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
            return $this->redirect()->toRoute("user",array("controller"=>"authentication","action"=>"login"));
//        }
    }
}