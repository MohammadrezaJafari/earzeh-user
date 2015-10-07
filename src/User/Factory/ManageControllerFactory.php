<?php

namespace User\Factory;
use User\Controller\ManageController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ManageControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $authenticationService = $realServiceLocator->get('Ellie\Service\Authentication');


        $services =  array("authenticationService"=>$authenticationService);
        return new ManageController($services);
    }
}