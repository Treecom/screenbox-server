<?php

/**
 * User Model Class
 * 
 * Model for users and his grouops
 * 
 * Fields descriptions:
 *  email:	as user login name
 *  active:  -1 blocked, 0 not activated, 1 active 
 *  
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 */

class User extends AppModel {
	/**
	 * @var string Component name 
	 */
	var $name = 'User';
	/**
	 * @var string dysplay field 
	 */
	var $displayField = "first_name";
	/**
	 * @var array belongs to definition
	 */
	var $belongsTo = array('UserGroup');
	/**
	 * @var array act as definition
	 */
	var $actsAs = array('Acl' => 'requester');
	/**
	 * @var array validation rules
	 */
	var $validate = array();
	
	/**
	 * @var integer default user group
	 */
	var $defaultUserGroupId = null;
	
	function __construct($id = false, $table = null, $ds = null){	
		parent::__construct($id, $table, $ds);
		
		// @todo set with domain config
		$this->defaultUserGroupId = 3;
		
		$this->validate = array(
			'first_name' => array(
					'rule' => 'notEmpty',
					'required' => true,
					'massage' => __('This field cannot be left blank', true)
					       
			),  
			'last_name' => array(
					'rule' => 'notEmpty',  
					'required' => true,
					'massage' => __('This field cannot be left blank', true)     
			),      
			'email' => array(
				'required' => true,
				'email1' => array(
					'rule' => 'isUnique', 
					'message' => __('This email address is all ready registred!', true)
				),
				'email2' => array(
					'rule' => 'email',   
					'message' => __('Please supply a valid email address.', true)
				),
			),
			'password2' => array(            
					 'rule' => 'repeatPassword',   
					 'message' => __('Reapeating password fail.', true),
			),      
			'password' => array(            
					 'rule' => array('between', 4, 15),  
					 'message' => sprintf(__('Must be between %s and %s characters long.', true), '4', '15')
			),
			'alias' => array(
				'required' => false,
				'allowEmpty' => true,
				'alias1' => array(
				 	'rule' => 'alphaNumeric',
				 	'message' => __('Usernames must only contain letters and numbers.', true)
				),
				'alias2' => array(
					'rule' => array('between', 2, 10),  
					'message' => sprintf(__('Must be between %s and %s characters long.', true), '4', '15')
				)
			),
			'timezone' => array(            
					 'rule' => 'notEmpty',    
			)
		);				
	}
	
	/**
	 * Validation for reapeting password on user registration
	 * @param array $check
	 * @return true if valid 
	 */
	function repeatPassword($check){		
		return ($this->data['User']['password'] == $this->data['User']['password2']);
	}
 	
	/**
	 * Hash Passwords for Auth Component and authentification 
	 * @param array $data
	 * @return array $data
	 */
	function hashPasswords($data) {
        if (isset($data['User']['password']) && !isset($data['User']['password2'])) {
           $data['User']['password'] = $this->hassPass($data['User']['password']);
        }  
        return $data;
    }
	
	/**
	 * Hass Pasword with Security::hash()
	 * @param string password
	 * @return string hassed password
	 */
	function hassPass($pass){
		return Security::hash($pass, null, true);
	}
	
	/**
	 * Before save callback
	 * @return boolean  
	 */
	function beforeSave(){
		if (!empty($this->data['User']['password']) && !empty($this->data['User']['password2'])){
			$this->data['User']['password'] = $this->hassPass($this->data['User']['password']);
		}
		if (empty($this->data['User']['user_group_id'])){
			$this->data['User']['user_group_id'] = $this->defaultUserGroupId;
		}
	 	return true;
	}
	
	/**
	 * afterSave
	 * After save callback 
	 * Update the aro for the user.
	 *
	 * @access public
	 * @return void
	 */
    function afterSave($created) {
        if (!$created) {
            $parent = $this->parentNode();
            $parent = $this->node($parent);
            $node = $this->node();
            $aro = $node[0];
            $aro['Aro']['parent_id'] = $parent[0]['Aro']['id'];
            $this->Aro->save($aro);
        } else {
			$this->read(null, $this->getLastInsertID());
			$aro['Aro']['alias'] = $this->data["User"]["email"];;
			$this->Aro->save($aro);			
        }
    }
	/**
	 * parentNode
	 * get parent node witch is UserGroup 
	 * @return mixed parent node
	 */
	function parentNode() {
	    if (!$this->id && empty($this->data)) {
	        return null;
	    }
	    $data = $this->data;
	    if (empty($this->data)) {
	        $data = $this->read();
	    }
	    if (!$data['User']['user_group_id']) {
	        return null;
	    } else {
	        return array('UserGroup' => array('id' => $data['User']['user_group_id']));
	    }
	}
	
}
