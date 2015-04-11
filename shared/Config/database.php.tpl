<?php

class DATABASE_CONFIG
{

    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => '{{mysql.host}}',
        'login'      => '{{mysql.username}}',
        'password'   => '{{mysql.password}}',
        'database'   => '{{mysql.dbname}}',
        'prefix'     => '{{mysql.prefix}}',
        'encoding'   => 'utf8',
    );
    
    public $test    = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => '{{mysql.host}}',
        'login'      => '{{mysql.username}}',
        'password'   => '{{mysql.password}}',
        'database'   => '{{mysql.dbname}}',
        'prefix'     => 'test',
        'encoding'   => 'utf8',
    );

}
