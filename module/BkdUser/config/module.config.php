<?php
/**
 * BkdUser - Coolbkd Zend Framework 2 User Module
 * 
 * @link https://github.com/coolbkd/BkdUser for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdUser/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'BkdUser\Controller\Index' => 'BkdUser\Controller\IndexController',
            'BkdUser\Controller\Registration' => 'BkdUser\Controller\RegistrationController',
            'BkdUser\Controller\Admin' => 'BkdUser\Controller\AdminController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'user-index' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'BkdUser\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
            'user-register' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/register[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'BkdUser\Controller\Registration',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
            'user-admin' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/admin[/:action][/:id][/:state]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'state' => '[0-9]',
                    ),
                    'defaults' => array(
                        'controller' => 'BkdUser\Controller\Admin',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'view_manager' => array(
        'display_exceptions' => true,
        'template_path_stack' => array(
            'bkd-user' => __DIR__ . '/../view'
        ),
        'strategies' => array(
        'ViewJsonStrategy',
             ),
    ),
    'service_manager' => array (
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => 'BkdUser\Service\Factory\AuthenticationFactory',
            'mail.transport' => 'BkdUser\Service\Factory\MailTransportFactory',
            'bkduser_module_options' => 'BkdUser\Service\Factory\ModuleOptionsFactory',
            'bkduser_error_view' => 'BkdUser\Service\Factory\ErrorViewFactory',
            'bkduser_user_form' => 'BkdUser\Service\Factory\UserFormFactory',
        ),
    ),
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => true,
            ),
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'BkdUser\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'BkdUser\Service\UserService::verifyHashedPassword',
            ),
        ),
        'driver' => array(
            'bkduser_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/BkdUser/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'BkdUser\Entity' => 'bkduser_driver',
                ),
            ),
        ),
    ),
);
