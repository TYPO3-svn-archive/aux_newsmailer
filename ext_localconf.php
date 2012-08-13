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


?>