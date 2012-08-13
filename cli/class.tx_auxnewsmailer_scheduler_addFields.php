<?php 
/****************************************************************
*  Copyright notice
*
*  (c) 2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
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
 
// stop implementation in frontend (only for backend)
if (!interface_exists(tx_scheduler_AdditionalFieldProvider)) {
	return;
}

/**
 * Plugin 'tx_auxnewsmailer' for the 'aux_newsmailer' extension.
 *
 * @author	Jean-Sebastien Gervais <jsgervais@hotmail.com>
 * @package	TYPO3
 * @subpackage	tx_powermail_scheduler_addFields
 */

class tx_auxnewsmailer_scheduler_addFields implements tx_scheduler_AdditionalFieldProvider {
   
	/**
	* Add additional fields to the scheduler
	*
	* @return    array
	*/
    public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
    	$additionalFields = array(); 
		
        if (empty($taskInfo['idctrl'])) {
            if ($parentObject->CMD == 'edit') {
                $taskInfo['idctrl'] = $task->idctrl;
            } else {
                $taskInfo['idctrl'] = '';
            }
        }
		
        // Write the code for the pid field
        $fieldID = 'task_idctrl';
        $fieldCode = '<input type="text" name="tx_scheduler[idctrl]" id="' . $fieldID . '" value="' . intval($taskInfo['idctrl']) . '" />';
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'Newsmailers uid'
        );

        return $additionalFields;
    }

	/**
	* Validate user values
	*
	* @return    bool
	*/
    public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
        $blReturn = false;
	$submittedData['idctrl'] = intval($submittedData['idctrl']); // should be integer

	if ( intval($submittedData['idctrl']) > 0 ) 
	    $blReturn = true;

	return $blReturn;
    }
   
	/**
	* make values available in scheduler object
	*
	* @return    void
	*/
    public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
        $task->idctrl = $submittedData['idctrl'];
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/cli/class.tx_aux_newsmailer_scheduler_addFields.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aux_newsmailer/cli/class.tx_auxnewsmailer_scheduler_addFields.php']);
}
?>
