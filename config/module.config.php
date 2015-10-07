<?php
namespace User;

return array(
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__.'_driver' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/'.__NAMESPACE__.'/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )

            )),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'User\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password',
            )
        )),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(

        )
    ),

    'controllers' => array(
        'factories' => array( // for passing variable
            'authentication' => 'User\Factory\AuthenticationControllerFactory',
            'manage' => 'User\Factory\ManageControllerFactory',
        ),
        'invokables' =>array(
            //without passing variable controlers
        )
    ),

    'service_manager' => array(
        'factories' => array(
            'Ellie\Service\Log' => 'Ellie\Service\Log\LogServiceFactory',
            'Ellie\Service\Authentication' => 'Ellie\Service\Authentication\ServiceFactory',
        )
    ),

    // This lines opens the configuration for the RouteManager
    'router' => array(
        // Open configuration for all possible routes
        'routes' => array(
            // Define a new route called "post"
            'user' => array(
                // Define the routes type to be "Zend\Mvc\Router\Http\Literal", which is basically just a string
                'type' => 'segment',
                // Configure the route itself
                'options' => array(
                    // Listen to "/blog" as uri
                    'route'    => '/user[/:controller[/:action]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',

                    ),
                    // Define default controller and action to be called when this route is matched
                    'defaults' => array(
                        'controller' => 'authentication',
                        'action'     => 'login',
                    )
                )
            )
        )
    ),

    'menu'  => [
        'User Management' => [
            'Create New User' => 'manage/create',
            'Company List' => 'manage/list',
            'Manager List' => 'user/manage/',
            'Operator List' => 'user/manage/',
            'Unregisted List' => 'user/manage/',
        ]
    ]
);