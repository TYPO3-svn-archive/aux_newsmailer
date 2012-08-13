<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txauxnewsmailerM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}





//t3lib_div::loadTCA("tt_content");
//t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);
//t3lib_extMgm::addToAllTCAtypes("tt_content","");

$tempColumns = Array (
	"tx_auxnewsmailer_newscat" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:fe_groups.tx_auxnewsmailer_newscat",		
		"config" => Array (
			"type" => "group",	
			"internal_type" => "db",	
			"allowed" => "tt_news_cat",	
			"size" => 10,	
			"minitems" => 0,
			"maxitems" => 50,	
			"MM" => "tx_auxnewsmailer_newscat_fe_groups_mm",
		)
	),
);


t3lib_div::loadTCA("fe_groups");
t3lib_extMgm::addTCAcolumns("fe_groups",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_groups","tx_auxnewsmailer_newscat;;;;1-1-1");




$tempColumns = Array (
	"tx_auxnewsmailer_newsletter" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:fe_users.tx_auxnewsmailer_newsmailer",		
		"config" => Array (
			"type" => "check",	
			"default"=>"1",
		)
	),
	"tx_auxnewsmailer_html" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:fe_users.tx_auxnewsmailer_html",		
		"config" => Array (
			"type" => "check",	
			"default"=>"1",
		)
	),
);


t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_auxnewsmailer_newsletter;;;;1-1-1");
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_auxnewsmailer_html;;;;1-1-1");

t3lib_extMgm::allowTableOnStandardPages("tx_auxnewsmailer_usercat");

$TCA["tx_auxnewsmailer_usercat"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_usercat",		
		"label" => "iduser",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_auxnewsmailer_usercat.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, iduser, mailcat, domail, dosms",
	)
);



$TCA["tx_auxnewsmailer_maillist"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_maillist",		
		"label" => "uid",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_auxnewsmailer_maillist.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, iduser, idnews, state, msgtype",
	)
);

t3lib_extMgm::allowTableOnStandardPages("tx_auxnewsmailer_control");

$TCA["tx_auxnewsmailer_control"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control',		
		'label' => 'organisation',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs'=>true,
		"default_sortby" => "ORDER BY crdate",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_auxnewsmailer_control.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, template, stylesheet, userpage, organisation, name, returnmail, sendermail, sendtime, duration, folders, lasttime,userpid",
	)
);


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY."_pi1"]='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY."_pi1", 'FILE:EXT:aux_newsmailer/flexform_ds.xml');

t3lib_extMgm::addPlugin(Array("LLL:EXT:aux_newsmailer/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","FE User newsletter subscription");
?>