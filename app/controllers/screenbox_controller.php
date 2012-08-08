<?php

/** 
 * ScreenboxServerController
 *
 * Controller for Screenbox server
 *
 * @package cake
 * @subpackage cake.app.controllers.ScreenboxServerController
 * @author Martin Bucko, Treecom s.r.o.
 * @copyright Copyright (c) 2012 Treecom s.r.o. 
 */
  
class ScreenboxController extends AppController {
 
	/**
	 * @var array Controller name
	 */
	var $name = 'Screenbox';
			
	/**
	 * @var array default components
	 */ 
	var $components = array(
			'Cookie',
			'RequestHandler',
		//	'Json'
	);  

	/**
	 * @var array default helpers
	 */
	var $helpers = array(
			'Cache',
			'Time', 
			'Number', 			
			// 'Firecake' (Problems with session in Firecake)
	); 
	
	/**
	 * @var array pagination holder
	 */
	var $paginate = array();
	
	
	/**
	 * @var string view template/action (default index)
	 */ 
	//var $viewName = 'default';
	
	/**
	 * @var string pageTitle
	 */
	var $pageTitle = 'Untitled';
	
	
	/**
	 * @var context action cache, try change cache policty if is web server overloaded
	 */
	var $cacheAction = array(
 		// 'index'  => array('callbacks' => false, 'duration' => 48000)
	);
 	
 	var $uses = array();

	/**
	 * beforeFilter
	 * Overriding Controller beforeFilter
	 * 
	 * @return void 
	 */
	function beforeFilter(){
		parent::beforeFilter();
		
  		// ! Security warning ! 
		// Here yo can describe site public methods/actions to run it without authentication!
		// Other methods require authentication by default
		//$this->Auth->allowedActions = array('index','login', 'user_activation'); 	
	}
 
	/**
	 * beforeRender
	 * Overriding Controller beforeFilter
	 * 
	 * @return void 
	 */
	function beforeRender(){
		parent::beforeRender();		
	}
    
	/**
	 * index
	 * 
	 * [action]
	 * 
	 * Site index method action. This is main (and entry) action for web.
	 * 
	 * @return void 
	 */
	function index(){
     	$this->autoRender = false;
    	$this->render('default');
     }

     /**
	 * boxes
	 * 
	 * [action]
	 * 
	 * ...
	 * 
	 * @return void 
	 */
	function boxes(){
	 
     }
 

 	/**
	 * media
	 * 
	 * [action]
	 * 
	 * Site index method action. This is main (and entry) action for web.
	 * 
	 * @return void 
	 */
	function media(){
	 
     }
 
 	/**
	 * stats
	 * 
	 * [action]
	 * 
	 * Site index method action. This is main (and entry) action for web.
	 * 
	 * @return void 
	 */
	function stats(){
 
     }
 
 	/**
	 * users
	 * 
	 * [action]
	 * 
	 * Site index method action. This is main (and entry) action for web.
	 * 
	 * @return void 
	 */
	function users(){
 
     }
 
 	/**
	 * settings
	 * 
	 * [action]
	 * 
	 * Site index method action. This is main (and entry) action for web.
	 * 
	 * @return void 
	 */
	function settings(){
	 
     }
 
 	/**
	 * login
	 * 
	 * [action]
	 * 
	 * Site index method action. This is main (and entry) action for web.
	 * 
	 * @return void 
	 */
	function login($out = false){
		
     }
}
