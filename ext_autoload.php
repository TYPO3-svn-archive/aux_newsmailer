<?php
/*
 * Register necessary class names with autoloader
 */
$auxnewsmailerExtPath = t3lib_extMgm::extPath('aux_newsmailer');

$arr = array ( 
	'tx_auxnewsmailer_scheduler' 		=> $auxnewsmailerExtPath . 'cli/class.tx_auxnewsmailer_scheduler.php',
	'tx_auxnewsmailer_scheduler_addFields' 	=> $auxnewsmailerExtPath . 'cli/class.tx_auxnewsmailer_scheduler_addFields.php'
);

return $arr;
?>
