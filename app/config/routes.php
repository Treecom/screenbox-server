<?php

/**
 * Screenboc server routing
 * ------------------------
 */

/**
 * Web app basic route
 * Example: http://www.domain.com/
 */
Router::connect('/', array('controller' => 'screenbox','action'=>'index'));


/**
 * Context languages routes
 * Example: http://www.domain.com/eng/*
 * Notice: maybe slow on many request and many languages!
 */
$route_languages = Configure::read('Domain.availableLanguages');

if (is_array($route_languages) && !empty($route_languages)){		
	foreach ($route_languages as $key){
		Router::connect('/'.$key.'/*', array('controller' => 'screenbox','action' => 'index','lang' => $key));
	}	
	unset($route_languages);
}  

/**
 * Server route for all others not routed paths
 * Example: http://www.domain.com/ 
 */

Router::connect('/login/*', array('controller' => 'screenbox', 'action' => 'login'));
Router::connect('/logout/*', array('controller' => 'screenbox', 'action' => 'logout'));
Router::connect('/sitemap.xml', array('controller' => 'screenbox', 'action' => 'sitemap', 'url' => array('ext' => 'xml')));
Router::connect('/user-activation/*', array('controller' => 'screenbox', 'action' => 'user_activation'));
Router::connect('/screenbox/:action', array('controller' => 'screenbox', 'action' => 'index'));

  
/**
 * Deafult route
 */
Router::connect('/:action', array('controller' => 'screenbox', 'action' => 'index'));
Router::connect('/:action/*', array('controller' => 'screenbox', 'action' => 'index'));
