<?php
class tx_auxnewsmailer_static{
  
  	function getLang(&$params,&$pObj){

		$ls = explode('|',TYPO3_languages);
		while(list($i,$v)=each($ls)) {   
			$params['items'][]=array($v,$v);
	  	}
	}
  
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/class_tx_auxnewsmailer_static.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/class_tx_auxnewsmailer_static.php']);
	}

?>