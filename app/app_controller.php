<?php
  
/**
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */


class AppController extends Controller {
	
	/**
	 * @var Controller Name
	 */
	var $name = "AppController";
	
	/**
	 * @var array App Components
	 */
	var $components = array('Session');
	
	/**
	 * @var array App Halpers
	 */
	var $helpers = array('Session','Html','Form','Javascript','Text');
	
	/**
	 * beforeFilter
	 * Before filter for all controllers in app
	 * @return void  
	 */
	function beforeFilter(){	
	
		parent::beforeFilter();
		
		/*
		$this->Auth->authenticate = ClassRegistry::init('User');		
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
	 	$this->Auth->userScope = array('User.active' => 1);
		$this->Auth->loginAction = array('controller' => 'context', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'context', 'action' => 'index');
		$this->Auth->logoutRedirect = array('controller' => 'context', 'action' => 'index');
		$this->Auth->authError = __("Sorry, you are lacking access!", true);
		$this->Auth->loginError = __("Login or Password was incorrectly entered!", true);
				
		$this->set('meta_for_layout', Configure::read('Domain.metaForLayout'));
		$this->set('css_for_layout', Configure::read('Domain.cssForLayout'));
		$this->set('header_content', Configure::read('Domain.headerContent'));
		$this->set('footer_content', Configure::read('Domain.footerContent'));		
		*/
	}
	
	/**
	 * Add helper just in time (inside actions - only when needed)
	 * aware of plugins
	 * @param mixed $helpers (single string or multiple array)
	 * @return void
	 */
	function loadHelper($helpers = array()) {
	    $this->helpers = array_merge($this->helpers, (array)$helpers);
	}

	/**
	 * Add component just in time (inside actions - only when needed)
	 * aware of plugins and config array (if passed)
	 * @param mixed $helpers (single string or multiple array)
	 * @return object component or null
	 */
	function loadComponent($components = array(), $callbacks = true) {
	    foreach ((array)$components as $component => $config) {
	        if (is_int($component)) {
	            $component = $config;
	            $config = null;
	        }
			$componentName = $component;
			
	        if (isset($this->{$componentName})) {
	            return $this->{$componentName};
	        }
	        App::import('Component', $componentName);
	
	        $componentFullName = $componentName.'Component';
	        $component = new $componentFullName($config);
			
			if ($callbacks){
		        if (method_exists($component, 'initialize')) {
		            $component->initialize($this);
		        }
		        if (method_exists($component, 'startup')) {
		            $component->startup($this);
		        }
			}
	        return $this->{$componentName} = $component;
	    }
		return null;
	}
	
	/**
	 * isAuthorized
	 * Controller authorization by group and controller from ACL. Function need defined $this->Auth->allowedActions array in controller.
	 * @return boolean athorized status 
	 */ 
	function isAuthorized($path = NULL, $crud = '*') {
		if (empty($path)){
			$path = 'Controllers/' . $this->name;
		} else {
			$path = trim($path);			
		}        
		
		try {
	  		if (!in_array($this->action, $this->Auth->allowedActions)) {           
				 if (@$this->Acl->check($this->Auth->user('email'), $path, $crud)) {  
				 	return true;            
				 }  
				 return false;        
			} else {
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
		return false;    
	}
	
	/**
	 * Custom 404 Errors
	 * @param object $controller
	 * @return void
	 */
	function error404(&$controller, $url = null){
			if (empty($url)) {
				$url = $controller->here;
			}
			$url = Router::normalize($url);
			if (!headers_sent()){
				header("HTTP/1.0 404 Not Found");	
			}
			$controller->pageTitle = __("Page not found!", true);
			$controller->set('title_for_layout', $controller->pageTitle);
			$controller->set(array(
				'code' => '404',
				'name' => $controller->pageTitle,
				'message' => h($url),
				'base' => $controller->base,
				'title' => $controller->pageTitle
			));
			$controller->viewName = "/errors/error404";			
	}

 	/**
	 * This vars is usefull for autocomplete in eclipse ....
	 * ------------------------------------------------------
	 */	
		
	/** 
	 * Post Model 
	 * 
	 * @var object Post 
	 */ 
	 var $Context; 
	
	/** 
	 * User Model 
	 * 
	 * @var object User 
	 */ 
	 var $User; 
	
	 /** 
	 * Group Model 
	 * 
	 * @var object Group 
	 */ 
	 var $UserGroup; 
	  
	 /** 
	 * AuthComponent 
	 * 
	 * @var object AuthComponent 
	 */ 
	 var $Auth; 
	 
	 /** 
	 * AclComponent 
	 * 
	 * @var object AclComponent 
	 */ 
	 var $Acl; 
	  
	 /** 
	 * SessionComponent 
	 * 
	 * @var object SessionComponent 
	 */ 
	 var $Session; 
	  
	 /** 
	 * RequestHandlerComponent 
	 * 
	 * @var object RequestHandlerComponent 
	 */ 
	 var $RequestHandler; 
	 
	 /** 
	 * DomainLoaderComponent 
	 * 
	 * @var object DomainLoaderComponent 
	 */ 
	 var $DomainLoader; 
	 /**
	  * dummy function
	  * @return void
	  */
	 function dummy(){}
}