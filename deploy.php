<?php
// require common recipe
require 'recipe/common.php';
foreach (glob(__DIR__ . '/stage/*.php') as $filename) {
    include $filename;
}

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
 * Make shared_dirs and configure files from templates
 */
task('configure', function () {

    /**
     * Compiler template of configure files
     * 
     * @param string $contents
     * @return string
     */
    $compiler = function ($contents) {
        if (preg_match_all('/\{\{(.+?)\}\}/', $contents, $matches)) {
            foreach ($matches[1] as $name) {
                $value = env()->get($name);
                if (is_null($value) || is_bool($value) || is_array($value)) {
                    $value = var_export($value, true);
                }
                $contents = str_replace('{{' . $name . '}}', $value, $contents);
            }
        }
        return $contents;
    };

    $finder   = new \Symfony\Component\Finder\Finder();
    $iterator = $finder
        ->files()
        ->name('*.tpl')
        ->in(__DIR__ . '/shared');

    $tmpDir = sys_get_temp_dir();

    /* @var $file \Symfony\Component\Finder\SplFileInfo */
    foreach ($iterator as $file) {
        $success = false;
        // Make tmp file
        $tmpFile = tempnam($tmpDir, 'tmp');
        if (!empty($tmpFile)) {
            try {
                $contents = $compiler($file->getContents());
                $target   = preg_replace('/\.tpl$/', '', $file->getRelativePathname());
                // Put contents and upload tmp file to server
                if (file_put_contents($tmpFile, $contents) > 0) {
                    run('mkdir -p {{deploy_path}}/shared/' . dirname($target));
                    upload($tmpFile, 'shared/' . $target);
                    $success = true;
                }
            } catch (\Exception $e) {
                if (isVerbose()) {
                    write(sprintf("<fg=red>✘</fg=red> %s", $e->getMessage()));
                }
                $success = false;
            }
            // Delete tmp file
            unlink($tmpFile);
        }
        if ($success) {
            writeln(sprintf("<info>✔</info> %s", $file->getRelativePathname()));
        } else {
            writeln(sprintf("<fg=red>✘</fg=red> %s", $file->getRelativePathname()));
        }
    }
})->desc('Make configure files for your stage');

/**
 * Upload cert files
 */
task('upload:cert_files', function(){
    
    writeln('Upload cert files');
    
    $finder   = new \Symfony\Component\Finder\Finder();
    $iterator = $finder
        ->files()
        ->name('/\.(crt|key|cer)$/')
        ->in(__DIR__ . '/shared');
    
    /* @var $file \Symfony\Component\Finder\SplFileInfo */
    foreach ($iterator as $file) {
        $success = false;
        $target  = $file->getRelativePathname();
        try {
            run('mkdir -p {{deploy_path}}/shared/' . dirname($target));
            upload($file->getRealPath(), 'shared/' . $target);
            $success = true;
        } catch (\Exception $e) {
            $success = false;
        }

        if ($success) {
            writeln(sprintf("<info>✔</info> %s", $target));
        } else {
            writeln(sprintf("<fg=red>✘</fg=red> %s", $target));
        }
    }
})->desc('Upload cert files');

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
after('configure', 'upload:cert_files');