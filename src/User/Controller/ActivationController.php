<?php
/**
 * Created by PhpStorm.
 * User: mreza
 * Date: 10/12/15
 * Time: 1:38 AM
 */

namespace User\Controller;


use Application\Controller\BaseController;
use User\Helper\Helper;
use Zend\EventManager\EventManager;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ActivationController extends BaseController{

    protected $objectManager;
    protected $eventHandler;

    public function __construct($services,$eventHandler){
        $this->objectManager = $services["doctrine"];
        $this->eventHandler = $eventHandler;
        $this->request = $this->getRequest();
    }

    public function getuserAction()
    {


        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent('
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="i-circle info"><i class="fa fa-check"></i></div>
                        <h4>Awesome!</h4>
                        <p>Changes has been saved successfully!</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                    <button type="button" id="not-primary" class="btn btn-info btn-flat" data-dismiss="modal">Proceed</button>
                </div>
            </div><!-- /.modal-content -->
        ');
        return $response;
    }

    public function enableAction()
    {
        $password = Helper::passwordGenerator();
        $user = $this->objectManager->getRepository('Application\Entity\User')->find($this->params('id'));
        $user->setStatus('enable');
        $this->objectManager->persist($user);
        $this->objectManager->flush();
        $this->eventHandler->activate();

        return $this->redirect()->toRoute("user",array('lang' => $this->params('lang'),"controller"=>"manage","action"=>"list", 'id' => lcfirst($user->getRole()->getName())));
    }

    public function disableAction()
    {
        $user = $this->objectManager->getRepository('Application\Entity\User')->find($this->params('id'));
        $user->setStatus('disable');
        $this->objectManager->persist($user);
        $this->objectManager->flush();

        $this->eventHandler->deactive();

        return $this->redirect()->toRoute("user",array('lang' => $this->params('lang'), "controller"=>"manage","action"=>"list", 'id' => lcfirst($user->getRole()->getName())));
    }

}