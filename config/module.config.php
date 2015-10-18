<?php
namespace User;

return array(
    'doctrine' => array(
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Application\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password',
            )
        )),

    'registration' => [
        'isUnique' => [
            // column => input
//            'name' => 'username',
//            'phone' => 'phone',
//            'email' => 'email',
                'username' => 'Username'
        ],
        'model' => 'Application\Entity\User',
    ],

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(

        )
    ),

    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),

    'controllers' => array(
        'factories' => array( // for passing variable
            'authentication' => 'User\Factory\AuthenticationControllerFactory',
            'manage' => 'User\Factory\ManageControllerFactory',
            'registration' => 'User\Factory\RegistrationControllerFactory',
            'activation' => 'User\Factory\ActivationControllerFactory',
        ),
        'invokables' =>array(
            //without passing variable controlers
        )
    ),

    'service_manager' => array(
        'factories' => array(
            'Ellie\Service\Log' => 'Ellie\Service\Log\LogServiceFactory',
            'Ellie\Service\Authentication' => 'Ellie\Service\Authentication\ServiceFactory',
            'Ellie\Service\Registration' => 'Ellie\Service\Registration\ServiceFactory',
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
                    'route'    => '[/:lang]/user[/:controller[/:action[/:id]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',

                    ),
                    // Define default controller and action to be called when this route is matched
                    'defaults' => array(
                        'controller' => 'authentication',
                        'action'     => 'login',
                    )
                )
            ),

            'sign-up' => array(
                // Define the routes type to be "Zend\Mvc\Router\Http\Literal", which is basically just a string
                'type' => 'segment',
                // Configure the route itself
                'options' => array(
                    // Listen to "/blog" as uri
                    'route'    => '/sign-up[/:controller[/:action]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',

                    ),
                    // Define default controller and action to be called when this route is matched
                    'defaults' => array(
                        'controller' => 'registration',
                        'action'     => 'register',
                    )
                )
            ),

            'activate' => array(
                // Define the routes type to be "Zend\Mvc\Router\Http\Literal", which is basically just a string
                'type' => 'segment',
                // Configure the route itself
                'options' => array(
                    // Listen to "/blog" as uri
                    'route'    => '/activate[/:controller[/:action]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',

                    ),
                    // Define default controller and action to be called when this route is matched
                    'defaults' => array(
                        'controller' => 'activation',
                        'action'     => 'getuser',
                    )
                )
            ),
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