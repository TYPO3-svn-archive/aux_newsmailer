<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Tim Wentzlau (tim.wentzlau@auxilior.com)
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
 * Module News'letter for the 'aux_newsmailer' extension.
 *
 * @author	Tim Wentzlau <tim.wentzlau@auxilior.com>
 */
require_once (PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once (PATH_tslib.'class.tslib_content.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');

require_once('conf.php');
require_once(PATH_site.'typo3/init.php');
require_once(PATH_site.'typo3/template.php');

$GLOBALS['LANG']->includeLLFile('EXT:aux_newsmailer/mod1/locallang.xml');




/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   72: class tx_auxnewsmailer_core extends t3lib_SCbase
 *   86:     function loadControl($idctrl=0)
 *  128:     private function loadTSConfig($userPageId)
 *  165:     function batch($action = "", $idctrl = 0)
 *  196:     function mailList($idctrl)
 *  268:     function createMsg($uid,$newslist,$ctrl,$preview=false)
 *  355:     function formatHTML($ctrl,$news,&$resources)
 *  441:     function formatPlain($ctrl,$news)
 *  475:     private function html2plain($html)
 *  493:     function createNewsLetter($ctrl,$news,$type='html',&$resources)
 *  635:     function smsList()
 *  722:     function getUserInfo($uid)
 *  748:     function getMessageInfo($uid)
 *  797:     function scanNews($list, $idctrl)
 *  883:     function checkDuration($ctrl)
 *  929:     function sendMail($inbatch=false, $idctrl=0)
 * 1010:     function domail($email,$name,$subject,$message,$fromEMail,$fromName,$html='',$ctrl,$resources,$returnMail)
 * 1118:     function formatStr($str)
 * 1134:     function getImage($file,$width,$height,$preview=false)
 * 1200:     public function processCmdmap_postProcess($command, $tableName, $recordId)
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_auxnewsmailer_core extends t3lib_SCbase {
	var $pageinfo;
	var $cObj;
	var $inBatch=false;
	var $lConf = array();
	var $extPi1 = 'tx_auxnewsmailer_pi1';
	var $extKey = 'aux_newsmailer';

	/**
	 * Loads a newsletter control record
	 *
	 * @param	int		$idctrl: the uid of a newsletter control
	 * @return	array		array with newsletter control record settings
	 */
	function loadControl($idctrl=0) {

 		if (!$idctrl){
			 $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	                '*',
	                'tx_auxnewsmailer_control',
	                'pid='.intval($this->id),
	                '',
	                '',
					''
		    );
		}
		else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	                '*',
	                'tx_auxnewsmailer_control',
	                'uid='.intval($idctrl),
	                '',
	                '',
					''
		    );

		}

		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$pid=$row['folders'];
			if ($pid=='')
				$pid=$row['pid'];
			$pid='('.$pid.')';
			$row['pages']=$pid;

			return $row;
		}
		return array();
	}

	/**
	 * Loads TS config of the plugin
	 *
	 * @param	int		$pid: User profile page pid
	 * @return	array
	 */
	private function loadTSConfig($userPageId) {

		// import t3lib_page class if not already done (this should be a problem of TYPO3 4.2.x only)
		if (!class_exists('t3lib_pageSelect', FALSE)) {
			t3lib_div::requireOnce(PATH_t3lib."class.t3lib_page.php");
		}

		// Initialize the page selector
		$sysPage = t3lib_div::makeInstance('t3lib_pageSelect');
		$sysPage->init(true);

		// Initialize the TS template
		$template = t3lib_div::makeInstance('t3lib_TStemplate');
		$template->init();

		// Avoid an error
		$template->tt_track = 0;

		// Get rootline for current PID
		$rootline = $sysPage->getRootLine($userPageId);

		// Start TS template
		$template->start($rootline);

		// Generate TS config
		$template->generateConfig();

		return $template->setup['plugin.'][$this->extPi1 . '.'];
	}

	/**
	 * Executes the scan and mailing actions in cronjobs
	 *
	 * @param	string		$action: -s scans and creates messages. -m mails then next 50 mails pending.
	 * @param	int		$idctrl: unique id of a newsmailer control. (Opt). 0 for all controls.
	 * @return	void		outputs status to stdout.
	 */
	function batch($action = "", $idctrl = 0) {
		$msg = "Auxnewsmailer running in batch mode:\n--------\n";

	  	$this->cObj=t3lib_div::makeInstance("tslib_cObj");
		//$GLOBALS['TYPO3_DB']->debugOutput=true;

		$this->inBatch=true;
		if (($action=='')||($action=='-s')){
		  	$msg.= "Scanning news:\n";
	  		$msg.= '  added '.$this->scanNews('email',$idctrl)." messages\n";
	  	}
	  	if (($action=='')||($action=='-s')){
		  	$msg.="Create messages:\n";
	  		$msg.='  created: '.$this->mailList($idctrl);
	  	}
	  	if (($action=='')||($action=='-m')){
		  	$msg.="\nSend e-mails:\n";
	  		$msg.='  send: '.$this->sendMail(true, $idctrl);
	  	}

	  	$msg.= "\n-------\nBatch done\n";

		return $msg;
	}

	/**
	 * creates the messages that should be send by mail.
	 *
	 * @param	int		$idctrl: unique id of a newsmailer control. (Opt). 0 for all controls.
	 * @return	int		Number of messages created.
	 */
	function mailList($idctrl) {

		$where='hidden=0';
		if ($idctrl){
			$where.=' and uid='.intval($idctrl);
		}
		$cnt=0;
		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_auxnewsmailer_control',
			$where,
			'',
			'',
			''
			);

		while($ctrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {


			//$ctrl=$this->loadControl($idctrl);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_auxnewsmailer_maillist',
				'state=0 and msgtype=1 and idctrl='.intval($ctrl['uid']),
				'',
				'iduser',
				''
	           		);

			$cid=0;

			$newslist='';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

				if ($row['iduser']!=$cid){

					if ($cid!=0){
						$this->createMsg($cid,$newslist,$ctrl);
						$cnt++;

					}
					$newslist='';
					$cid=$row['iduser'];
				}
				if ($newslist=='')
					$newslist.=$row['idnews'];
				else
					$newslist.=','.$row['idnews'];

			}
			if ($newslist!=''){
				$this->createMsg($cid,$newslist,$ctrl);
				$cnt++;
			}
		

			$updateArray=array(
				'state'=>'2'
			);
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_auxnewsmailer_maillist','state=0 and msgtype=1 and idctrl=' . intval($ctrl['uid']) , $updateArray);

		}
		return $cnt;
	}

	/**
	 * Creates a message both plain version and html.
	 * If the message contains the same news items as a former mail the new one is discarded and the uid of the old message is used.
	 *
	 * @param	int		$uid: ...
	 * @param	string		$newslist: list with news ids that should go into the message
	 * @param	array		$ctrl: Newsletter control array
	 * @param	string		$preview: if ommited the new message is stored in the tx_auxnewsmailer table.
	 * @return	string		if $preview='plain' the plain version of the message is returned. preview='html' the html version is returned.
	 */
	function createMsg($uid,$newslist,$ctrl,$preview=false) {
		global $LANG;

		$LANG->init($ctrl['lang']);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid',
				'tx_auxnewsmailer_msglist',
				'msgsignature="'.md5($newslist).'"',
				'',
				'',
				''
		);

		if ((!$preview)&&($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
			$idmsg=$row['uid'];
		} else{
			$plain='';
			$html='';
			$resources=array();
			if ($newslist){

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tt_news',
						'tt_news.uid in ('.$newslist.')',
						'',
						'datetime',
						''
				);

				while($newsrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

					$plain .= $this->formatPlain($ctrl,$newsrow);
					$html .= $this->formatHTML($ctrl,$newsrow,$resources);
				}
			}

			$plain = $this->createNewsLetter($ctrl,$plain,'plain',$resources);
			$html = $this->createNewsLetter($ctrl,$html,'html',$resources);

			if (!$preview){
				$insertArray = array(
					'msgsignature'=>md5($newslist),
					'plaintext'=>$plain,
					'htmltext' =>$html,
					'idctrl'=>$ctrl['uid'],
					'resources'=>serialize($resources)
				);

				$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_auxnewsmailer_msglist', $insertArray);
				$idmsg=$GLOBALS['TYPO3_DB']->sql_insert_id();
			}
			else if ($preview=='html') {
				$marker=array();
				foreach ($resources as $i=>$res){
					$marker['###RES_'.$i.'###']=$res;
				}
				$html=$this->cObj->substituteMarkerArray($html,$marker);

			}
		}

		$insertArray = array(
			'iduser'=>intval($uid),
			'idmsg'=>intval($idmsg),
		);

		if (!$preview)
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_auxnewsmailer_usrmsg', $insertArray);

		if ($preview=='html')
			return $html;

		if ($preview=='plain')
			return $plain;

		return 0;
	}

	/**
	 * Formats a single news items for html
	 *
	 * @param	array		$ctrl: Newsletter control array.
	 * @param	array		$news: row from tt_news table.
	 * @param	array		$resources: image ressources to be embeded in mail.  absolute file paths.
	 * @return	string		newsitem html formmated.
	 */
	function formatHTML($ctrl,$news,&$resources){
		global $LANG;

/*
		if (t3lib_extMgm::isLoaded('tx_ttnews')) {
			require_once(t3lib_extMgm::extPath('tx_ttnews') . 'pi/class.tx_ttnews.php');

			$tx_ttnews = t3lib_div::makeInstance('tx_ttnews');
			$tx_ttnews->cObj = $this->cObj;
			$tx_ttnews->piVars['swords'] = $swords ;
			$tx_ttnews_config = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tt_news.'];
			$tx_ttnews_config['templateFile'] = 'fileadmin/templates/infolettre/tt_news_latest_infolettre.html';  #'EXT:customsearch/res/search_tt_news.tmpl';
			$tx_ttnews_config['code'] = 'LATEST';
			$tx_ttnews_config['code.'] = null;

			$newsList = $tx_ttnews->main_news('',$tx_ttnews_config);


			return $newsList;
		}
*/

		$image = '';
		$i = explode(',', $news['image']);
		if (count($i) > 0 )
			$image = $i[0];

			// Get news date & time
		$newsdate = strftime($ctrl['dateformat'], $news['datetime']);
		$newstime = strftime($ctrl['timeformat'], $news['datetime']);

		$showitems = $ctrl['showitems'];
		$result = '<div class="newsmailitem">';
		$result .= '	<div class="newsitemtext">';

			// draw image
		if (t3lib_div::inlist($showitems,'2')) {

			if ($image !== '') {

				$image=$this->getImage($image,$ctrl['listimagew'],$ctrl['listimageh']);
				if($image['url']) {

					$resources[] = $image['url'];
					$resID = count($resources)-1;
					$tag = str_replace('###URL###', '###RES_'.$resID.'###', $image['tag']);
				}

				$result.='	<div class="newsmailimage">'.$tag.'</div>';
			}
		}

		// draw date & time
		if (t3lib_div::inlist($showitems,'4') && t3lib_div::inlist($showitems,'5'))
			$result .= '		<div class="newsmaildate">'.$newsdate.' '.$newstime.'</div>';
		else {
			if (t3lib_div::inlist($showitems,'4'))
				$result .= '		<div class="newsmaildate">'.$newsdate.'</div>';
			if (t3lib_div::inlist($showitems,'5'))
				$result .= '		<div class="newsmaildate">'.$newstime.'</div>';
		}
			// draw abbreviation
		if (t3lib_div::inlist($showitems,'7'))
		$result .= '		<div class="newsmailshort">'.$news['short'].'</div>';

                        // draw title
                if (t3lib_div::inlist($showitems,'1'))
                        $result.='              <div class="newsmailtitle">'.$news['title'].'</div>';


			// draw body
		if (t3lib_div::inlist($showitems,'3'))
			$result .= '	<div class="newsmailbody">'.$this->formatStr($news['bodytext']).'</div>';


		if (t3lib_div::inlist($showitems,'6'))
			$result .= '	<div class="newsmaillink"><a href="http://'.$ctrl['orgdomain'].'/index.php?id='.$ctrl['newspage'].'&tx_ttnews[tt_news]='.$news['uid'].'">'.$LANG->getLL("readmore").'</a></div>';


		$result .= '	</div>';
		$result .= '<div class="ffclear"></div></div>';

		return $result;
	}

	/**
	 * Formats a news item in plain text
	 *
	 * @param	array		$ctrl: Newsletter control array
	 * @param	array		$news: row from tt_news table
	 * @return	string		news item in plain text.
	 */
	function formatPlain($ctrl,$news){
		global $LANG;

		$newsdate = strftime($ctrl['dateformat'], $news['datetime']);
		$newstime = strftime($ctrl['timeformat'], $news['datetime']);
		$showitems = $ctrl['showitems'];
		$result = '';

		if (t3lib_div::inlist($showitems,'1'))
			$result .= $news['title'] . "\n";
		if (t3lib_div::inlist($showitems,'4') && t3lib_div::inlist($showitems,'5'))
			$result .=  $newsdate . ' ' . $newstime . "\n";
		else {
			if (t3lib_div::inlist($showitems,'4'))
				$result .= $newsdate . "\n";
			if (t3lib_div::inlist($showitems,'5'))
				$result .= $newstime . "\n";
		}
		$result .= $news['short'] . "\n";
		if (t3lib_div::inlist($showitems,'3'))
			$result .= $this->formatStr($news['bodytext']) . "\n";
//		else
//			$result.='<a href="http://'.$ctrl['orgdomain'].'/index.php?id='.$ctrl['newspage'].'">'.$LANG->getLL("readmore").'</a></div>';
		$result .= "\n";

		return $result;
	}

    /**
 * Converts all HTML page breaks to \n and removes all HTML tags
 *
 * @param	string		$html: HTML text to convert
 * @return	string		Converted text
 */
	private function html2plain($html) {

		$plain = str_ireplace('<br>', "\n", $html);
		$plain = str_ireplace('<br />', "\n", $plain);
		$plain = strip_tags($plain);

		return $plain;
	}

	/**
	 * Creates the compleate newsletter
	 *
	 * @param	array		$ctrl: Newsletter control array
	 * @param	array		$news: String with the news items that should go into the message.
	 * @param	string		$type: the encoding of the newsletter can be 'plain' or 'html'
	 * @param	array		$resources: image ressources to be embeded in mail. Absolute file paths.
	 * @return	string		The compleate message
	 */
	function createNewsLetter($ctrl,$news,$type='html',&$resources) {
		global $LANG;

		$this->lConf = $this->loadTSConfig(intval($ctrl['userpage']));

		/*
		   Template loading order:
		   1. User Control
		   2. TypoScript Setup
		   3. Default extension template
		*/
		$file = PATH_site . 'uploads/tx_auxnewsmailer/' . $ctrl['template'];
		if (!file_exists($file) || (substr($file, -1) == '/') ) {

			$file = PATH_site . $this->lConf['templateFile'];
			if (!file_exists($file) || (substr($file, -1) == '/') ) {

				$file = t3lib_extMgm::extPath($this->extKey) . "res/template.tmpl";
			}
		}
		$templateCode = t3lib_div::getURL($file);

		$stylesheet = $ctrl['stylesheet'];
		if ($stylesheet) {
			$stylesheet = t3lib_div::getIndpEnv("TYPO3_SITE_URL") . 'uploads/tx_auxnewsmailer/' . $stylesheet;
		}
		else {
			$stylesheet = $this->lConf['templateCSS'];
			if ($stylesheet) {
				$stylesheet = t3lib_div::getIndpEnv("TYPO3_SITE_URL") . $stylesheet;
			}
			else {
				$stylesheet = t3lib_div::getIndpEnv("TYPO3_SITE_URL") . t3lib_extMgm::siteRelPath($this->extKey) .  'res/mail.css';
			}
		}
		$cssStyles = t3lib_div::getURL($stylesheet);

		if ($type=='html')
			$templateMarker = '###HTMLMAIL###';
		else
			$templateMarker = '###PLAINMAIL###';
		$template = $this->cObj->getSubpart($templateCode, $templateMarker);

		$html = '';
		$marker = array();
		$wrapped = array();

		$newsletterDate = strftime($ctrl['dateformat'], time());
		$newsletterTime = strftime($ctrl['timeformat'], time());
		$marker['###DATE###'] = $newsletterDate . ' ' . $newsletterTime;

		$image = $this->getImage($ctrl['image'],$ctrl['imagew'],$ctrl['imageh']);
		$resources[] = $image['url'];
		$resID = count($resources)-1;
		$marker['###IMAGE###'] = str_replace('###URL###','###RES_'.$resID.'###',$image['tag']);
		//$marker['###IMAGE###']=$this->getImage($ctrl['image'],$ctrl['imagew'],$ctrl['imageh'],$preview);

		if ($type == 'html'){
			$resources[] = $stylesheet;
			$resID = count($resources)-1;
			$marker['###CSS###'] = '###RES_'.$resID.'###';
		}

		$marker['###TITLE###'] = $ctrl['subject'];

		if ($type == 'html')
			$marker['###NEWSHEADER###'] = nl2br($ctrl['pretext']);
		else
			$marker['###NEWSHEADER###'] = $this->html2plain($ctrl['pretext']);

		if ($type == 'html')
			$marker['###NEWSFOOTER###'] = nl2br($ctrl['posttext']);
		else
			$marker['###NEWSFOOTER###'] = $this->html2plain($ctrl['posttext']);

		$marker['###PROFILEMESSAGE###'] = $LANG->getLL('signoff');

		if ($type == 'html')
			$marker['###PROFILELINK###'] = '<a  href="http://'.$ctrl['orgdomain'].'/index.php?id='.$ctrl['userpage'].'">'.$LANG->getLL('editprofile').'</a>';
			/*$marker['###PROFILELINK###'] = $this->cObj->typolink(
				$LANG->getLL('editprofile'),
				array(
					'parameter'	=> intval($ctrl['userpage']),
					'linkAccessRestrictedPages' => TRUE
				)
			);*/
		else
			$marker['###PROFILELINK###'] = 'http://'.$ctrl['orgdomain'].'/index.php?id='.$ctrl['userpage'];
			/*$marker['###PROFILELINK###'] = $this->cObj->typolink_url(
				'',
				array(
					'parameter'	=> intval($ctrl['userpage']),
					'linkAccessRestrictedPages' => TRUE
				)
			);*/

		if ($type == 'html')
			$marker['###NEWSLIST###'] = $news;
		else
			$marker['###NEWSLIST###'] = $this->html2plain($news);

		$html .= $this->cObj->substituteMarkerArray($template,$marker);

		if ($type === 'html' ) {

			/* older mail agents can't use css styles shees included as cid.  Need to add them inline*/
			//$html .= '<style>' . $cssStyles  . '</style>';

			/* include images in the custom template as ressources  */
			require_once(PATH_t3lib . 'class.t3lib_parsehtml.php');
			$htmlParse = t3lib_div::makeInstance('t3lib_parsehtml');

			$arrIMGTag = $htmlParse->splitTags("img", $html);

			t3lib_div::devLog("arrIMGTag","tx_auxnewsmailer", 0,$arrIMGTag);

			foreach  ($arrIMGTag as $k => $v ) {
				// array contains outer html in even #, wanted tags in odd #'s
				if ($k%2) {
					$imgTag= $htmlParse->get_tag_attributes($v, true);
					$imgSrc = t3lib_div::htmlspecialchars_decode($imgTag[0]['src']);

					//skip already mapped images from articlse -- starting by ###RES_
					if ( (strpos($imgSrc,"###RES_") === false) ) {
		 				t3lib_div::devLog("IMGTag","tx_auxnewsmailer", 0, $imgTag);
						$resources[]= $imgSrc;
		 				$resID= count($resources)-1;
						$html= str_replace( $imgSrc , '###RES_' .$resID . '###', $html);
					}
				}
			}
		}

	  	return $html;
	}


	/**
	 * Checks if there are unsend sms messages pending
	 *
	 * @return	void
	 */
	function smsList() {

		$sql='select * from tx_auxnewsmailer_maillist where state=0 and msgtype=2 order by idnews limit 0,50';
		$dbres = mysql(TYPO3_db,$sql) or $content .= 'Error Mysql:'.mysql_error().'<br>';

		$cid=0;

		$clearitems='';
		$xmllist=array();
		while($row = mysql_fetch_array($dbres)) {
			$author='';
			$plain='';
			$title='';
			if ($clearitems=='')
				$clearitems='uid='.$row['uid'];
			else
				$clearitems.=' or uid='.$row['uid'];

			if ($row['idnews']!=$cid)
			{
				$cid=$row['idnews'];
				$sql='select author,title,short,bodytext from tt_news where uid='.$cid;
				$dbnewsres = mysql(TYPO3_db,$sql) or $content .= 'Error Mysql:'.mysql_error().'<br>';
				$newsrow = mysql_fetch_array($dbnewsres);
				$author=
				$title=
				$short=$newsrow['short'];
				$body=$this->formatStr($this->local_cObj->stdWrap($newsrow['bodytext'], $lConf['content_stdWrap.']));
				$plain=$short."\n\r".$body."\n\r\n\r".$author;
				$xmllist[$cid]['author']=$newsrow['author'];
				$xmllist[$cid]['authormail']=$newsrow['author_email'];
				$xmllist[$cid]['title']=$newsrow['title'];
				$xmllist[$cid]['short']=$newsrow['short'];
				$xmllist[$cid]['body']=$newsrow['bodytext'];
				$xmllist[$cid]['phones']=array();
			}
			$userinfo=$this->getUserInfo($row['iduser']);
			if ($userinfo['phone'])
				$xmllist[$cid]['phones'][]=$userinfo['phone'];








		}

		$xml='<smslist>';
		for(reset($xmllist);$k=key($xmllist);next($xmllist)){
			$c=current($xmllist);
			$xml.='<smsitem>';
			$xml.='<author>'.$c['author'].'</author>';
			$xml.='<authormail>'.$c['authormail'].'</authormail>';
			$xml.='<title>'.$c['title'].'</title>';
			$xml.='<short>'.$c['short'].'</short>';
			$xml.='<body>'.$c['body'].'</body>';
			$xml.='<phones>';
			$phones=$c['phones'];
			reset($phones);
			while (list($p,$pc)=each($phones)){
				$xml.='<phone>'.$pc.'</phone>';
			}
			$xml.='</phones></smsitem>';
		}
		$xml.='</smslist>';

		$content.=$xml;

		//$content.=serialize($xmllist);
  		if ($clearitems!='')
		{
			$sql='update tx_auxnewsmailer_maillist set state=1 where '.$clearitems;
			//$content.=$sql.'<br>';
		}
		//$dbres = mysql(TYPO3_db,$sql) or $content .= 'Error Mysql:'.mysql_error().'<br>';
		$content.='list sendt';
		return $content;
	}

	/**
	 * Look up are FE user in the table fe_users
	 *
	 * @param	int		$uid: id of the user.
	 * @return	array		fe_users field values.
	 */
	function getUserInfo($uid) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'fe_users',
			'uid='.$uid,
			'',
			'',
			''
		);

		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$userinfo=array();
		$userinfo['name']=$row['name'];
		$userinfo['mail']=$row['email'];
		$userinfo['phone']=$row['telephone'];
		$userinfo['html']=$row['tx_auxnewsmailer_html'];
		return $userinfo;
	}

	/**
	 * Returns a message tx_auxnewsmailer_msglist
	 *
	 * @param	int		$uid: id of message
	 * @return	array		array with message details and control info
	 */
	function getMessageInfo($uid) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_auxnewsmailer_msglist,tx_auxnewsmailer_control',
				'tx_auxnewsmailer_msglist.uid='.$uid.' and tx_auxnewsmailer_control.uid=idctrl',
				'',
				'',
				''
			);

		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		$newsinfo=array();
		//$newsinfo['title']=$row['title'];
		$newsinfo['plain']=$row['plaintext'];
		$newsinfo['html']=$row['htmltext'];
		$newsinfo['resources']=$row['resources'];
		$newsinfo['template']=$row['template'];
		$newsinfo['stylesheet']=$row['stylesheet'];
		$newsinfo['organisation']=$row['organisation'];
		$newsinfo['orgdomain']=$row['orgdomain'];
		$newsinfo['name']=$row['name'];
		$newsinfo['subject']=$row['subject'];
		$newsinfo['returnmail']=$row['returnmail'];
	 	$newsinfo['sendermail']=$row['sendermail'];
		$newsinfo['feprofilepage']=$row['userpage'];
		$newsinfo['userpid']=$row['userpid'];
		$newsinfo['image']=$row['image'];
		$newsinfo['imagew']=$row['imagew'];
		$newsinfo['imageh']=$row['imageh'];
		$newsinfo['listimagew']=$row['listimagew'];
		$newsinfo['listimageh']=$row['listimageh'];
		$newsinfo['usecat']=$row['usecat'];
		$newsinfo['pretext']=$row['pretext'];
		$newsinfo['posttext']=$row['posttext'];
		$newsinfo['autoscan']=$row['autoscan'];
		$newsinfo['showitems']=$row['showitems'];

		return $newsinfo;
	}

	/**
	 * Scans the tt_news table for unsend news items.
	 *
	 * @param	string		$list: if $list='mail' the messages are prepared for mail based newsletters.  if $list='sms' the messages are prepared for sms based newsletters
	 * @param	int		$idctrl: uid of a certain newsletter control record. if $idctrl=0 all newsletter controls are scanned
	 * @return	int		Number of news items scanned
	 */
	function scanNews($list, $idctrl) {

		$msgType=1;
		$catfield='domail';

		if ($list=='sms'){
		  	$msgType=2;
		  	$catfield='dosms';
		}

		$where='hidden=0';
		if ($idctrl){
			$where.=' and uid='.$idctrl;
		}

		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                'tx_auxnewsmailer_control',
            	$where,
                '',
                '',
                ''
        );

		$updateArray=array(
			'tx_auxnewsmailer_scanstate'=>'1'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_news','tx_auxnewsmailer_scanstate=0', $updateArray);


		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
			if (!$this->checkDuration($row))
				return;

			$pid=$row['folders'];
			if ($pid=='')
				$pid=$row['pid'];
			$pid='('.$pid.')';
			$sql='insert into tx_auxnewsmailer_maillist (idnews,iduser,msgtype,idctrl)';
			if ($row['usecat']){
			  	//scan for news items that FE users scubscribe to directly thru tt_news cat
				$sql.=' SELECT distinct tt_news.uid, iduser ,'.$msgType.','.$row['uid'];
				$sql.=' FROM fe_users,tt_news,tt_news_cat_mm catmm, tx_auxnewsmailer_usercat usercat ';
				$sql.=' WHERE ';
				$sql.='usercat.iduser=fe_users.uid and ';
				$sql.='uid_foreign=mailcat and ';
				$sql.='tt_news.uid=catmm.uid_local and ';
				$sql.='catmm.uid_foreign>0  and ';
				$sql.=$catfield.'>0 and ';

			} else
			{
			 	//scan for news items that are not send and join with FE users

				$sql.='SELECT distinct tt_news.uid, fe_users.uid,'.$msgType.','.$row['uid'];
				$sql.=' FROM tt_news,fe_users';
				$sql.=' WHERE ';
			}

			$sql.='fe_users.pid='.$row['userpid'].' and ';
			$sql.='fe_users.tx_auxnewsmailer_newsletter=1 and ';
			$sql.='tt_news.pid in '.$pid.' and ';
			$sql.='tt_news.hidden=0 and ';
			$sql.='tt_news.deleted=0 and ';
			$sql.='tt_news.starttime<'.time().' and ';
			$sql.='tx_auxnewsmailer_scanstate=1';
			$sql.=' order by tt_news.uid';

			$res =$GLOBALS['TYPO3_DB']->sql_query($sql);
			$cnt=$GLOBALS['TYPO3_DB']->sql_affected_rows();
		 }
		$updateArray=array(
			'tx_auxnewsmailer_scanstate'=>'2'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_news','tx_auxnewsmailer_scanstate=1 AND ' .
									'tt_news.hidden = 0 AND tt_news.deleted = 0 AND '.
									'tt_news.starttime<' . time() . ' AND ' . 
								        'tt_news.pid in ' . $pid  , $updateArray);


		return $cnt;
	}

	/**
	 * check if a news control record are due for checking.
	 *
	 * @param	array		$ctrl: Newsletter control array
	 * @return	boolean		true if the newsletter control must be scanned
	 */
	function checkDuration($ctrl) {

	  	$res=false;

	  	if (!$this->inBatch)
	  		$res=true;
		else if ($ctrl['duration']){
			$weekday=date('w');
			$days=time()-$ctrl['lasttime'];
			$dayspan=$days/(60*60*24);

			$lastmonth=date('n',$ctrl['lasttime']);
			$lastyear=date('Y',$ctrl['lasttime']);
			$thismonth=date('n');
			$thisyear=date('Y');
			$monthspan=12-$lastmonth+($thisyear-$lastyear-1)*12+$thismonth;
			if (t3lib_div::inList($ctrl['duration'],'10'))
				//send as soon there is messages
				$res=true;
			if ((dayspan>0)&&(t3lib_div::inList($ctrl['duration'],$weekday)))
				//match day of week and only once each day
				$res=true;
			if (($monthspan>0)&&(t3lib_div::inList($ctrl['duration'],'9')))
				//match month checking only once each month
				$res=true;
			if (($dayspan>14)&&(t3lib_div::inList($ctrl['duration'],'8')))
				//match every 14th day
				$res=true;
		}
		if ($res){
			$updateArray=array(
				'lasttime'=>time()
			);
			$dbres = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_auxnewsmailer_control','uid='.$ctrl['uid'], $updateArray);
		}

	  	return $res;
	}

	/**
	 * Sends the next 50 e-mails that are pending.
	 *
	 * @param	boolean		$inbatch: if true the function is called by the cron job
	 * @param	int		$idctrl: unique id of a newsmailer control. (Opt). 0 for all controls.
	 * @return	int		number of mails send.
	 */
	function sendMail($inbatch=false, $idctrl=0) {
		if (!$inbatch){
			$ctrl=$this->loadControl();
			$wctrl='and tx_auxnewsmailer_msglist.idctrl='.$ctrl['uid'];
		}
		else{
			$ctrl=$this->loadControl($idctrl);
                        $wctrl='and tx_auxnewsmailer_msglist.idctrl='.$ctrl['uid'];

		}

		$limit=50;

        $dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'tx_auxnewsmailer_usrmsg.*',
                'tx_auxnewsmailer_usrmsg,tx_auxnewsmailer_msglist, fe_users',
            	'tx_auxnewsmailer_usrmsg.state=0 and tx_auxnewsmailer_usrmsg.iduser=fe_users.uid and fe_users.disable = 0 and fe_users.deleted = 0 and tx_auxnewsmailer_usrmsg.idmsg=tx_auxnewsmailer_msglist.uid '.$wctrl,
                '',
                'idmsg',
                '0,'.$limit
        );
		$cnt=0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
			$cnt++;
	  		$userinfo=$this->getUserInfo($row['iduser']);
			$msg=$this->getMessageInfo($row['idmsg']);

			$title=$msg['subject'];
			$fromEmail=$msg['sendermail'];
			$returnMail=$msg['returnmail'];
			$fromName=$msg['organisation'];
			if ($fromName!='')
				$fromName.='-';
			$fromName.=$msg['name'];

			$marker=array();
			$marker['###name###']=$userinfo['name'];
			$marker['###orgname###']=$msg['name'];
			$marker['###org###']=$msg['organisation'];
			$marker['###domain###']=$msg['orgdomain'];

			$resources=unserialize($msg['resources']);


			$plain=$this->cObj->substituteMarkerArray($msg['plain'],$marker);
			$title=$this->cObj->substituteMarkerArray($title,$marker);

			if ($userinfo['html'])
				$html=$this->cObj->substituteMarkerArray($msg['html'],$marker);
			else
				$html='';
			$this->domail($userinfo['mail'],$userinfo['name'],$title,$plain,$fromEmail,$fromName,$html,$msg,$resources,$returnMail);
			$content.='----------------------</br>';
			$content.=$userinfo['mail'].'</br>';
			$content.=$msg['plain'].'</br>';
			//echo($content);
			$updateArray=array(
				'state'=>'2'
			);
			$ures = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_auxnewsmailer_usrmsg','idmsg='.$row['idmsg'].' and iduser='.$row['iduser'], $updateArray);
		}

		return $cnt;

	}

	/**
	 * Sends a mail
	 *
	 * @param	string		$email: e-mail address
	 * @param	string		$name:  name of reciever
	 * @param	string		$subject: subject of the message
	 * @param	string		$message: the plain version of the message
	 * @param	string		$fromEMail: sender e-mail
	 * @param	string		$fromName: sender name
	 * @param	string		$html: html version of the mail
	 * @param	array		$ctrl: nesmailer control record
	 * @param	array		$ressources: array of file ressources
	 * @param	string		$returnMail: return path email for message displayed notifications. (required for swiftmailer and RFC 2822 3.6.2
	 * @return	void
	 */
	function domail($email,$name,$subject,$message,$fromEMail,$fromName,$html='',$ctrl,$resources,$returnMail) {

		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		if ($mail){
			$mail->setFrom(array($fromEMail => $fromName));
			$mail->setTo(array($email => $name));
			$mail->setSubject($subject);
			$mail->setReturnPath($returnMail);

			if ($html){
				/*$stylesheetFile=$ctrl['stylesheet'];
				if (!$stylesheetFile)
					$stylesheetFile='../res/mail.css';

				$stylesheet=file_get_contents($stylesheetFile);
				$cidStylesheet = $mail->embed(Swift_Image::newInstance($stylesheet, 'mail.css', 'text/css'));
				$marker=array();
				$marker['###CSS###']=$cidStylesheet;
				$marker['###IMAGE###']='';*/

				//$i=0;
				foreach ($resources as $i=>$res){
					if ($res) {
						$resData=file_get_contents($res);
						$info = pathinfo($res);
						$cidRes=$mail->embed(Swift_Image::newInstance($resData, $info['filename'].'.'.$info['extension'], 'text/css'));
						$marker['###RES_'.$i.'###']=$cidRes;
					//$i++;
					}
					else{
						$marker['###RES_'.$i.'###']="";
					}
				}

				/*if ($ctrl['image']){
					$image=$this->getImage($ctrl['image'],$ctrl['imagew'],$ctrl['imageh']);
					$imageData=file_get_contents($image['url']);
					$info = pathinfo($image['url']);
					$cidImage=$mail->embed(Swift_Image::newInstance($imageData, $info['filename'].'.'.$info['extension'], 'text/css'));
					$tag=str_replace('###URL###',$cidImage,$image['tag']);
					$marker['###IMAGE###']=$tag;
				}*/


		//print_r($cid);

		//print_r($stylesheet);
				$html=$this->cObj->substituteMarkerArray($html,$marker);

				$mail->setBody($html, 'text/html');
				$mail->addPart($message, 'text/plain');


				//$mail->addPart($stylesheet, 'text/css');
			} else {
				$mail->setBody($message);
			}
			$mail->send();
		} else {
	 		// swiftmailer not available?
			require_once (PATH_t3lib.'class.t3lib_htmlmail.php');

			$cls=t3lib_div::makeInstanceClassName('t3lib_htmlmail');

			if (class_exists($cls))
			{

				$Typo3_htmlmail = t3lib_div::makeInstance('t3lib_htmlmail');
				$Typo3_htmlmail->start();
				//$Typo3_htmlmail->useBase64();

				$Typo3_htmlmail->subject = $subject;
				$Typo3_htmlmail->from_email = $fromEMail;
				$Typo3_htmlmail->from_name = $fromName;
				$Typo3_htmlmail->replyto_email = $Typo3_htmlmail->from_email;
				$Typo3_htmlmail->replyto_name = $Typo3_htmlmail->from_name;
				$Typo3_htmlmail->organisation = '';
				$Typo3_htmlmail->priority = 3;

				$Typo3_htmlmail->addPlain($message);
				if (trim($html)) {
					$Typo3_htmlmail->theParts['html']['content'] = $html;
					$Typo3_htmlmail->theParts['html']['path'] = '';
					$Typo3_htmlmail->extractMediaLinks();
					$Typo3_htmlmail->extractHyperLinks();
					$Typo3_htmlmail->fetchHTMLMedia();
					$Typo3_htmlmail->substMediaNamesInHTML(0); // 0 = relative
					$Typo3_htmlmail->substHREFsInHTML();
					$Typo3_htmlmail->setHTML($Typo3_htmlmail->encodeMsg($Typo3_htmlmail->theParts['html']['content']));
				}


				$Typo3_htmlmail->setHeaders();
				$Typo3_htmlmail->setContent();
				$Typo3_htmlmail->setRecipient(explode(',', $email));
				$Typo3_htmlmail->sendtheMail();
			}
		}
	}



	/**
	 * Format string with general_stdWrap from configuration
	 *
	 * @param	string		$string to wrap
	 * @return	string		wrapped string
	 */
	function formatStr($str) {
		if (is_array($this->conf['general_stdWrap.'])) {
			$str = $this->local_cObj->stdWrap($str, $this->conf['general_stdWrap.']);
		}
		return $str;
	}

	/**
	 * creates an image tag imagemagic for sizing the image
	 *
	 * @param	string		$file: file name of image file in /uploads/pic
	 * @param	int		$height: height of the image
	 * @param	int		$width: width of the image
	 * @param	boolean		$preview: boolean indicates if current mode is preview or not
	 * @return	string		Fully qualyfied image tag
	 */
	function getImage($file,$width,$height,$preview=false) {
		// overwrite image sizes from TS with the values from the content-element if they exist.
		if ($file=='')
			return $file;


		//$theImgCode = '';
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
		$imgObj->absPrefix = PATH_site;
		$uploadfolder=PATH_site.'/uploads/pics/'.$file;

        if (!@is_file($uploadfolder))        die('Error: '.$uploadfolder.' was not a file');

		//$imgObj->dontCheckForExistingTempFile=true; //should be true only for debugging
		$imgInfo = $imgObj->imageMagickConvert($uploadfolder,'jpg',$width.' m',$height.' m','','',1);

		//$url='../../../../'.substr($imgInfo[3],strlen(PATH_site));
		$url=$imgInfo[3];

		$lConf['image.']['file'] =$url;
		$lConf['image.']['altText'] = '';
		$lConf['image.']['titleText'] = '';


		//$theImgCode .= $this->local_cObj->IMAGE($lConf['image.']);
		if ($preview){
			$theImgCode= '<img src="'. $url .'" border="0"/>';
			return $theImgCode;
		} else{
			$theImgCode=array();
			$theImgCode['tag']= '<img src="###URL###" style="height:'.$imgInfo[0].' px" border="0"/>';
			$theImgCode['url']=$url;
			return $theImgCode;
		}





	}

	 /**
	 * A TCEMain hook to delete subscription when a fe_user is deleted
	 * language overlay.
	 *
	 * @param	string		$command
	 * @param	string		$table
	 * @param	int		$id
	 * @param	mixed		$value
	 * @return	void
	 */
        public function processCmdmap_postProcess($command, $tableName, $recordId) {

                if ($command == 'delete' && $tableName = 'fe_users' && $recordId > 0 ){
                        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_auxnewsmailer_usercat', 'iduser=' . intval($recordId) );
                        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_auxnewsmailer_usermsg', 'iduser=' . intval($recordId) );
                        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_auxnewsmailer_usercat', 'iduser=' . intval($recordId) );
                        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_auxnewsmailer_maillist','iduser=' . intval($recordId) );

                }

        }



}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/mod1/class_auxnewsmailer_core.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/mod1/class_auxnewsmailer_core.php']);
	}

?>
