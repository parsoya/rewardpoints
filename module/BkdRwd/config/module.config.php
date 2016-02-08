<?php

namespace BkdRwd;

return array(
    'controllers' => array(
        'invokables' => array(
            'BkdRwd\Controller\Index' => 'BkdRwd\Controller\IndexController',
            'BkdRwd\Controller\Program' => 'BkdRwd\Controller\ProgramController',
            'BkdRwd\Controller\Product' => 'BkdRwd\Controller\ProductController',
            'BkdRwd\Controller\Category' => 'BkdRwd\Controller\CategoryController' // Program Type
        ),
    ),
    'router' => array(
        'routes' => array(
            'bkd-rwd' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/bkd-rwd',
                    'defaults' => array(
                        '__NAMESPACE__' => 'BkdRwd\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            // 'route'    => '/[:controller[/:action[/:id]]]',
                            'route'    => '/[:controller[/:action[/:id[/:id2]]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
					'paginator-doctrine' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/list/[:controller[/page:page]]',
							'constraints' => array(
								'page' => '[0-9]*',
							),
							'defaults' => array(
								'__NAMESPACE__' => 'BkdRwd\Controller',
								'controller'    => 'program',
								'action'        => 'index',
							),
						),
					),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'bkd-rwd' => __DIR__ . '/../view'
        ),

        'display_exceptions' => true,
    ),
    'view_helpers' => array(
        'factories' => array(
            'vote' => function($sm) {
              $sm = $sm->getServiceLocator(); // $sm was the view helper's locator
              $em = $sm->get('doctrine.entitymanager.orm_default');

              $helper = new \BkdRwd\View\Helper\Vote($em);
              return $helper;
            },
            'reward' => function($sm) {
              $sm = $sm->getServiceLocator(); // $sm was the view helper's locator
              $em = $sm->get('doctrine.entitymanager.orm_default');

              $helper = new \BkdRwd\View\Helper\Reward($em);
              return $helper;
            },
            'programIntro' => function($sm) {
              $sm = $sm->getServiceLocator(); // $sm was the view helper's locator
              $em = $sm->get('doctrine.entitymanager.orm_default');

              $helper = new \BkdRwd\View\Helper\ProgramIntro($em);
              return $helper;
            }
        ),
    ),
    
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                )
            )
        )
    ),
);
