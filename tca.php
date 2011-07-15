<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_auxnewsmailer_usercat"] = Array (
	"ctrl" => $TCA["tx_auxnewsmailer_usercat"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,iduser,mailcat,domail,dosms"
	),
	"feInterface" => $TCA["tx_auxnewsmailer_usercat"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"iduser" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_usercat.iduser",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "fe_users",	
				"foreign_table_where" => "ORDER BY fe_users.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"mailcat" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_usercat.mailcat",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tt_news_cat",	
				"foreign_table_where" => "ORDER BY tt_news_cat.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"domail" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_usercat.domail",		
			"config" => Array (
				"type" => "check",
			)
		),
		"dosms" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_usercat.dosms",		
			"config" => Array (
				"type" => "check",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, iduser, mailcat, domail, dosms")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_auxnewsmailer_maillist"] = Array (
	"ctrl" => $TCA["tx_auxnewsmailer_maillist"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,iduser,idnews,state,msgtype"
	),
	"feInterface" => $TCA["tx_auxnewsmailer_maillist"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"iduser" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_maillist.iduser",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "fe_users",	
				"foreign_table_where" => "ORDER BY fe_users.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"idnews" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_maillist.idnews",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tt_news",	
				"foreign_table_where" => "ORDER BY tt_news.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"state" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_maillist.state",		
			"config" => Array (
				"type" => "input",	
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"msgtype" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.php:tx_auxnewsmailer_maillist.msgtype",		
			"config" => Array (
				"type" => "input",	
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, iduser, idnews, state, msgtype")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);


$TCA["tx_auxnewsmailer_control"] = Array (
	"ctrl" => $TCA["tx_auxnewsmailer_control"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,template,stylesheet,userpage,organisation,name,returnmail,sendtime,duration,folders,lasttime"
	),
	"feInterface" => $TCA["tx_auxnewsmailer_control"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"template" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.template",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_auxnewsmailer",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"stylesheet" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.stylesheet",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_auxnewsmailer",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		
		'image' => Array (
			'exclude' => 1,
			'l10n_mode' => $l10n_mode_image,
			'label' => 'LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '1000',
				'uploadfolder' => 'uploads/pics',
				'show_thumbs' => '1',
				'size' => '3',
				'maxitems' => '10',
				'minitems' => '0'
			)
		),
		"imagew" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.imagew",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"400",
			)
		),
		"imageh" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.imageh",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"100",
			)
		),
		"listimagew" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.listimagew",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"100",
			)
		),
		"listimageh" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.listimageh",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"100",
			)
		),
		"dateformat" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.dateformat",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"%m %d %y",
			)
		),
		"timeformat" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.timeformat",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"%H:%M",
			)
		),
		
		"organisation" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.organisation",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"My organization",
			)
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"My Name",
			)
		),
		"subject" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.subject",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"News from ###domain###",
			)
		),
		"returnmail" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.returnmail",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default"=>"info@myorg.com",
			)
		),
		"sendtime" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.sendtime",		
			"config" => Array (
				"type" => "input",
				"size" => "12",
				"max" => "20",
				"eval" => "time",
				"checkbox" => "0",
				"default" => ""			)
		),
		
		"duration" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.0", "1"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.1", "2"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.2", "3"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.3", "4"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.4", "5"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.5", "6"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.6", "0"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.7", "8"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.8", "9"),
					Array("LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.duration.I.9", "10"),
				),
				"size"=>10,
				"maxitems"=>10,
				"default" => "0,1,2,3,4,5,6",
			)
		),
		"folders" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.folders",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "pages",	
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 20,
			)
		),
		"userpage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.userpage",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "pages",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"userpid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.userpid",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "pages",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"newspage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.newspage",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "pages",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"lasttime" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.lasttime",		
			"config" => Array (
				"type" => "input",
				"size" => "12",
				"max" => "20",
				"eval" => "datetime",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"usecat" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.usecat",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"pretext" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.pretext",		
			"config" => Array (
				"type" => "text",	
				"cols" => "40",
				"rows" => "15",
				"default"=>"Dear ###name###\n\nWe are happy to send you the latest news from ###domain###",
				'wizards' => Array(
					'_PADDING' => 4,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				)
			)
		),
		"posttext" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.posttext",		
			"config" => Array (
				"type" => "text",	
				"cols" => "40",
				"rows" => "15",
				"default" => "Yours sincerely\n###orgname###\n###org###",
			)
		),
		"showitems" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.showitems",		
			"config" => Array (
				"type" => "select",	
				"items" => array(
					array("title",1),
					array("image",2),
					array("body",3),
					array("date",4),
					array("time",5),	
				),
				"size"=>10,
				"maxitems"=>20,
				"default"=>"1,2,4,5",
			)
		),
		"lang" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.lang",		
			"config" => Array (
				"type" => "select",	
				"itemsProcFunc"=>'EXT:aux_newsmailer/class_tx_auxnewsmailer_static.php:tx_auxnewsmailer_static->getLang',
				
				
				"size"=>1,
				"maxitems"=>1,
				"default"=>"default",
			)
		),
		"orgdomain" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.domain",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"default" => "www.myorg.com",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "--div--;LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.template;hidden;;1;;1-1-1, template, stylesheet,lang,image,imagew,imageh,listimagew,listimageh,dateformat,timeformat,showitems, --div--;LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.organisation, organisation, name,subject, returnmail,orgdomain,--div--;LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.settings,usecat,duration,sendtime, lasttime,--div--;LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.ctrlfolders,userpage,userpid,newspage, folders,--div--;LLL:EXT:aux_newsmailer/locallang_db.xml:tx_auxnewsmailer_control.text,pretext,posttext")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

?>