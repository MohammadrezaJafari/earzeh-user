<?php

namespace User\Factory;
use User\Controller\ManageController;
use User\Controller\RegistrationController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationControllerFactory implements FactoryInterface
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

        $registrationService = $realServiceLocator->get('Ellie\Service\Registration');

        $services =  array("registration"=>$registrationService);
        return new RegistrationController($services);
    }
}