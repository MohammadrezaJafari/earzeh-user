<?php

namespace User\Factory;
use User\Controller\Company\B2BController;
use User\EventHandler\EventHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class B2BControllerFactory implements FactoryInterface
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

        return new B2BController($services,$eventHandler);
    }
}