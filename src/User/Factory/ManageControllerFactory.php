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
        $doctrineService = $realServiceLocator->get('Doctrine\ORM\EntityManager');
        $services = array("doctrine"=>$doctrineService);
        return new ManageController($services);
    }
}