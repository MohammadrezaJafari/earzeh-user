<?php
namespace User\Controller;
use Zend\Mvc\Controller\AbstractActionController;// for run in zend's MVC
use Ellie\Interfaces\ControllerInterface;
use Zend\View\Model\ViewModel;


class AuthenticationController extends  AbstractActionController
    implements ControllerInterface
{
    protected $AuthService;
    protected $request;
    protected $message;
    public function __construct($services){
        $this->AuthService = $services["authenticationService"];
        $this->request = $this->getRequest();
    }

    public function loginAction(){
    $data = array("username"=>"asghar" , "password"=>"123");

    if($this->request->isPost())
        {

            $this->message ="wrong username or pass ...";
            if ($this->AuthService->authenticate($data["username"],$data["password"])) {
                $this->message = "u are logedin :)";
            }
        }

        if($this->AuthService->hasIdentity())
        {
            return $this->redirect()->toRoute("user",array("controller"=>"authentication","action"=>"logout"));
        }

        return new ViewModel(array(
            "result"=>$this->message
        ));
    }

    public function logoutAction(){
        $user = $this->AuthService->getIdentity();
        if($this->request->isPost())
        {
            $this->AuthService->clearIdentity();
            return $this->redirect()->toRoute("user",array("controller"=>"authentication","action"=>"login"));
        }
        return new ViewModel(array(
            "user"=>$user,
            "result"=>$this->message
        ));
    }
    public function createAction()
    {
        // TODO: Implement createAction() method.
    }

    public function editAction()
    {
        // TODO: Implement editAction() method.
    }

    public function deleteAction()
    {
        // TODO: Implement deleteAction() method.
    }

    public function listAction()
    {
        // TODO: Implement listAction() method.
    }
}