<?php

/**
 * O.C. UpDate CMS app routing
 * --------------------------- 
 */

/**
 * Web app basic route
 * Example: http://www.domain.com/
 */
Router::connect('/', array('controller' => 'context','action'=>'index'));


/**
 * Context languages routes
 * Example: http://www.domain.com/eng/*
 * Notice: maybe slow on many request and many languages!
 */
$route_languages = Configure::read('Domain.availableLanguages');

if (is_array($route_languages) && !empty($route_languages)){		
	foreach ($route_languages as $key){
		Router::connect('/'.$key.'/*', array('controller' => 'context','action' => 'index','lang' => $key));
	}
	//Router::connect('/:lang/*', array('controller' => 'context','action' => 'index','lang' => '[a-z]{2,3}'));
	unset($route_languages);
}  

/**
 * Context route for all others not routed paths
 * Example: http://www.domain.com/ 
 */

Router::connect('/login/*', array('controller' => 'context', 'action' => 'login'));
Router::connect('/logout/*', array('controller' => 'context', 'action' => 'logout'));
Router::connect('/sitemap.xml', array('controller' => 'context', 'action' => 'sitemap', 'url' => array('ext' => 'xml')));
Router::connect('/user-activation/*', array('controller' => 'context', 'action' => 'user_activation'));
Router::connect('/context/:action', array('controller' => 'context', 'action' => 'index'));

/**
 * Admin app route
 * Example: http://www.domain.com/admin
 */
Router::connect('/admin', array('controller' => 'admin','action'=>'index'));
Router::connect('/admin/:action', array('controller' => 'admin','action'=>'index'));
Router::connect('/admin/:action/*', array('controller' => 'admin','action'=>'index'));

/**
 * Tester for developers
 * Example: http://www.domain.com/tester
 */
// Router::connect('/tester/:action/*', array('controller' => 'tester','action'=>'index'));
/**
 * Scaffold tester
 * Example: http://www.domain.com/sc
 */
// Router::connect('/sc/:action/*', array('controller' => 'sc','action'=>'index'));

/**
 * Main Route for context tree.
 */
Router::connect('/*', array('controller' => 'context', 'action' => 'index'));
