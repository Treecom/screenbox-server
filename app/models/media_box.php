<?php

/**
 * MediaBox Model Class
 * 
 * This is example model for example component. You can used as template for new models. 
 * 
 * Description for models call backs is from cakephp online book.
 * http://book.cakephp.org/view/76/Callback-Methods
 * 
 * More about models:
 * http://book.cakephp.org/view/66/Models
 * http://api13.cakephp.org/class/model
 * 
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2011 Treecom s.r.o.
 */

class MediaBox extends AppModel {
	/**
	 * @var string model name
	 */
	var $name = 'MediaBox';
	
	/**
	 * @var string use this table, required only if have other name as model name in plural 
	 */
	// var $useTable = 'examples';	

	/**
	 * Model display field
	 * @var string
	 */
 	var $displayField = 'id';
	
	/**
	 * @var string (or array) The column name(s) and direction(s) to order find results by default.
	 */
	var $order = "MediaBox.id DESC"; 

	/**
	 * @var array validation rules
	 */
	var $validate = array();
	
	/**
	 * @var array Using virtualFields
	 */
	var $virtualFields = array(
    	// 'full' => "CONCAT('<b>', MediaBox.title, '</b><br>', MediaBox.description)"
	);
	
	/**
	 * @var array Behaviors (like TranslateIt, Upload, Serialized, etc.)
	 */
	var $actsAs = array();
	
	
	var $defaultConfig = array();
	
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
		 
 		 $this->defaultConfig = array(
 				"volume" => 1,
 				"confInterval"=> (1000*30),
				"plsReload" => (1000*30),				
				"playlogInterval" => (1000*60),	
				"ratio"=>false,		 
				"x" => 0,
				"y" => 0,				
				"streamAudio" => false,	
				"streamAudioVolume" => 1,				
				"streamAudioUrl" => '',
 				"streamAudioProxy" => true,			
				"loger" => true,
				"logToAlert" => false,
				"playerClipTween" => false
			);
	} 
	
	/**
	 * Called before any find-related operation.
	 * @param array $queryData
	 * @return boolean
	 */
	function beforeFind($queryData){
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
		if ($primary && !empty($results[0]['MediaBox']['config'])){
			$results[0]['MediaBox']['config'] = unserialize($results[0]['MediaBox']['config']);
		} 	
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
		$this->addCUTime();
		$this->cleanFields($this, array('key','description'));
		return true;
	}
	
	/**
	 * Place any pre-save logic in this function. This function executes immediately after model data has been successfully validated, but just before the data is saved. This function should also return true if you want the save operation to continue.
	 * @return boolean 
	 */
	function beforeSave(){
		// $this->data[$this->name]['key'] = strtoupper(substr(md5(time()), 0, 8));
		
		if (!empty($this->data[$this->name]['key'])){
				$this->data[$this->name]['key'] = strtoupper($this->data[$this->name]['key']);
				$this->data[$this->name]['key'] = str_replace(':', '', $this->data[$this->name]['key']);
		}
		 
 		if (empty($this->data[$this->name]['config'])){
			$this->data[$this->name]['config'] = $this->defaultConfig;
		}
		
		if (!empty($this->data[$this->name]['config'])){
			if (is_array($this->data[$this->name]['config'])){
				if (!empty($this->defaultConfig) && is_array($this->defaultConfig)){
					$this->data[$this->name]['config'] = array_merge($this->defaultConfig, $this->data[$this->name]['config']);
					$this->data[$this->name]['config'] = serialize($this->data[$this->name]['config']);
				}
			}
		}
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
	
	function getByCompany($id, $fields = null){
		return $this->find('all', array('conditions'=>array('MediaBox.company_id'=>intval($id)), 'fields'=>$fields));
	}	

	/**
	 * Called if any problems occur.
	 * Can be used allso for errors loging and etc.
	 * @return void
	 */
	function onError(){
		LogError('Error in model MediaBox!');
	}
}