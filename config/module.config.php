<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Email\Controller\Email' => 'Email\Controller\EmailController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'email' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/email[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Email\Controller\Email',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'email/default'           => __DIR__ . '/../view/email/email/default.phtml',
            'layout/html'           => __DIR__ . '/../view/email/layout/html.phtml',
            'layout/text'           => __DIR__ . '/../view/email/layout/text.phtml',
        ),
        'layout' => 'layout/layout',
    ),
    'service_manager' => array(
        'invokables' => array(
            'Email\Mapper\Email' => 'Email\Mapper\Email',
            'Email\Entity\Email' => 'Email\Entity\EmailEntity',
            'Zend\Mail' => 'Zend\Mail'
        ),
        'factories' => array(
            'email' => function ($sm) {
                $config = $sm->get('email\get_config');

                $mapper = new Email\Mapper\Email;
                $mapper->setEntity(new Email\Entity\Email());
                $mapper->setPathStack($sm->get('ViewTemplateMapResolver'));
                $mapper->setConfig($config);

                // Select default transport based on config options
                if (strtolower($config['transport']['default']) == 'smtp') {
                    $transport = new \Zend\Mail\Transport\Smtp();
                    $transport->setOptions(new \Zend\Mail\Transport\SmtpOptions($config['transport']['options']));
                } elseif (strtolower($config['transport']['default']) == 'sendmail') {
                    $transport = new \Zend\Mail\Transport\Sendmail();
                } else {
                    // Default option
                    $transport = new \Zend\Mail\Transport\Sendmail();
                }
                
                $mapper->setTransport($transport);

                return $mapper;
            },
            'email\get_config' => function ($sm) {
                $config = $sm->get('Config');
                return $config['mail'];
            }
        )
    )
);