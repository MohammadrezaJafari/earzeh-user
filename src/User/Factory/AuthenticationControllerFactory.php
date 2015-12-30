<?php

/**
 * Created by PhpStorm.
 * User: pooria
 * Date: 9/28/15
 * Time: 2:11 AM
 */

namespace User\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Controller\AuthenticationController;

class AuthenticationControllerFactory implements FactoryInterface
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
        return new AuthenticationController($services);
    }
}