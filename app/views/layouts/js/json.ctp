<?php 

/**
 * JSON layout/view
 */

// Configure::write('debug', 0); 

header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: text/x-json');
 
echo $content_for_layout; 
