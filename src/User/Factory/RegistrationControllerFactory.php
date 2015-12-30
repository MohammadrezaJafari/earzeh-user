<?php

namespace User\Factory;
use User\Controller\ManageController;
use User\Controller\RegistrationController;
use User\EventHandler\EventHandler;
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
        $doctrineService = $realServiceLocator->get('Doctrine\ORM\EntityManager');
        $services =  array("registration"=>$registrationService , 'doctrineService' => $doctrineService);
        $eventHandler = new EventHandler("User");

        return new RegistrationController($services,$eventHandler);
    }
}