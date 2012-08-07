<?php

/**
 * Rss channel View
 * @author Martin Bucko (bucko@treecom.net)
 */

header("Content-Type: application/xml; charset=".Configure::read('App.encoding'));

// Configure::write('debug', 2);
// fb('RSS view!');

echo $rss->header();
 
if (!isset($channel)) {
	$channel = array();
}
if (!isset($document)){
	$document = array(
		'xmlns:content'=>"http://purl.org/rss/1.0/modules/content/",
 		'xmlns:dc'=>"http://purl.org/dc/elements/1.1/",
		'xmlns:sy'=>"http://purl.org/rss/1.0/modules/syndication/",		
		'xmlns:rdf'=>"http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	); 
}
if (!isset($channel['title'])) {
	$channel['title'] = $title_for_layout;
}
if (!isset($channel['description'])) {
	$channel['description'] = $meta_description;
}
if (!isset($channel['pubDate'])) {
	$channel['pubDate'] = $rss->time(time());
}
if (!isset($channel['language'])) {
	$channel['language'] = Configure::read('Domain.language');
}
if (!isset($channel['link'])) {
	$channel['link'] = $rss->url();
}
echo $rss->document($document, 
	$rss->channel(
		array(), $channel, $content_for_layout
	)
);
