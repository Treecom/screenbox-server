<?php

/**
 * MediaFormat Model Class
 * 
 * Media formats for screenboxes. One file medium can havediferent sizes or formats. Formats are creted from screenboxes properties.
 * 
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2011 Treecom s.r.o.
 */

class MediaFormat extends AppModel {
	/**
	 * @var string model name
	 */
	var $name = 'MediaFormat';
	
	/**
	 * @var string use this table, required only if have other name as model name in plural 
	 */
	// var $useTable = 'examples';	

	/**
	 * Model display field
	 * @var string
	 */
 	var $displayField = 'format';
	
	/**
	 * @var string (or array) The column name(s) and direction(s) to order find results by default.
	 */
	var $order = "MediaFormat.id DESC"; 

	/**
	 * @var array validation rules
	 */
	var $validate = array();
	
	/**
	 * @var array Using virtualFields
	 */
	var $virtualFields = array(
    	// 'full' => "CONCAT('<b>', MediaFormat.title, '</b><br>', MediaFormat.description)"
	);
	
	/**
	 * @var array Behaviors (like TranslateIt, Upload, Serialized, etc.)
	 */
	var $actsAs = array();
	
	/**
	 * Constructor
	 */
	function __construct($id = false, $table = null, $ds = null) {
		 parent::__construct($id, $table, $ds);
		 
		 // fill validation rulez with transalated massages
		 $this->validate = array(
				'width' => array (
		 			'rule' => 'notEmpty',
					'required' => true,
					'massage' => __('This field is required', true)
				),
				'height' => array (
		 			'rule' => 'notEmpty',
					'required' => true,
					'massage' => __('This field is required', true)
				)  
		 );
	} 
	
	/**
	 * Called before any find-related operation.
	 * @param array $queryData
	 * @return boolean
	 */
	function beforeFind($queryData){
		// bind users to add item manipulations info
		// for make better performance is bether to us it in Component only if needed.
		// $this->bindUsers();
		return true;
	}
	/**
	 * Use this callback to modify results that have been returned from a find operation, or to perform any other post-find logic. 
	 * The $results parameter passed to this callback contains the returned results from the model's find operation.
	 * @param array $results
	 * @param boolean $primary
	 * @return array modified results
	 */
	function afterFind($results, $primary){
		return $results;
	}
	
	/**
	 * beforeValidate
	 * Use this callback to modify model data before it is validated, or to modify validation rules if required. This function must also return true, otherwise the current save() execution will abort.
	 * @return boolean
	 */
	function beforeValidate(){
		// users data to item
		// see app_model.php
		// $this->addCUTime();
		$this->cleanFields($this, array('format'));
		return true;
	}
	
	/**
	 * Place any pre-save logic in this function. This function executes immediately after model data has been successfully validated, but just before the data is saved. This function should also return true if you want the save operation to continue.
	 * @return boolean 
	 */
	function beforeSave(){
		return true;
	}
	
	/**
	 * If you have logic you need to be executed just after every save operation, place it in this callback method. The value of $created will be true if a new record was created (rather than an update).
	 * @param boolean $created
	 * @return void
	 */
	function afterSave($created){
		
	}
	
	/**
	 * Place any pre-deletion logic in this function. This function should return true if you want the deletion to continue, and false if you want to abort. 
	 * The value of $cascade will be true if records that depend on this record will also be deleted.
	 * @param object $cascade
	 * @return boolean 
	 */
	function beforeDelete($cascade){
		return true;
	}
	/**
	 * Place any logic that you want to be executed after every deletion in this callback method.
	 * @return void 
	 */
	function afterDelete(){
		
	}
	
	/**
	 * Called if any problems occur.
	 * Can be used allso for errors loging and etc.
	 * @return void
	 */
	function onError(){
		LogError('Error in model MediaFormat!');
	}
}