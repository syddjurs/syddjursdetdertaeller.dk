<?php

namespace Deployer;

use Symfony\Component\Console\Input\InputOption;

//require_once __DIR__ . '/common.php';
require 'webroot/vendor/deployer/deployer/recipe/drupal8.php';
require 'webroot/vendor/deployer/recipes/recipe/slack.php';
//require 'deployer/recipe/composer.php;'
require 'deployer/recipe/sql.php';
require 'deployer/recipe/maintenance.php';
require 'deployer/recipe/drupal.php';
require 'deployer/recipe/slack.php';

// Project name
set('application', 'Syddjurs det der tæller');

// Hosts
inventory('deployer/hosts.yml');

// Git repository
set('repository', 'git@github.com:syddjurs/syddjursdetdertaeller.dk.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

//Set drupal site. Change if you use different site
set('drupal_site', 'syddjursdetdertaeller.dk');

//Drupal 8 shared dirs
set('shared_dirs', [
  'webroot/sites/syddjursdetdertaeller.dk/files',
  'private',
]);

//Drupal 8 shared files
set('shared_files', [
  'webroot/sites/syddjursdetdertaeller.dk/settings.local.php',
  'webroot/sites/syddjursdetdertaeller.dk/services.local.yml',
  'webroot/sites/sites.local.php',
]);

//Drupal 8 Writable dirs
set('writable_dirs', [
  'webroot/sites/syddjursdetdertaeller.dk/files',
]);

//if deploy fails, should database be rolled back?
set('rollback_db', 'false');

// Options
option('cim', null, InputOption::VALUE_NONE, 'Perform config import at the end of deployment');
option('no-cim', null, InputOption::VALUE_NONE, 'Prevent config import at the end of deployment');
option('no-updb', null, InputOption::VALUE_NONE, 'Prevent database update at the end of deployment');
option('no-entup', null, InputOption::VALUE_NONE, 'Prevent entity update at the end of deployment');
option('no-locale-update', null, InputOption::VALUE_NONE, 'Prevent locale update at the end of deployment');

// Tasks
task('test', function(){
  if(askConfirmation('Vil du?')){
    input()->setOption('cim','true');
  } else {
    input()->setOption('cim',NULL);
  }
  writeln(input()->getOption('cim'));
})
  ->once()
//  ->shallow()
//  ->setPrivate()
;


task('success', function(){
  writeln("✈︎ Deployment on <fg=cyan>{{hostname}}</fg=cyan> was successful.");
  writeln("<fg=magenta>♥ You're awesome! ♥</fg=magenta>");
})
  ->once()
  ->shallow()
  ->setPrivate();

// Slack
before('slack:notify', 'slack:notify:init');
before('slack:notify:init', 'deploy:drupal:pre_deploy');
before('deploy', 'slack:notify');
before('deploy', 'deploy:drupal:pre_deploy');
after('deploy', 'success');
after('success', 'slack:notify:success');
before('slack:notify:success', 'slack:notify:success:init');

// Additional Drupal release stuff
after('deploy:update_code', 'deploy:composer:install');
after('deploy:shared', 'deploy:maintenance_mode:enable');
before('deploy:symlink', 'deploy:db:dump');
before('cleanup', 'deploy:db:cleanup');
before('cleanup', 'deploy:drupal:post_deploy_updates');
after('deploy:drupal:post_deploy_updates', 'deploy:maintenance_mode:disable');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:db:rollback');
after('deploy:failed', 'rollback');
after('deploy:failed', 'deploy:maintenance_mode:disable');
after('deploy:failed', 'slack:notify:failed');
after('deploy:failed', 'slack:notify');
after('deploy:failed', 'deploy:unlock');

