<?php

########################################################################
# Extension Manager/Repository config file for ext "aux_newsmailer".
#
# Auto generated 09-09-2010 19:44
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Newsletter via news (tt_news)',
	'description' => 'Create newsletters based on news (tt_news). 
Compiles several news items into one newsletter.
Individual newsletters based on subscription to tt_news categories.
FE plug-in for subscription sign-up.
Newsletters in plain text or HTML.
Support for cronjobs.',
	'category' => 'module',
	'shy' => 0,
	'version' => '0.0.7',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_news,tt_content,fe_groups',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Tim Wentzlau',
	'author_email' => 'tim.wentzlau@auxilior.com',
	'author_company' => 'Auxilior Technology',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'tt_news' => '',
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:35:{s:9:"Thumbs.db";s:4:"3516";s:33:"class_tx_auxnewsmailer_static.php";s:4:"1544";s:12:"ext_icon.gif";s:4:"0903";s:17:"ext_localconf.php";s:4:"acae";s:15:"ext_php_api.dat";s:4:"efa6";s:14:"ext_tables.php";s:4:"2bbe";s:14:"ext_tables.sql";s:4:"5d18";s:24:"ext_typoscript_setup.txt";s:4:"7171";s:15:"flexform_ds.xml";s:4:"3e30";s:33:"icon_tx_auxnewsmailer_control.gif";s:4:"68a6";s:34:"icon_tx_auxnewsmailer_maillist.gif";s:4:"475a";s:33:"icon_tx_auxnewsmailer_usercat.gif";s:4:"b4e5";s:16:"locallang_db.xml";s:4:"dc4a";s:7:"tca.php";s:4:"047f";s:14:"doc/manual.sxw";s:4:"719d";s:19:"doc/wizard_form.dat";s:4:"a89d";s:20:"doc/wizard_form.html";s:4:"c8d3";s:16:"mailer/mailer.sh";s:4:"1991";s:14:"mod1/Thumbs.db";s:4:"c46d";s:33:"mod1/class_auxnewsmailer_core.php";s:4:"a384";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"b6c2";s:14:"mod1/index.php";s:4:"6042";s:18:"mod1/locallang.xml";s:4:"24ed";s:22:"mod1/locallang_mod.xml";s:4:"7aa2";s:19:"mod1/moduleicon.gif";s:4:"db4c";s:34:"pi1/class.tx_auxnewsmailer_pi1.php";s:4:"4de1";s:17:"pi1/locallang.xml";s:4:"688d";s:24:"pi1/static/editorcfg.txt";s:4:"84dc";s:20:"pi1/static/setup.txt";s:4:"44c8";s:13:"res/Thumbs.db";s:4:"65a1";s:25:"res/mail-reply-sender.png";s:4:"fb1e";s:12:"res/mail.css";s:4:"f888";s:23:"res/mobile_phone_16.gif";s:4:"60c9";s:17:"res/template.tmpl";s:4:"f289";}',
);

?>