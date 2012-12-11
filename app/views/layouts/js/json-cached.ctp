<?php

/**
 * JSON layout/view
 */
//Configure::write('debug', 0); 

// seconds, minutes, hours, days
$expires = 60*60*24*7;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
header('Content-Type: text/x-json');

echo $content_for_layout; 
