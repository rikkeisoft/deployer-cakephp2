<?php
// require common recipe
require 'recipe/common.php';
require 'vendor/deployphp/recipes/recipes/configure.php';

//set('ssh_type', 'ext-ssh2');

/**
 * Set parameters
 */
set('repository', 'git@github.com:rikkeisoft/cakephp2.git');
set('keep_releases', 5);
set('shared_dirs', [
    'Plugin',
    'tmp',
    'Vendor',
    'webroot/files',
]);
set('shared_files', [
    'Config/core.php',
    'Config/database.php',
    'Config/email.php',
    'composer.lock',
]);
set('writable_dirs', [
    'tmp',
    'webroot/files',
]);
set('writable_use_sudo', false); // Using sudo in writable commands?

/**
 * Deploy start, prepare deploy directory
 */
task('deploy:start', function() {
    cd('~');
    run("if [ ! -d {{deploy_path}} ]; then mkdir -p {{deploy_path}}; fi");
    cd('{{deploy_path}}');
})->setPrivate();

/**
 * Clear cache and restart backend services
 */
task('backend:restart', function () {
    
    within('{{deploy_path}}/current', function () {
        // TODO: stop all services
    
        // Clear map files
        run("redis-cli -h {{redis.host}} -p {{redis.port}} -n {{redis.db}} --raw keys \"{{app.prefix}}cake_core_file_map\" | xargs redis-cli -h {{redis.host}} -p {{redis.port}} -n {{redis.db}} del");
    
        // TODO: clear cache
        run('Console/cake cache clear -f 1');
    
        // TODO: start all service
    });
    
})->desc('Restart backend service for your system');

/**
 * Main task
 */
task('deploy', [
    'deploy:start',
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup',
    'backend:restart',
])->desc('Deploy your project');

after('deploy', 'success');

/**
 * Load stage and list server
 */
foreach (glob(__DIR__ . '/stage/*.php') as $filename) {
    include $filename;
}
//serverList(__DIR__ . '/stage/servers.yml');