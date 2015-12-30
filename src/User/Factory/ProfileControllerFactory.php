<?php

namespace User\Factory;
use User\Controller\Company\ProfileController;
use User\EventHandler\EventHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProfileControllerFactory implements FactoryInterface
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

        return new ProfileController($services,$eventHandler);
    }
}