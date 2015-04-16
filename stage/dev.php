<?php

server('dev-svr', '192.168.1.2', 22)
    ->user('deployer')
    ->forwardAgent()
    ->stage(['dev'])
    
    ->env('deploy_path',        '/var/www/apps/cakephp2')
    ->env('branch',             'master')
    
    ->env('app.debug',          2)
    ->env('app.prefix',         'cakephp2_')
    ->env('app.salt',           'b8e6d397b4b0f0c6758fb4b818bd115437e4587a')
    ->env('app.cipherSeed',     '353533306162313964613031341102')
    
    ->env('app.domain',         'cakephp2.dev')
        
    ->env('app.timezone',       'UTC')
    
    ->env('redis.host',         '127.0.0.1')
    ->env('redis.port',         '6379')
    ->env('redis.db',           '1')
    
    ->env('mysql.host',         '127.0.0.1')
    ->env('mysql.port',         '3306')
    ->env('mysql.username',     'devcakephp2')
    ->env('mysql.password',     'devcakephp2')
    ->env('mysql.dbname',       'devcakephp2')
    ->env('mysql.prefix',       '')
    
    ->env('email.default.from', array('no-reply@cakephp2.dev' => 'CakePHP Team'))
    ->env('email.default.host', '127.0.0.1')
    ->env('email.default.port', 25)
    ->env('email.default.user', null)
    ->env('email.default.pass', null)
;