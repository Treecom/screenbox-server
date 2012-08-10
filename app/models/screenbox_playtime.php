<?php
/**
 * ScreenboxPlaytime Model Class
 * 
 * Model for Screenbox Playtime or playlist.
 * 
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2011 Treecom s.r.o.
 */

class ScreenboxPlaytime extends AppModel {
	/**
	 * @var string model name
	 */
	var $name = 'ScreenboxPlaytime';
	
	/**
	 * @var string use this table, required only if have other name as model name in plural 
	 */
	// var $useTable = 'examples';	

	/**
	 * Model display field
	 * @var string
	 */
 	// var $displayField = 'title';
	
	/**
	 * @var string (or array) The column name(s) and direction(s) to order find results by default.
	 */
	var $order = "ScreenboxPlaytime.id DESC"; 

	/**
	 * @var array validation rules
	 */
	var $validate = array();
	
	/**
	 * @var array Using virtualFields
	 */
	var $virtualFields = array(
    	// 'full' => "CONCAT('<b>', ScreenboxPlaytime.title, '</b><br>', ScreenboxPlaytime.description)"
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
		 		'media_id' => array (
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
		// $this->cleanFields($this, array('title','description'));
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
	
	function getPlaytimeFormat(){
		$pt = array();
		for ($i = 0; $i < 24; $i++){
			$pt[$i+1] = array($i+1, $i.'00' , ($i+1) .'00'); 
		}
		return $pt;
	}
	
	
	function getFormated($media_id){
		
		$out = array();
		
		if ($media_id>0){
			$times = $this->find('all',array(
				'conditions' => array(
					'ScreenboxPlaytime.media_id' => $media_id
				)
			));
			
			foreach((array) $times as $t){
				$out[] = (intval($t['ScreenboxPlaytime']['time_from'])/100)+1;
			}
		}
		return $out;
	}
	
	/**
	 * Called if any problems occur.
	 * Can be used allso for errors loging and etc.
	 * @return void
	 */
	function onError(){
		LogError('Error in model ScreenboxPlaytime!');
	}
}