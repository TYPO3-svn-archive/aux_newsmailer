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
 * Plugin 'Mail News settings' for the 'aux_newsmailer' extension.
 *
 * @author	Tim Wentzlau <tim.wentzlau@auxilior.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once (PATH_t3lib."class.t3lib_htmlmail.php");


class tx_auxnewsmailer_pi1 extends tslib_pibase {
	var $prefixId = "tx_auxnewsmailer_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_auxnewsmailer_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "aux_newsmailer";	// The extension key.
	var $lConf=array();
	var $pidQuery='';

	/**
	 * main function called by T3 fe render engine
	 *
	 * @param	array		$content: ...
	 * @param	array		$conf: ...
	 * @return	string		The plugin result that shuld be included on the page.
	 */
	function main($content,$conf)	{
		echo('a');
		$this->init($conf);
		$GLOBALS["TSFE"]->set_no_cache();
		//$GLOBALS['TYPO3_DB']->debugOutput=true;
		$this->Template = $this->cObj->fileResource($this->conf['templateFile']);
		if (!$GLOBALS['TSFE']->fe_user->user['uid']){
				$content="no fe user logged in";
				return $this->pi_wrapInBaseClass($content);
		}


		$command = t3lib_div::_GET('command');

		if(isset($_POST['command']))
			$command =t3lib_div::_POST('command');

		switch($command) {
			case 'Send':
			case 'submit':
				$content=$this->submitusercat();
			break;
			default:
					$content=$this->listusercat();
		}



		return $this->pi_wrapInBaseClass($content);

	}

	/**
	 * Initializes the fe plugin
	 *
	 * @param	array		$conf: ...
	 * @return	void		...
	 */
	function init($conf){
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->conf = $conf;
		$this->pi_loadLL();
		$this->pi_setPiVarDefaults();

		$this->pi_initPIflexForm();

		$piFlexForm = $this->cObj->data['pi_flexform'];
		foreach ( $piFlexForm['data'] as $sheet => $data )
			foreach ( $data as $lang => $value )
   				foreach ( $value as $key => $val )
    				$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);

		$this->initPidList();
	}



	/**
	 * Creates a form with availeble news categories to subscribe to whith the state set according to the current fe users selections
	 *
	 * @return	string		the form.
	 */

	function listusercat() {



		$content.='';
		$content.='<form enctype="multipart/form-data" name="auxnewsmailer_form" method="POST">';
		$content.='<input type="hidden" name="command" value="submit">';

		if ($GLOBALS['TSFE']->fe_user->user['tx_auxnewsmailer_newsletter'])
			$content.='<p><input type="checkbox" name="newsletter" value="0" checked>'.$this->pi_getLL('usenewsletters').'</p>';
		else
			$content.='<p><input type="checkbox" name="newsletter" value="1">'.$this->pi_getLL('usenewsletters').'</p>';

		if ($GLOBALS['TSFE']->fe_user->user['tx_auxnewsmailer_html'])
			$content.='<p><input type="checkbox" name="html" value="0" checked>'.$this->pi_getLL('usehtml').'</p>';
		else
			$content.='<p><input type="checkbox" name="html" value="1">'.$this->pi_getLL('usehtml').'</p>';


		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                'tx_auxnewsmailer_usercat',
                'pid='.$this->lConf['userPID'].' and iduser='.$GLOBALS['TSFE']->fe_user->user['uid'].' AND deleted=0',
                '',
                '',
                ''
            );

		if ($this->lConf['usecat']){
			$usercat=array();
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)){

				$catid=$row['mailcat'];
				$usercat[$catid]['mail']=$row['domail'];
				$usercat[$catid]['sms']=$row['dosms'];
			}


			$content.='<h2>'.$this->pi_getLL('cat').'</h2>';
			$content.=$this->buildCatTree(0,$this->pidQuery,$usercat);
		}
		$content.='<p><input type="submit" value="'.$this->pi_getLL('submit').'"></p>';
		$content.='</form>';
		return ($content);


	}

	/**
	 * Builds the tree of tt_news categories and applies the fe users subscription
	 *
	 * @param	int		$idparent: parent uid
	 * @param	string	$pages: pages to look up tt_news categories
	 * @param	array	$usercat: array with the users subscriptions.
	 * @return	string	html representation of the tree.
	 */
	function buildCatTree($idparent,$pages,$usercat){
		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                'tt_news_cat',
                $pages.'and parent_category='.$idparent.' AND deleted=0',
                '',
                'title',
                ''
        );
        $imgpath=t3lib_div::getFileAbsFileName('EXT:aux_newsmailer/res/mobile_phone_16.gif',1,0);
        $imgpath=substr($imgpath,strlen(PATH_site));
   		$imgphone='<img border=0 src="'.$imgpath.'"/>';

        $imgpath=t3lib_div::getFileAbsFileName('EXT:aux_newsmailer/res/mail-reply-sender.png',1,0);
        $imgpath=substr($imgpath,strlen(PATH_site));
   		$imgmail='<img border=0 src="'.$imgpath.'"/>';


        $tree='';
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
        	$sub=$this->buildCatTree($row['uid'],$pages,$usercat);
			if ($sub)
				$tree.='<LI class="newscatlistparent">';
			else
				$tree.='<LI class="newscatlistchild">';
			$tree.=$row['title'];
			if (!$sub){
				if ($usercat[$row[uid]]['mail']=="1")
					$tree.='<br>'.$imgmail.'<input type="checkbox" name="'.$row['uid'].'-mail" value="mail" checked>';
				else
					$tree.='<br>'.$imgmail.'<input type="checkbox" name="'.$row['uid'].'-mail" value="mail">';
				if ($usercat[$row['uid']]['sms']=="1")
					$tree.=$imgphone.'<input type="checkbox" name="'.$row['uid'].'-sms"  value="sms" checked>';
				else
					$tree.=$imgphone.'<input type="checkbox" name="'.$row['uid'].'-sms"  value="sms">';
			}
			$tree.='</br>'.t3lib_div::formatForTextArea($row['description']).$sub.'</LI>';

        }
        if ($tree)
        	$tree='<UL>'.$tree.'</UL>';
        return $tree;


	}

	/**
	 * updates the fe users subscription settings
	 *
	 * @return	string	html representation of the tree.
	 */

	function submitusercat(){
		$content='';
		//Clear the users previous selections, if not dublicates will appear
		$sql="delete from tx_auxnewsmailer_usercat WHERE pid=".$this->lConf['userPID']." and iduser=".$GLOBALS['TSFE']->fe_user->user['uid'];

		//$dbres = mysql(TYPO3_db,$sql) or $content .= "Error Mysql:".mysql_error()."<br>";
		$dbres = mysql_query($sql,TYPO3_db) or $content .= "Error Mysql:".mysql_error()."<br>";
		$newsletter=0;
		$html=0;

		$cmd=array();

		for(reset($_POST);$k=key($_POST);next($_POST)){
			if ($k=='newsletter'){
				$newsletter=1;
			}
			if ($k=='html'){
			 	$html=1;
			}
			if (($k!='html')&&($k!='newsletter')&&($k!='command')&&($k!=$this->pi_getLL('submit')))
			{
				$cat=explode('-',$k);
				$cmd[$cat[0]][$cat[1]]='1';
			}

		}

		$updateArray=array(
			'tx_auxnewsmailer_newsletter'=>$newsletter,
			'tx_auxnewsmailer_html'=>$html,
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid='.$GLOBALS['TSFE']->fe_user->user['uid'], $updateArray);

		$GLOBALS['TSFE']->fe_user->user['tx_auxnewsmailer_newsletter']=$newsletter;
		$GLOBALS['TSFE']->fe_user->user['tx_auxnewsmailer_html']=$html;

		for(reset($cmd);$k=key($cmd);next($cmd)){

			$v=current($cmd);
			$mail=0;
			$sms=0;
			if ($v['mail']) $mail=1;
			if ($v['sms']) $sms=1;
			$this->setUserCat($k,$GLOBALS['TSFE']->fe_user->user['uid'],$mail,$sms);

			/*$sql='insert into tx_auxnewsmailer_usercat (pid,iduser,mailcat,domail,dosms) ';
			$sql.="values (".$this->lConf['userPID'].",".$GLOBALS['TSFE']->fe_user->user['uid'].",".$k.",".$mail.",".$sms.")";
			$sql.="";

			$dbres = mysql(TYPO3_db,$sql) or $content .= "Error Mysql:".mysql_error()."<br>";
			$this->setParents($k,$mail,$sms);*/
		}

		$content.=$this->listusercat();
		return ($content);

	}

	/**
	 * Look up a fe users subscription to a certain tt_news category
	 *
	 * @param	int		$idcat: id of category to check
	 * @param	int		$iduser: id of user
	 * @return	array	array with the users subscription
	 */
	function getUserCat($idcat,$iduser){
		$usercat=array();
		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'tx_auxnewsmailer_usercat.uid,domail,dosms,parent_category',
                'tx_auxnewsmailer_usercat,tt_news_cat',
                'tt_news_cat.uid=mailcat and iduser='.$iduser.' and mailcat='.$idcat,
                '',
                '',
                '' );
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)){
		  	$usercat['uid']=$row['uid'];
		  	$usercat['domail']=$row['domail'];
		  	$usercat['dosms']=$row['dosms'];
		  	$usercat['parent']=$row['parent_category'];
		}
		return $usercat;

	}

	/**
	 * Updates a fe users  category subscription including category parents
	 *
	 * @param	int		$idcat: id of tt_news category
	 * @param	int		$iduser: id of user
	 * @param	int		$mail: mail subscription
	 * @param	int		$sms: sms subscription
	 * @return	void		...
	 */
	function setUserCat($idcat,$iduser,$mail,$sms)	{
		$usercat=$this->getUserCat($idcat,$iduser,$mail);
		$domail=0;
		$dosms=0;
		if (sizeof($usercat)){
		  	$domail=$usercat['domail'];
		  	$dosms=$usercat['dosms'];
		  	if (($usercat['domail']==0))
		  		$domail=$mail;
		  	else if (($mail==1)&&($usercat['domail']==2))
		  		$domail=1;

		  	if (($usercat['dosms']==0))
		  		$dosms=$sms;
		  	else if (($sms==1)&&($usercat['dosms']==2))
		  		$dosms=1;

		  	$updateArray=array(
				'domail'=>intval($domail),
				'dosms'=>intval($dosms),
			);
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_auxnewsmailer_usercat','uid='.$usercat['uid'], $updateArray);
			if ($usercat['parent']){
			  	if	($domail==1)
			  		$domail=2;
			  	if	($dosms==1)
			  		$dosms=2;
			  	$this->setUserCat($usercat['parent'],$idUser,$domail,$dosms);
			}

		} else{
			$insertArray = array(
			   	'iduser'=>intval($iduser),
			   	'mailcat'=>intval($idcat),
				'domail'=>intval($mail),
	   			'dosms' =>intval($sms),
	   			'pid'=>intval($this->lConf['userPID']),
			);
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_auxnewsmailer_usercat', $insertArray);
			$parent=$this->getParentCat($idcat);

			if ($parent){
			  	$domail=0;
			  	$dosms=0;
			  	if ($mail)
			  		$domail=2;
			  	if ($sms)
			  		$dosms=2;

				$this->setUserCat($parent,$iduser,$domail,$dosms);
			}
		}


	}

	/**
	 * Returns a tt_news categorys parent
	 *
	 * @param	int		$idcat: id of category
	 * @return	int		id of parent
	 */
	function getParentCat($idcat){
		$r=0;
		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'parent_category',
                'tt_news_cat',
                'tt_news_cat.uid='.$idcat,
                '',
                '',
                '' );
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)){
		  	$r=$row['parent_category'];
		}
		return $r;

	}

	/**
	 * Initializes the pid list to lookup tt_news categories.
	 *
	 * @return	[type]		...
	 */
	function initPidList () {
		// pid_list is the pid/list of pids from where to fetch the news items.
		$pid_list = $this->lConf['storagePID'];
		$pid_list = $pid_list?$pid_list:1;

		$recursive = $this->lConf['recursive'];
		$recursive = is_numeric($recursive)?$recursive:false;
		// extend the pid_list by recursive levels
		$this->pid_list = $this->pi_getPidList($pid_list, $recursive);
		$this->pid_list = $this->pid_list?$this->pid_list:'';
		if ($this->pid_list!='')
			$this->pidQuery='pid IN (' . $this->pid_list . ')';
		else
			$this->pidQuery='';



	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$file: ...
	 * @param	[type]		$height: ...
	 * @param	[type]		$width: ...
	 * @return	[type]		...
	 */
	function getImage($file,$height,$width) {
		// overwrite image sizes from TS with the values from the content-element if they exist.
		if ($file=='')
			return $file;


		$theImgCode = '';
		//$imgs = t3lib_div::trimExplode(',', $row['image'], 1);
		//$imgsCaptions = explode(chr(10), $row['imagecaption']);
		//$imgsAltTexts = explode(chr(10), $row['imagealttext']);
		//$imgsTitleTexts = explode(chr(10), $row['imagetitletext']);

		//reset($imgs);
		$lConf=array();

		$lConf['image.']['file.']['maxW']= $width;
		$lConf['image.']['file.']['maxH']= $height;



		$imgObj = t3lib_div::makeInstance('t3lib_stdGraphic'); // instantiate object for image manipulation
		$imgObj->init();
		//$imgObj->mayScaleUp = 1;
		//$imgObj->absPrefix = PATH_site;
		$uploadfolder='uploads/pics/';

		$imgInfo = $imgObj->imageMagickConvert($uploadfolder.$file,'jpg',$width.' m',$height.' m','','',1);


		$lConf['image.']['file'] = $imgInfo[3];
		$lConf['image.']['altText'] = '';
		$lConf['image.']['titleText'] = '';


		//$theImgCode .= $this->local_cObj->IMAGE($lConf['image.']);
		$theImgCode.= '<img src="'. $imgInfo[3] .'" border="0"/>';



		return $theImgCode;
	}

}




if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/aux_newsmailer/pi1/class.tx_auxnewsmailer_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/aux_newsmailer/pi1/class.tx_auxnewsmailer_pi1.php"]);
}
