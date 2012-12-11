<?php 

/**
 * XML Default Layout 
 */

// Configure::write('debug', 2);
// fb('XML view!');

header("Content-Type: application/xml; charset=".Configure::read('App.encoding'));
echo $xml->header(); 
echo $content_for_layout; 