<?php
namespace User\Controller\Company;


use Application\Controller\BaseController;
use Zend\View\Model\ViewModel;

class B2BController extends BaseController{
    public function ihaveAction()
    {

    }

    public function iwantAction()
    {
        echo "salam2";
        return 1;
    }

    public function listAction()
    {
        $view = new ViewModel();
        $view->setTemplate('user/request');
        return $view;
    }

    public function create()
    {
        $view = new ViewModel();
        return $view;
    }
}