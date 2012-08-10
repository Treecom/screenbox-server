<?php

/**
 * UserGroup Model Class
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 */

class UserGroup extends AppModel {
	
	/**
	 * @var string model name
	 */
	var $name = 'UserGroup';
	/**
	 * @var string display field
	 */
	var $displayField = 'name';
	/**
	 * @var array Behaviors
	 */
	var $actsAs = array('Acl' => array('requester'));
	
	/**
	 * parentNode
	 * @return null
	 */
	function parentNode(){
		return null;
	}
		
	/**
	 * beforeSave
	 * Callback before save. Add User times CU
	 * @return boolean
	 */
	function beforeSave(){		
		$this->addCUTime();
		return true;
	} 
	
}
