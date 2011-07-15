<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Tim Wentzlau (tim.wentzlau@auxilior.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'News mailer' for the 'aux_newsmailer' extension.
 *
 * @author	Tim Wentzlau <tim.wentzlau@auxilior.com>
 */



	// DEFAULT initialization of a module [BEGIN]

unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:aux_newsmailer/mod1/locallang.php');
#include ('locallang.php');
require_once ('class_auxnewsmailer_core.php');

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]


require_once (PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once (PATH_t3lib.'class.t3lib_htmlmail.php');
require_once (PATH_site.'typo3/sysext/cms/tslib/class.tslib_content.php');

class tx_auxnewsmailer_module1 extends tx_auxnewsmailer_core {
	var $pageinfo;
	var $cObj;
	var $inBatch=false;

	/**
	 * Initializes the module
	 * @return	void	
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;


		parent::init();
		//$GLOBALS['TYPO3_DB']->debugOutput=true;
		$this->cObj=t3lib_div::makeInstance('tslib_cObj');
				/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[]=PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}

	/**
	 * Configures the menu
	 *
	 * @return	void		...
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('function1'),
				'2' => $LANG->getLL('function2'),
				//'3' => $LANG->getLL('function3'),
			)
		);
		parent::menuConfig();
	}

		
	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;


		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

			if (t3lib_div::_GP('cmd')=='previewhtml'){
			  	$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->content.=$this->doc->startPage($LANG->getLL('title'));
				$this->content=$this->renderPreview('html');
			} else if (t3lib_div::_GP('cmd')=='previewplain'){
			  	$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->content.=$this->doc->startPage($LANG->getLL('title'));
				$this->content=$this->renderPreview('plain');
			}

			else{

					// Draw the header.
				$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->form='<form action="" method="POST">';

					// JavaScript
				$this->doc->JScode = '
					<script language="javascript" type="text/javascript">
						script_ended = 0;
						function jumpToUrl(URL)	{
							document.location = URL;
						}
					</script>
				';
				$this->doc->postCode='
					<script language="javascript" type="text/javascript">
						script_ended = 1;
						if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
					</script>
				';

				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

				$this->content.=$this->doc->startPage($LANG->getLL('title'));
				$this->content.=$this->doc->header($LANG->getLL('title'));
				$this->content.=$this->doc->spacer(5);
				$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content.=$this->doc->divider(5);

				if (t3lib_div::_GP('cmd')=='invoke'){
					$this->content.=$LANG->getLL('send').' '.$this->sendMail(t3lib_div::_GP('msg')).' '.$LANG->getLL('emails');
				}
				if (t3lib_div::_GP('cmd')=='scan'){
					$this->content.=$LANG->getLL('scanned').' '.$this->scanNews('mail',t3lib_div::_GP('ctrl')).$LANG->getLL('messages');
					$this->content.='<br>'.$LANG->getLL('created').' '.$this->mailList(t3lib_div::_GP('ctrl')).$LANG->getLL('mailmessages');
				}


				// Render content:
				$this->moduleContent();


				// ShortCut
				if ($BE_USER->mayMakeShortcut())	{
					$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}

				$this->content.=$this->doc->spacer(10);
			}
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void		...
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void		...
	 */
	function moduleContent()	{
		global $LANG;
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:

				$content=$this->renderOverview();

				$this->content.=$this->doc->section($LANG->getLL('function1'),$content,0,1);
			break;
			case 2:
				
				$url='index.php?id='.$this->id.'&cmd=previewhtml';
				$content='<div align=center><strong>HTML preview</strong></div><BR>';
				$content.='<iframe src="'.$url.'" width=475 height=600></iframe>';
				$url='index.php?id='.$this->id.'&cmd=previewplain';
				$content.='<div align=center><strong>Plain mail preview</strong></div><BR>';
				$content.='<TEXTAREA NAME="" COLS=80 ROWS=30>'.t3lib_div::formatForTextArea($this->renderPreview('plain')).'</TEXTAREA>';

				
				$this->content.=$this->doc->section($LANG->getLL('function2'),$content,0,1);
			break;
			case 3:
				$content='<div align=center><strong>Menu item #3...</strong></div>';
				$this->inBatch=true;
				$ctrl['duration']='0,1,2,4,5,6';
				$ctrl['lasttime']=mktime(0,0,0,2,10,2004);
				$this->content.='--10-2-2004='.$this->checkDuration($ctrl);
				$ctrl['lasttime']=mktime(0,0,0,12,10,2005);
				$this->content.='2-10-2004'.$this->checkDuration($ctrl);
				$ctrl['lasttime']=mktime(0,0,0,5,10,2006);
				$this->content.='2-10-2004'.$this->checkDuration($ctrl);
				$this->content.=$this->doc->section('Message #3:',$content,0,1);
				$ctrl['lasttime']=mktime(0,0,0,6,1,2006);
				$this->content.='10-2-2004'.$this->checkDuration($ctrl);

			break;
			case 4:
				$this->$content=$this->renderPreview();

			break;
		}
	}

	/**
	 * Creates the overview page
	 *
	 * @return	string		the finished page
	 */
	function renderOverview(){
	  	global $LANG;

		$ctrl=$this->loadControl();
	  	$content='';


	  	if ($ctrl['uid']!=0)
	  	{

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'uid,title,datetime,starttime',
                'tt_news',
                'deleted=0 and hidden=0 and tx_auxnewsmailer_scanstate<2 and tt_news.pid in '.$ctrl['pages'],
                '',
                'datetime',
				''
            );
		  	if ($res){
		  	  	$content.='</br><b>'.$LANG->getLL('unsendnews').'</b></br>';
		  	  	$content.='<br>'.$LANG->getLL('lastscan').strftime($ctrl['dateformat'].' '.$ctrl['timeformat'], $ctrl['lasttime']);
				$urlinvoke='index.php?id='.$this->id.'&cmd=scan&ctrl='.$ctrl['uid'];
				$content.='<br><a href="'.$urlinvoke.'">['.$LANG->getLL('createmsg').']</a></br>';

		  		$content.='<table border="1px">';
				$content.='<tr>';



				$content.='<td>'.$LANG->getLL('datetime').'</td>';
				$content.='<td>'.$LANG->getLL('starttime').'</td>';
				$content.='<td>'.$LANG->getLL('catcount').'</td>';
				$content.='<td>'.$LANG->getLL('newstitle').'</td>';
				
				$showcatmsg=false;
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				  	$catcount=$this->getCatCount($row['uid']);
					$content.='<tr>';
				  	$content.='<td>'.strftime($ctrl['dateformat'].' '.$ctrl['timeformat'], $row['datetime']).'</td>';
				  	$starttime='';
					
					if (($row['starttime'])&&($row['starttime']>time()))
						$starttime.=strftime($ctrl['dateformat'].' '.$ctrl['timeformat'], $row['starttime']);
					if (($ctrl['usecat'])&&($catcount==0)){
					  	$showcatmsg=true;
						if ($starttime) $starttime.='<br>';
						$starttime.='<font color="red">'.$LANG->getLL('nocat').'</font>';
					}
					if (!$starttime)
						$starttime=	$LANG->getLL('nextscan');
					
					$content.='<td>'.$starttime.'</td>';
					$content.='<td>'.$catcount.'</td>';
					$content.='<td>'.$row['title'].'</td>';
					$content.='</tr>';
					
				}
				$content.='</table>';
				if ($showcatmsg)
					$content.='<br>'.$LANG->getLL('catmsg').'<br>';
			}



			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                'tx_auxnewsmailer_msglist',
                'state=0 and idctrl='.$ctrl['uid'],
                '',
                '',
				''
            );
		  	if ($res){

				$content.='<br><b>'.$LANG->getLL('pendingmsg').'</b>';
				$urlinvoke='index.php?id='.$this->id.'&cmd=invoke&msg=0';
				$content.='<br><a href="'.$urlinvoke.'">['.$LANG->getLL('invoke').']</a>';

		  		$content.='<table border="1px">';
				$content.='<tr>';


				$content.='<td>#</td>';
				//$content.='<td>'.$LANG->getLL('start').'</td>';
				//$content.='<td>'.$LANG->getLL('end').'</td>';
				$content.='<td>'.$LANG->getLL('unsend').'</td>';
				$content.='<td>'.$LANG->getLL('send').'</td>';

				$content.='<td>&nbsp;</td>';

				$content.='</tr>';

				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				  	$urlinvoke='index.php?id='.$this->id.'&cmd=markall&msg='.$row['uid'];
					$cnt=$this->getUsrMsgCount($row['uid']);
					if ($cnt['unsend']){
						$content.='<tr>';


						$content.='<td>'.$row[uid].'</td>';
						//$content.='<td>123456</td>';
						//$content.='<td>123456</td>';
						$content.='<td>'.$cnt['unsend'].'</td>';
						$content.='<td>'.$cnt['sendto'].'</td>';
						$content.='<td><a href="'.$urlinvoke.'">['.$LANG->getLL('markall').']</a></td>';

						$content.='</tr>';
					}
				}
				$content.='</table>';











			}
			else
				$content.='no news are ready ';
		}
		else
			$content.='no news mail control record located in this folder';
	  	return $content;
	}

	/**
	 * Returns the number of users that should receive or have received a message
	 *
	 * @param	int		$msg: id of message.
	 * @return	array	Array holding the counts.
	 */
	function getUsrMsgCount($msg){
	  	$cnt=array();
	  	$cnt['unsend']=0;
	  	$cnt['sendto']=0;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'count(idmsg)',
                'tx_auxnewsmailer_usrmsg',
                'state=0 and idmsg='.$msg,
                '',
                '',
				''
        );
		list($cnt['unsend']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
	  	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'count(idmsg)',
                'tx_auxnewsmailer_usrmsg',
                'state=2 and idmsg='.$msg,
                '',
                '',
				''
        );
		list($cnt['sendto']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

		return $cnt;
	}
	
	function getCatCount($uid){
	  	
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'count(uid_local)',
                'tt_news_cat_mm',
                'uid_local='.$uid,
                '',
                '',
				''
        );	  	
        list($cnt) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
        return $cnt;
	}

	/**
	 * Creates the preview message of unsend news items.
	 *
	 * @param	string		$type: plain message $type='plain'. html message $type='html'
	 * @return	string		the message.
	 */
	function renderPreview($type){
		$html='';
		$ctrl=$this->loadControl();
		if ($ctrl['uid']){		
		  	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	                'distinct uid',
	                'tt_news',
	                'deleted=0 and hidden=0 and tt_news.pid in '.$ctrl['pages'].' and tx_auxnewsmailer_scanstate<2 and starttime<'.time(),
	                '',
	                'uid',
					''
	        );
	
	
			$newslist='';
			if ($res){
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if ($newslist!='')
						$newslist.=',';
					$newslist.=$row['uid'];
				}
				$html=$this->createMsg(0,$newslist,$ctrl,$type);
			}
			else
				$html.='no news are ready ';
	
			$marker=array();
			$marker['###name###']='John Doe';
			$marker['###orgname###']=$ctrl['name'];
			$marker['###org###']=$ctrl['organisation'];
			$marker['###domain###']=$ctrl['orgdomain'];
	
			$html=$this->cObj->substituteMarkerArray($html,$marker);
	
			//$ctrl['html']=$html;
			//$html=$this->createHTMLMSG($ctrl,array());
		} else
			$html.='no news mail control record located in this folder';
		return $html;
	}


}





	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/mod1/index.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/mod1/index.php']);
	}
// Make instance:
	$SOBE = t3lib_div::makeInstance('tx_auxnewsmailer_module1');

	$SOBE->init();
	// Include files?
	foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

	$SOBE->main();
	$SOBE->printContent();


?>