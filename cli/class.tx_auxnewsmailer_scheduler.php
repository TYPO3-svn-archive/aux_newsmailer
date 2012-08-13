<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 aux_newsmailer development team (details on http://forge.typo3.org/projects/show/extension-aux_newsmailer)
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
 * Plugin 'tx_auxnewsmailer' for the 'aux_newsmailer' extension.
 *
 * @author	Tim Wentzlau <tim.wentzlau@auxilior.com>
 * @author 	Jean-Sebastien Gervais <jsgervais@hotmail.com>   (scheduler)
 * @package	TYPO3
 * @subpackage	tx_auxnewsmailer_scheduler
 */

require_once('conf.php');
require_once(PATH_site.'/typo3conf/ext/aux_newsmailer/mod1/class_auxnewsmailer_core.php');


class tx_auxnewsmailer_scheduler extends tx_scheduler_Task {
	
	public $lang;
	public $msg;

	
	/**
	* Function executed from the Scheduler.
	*
	* @return    bool
	*/
	public function execute() {
		

		// Defining circumstances for CLI mode:
		define('TYPO3_cliMode', TRUE);

		$mailer=new tx_auxnewsmailer_core;
		$mailer->init();
	
		$this->msg = $mailer->batch('',$this->idctrl);


		return true;
	}
	
	/**
	* Return message for backend
	*
	* @return   string
	*/
	public function getAdditionalInformation() {
		return $this->msg;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/cli/class.tx_auxnewsmailer_scheduler.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/cli/class.tx_auxnewsmailer_scheduler.php']);
}
?>
