<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_auxnewsmailer_pi1 = < plugin.tx_auxnewsmailer_pi1.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_auxnewsmailer_pi1.php","_pi1","list_type",1);


t3lib_extMgm::addTypoScript($_EXTKEY,"setup","
	tt_content.shortcut.20.0.conf.tx_auxnewsmailer_usercat = < plugin.".t3lib_extMgm::getCN($_EXTKEY)."_pi1
	tt_content.shortcut.20.0.conf.tx_auxnewsmailer_usercat.CMD = singleView
",43);


/* SCHEDULER SETTINGS */
/*
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_auxnewsmailer_scheduler'] = array(
	'extension' => 'aux_newsmailer',
	'title' => 'Automatic newsletter',
	'description' => 'Scans for new tt_news articles that people susbscribed to, creates and sends newsletter',
//	'additionalFields' => 'tx_auxnewsmailer_scheduler_addFields'
);
*/

include_once(t3lib_extMgm::extPath($_EXTKEY) . 'cli/class.tx_auxnewsmailer_scheduler_addFields.php'); // Scheduler addFields class


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_auxnewsmailer_scheduler'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:newsmailer_scheduler.name',
	'description'      => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:newsmailer_scheduler.description',
	'additionalFields' => 'tx_auxnewsmailer_scheduler_addFields'
);

//registering a hook when records are inserted/copied/deleted etc.  This extension needs to know when a fe_users is deleted to remove it's subscriptions.
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['tx_auxnewsmailer'] = 'EXT:aux_newsmailer/mod1/class_auxnewsmailer_core.php:&tx_auxnewsmailer_core';

?>