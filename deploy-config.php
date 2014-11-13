<?php
/**
 * The repos that we want to deploy.
 *
 * Each repos will be an entry in the array in the following way:
 * 'repo name' => array( // Required. This is the repo name
 * 		'path' 	 => '/path/to/local/repo/' // Required. The local path to your code.
 * 		'branch' => 'the_desired_deploy_branch', // Required. Deployment branch.
 *		'remote' => 'git_remote_repo', // Optional. Defaults to 'origin'
 * 		'post_deploy' => 'callback' // Optional callback function for whatever.
 * )
 *
 * You can put as many of these together as you want, each one is simply 
 * another entry in the $repos array. To set up a deploy create a deploy key
 * for your repo on github or bitbucket. You can generate multiple deploy keys
 * for multiple repos.
 * @see https://confluence.atlassian.com/pages/viewpage.action?pageId=271943168
 *
 * Note that deploy keys are only necessary if the repo is private. If it is a
 * public repo, then you do not need a key to get read only access to the repo
 * which is really what we are after for deployment.
 *
 * Once you have done an initial git pull in the desired code location, you can
 * run 'pwd' to get the full directory of your git repo. Once done, enter that
 * full path in the 'path' option for that repo. The optional callback will allow
 * you to ping something else as well such as hitting a DB update script or any
 * other configuration you may need to do for the newly deployed code.
 * 
 * NEW: 'branch' can be an associative array:
 *  		'branch' => array ('release/v1.1' => '/var/www/html/v1.1')
 * This allows you to deploy different branches of the same repository to different directories.
 * 
 * Also, if your commit is to release/v1.1.1, it will match that to the most specific option. E.g.:
 *  		'branch' => array (
 * 			'master' => '/var/www/html/master',
 * 			'release/v1.1.2' => '/var/www/html/v1.1.2',
 *  			'release/v1.1' => '/var/www/html/v1.1'
 * 			)
 * Will push commits against release v1.1.1 and v1.1.3 etc. to /var/www/html/v1.1.1
 * Note that order matters - it will match the first thing it can, so put your more specific matches first.
 */
/**
 * Fix for Apache
 */
putenv('HOME=/var/www');


$repos = [
	'practera' => [
		'branch' => 'develop',
		'remote' => 'origin',
		'path' => '/var/www/html/practera-develop/',
		'post_deploy' => 'stage'
	]
];

/**
 * Sets the deploy log directory
 */
define( 'DEPLOY_LOG_DIR', dirname( __FILE__ ) );

function stage() {
	exec('cd /var/www/html/practera-develop');
	exec('composer.phar install');
	exec('/usr/local/bin/dev-deploy');
}

/* Do not edit below this line */
require_once 'inc/class.deploy.php';
