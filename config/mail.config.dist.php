<?php
return array(
    'mail' => array(
        'from_email' => 'FILL_IN',
        'to_email' => 'FILL_IN',
        'dev_email' => 'FILL_IN',
        'development_mode' => true, // If true, will send to dev_email above
        'transport' => array(
            'default' => 'smtp', // Change to sendmail for basic php mail
            'options' => array(
                'host' => 'FILL_IN', // Eg smtp.gmail.com
                'connection_class' => 'plain',
                'connection_config' => array(
                    'username' => 'FILL_IN',
                    'password' => 'FILL_IN',
                    'ssl' => 'tls'
                ),
            ),
        ),
        'site_name' => 'FILL_IN' // Eg. www.example.com
    )
);