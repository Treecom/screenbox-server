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
			'User',
			'Screenboxserver',
	);  

	/**
	 * @var array default helpers
	 */
	var $helpers = array(
			'Form',
			'Cache',
			'Time', 
			'Number', 			
		//	'Firecake' // (Problems with session in Firecake)
	); 
	
	/**
	 * @var array pagination holder
	 */
	var $paginate = array();
	
	
	/**
	 * @var string default view template name
	 */ 
	var $viewName = null;
	
	/**
	 * @var string pageTitle
	 */
	var $pageTitle = 'Screenbox Server';
	
	
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
		$this->Auth->allowedActions = array('index','login', 'user_activation'); 	
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
	 * Boxes managment
	 * 
	 * @return void 
	 */
	function boxes($go = null, $id = null, $value = null){	 	
	 	$data = array();
	 	
	 	
	 	if ($on=="delete"){
			$this->params['form']['id'] = intval($id);
			$this->Screenboxserver->admin_deleteScreenbox($this);
			$this->Session->setFlash(__('Screenbox succesfuly removed!', true));
			$this->redirect('/boxes/', null, true);
		}

		if ($on=="active"){
			$this->params['form']['id'] = intval($id);
			$this->params['form']['active'] = intval($val)==1 ? 1 : 0;
			$status = $this->Screenboxserver->admin_setScreenbox($this);
			fb($status);
			if (intval($val)==1){
				$this->Session->setFlash(__('Screenbox succesfuly activated!', true));
			} else {
				$this->Session->setFlash(__('Screenbox are deactivated!', true));
			}
			$this->redirect('/boxes/', null, true);
		 }

		
		$data = $this->Screenboxserver->admin_getScreenboxes($this);
	 	
	 	fb($data);
	 	
	 	$this->set('data', $data);
     }
 

 	/**
	 * box
	 * 
	 * [action]
	 * 
	 * Box add/edit
	 * 
	 * @return void 
	 */
	function box($id = null){	 	
	 	 
	 	$this->loadModel('Company');
	 	
		$data = array();
 		

 		if (!empty($this->data['Screenbox'])){
 			$this->params['form']  = $this->data['Screenbox'];
 			$data = am($data, $this->Screenboxserver->admin_setScreenbox($this));
 		}  

 		if (!empty($id)){
 			$this->params['form']['id']  = $id;
 			$data = $this->Screenboxserver->admin_getScreenboxById($this);
 			$data['success'] = null;
 		}

		$data['Companies']  = $this->Company->find('list');

 		fb($data);
 	 
 		$this->set('data', $data);
     }

 	/**
	 * media
	 * 
	 * [action]
	 * 
	 * Media managment
	 * 
	 * @return void 
	 */
	function media($go = null, $id = null, $value = null){
	 	$data = array();
	 	$data = $this->Screenboxserver->admin_getMedia($this);
	 	
	 	fb($data);
	 	
	 	$this->set('data', $data);
    }

    /**
	 * medium
	 * 
	 * [action]
	 * 
	 * Add/Edit medium
	 * 
	 * @return void 
	 */
	function medium($id = null){
	 
	 	$this->loadModel('Screenbox');
	 	$this->loadModel('Company');

		$data = array();
 		

 		if (!empty($this->data['Media'])){
 			$this->params['form']  = $this->data['Media'];
 			$data = am($data, $this->Screenboxserver->admin_setMedia($this));
 		}  

 		if (!empty($id)){
 			$this->params['form']['id']  = $id;
 			$data = $this->Screenboxserver->admin_getMediaById($this);
 			$data['success'] = null;
 		}

 		$data['Companies']  = $this->Company->find('list'); 	
 		$data['Screenboxes']  = $this->Screenbox->find('list'); 			

 		fb($data);
 	 
 		$this->set('data', $data);
    }
 
 	/**
	 * stats
	 * 
	 * [action]
	 * 
	 * Stats page.
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
	 * Users managment.
	 * 
	 * @return void 
	 */
	function users($on = null, $id = null, $val = null){ 		 		
 		

		if ($on=="delete"){
			$this->params['form']['id'] = intval($id);
			$this->User->admin_deleteUser($this);
			$this->Session->setFlash(__('User succesfuly deleted!', true));
			$this->redirect('/users/', null, true);
		}

		if ($on=="active"){
			$this->params['form']['id'] = intval($id);
			$this->params['form']['active'] = intval($val)==1 ? 1 : 0;
			$status = $this->User->admin_editUser($this);
			fb($status);
			if (intval($val)==1){
				$this->Session->setFlash(__('User succesfuly activated!', true));
			} else {
				$this->Session->setFlash(__('User are deactivated!', true));
			}
			$this->redirect('/users/', null, true);
		 }

 		$data = $this->User->admin_getUsers($this);	
 		
 		fb($data);

 		$this->set('data', $data);
    }

    /**
	 * users_add
	 * 
	 * [action]
	 * 
	 * Users managment.
	 * 
	 * @return void 
	 */
	function user($id = null){ 		 		
 		
 		$this->loadModel('UserGroup');

		$data = array();
 		 		

 		if (!empty($this->data['User'])){
 			$this->params['form']  = $this->data['User'];
 			$data = am($data, $this->User->admin_editUser($this));
 		}  

 		if (!empty($id)){
 			$this->params['form']['id']  = $id;
 			$data = $this->User->admin_getUserById($this);
 			$data['success'] = null;
 		}

		$data['UserGroup']  = $this->UserGroup->find('list');

 		fb($data);
 	 
 		$this->set('data', $data);
    }


    /**
	 * companies
	 * 
	 * [action]
	 * 
	 * Companies managment.
	 * 
	 * @return void 
	 */
	function companies($on = null, $id = null, $val = null){ 		 		
 		

		if ($on=="delete"){
			$this->params['form']['id'] = intval($id);
			$this->User->admin_deleteCompany($this);
			$this->Session->setFlash(__('Company succesfuly deleted!', true));
			$this->redirect('/companies/', null, true);
		}

	 
 		$data = $this->User->admin_getCompanies($this);	
 		
 		fb($data);

 		$this->set('data', $data);
    }

    /**
	 * company
	 * 
	 * [action]
	 * 
	 * Company edit/add.
	 * 
	 * @return void 
	 */
	function company($id = null){ 		 		
 		
		$data = array();

 		if (!empty($this->data['Company'])){
 			$this->params['form']  = $this->data['Company'];
 			$data = am($data, $this->User->admin_editCompany($this));
 		}  

 		if (!empty($id)){
 			$this->params['form']['id']  = $id;
 			$data = $this->User->admin_getCompanyById($this);
 			$data['success'] = null;
 		}

 		fb($data);
 	 
 		$this->set('data', $data);
    }
 
 	/**
	 * user_groups
	 * 
	 * [action]
	 * 
	 * User groups listing.
	 * 
	 * @return void 
	 */
	function user_groups($on = null, $id = null, $value = null){
	  

		 if ($on=="delete"){
			$this->params['form']['id'] = $id;
			$this->User->admin_deleteGroup($this);	
			$this->Session->setFlash(__('Users group succesfuly deleted!', true));
			$this->redirect('/user_groups/');
		}

		 

 		$data = $this->User->admin_getGroups($this);	
 		
 		fb($data);

 		$this->set('data', $data);
    }

    /**
	 * user_groups
	 * 
	 * [action]
	 * 
	 * Add/Edit user group/
	 * 
	 * @return void 
	 */
	function user_group($id = null){
	 	$data = array();
 	 		

 		if (!empty($this->data['UserGroup'])){
 			$this->params['form']  = $this->data['UserGroup'];
 			$data = am($data, $this->User->admin_editGroup($this));
 		}  

 		if (!empty($id)){
 			$this->params['form']['id']  = $id;
 			$data = $this->User->admin_getGroupById($this);
 			$data['success'] = null;
 		}

 		fb($data);
 	 
 		$this->set('data', $data);
    }

    /**
	 * user_rights
	 * 
	 * [action]
	 * 
	 * ADD DESC!
	 * 
	 * @return void 
	 */
	function user_rights(){
	 
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
	 * Login and logut action.
	 * 
	 * @return void 
	 */
	function login($out = false){
		if ($out=="off"){
			$this->Auth->logout();
		}
    }


     /**
	 * install
	 * 
	 * [action]
	 * 
	 * Install action
	 * 
	 * @return void 
	 */
	function install($out = false){
		
    }

    function isAuthorized($a = null, $b=null){
    	return true;
    }
}
