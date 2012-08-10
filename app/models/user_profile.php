<?php

/**
 * UserProfile Model Class
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 */

class UserProfile extends AppModel {
	/**
	 * @var string Model Name 
	 */
	var $name = 'UserProfile';
	/**
	 * @var string primary key
	 */
	var $primaryKey = 'user_id';	
	/**
	 * @var array validation rules
	 */
	var $validate = array();
	
	function __construct($id = false, $table = null, $ds = null){	
		parent::__construct($id, $table, $ds);
		
		$this->validate = array(		
			'user_id' => array(
					'rule' => 'numeric',   
					'allowEmpty' => true									
			),	     
			'email_2' => array(
					'rule' => 'email',   
					'allowEmpty' => true,     
					'message' => __('Please supply a valid email address.', true)
			),
			'email_3' => array(
					'rule' => 'email',   
					'allowEmpty' => true,     
					'message' => __('Please supply a valid email address.', true)
			),
			'latitude' => array(
					'rule' => 'decimal',  
					'allowEmpty' => true,   
					'message' => __('Value is incorrect', true)
			),
			'lontitude' => array(
					'rule' => 'decimal', 
					'allowEmpty' => true,  
					'message' => __('Value is incorrect', true)
			),
			'msn' => array(
					'rule' => 'email',   
					'allowEmpty' => true,     
					'message' => __('Please supply a valid email address.', true)
			) 
		);				
	}
}
 