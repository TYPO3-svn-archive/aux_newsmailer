#!/usr/local/bin/php 
<?php


// set typo path to point to the root of the T3 installation
$typopath='/www/my_t3_root/';

// create a BE user called _cli_auxnewsmailer (must not be admin), for better security change it to somethine else.
$MCONF['name'] = '_CLI_auxnewsmailer';

// *****************************************

// Standard initialization of a CLI module:

 // *****************************************


if (@is_file($typopath.'typo3conf/ext/aux_newsmailer/mod1/index.php'))
	$modulepath=$typopath.'typo3conf/ext/aux_newsmailer/';
else
	$modulepath=$typopath.'typo3/ext/aux_newsmailer/';

// Defining circumstances for CLI mode:

define('TYPO3_cliMode', TRUE);





$BACK_PATH = '../../../../typo3/';
define('TYPO3_mainDir', 'typo3/');
define(PATH_thisScript,$typopath.'typo3/typo3');
define('TYPO3_MOD_PATH', $modulepath.'/mailer/');
// Include init file:
require($typopath.'typo3/init.php');
require($typopath.'typo3/sysext/lang/lang.php');

$LANG=t3lib_div::makeInstance('language');
$LANG->init('default');


require($modulepath.'mod1/class_auxnewsmailer_core.php');


$mailer=new tx_auxnewsmailer_core;
$mailer->init();

$mailer->batch($argv[1],'','');



?>