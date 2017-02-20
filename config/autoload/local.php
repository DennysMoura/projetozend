<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

$db = array(
    'database' => 'projetozend',
    'username' => 'root',
    'password' => 'Redes21220',
    'hostname' => '192.168.10.10'  
);

return array(
    'service_manager' => array(
        'factories' => array(
            'zend_db_adapter' => function ($sm) use ($db) {
                return new Zend\Db\Adapter\Adapter(array(
                    'driver'    => 'pdo',
                    'dsn'       => 'mysql:dbname='. $db['database'] . ';host='. $db['hostname'],
                    'database'  => $db['database'],
                    'username'  => $db['username'],
                    'password'  => $db['password'],
                    'hostname'  => $db['hostname'],
                ));
            }
        )
    )
);
