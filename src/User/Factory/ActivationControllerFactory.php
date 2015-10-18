<?php

namespace User\Factory;
use User\EventHandler\EventHandler;
use User\Controller\ActivationController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ActivationControllerFactory implements FactoryInterface
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
        $doctrineService = $realServiceLocator->get('Doctrine\ORM\EntityManager');
        $services = array("doctrine"=>$doctrineService);
        $eventHandler = new EventHandler("User");
        return new ActivationController($services,$eventHandler);
    }
}