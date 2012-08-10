<?php
 
/**
 *  User Component
 * 
 *  User and groups component
 *
 *  @author Martin Bucko (bucko@oneclick.sk)
 *  @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 *  @version 1.0.1
 *  @created 2009-06-19
 */

class UserComponent extends Object {
    var $name = "User";
	var $lockedAcos = array('Controllers','Admin','Context');
	var $lockedGroups = array('Administrators','Users');
    
	/**
     * init
     * Used to initialize the components for current controller.
     * @params object $controller with components to load
     * @return void
     */
    function init(&$controller) {
      
    }
    
    /**
     * initilize
     * Called before the Controller::beforeFilter().
     * @params object $controller with components to initialize
     * @return void
     */
    function initialize(&$controller) {
       
    }
    
    /**
     *  startup 
     *  Called after the Controller::beforeFilter() and before the controller action
     *  @params object $controller with components to startup
     *  @return void
     */
    function startup(&$controller) {
       
    }
    
    /**
     *  beforeRender 
     *  Called after the Controller::beforeRender(), after the view class is loaded, and before the Controller::render()
     *  @params object $controller
     *  @return void
     */
    function beforeRender(&$controller) {
       
    }
    
    /**
     *  shutdown
     *  Called after Controller::render() and before the output is printed to the browser.
     *  @params object $controller with components to shutdown
     *  @return void
     */
    function shutdown(&$controller) {
       
    }
    
	/**
     *  getUserProfile
     *  .....
     *  @params object $controller with components to shutdown
     *  @return string
     */
    function getUserProfile(&$controller, &$element) {    	
		$element['properties']= 'showUserProfile (FIXME)';
    	return $element;
    }
    
	
	/**
     *  getUserLogin
     *  .....
     *  @params object $controller with components to shutdown
     *  @return array
     */
    function getUserLogin(&$controller, &$element) {    
		if ($controller->Session->read('Auth.User')){
			$controller->set('User', $controller->Session->read('Auth.User'));
			$element['data'] =  $controller->Session->read('Auth');
		} else {
			if (!empty($controller->data['User']['email'])){
				$controller->set('User', $controller->data['User']);
				$element['data'] = $controller->data;
			}
		}
    	return $element;
    }
    
	/**
     *  registrationForm
     *  .....
     *  @params object $controller with components to shutdown
     *  @return string
     */
    function getRegistrationForm(&$controller, &$element) {    	
    	
		$controller->loadModel('User');
		$controller->loadModel('UserProfile');
		$controller->loadModel('Company');
			 
		$controller->helpers[] = 'Timezone';
		$out = array();
		$editMode = false;
		
		$userId 	   = !empty($controller->data['User']['id']) 			 ? intval($controller->data['User']['id']) : null; 
		$userProfileId = !empty($controller->data['UserProfile']['user_id']) ? intval($controller->data['UserProfile']['user_id']) : null;
		$userCompanyId = !empty($controller->data['Company']['id']) 		 ? intval($controller->data['Company']['id']) : null;
		
		$element['properties']['showUserForm']		= isset($element['properties']['showUserForm']) 	? $element['properties']['showUserForm'] : true;
		$element['properties']['showProfileForm']	= isset($element['properties']['showProfileForm']) 	? $element['properties']['showProfileForm'] : false;
		$element['properties']['showCompanyForm']	= isset($element['properties']['showCompanyForm']) 	? $element['properties']['showCompanyForm'] : false;
		$element['properties']['sendCheckEmail']	= isset($element['properties']['sendCheckEmail']) 	? $element['properties']['sendCheckEmail'] : false;
		$element['properties']['needAdminConfirm']	= isset($element['properties']['needAdminConfirm']) ? $element['properties']['needAdminConfirm'] : false;
		$element['properties']['blockTimeAfterReg']	= isset($element['properties']['blockTimeAfterReg']) ? $element['properties']['blockTimeAfterReg'] : 3600;
		
		// if user registred or loged in return
		if ($controller->Session->read('Auth.User.regCreated')){
			if ((time()-intval($controller->Session->read('Auth.User.regCreated')))< $element['properties']['blockTimeAfterReg'] || $controller->Session->read('User.id')>0){
				// return array('okMsg' => __('You are all ready registred!', true));
			}
		}
		if (!empty($controller->params['named']['id'])){
			$editMode = true;
		}		
		
		// form sended
		if (!empty($controller->data)){
			
			App::import('Core','Sanitize');
			$controller->data = Sanitize::clean($controller->data, array('encode'=>false));
			
			$out['errorMsg']  =  __('Please check form fields for marked errors.', true);
			
			// validate User
			if ($element['properties']['showUserForm']){
				
				// security fix
				unset($controller->data['User']['user_group_id']);
				
				if (!$element['properties']['sendCheckEmail'] && !$editMode){
					$controller->data['User']['active'] = 1;
				}
				// remove password(&2) field on edit blank
				if ($editMode){
					if (isset($controller->data['User']['password']) && empty($controller->data['User']['password'])){
						unset($controller->data['User']['password']);
						if (isset($controller->data['User']['password2'])){
							unset($controller->data['User']['password2']);
						}					
					}
					// important! if not set admin user become normal user
					if ($controller->Session->read('Auth.User.user_group_id')){
						$controller->data['User']['user_group_id'] = $controller->Session->read('Auth.User.user_group_id');
					}
				}
				$controller->User->set($controller->data);
				if (!$controller->User->validates()){
					return $out;
				}
			}
			// validate Profile
			if ($element['properties']['showProfileForm']){
				$controller->UserProfile->set($controller->data);
				if (!$controller->UserProfile->validates()){
					return $out;
				}
			}
			// validate Company
			if ($element['properties']['showCompanyForm']){
				$controller->Company->set($controller->data);
				if (!$controller->Company->validates()){
					return $out;
				}
			}
			
			// save user data
			if ($element['properties']['showUserForm']){
					if ($controller->User->save()){
						$userId = $controller->User->getInsertID();			
						$userId = !empty($controller->data['User']['id']) && empty($userId) ? intval($controller->data['User']['id']) : $userId;
					} else {
						$out['errorMsg'] = __('Error on saving User data!', true);
						return $out;
					}
			}
			
			// save company data
			if ($element['properties']['showCompanyForm']){
				if ($userId>0 && empty($controller->data['Company']['id'])){
					$controller->data['Company']['created_user_id'] = $userId;					
					$controller->data['Company']['created_time'] = time();
				}
				$controller->Company->set($controller->data);
				if ($controller->Company->save()){
					$userCompanyId = $controller->Company->getInsertID();
					$userCompanyId = !empty($controller->data['Company']['id']) && empty($userCompanyId) ? intval($controller->data['Company']['id']) : null;
				} else {
					$out['errorMsg'] = __('Error on saving Company data!', true);
					return $out;
				}
			}
			
			// save profile data (not to be after company and user!)
			if ($element['properties']['showProfileForm'] && $userId>0){
				if (!empty($userCompanyId)){
					$controller->data['UserProfile']['company_id'] = $userCompanyId;
				}
				$controller->data['UserProfile']['user_id'] = $userId;
				$controller->data['UserProfile']['created_user_id'] = $userId;					
				$controller->data['UserProfile']['created_time'] = time();
				$controller->UserProfile->set($controller->data);
				if ($controller->UserProfile->save()){
					$userProfileId = $userId;
				} else {
					$out['errorMsg'] = __('Error on saving User Profile data!', true);
					return $out;
				}	
			}
			
			$out['errorMsg'] = null;
			$out['okMsg'] =  __('Registration was sucessfull saved! Thank you.', true);
			
			// send mail
			if (!$editMode && $userId>0 && $element['properties']['sendCheckEmail']){				
				if ($this->sendRegCheckEmail($userId, $controller)){
						$out['emailMsg'] =  __('Please check your mail box for user activation to complete this registration.', true);		
				}
			}
			
			// set ssession after reg
			if ($userId>0){
				$controller->Session->write('Auth.User.regCreated',time());
			}
		}
		
		if (!empty($controller->params['named']['id'])){
			if ($controller->Session->read('Auth.User.id') == intval($controller->params['named']['id'])){
				
				$controller->data = $controller->User->findById(intval($controller->params['named']['id']));
				if (!empty($controller->data['User']['password'])){
					unset($controller->data['User']['password']);
					$controller->data = am($controller->data, $controller->UserProfile->findByUserId(intval($controller->params['named']['id'])));
				}
				
				
			}
		}
		return $out;
    }
	
	/**
	 * Send Registration Check/Activation to user by id 
	 * @param integer $userId
	 * @param object $controller
	 * @return true if send
	 */
	function sendRegCheckEmail($userId, &$controller){
		if (!$controller->User){
			$controller->loadModel('User');
		}
		
		if (!empty($userId)){
			$data = $controller->User->read(null,$userId); 			
			if (!empty($data['User'])){
				$Email = $controller->loadComponent('Email');
				if (is_object($Email)){
					$data['User']['key'] = md5($data['User']['id'] . $data['User']['email']); 
					$controller->set($data);
					$Email->to = $data['User']['email'];									
					$Email->from = Configure::read('Domain.serverEmail');
					$Email->subject = __('Your user registration on ',true) . Configure::read('Domanin.name');
					$Email->template = 'user_registration';
					$Email->sendAs = 'text'; // @todo 'both' - make html tamplate 
					return $Email->send();
				}
			}
		}
		return false;
	}
	
	function activateUserReg(&$controller){
		
	}
 	
	/* --------------- Admin Function ------------- */
	
	/**
	 * admin_getUsers
	 * @param object $controller
	 * @return 
	 */
	function admin_getUsers(&$controller){
		$controller->loadModel('User');
		
		// set limit for query
        $opt = $controller->User->setLimit($controller->params['form']);
		$opt['fields'] = array('id','first_name','last_name','email','user_group_id','active');
		$opt['conditions'] = array();	
		
			
		if (!empty($controller->params['form']['q'])){					
			$opt['conditions']['OR'] = array(
					array('lower(first_name) like'=>'%'.low($controller->params['form']['q']).'%'),
					array('lower(last_name) like'=>'%'.low($controller->params['form']['q']).'%'),
					array('lower(email) like'=>'%'.low($controller->params['form']['q']).'%')
				);
		} 
		if (!empty($controller->params['form']['userGroupId'])){
			// @todo more parameters to search ...
			array_push($opt['conditions'], array('user_group_id'=>$controller->params['form']['userGroupId']));
 		} 
    	return array(
    			'count' => $controller->User->find('count'),
     			'Users' => Set::extract($controller->User->find('all', $opt),'{n}.User')
    	);	
	}
 
	/**
	 * admin_getUserById
	 * @param object $controller
	 * @return array
	 */
	function admin_getUserById(&$controller){
		if (!empty($controller->params['form']['id'])){
			$id = intval($controller->params['form']['id']);
			$controller->loadModel('User');
			$result = $controller->User->findById($id);
			if (!empty($result['User'])){
				unset($result['User']['password']);
				return array('success'=>true,'data'=>$result['User']);
			} 
		}
	}
 
	/**
	 * admin_editUser
	 * @param object $controller
	 * @return array
	 */
	function admin_editUser(&$controller){
			
		if (!empty($controller->params['form']['id'])){
			if (!$controller->isAuthorized('Controllers/Admin/User','update')){
				return $controller->unAuth();
			}
		} else {
			if (!$controller->isAuthorized('Controllers/Admin/User','create')){
				return $controller->unAuth();
			}
		}
  		$logout = false;		
		$out = array('success'=>false);
 		$controller->loadModel('User');

		if (empty($controller->params['form']['password']) && empty($controller->params['form']['password2'])){
			unset($controller->params['form']['password'], $controller->params['form']['password2']);
		}
		
		// change user aro
		// @todo not finished when moved to other group!
		if (!empty($controller->params['form']['id']) && !empty($controller->params['form']['email'])){
			if ($controller->params['form']['id']>0){
				$aro =& $controller->Acl->Aro;
				$data = $aro->find('first',array('conditions'=>array('foreign_key'=>intval($controller->params['form']['id']))));
				if (!empty($data['Aro']['id'])) {    
					$aro->id = $data['Aro']['id'];
					if ($aro->save(array('alias' => $controller->params['form']['email']))){
						// logout if current user changed 
						if ($controller->Session->read('Auth.User.id')==$data['Aro']['id']){
							$controller->Auth->logout();
							$out['logout'] = true;
						}
					}
				}
			}
		}
		if ($controller->User->save($controller->params['form'])){
				$out['success'] = true;
				if (empty($controller->params['form']['id'])){
					$out['id'] = $controller->User->getInsertID();
				}
		} else {
			$out = array('success' => false, 'errors' => $controller->User->invalidFields());
		}	
		 	
		return $out;
	}
	
	/**
	 * admin_deleteUser
	 * @param object $controller
	 * @return array 
	 */
	function admin_deleteUser(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/User','delete')){
			return $controller->unAuth();
		}
		
		$out = array('success'=>false);
		
		if (!empty($controller->params['form']['id'])){
			$id = intval($controller->params['form']['id']);
			if ($id>0){
				$controller->loadModel('User');
				$out['success'] = $controller->User->delete($id);
			}
		}
		return $out;
	}
	
	/**
	 * admin_getGroupById
	 * @param object $controller
	 * @return 
	 */
	function admin_getGroupById(&$controller){
		if (!empty($controller->params['form']['id'])){
			$id = intval($controller->params['form']['id']);
			$controller->loadModel('UserGroup');
			$result = $controller->UserGroup->findById($id);
			if (!empty($result['UserGroup'])){
 				return array('success'=>true,'data'=>$result['UserGroup']);
			} 
		}
	}
 
	/**
	 * admin_getGroupsList
	 * @param object $controller
	 * @return 
	 */
	function admin_getGroupsList(&$controller){
		$controller->loadModel('UserGroup');
		$data = $controller->UserGroup->find('all', array('fields'=>array('id','name')));
		// $data = $this->UserGroup->itemUsers($data);
		return array(
						'count'=> $controller->UserGroup->find('count'),
						'UserGroups'=> Set::extract($data, '{n}.UserGroup')
					);		
	}
 
	/**
	 * admin_getGroups
	 * @param object $controller
	 * @return array
	 */
	function admin_getGroups(&$controller){
		$controller->loadModel('UserGroup');
		$controller->UserGroup->bindUsers();
		
		$limit = $controller->params['form']['limit']>0 ? $controller->params['form']['limit'] : 30;
		$start = $controller->params['form']['start']>0 ? $controller->params['form']['start'] : 0;
    	$opt = array('limit'=>$limit,'offset'=>$start);
		$opt['conditions'] = array();		
		if (!empty($controller->params['form']['q'])){
			// @todo more parameters to search ...
			$opt['conditions'] = array('name'=>'%'.$controller->params['form']['q'].'%');			
		} 
		
		$data = $controller->UserGroup->find('all', $opt);	
		$data = $controller->UserGroup->itemUsers($data);
     	return array(
    			'count' => $controller->UserGroup->find('count'),
     			'UserGroups' => Set::extract($data,'{n}.UserGroup')
    	);	
	}
 
	/**
	 * admin_editGroup
	 * @param object $controller
	 * @return 
	 */
	function admin_editGroup(&$controller){
		
		if (!empty($controller->params['form']['id'])){
			if (!$controller->isAuthorized('Controllers/Admin/User','update')){
				return $controller->unAuth();
			}
		} else {
			if (!$controller->isAuthorized('Controllers/Admin/User','create')){
				return $controller->unAuth();
			}
		}
			
		$out = array('success'=>false);
		$controller->loadModel('UserGroup');
 		if (!empty($controller->params['form']['name'])){
 			$controller->UserGroup->save($controller->params['form']);	
			$out = array('success'=>true);				
		}
		return $out;
	}
 
	/**
	 * admin_deleteGroup
	 * @param object $controller
	 * @return array
	 */
	function admin_deleteGroup(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/User','delete')){
			return $controller->unAuth();
		}
			
		$out = array('success'=>false);
		if (!empty($controller->params['form']['id'])){
			$id = intval($controller->params['form']['id']);
			if ($id>0){
				$controller->loadModel('UserGroup');
				$gr =  $controller->UserGroup->findById($id);
				if (!empty($gr['UserGroup']['name'])){
					if (!in_array($gr['UserGroup']['name'],$this->lockedGroups)){
						$out['success'] = $controller->UserGroup->delete($id);
					}
				}
			}
		}
		return $out;
	}
 
	/**
	 * admin_getACOTree
	 * @param object $controller
	 * @return array
	 */
	function admin_getACOTree(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','read')){
				return $controller->unAuth();
		}
			
		$out = array();
		$controller->loadModel('Aco');		
		if (empty($controller->params['form']['node'])){
			$id = null;
		} else {
			$id = intval($controller->params['form']['node']);
			$id = $id>0 ? $id : null;
		}     	 
		$opt = array('conditions'=> array('parent_id'=>$id),'order'=>'alias ASC');		
 		$data = $controller->Aco->find('all', $opt);		
		if (is_array($data) && !empty($data)){			
			foreach($data as $line){
				$out[] = array(
							'id'=>$line['Aco']['id'],
							'text'=>$line['Aco']['alias'],
							'iconCls'=> (empty($line['Aco']['model'])) ? 'icon-folder_key' : 'icon-folder_database',
							'expanded'=> ($line['Aco']['parent_id']==null),
							'data'=>$line['Aco']
							
				);
			}
		}	
      	return $out;
	}
 
	/**
	 * admin_getAROs
	 * @param object $controller
	 * @return array
	 */
	function admin_getAroAco(&$controller){
 	
		$out = $aco = $data = array();
		
		$controller->loadModel('ArosAcos');
		$controller->loadModel('Aco');
		
		if (!empty($controller->params['form']['xaction'])){
			
			$action = $controller->params['form']['xaction'];
			$action = r('destroy','delete', $action);
			
			if (!$controller->isAuthorized('Controllers/Admin/UserACL',$action)){
				return $controller->unAuth();
			}
			
			if ($action=='update' || $action=='create'){
				$this->admin_setAroAco($controller);
			}
			if ($action=='delete' || $action=='destroy'){
 				$this->admin_deleteAroAco($controller);
 			}
  		} else {
  			if (!$controller->isAuthorized('Controllers/Admin/UserACL','read')){
				return $controller->unAuth();
			}
  		}
		
		$controller->ArosAcos->bindModel(
				array(
					'belongsTo'=>
						array('Aro'=>
							array(
								'className'=>'Aro',
								'foreignKey'=>'aro_id'
							)
						) 
				)
		);
		
		$start = !empty($controller->params['form']['start']) ? intval($controller->params['form']['start']) : 0;
		$limit = !empty($controller->params['form']['limit']) ? intval($controller->params['form']['limit']) : 30;			
		$limit = array('limit'=>$limit,'offset'=>$start);
		$acoId = !empty($controller->params['form']['id']) ? intval($controller->params['form']['id']) : 0; 	
		
		if ($acoId>0){
			$aco = $controller->Aco->findById($acoId);
		}	
			
		$opt = array('conditions'=>array('aco_id' => $acoId),'order'=>'aco_id ASC');
		$data = $controller->ArosAcos->find('all',am($opt,$limit));
		
 		if (is_array($data) && !empty($data)){
			foreach ($data as $line){
				$out[] = array(
								'id'=>intval($line['ArosAcos']['id']),								
								'alias'=>($line['Aro']['alias']),
								'aro_id'=>($line['ArosAcos']['aro_id']),
								'aro_model'=>($line['Aro']['model']),
								'aco_id'=>($line['ArosAcos']['aco_id']),
								'aco_model'=> !empty($aco['Aco']['model']) ? $aco['Aco']['model'] : '',
								'allow'=>intval($line['ArosAcos']['_read']),
								'_create'=>intval($line['ArosAcos']['_create']),
								'_read'=>intval($line['ArosAcos']['_read']),
								'_update'=>intval($line['ArosAcos']['_update']),
								'_delete'=>intval($line['ArosAcos']['_delete'])
						);
			}
		}		
		
		return array(
			'count'=>$controller->ArosAcos->find('count',$opt),
			'acos'=>$out
		);
	}
 
	/**
	 * admin_setAroAco
	 * manage ARO to ACO rights in ACL
	 * @param object $controller
	 * @return 
	 */
	function admin_setAroAco(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','update')){
			return $controller->unAuth();
		}
			
		$rights = array('_create','_read','_update','_delete');		
		
 		if (!empty($controller->params['form']['acos']) && !empty($controller->params['form']['id'])){
 			
			$this->decodeAcos($controller);
			$controller->loadModel('Aco');
			
			if (is_array($controller->params['form']['acos'])){				
				foreach ($controller->params['form']['acos'] as $aco){
		 
					$aco['aco_id'] = intval($aco['aco_id']);
					$aco['alias'] = trim($aco['alias']); // user alias .. not aco!
					
					if ($aco['aco_id']>0){																			
							$acoData['path'] = $controller->Aco->getpath($aco['aco_id']);
							$acoData['path'] = Set::extract($acoData['path'],'{n}.Aco.alias');
							if (is_array($acoData['path'])){
								$acoData['path'] = join('/',$acoData['path']);								
							}						
					}
					
					if (!empty($acoData['path'])){		
						//$controller->Acl->id = $acoData['Aco']['id'];
						if ($aco['allow']===true || $aco['allow']===1){ // as controller, action or object
							$controller->Acl->allow($aco['alias'], $acoData['path'], '*');
						} else {
							$controller->Acl->deny($aco['alias'], $acoData['path'], '*');
						}
						foreach ($rights as $r){
							if (isset($aco[$r])){  								
	  							if ($aco[$r]===true || $aco[$r]===1){
	  								$controller->Acl->allow($aco['alias'], $acoData['path'], r('_','',$r));
								} else {
									$controller->Acl->deny($aco['alias'], $acoData['path'], r('_','',$r));
								}
							}
						}
  						
					}
				}
			}
		}
	}
 	
	/**
	 * admin_deleteAroAco
	 * delete AroAco permissions in ACL
	 * @param object $controller
	 * @return array 
	 */
	function admin_deleteAroAco(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','delete')){
			return $controller->unAuth();
		}
 		
		$out = array('success'=>false);
		
		$controller->loadModel('ArosAcos');		
		$this->decodeAcos($controller);
		
		if (!empty($controller->params['form']['acos']) && !empty($controller->params['form']['id'])){
			if (is_array($controller->params['form']['acos'])){				
				foreach ($controller->params['form']['acos'] as $aco){
					if (!empty($aco['id'])) {
						$id = intval($aco['id']);
					}
					if (intval($aco)>0){
						$id = intval($aco);
					}
					if ($id>0) $controller->ArosAcos->delete($id);
 				}
			} elseif (intval($controller->params['form']['acos'])>0){				
				$controller->ArosAcos->delete(intval($controller->params['form']['acos']));				
			}
			$out = array('success'=>true);
		}
		
		return $out;
	}
	
 	/**
	 * admin_setAco
	 * Create and update ACO 
	 * @param object $controller
	 * @return 
	 */
	function admin_setAco(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','update')){
			return $controller->unAuth();
		}
		
 		$out = array('success'=>true);
 		if (!empty($controller->params['form']['alias'])){
			$controller->loadModel('Aco');
			if (!empty($controller->params['form']['id'])){
				$controller->Aco->create();
			}
			if (isset($controller->params['form']['parent_id'])){
				$controller->params['form']['parent_id'] = 
						intval($controller->params['form']['parent_id'])>0 ? 
							intval($controller->params['form']['parent_id']) : null;
			}
			if (!in_array($controller->params['form']['alias'], $this->lockedAcos)){
				$out['success'] = $controller->Aco->save($controller->params['form']);
			}
		}
 		return $out;
	}
	
	/**
	 * admin_getACOById
	 * @param object $controller
	 * @return array ACO by id
	 */	
	function admin_getAcoById(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','read')){
			return $controller->unAuth();
		}
		
		$data = array();		
		if (!empty($controller->params['form']['id'])){
			$id = intval($controller->params['form']['id']);
			if ($id>0){
				$controller->loadModel('Aco');
				$data = $controller->Aco->findById($id);
				if (!empty($data['Aco'])) $data = $data['Aco'];				
			}
		} 
		return $data;
	}
	
	/**
	 * admin_deleteAco
	 * delete ACO	 
	 * @param object $controller
	 * @return 
	 */
	function admin_deleteAco(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','delete')){
			return $controller->unAuth();
		}
		
		$out = array('success'=>false);
		if (!empty($controller->params['form']['id'])){
			$id = intval($controller->params['form']['id']);
			if ($id>0){								
				$controller->loadModel('Aco');
				$aco = $controller->Aco->findById($id);
				if (!empty($aco['Aco']['alias'])){
					if (!in_array($aco['Aco']['alias'],$this->lockedAcos)){
						$out['success'] = $controller->Aco->delete($id);
					}
				}
			}
		}
		return $out;
	}
	
	/**
	 * decodeAcos
	 * decode acos JSON array
	 * @param object $controller
	 * @return 
	 */
	function decodeAcos(&$controller){
		if (!empty($controller->params['form']['acos'])){
			if (is_string($controller->params['form']['acos']) && !is_array($controller->params['form']['acos'])){
				$controller->params['form']['acos'] = $controller->Json->decode($controller->params['form']['acos']);
				if (is_array($controller->params['form']['acos']) && !is_object($controller->params['form']['acos'])){
					foreach ($controller->params['form']['acos'] as $key=>$val){
						if (is_object($val)){
							$controller->params['form']['acos'][$key] = get_object_vars($val);
						} 
					}
				}
				if (is_object($controller->params['form']['acos']) && !is_array($controller->params['form']['acos'])){
					$controller->params['form']['acos'] = get_object_vars($controller->params['form']['acos']);
					$controller->params['form']['acos'] = array($controller->params['form']['acos']);
				}
			}
			return $controller->params['form']['acos'];
		}
		return array();
	}
	
	/**
	 * admin_getAros
	 * @param object $controller
	 * @return 
	 */
	function admin_getAros(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/UserACL','read')){
			return $controller->unAuth();
		}
		
		$opt = $data = array();
				
		$controller->loadModel('Aro');
		$fields = array('fields'=>array("id", "model", "alias", "foreign_key"),'order'=>'alias ASC');
		$query = !empty($controller->params['form']['query']) ? low(trim($controller->params['form']['query'])) : '';
 		$start = !empty($controller->params['form']['start']) ? intval($controller->params['form']['start']) : 0;
		$limit = !empty($controller->params['form']['limit']) ? intval($controller->params['form']['limit']) : 30;
		$optlimit = array('limit'=>$limit,'offset'=>$start);
		if (!empty($query)){
			$opt = array('conditions'=>array('LOWER(alias) like' =>'%'.$query.'%'));
		}
		
		$data = $controller->Aro->find('all',am($opt,$optlimit,$fields));
  		$data = Set::extract($data,'{n}.Aro');
		
		return array(
			'count'=>$controller->Aro->find('count',$opt),
			'aros'=>$data
		);
	}
	
	
	/**
     * admin_getComapnies
     * @param object $controller
     * @return array
     */
    function admin_getCompanies(&$controller) {
    
        // ACL list check for user permissions
       	if (!$controller->isAuthorized('Controllers/Admin/User', 'read')) {
        	return $controller->unAuth();
        }
		
        $opt = array();
		$controller->loadModel('Company');
		// set limit for query
        $opt = $controller->Company->setLimit($controller->params['form']);
        
		// query search conditions
        if (!empty($controller->params['form']['q'])) {
            $opt['conditions'] = array('lower(Company.name) like'=>'%'.low($controller->params['form']['q']).'%');
        } else {
            $opt['conditions'] = array();
        }
        
		// find query
        $data = $controller->Company->find('all', $opt);
		
		// modify users info
        $data = $controller->Company->itemUsers($data);
        
		// extract only Coupons item, no other like users ets.
		$data = array('Companies' => Set::extract($data, '{n}.Company'));
		// add counter
		$data['count'] = $controller->Company->find('count', array('conditions'=>$opt['conditions']));
		 
        return $data;
    }

	/**
     * admin_getComapnies
     * @param object $controller
     * @return array
     */
    function admin_getCompanyById(&$controller) {
    
        // ACL list check for user permissions
       	if (!$controller->isAuthorized('Controllers/Admin/User', 'read')) {
        	return $controller->unAuth();
        }
		
        $opt = array();
		$controller->loadModel('Company');
		// set limit for query
        $opt = $controller->Company->setLimit($controller->params['form']);
        
		// query search conditions
        if (!empty($controller->params['form']['id'])) {
            // $opt['conditions'] = array('Company.id'=>'%'.intval($controller->params['form']['id']));
			// find query
        	$data = $controller->Company->findById(intval($controller->params['form']['id']));
		
			// modify users info
        	$data = $controller->Company->itemUsers($data);
			$data = $data['Company']; 
        }
		 
        return array('success' => !empty($data), 'data' => (array) $data);
    }
	
	 /**
     * admin_editCompany
     * @param object $controller
     * @return array
     */
    function admin_editCompany(&$controller) {
        
        // ACL list check for user permissions
        if (!empty($controller->params['form']['id'])) {
            if (!$controller->isAuthorized('Controllers/Admin/User', 'update')) {
                return $controller->unAuth();
            }
        } else {
            if (!$controller->isAuthorized('Controllers/Admin/User', 'create')) {
                return $controller->unAuth();
            }
        }
		
        $out = array('succes'=>false, 'errorMessage'=>__('There was an error saving data to server...', true));
        $tableId = 0;
		
		$controller->loadModel('Company');
		
        // save data
        if (!empty($controller->params['form']['id']) || isset($controller->params['form']['name'])) {
            // save data
            if ($controller->Company->save($controller->params['form'])) {
                // return out with new id
                if (empty($controller->params['form']['id'])){
                	$tableId = $controller->Company->getLastInsertId();
                	$out = array('success'=>true, 'id'=>$tableId);
				} else {
					$out = array('success'=>true);
				}
            } else {
            	$out = array('success'=>false, 'id'=>$tableId, 'errors'=>$this->Company->invalidFiels());
			}
        }
        return $out;
    }

    /**
     * admin_deleteCoupon
     * @param object $controller
     * @return array
     */
    function admin_deleteCompany(&$controller) {
		if (!$controller->isAuthorized('Controllers/Admin/User', 'delete')){
            return $controller->unAuth();
        }
        $out = array('success'=>false);
        $id = intval($controller->params['form']['id']);
		
        if ($id > 0) {
        	$controller->loadModel('Company');
            $out['success'] = $controller->Company->delete($id);
        }
        return $out;
    }

	
	/**
	 * admin_reportBug
	 * @param object $controller
	 * @return array
	 */
	function admin_reportBug(&$controller){
		
		$msg = $send = false;				
		
		if (!empty($controller->params['form']['description']) && !empty($controller->params['form']['subject'])){
		
			App::import('Component','Email');
			$Email = new EmailComponent();
			$Email->initialize($controller);			  
		   	$Email->smtpOptions = Configure::read('Domain.smtpOptions');		
		    $Email->delivery = 'smtp';
			$Email->template = 'default';
			$Email->layout = 'default';
 			$Email->to = Configure::read('Domain.supportEmail');
			$Email->from = !empty($controller->params['form']['from']) ? $controller->params['form']['from'] : Configure::read('Domain.adminEmail');
			$Email->xMailer = 'UpDate CMS v2.0';
			$Email->subject = 'OC UpDate bug report: ' . $controller->params['form']['subject'];			
			$send = $Email->send(
				"Bug report from host: ". $_SERVER["HTTP_HOST"] ."\n" .
				"Problem type: " . $controller->params["form"]["problem_type"] . "\n \n" .
				"Problem description: \n".
				$controller->params["form"]["description"] . "\n" 				 
			);
		
			if (!empty($Email->smtpError)){
				$msg =  $Email->smtpError;
			} else {
				$msg = __('Massage was send', true);
			}
		}
		// fb(array('success' => $send, 'msg' => $msg, 'form' => $controller->params['form']));
		return array('success' => $send, 'msg' => $msg);
	}  
}
