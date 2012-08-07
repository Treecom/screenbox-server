<?php

/**
 * Medias Model Class
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

class Medias extends AppModel {
	/**
	 * @var string model name
	 */
	var $name = 'Medias';
	
	/**
	 * @var string use this table, required only if have other name as model name in plural 
	 */
	var $useTable = 'medias';	

	/**
	 * Model display field
	 * @var string
	 */
 	var $displayField = 'name';
	
	/**
	 * @var string (or array) The column name(s) and direction(s) to order find results by default.
	 */
	var $order = "Medias.id DESC"; 

	/**
	 * @var array validation rules
	 */
	var $validate = array();
	
	/**
	 * @var array Using virtualFields
	 */
	var $virtualFields = array(
    	// 'full' => "CONCAT('<b>', Medias.title, '</b><br>', Medias.description)"
	);
	
	/**
	 * @var array Behaviors (like TranslateIt, Upload, Serialized, etc.)
	 */
	var $actsAs = array();
	
	
	var $belongsTo= array(
		'FileStore' => array(
			'foreignKey' => 'file_id'
		)
	);
	
	/**
	 * Constructor
	 */
	function __construct($id = false, $table = null, $ds = null) {
		 parent::__construct($id, $table, $ds);
		 
		 // fill validation rulez with transalated massages
		 $this->validate = array(
		 		'name' => array (
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
		$this->addCUTime();
		$this->cleanFields($this, array('name'));
		return true;
	}
	
	/**
	 * Place any pre-save logic in this function. This function executes immediately after model data has been successfully validated, but just before the data is saved. This function should also return true if you want the save operation to continue.
	 * @return boolean 
	 */
	function beforeSave(){
		
		if (!empty($this->data['Medias']['file_id'])){
		//	$FileStore = Classregistry::init('FileStore');
		//	$file = $FileStore->findById($this->data['Medias']['file_id']);
 		}
		
		return true;
	}
	
	/**
	 * If you have logic you need to be executed just after every save operation, place it in this callback method. The value of $created will be true if a new record was created (rather than an update).
	 * @param boolean $created
	 * @return void
	 */
	function afterSave($created){
		$id = $this->id;
		
		if ($created){
			$id = $this->getLastInsertId();
		}
		
		$MediaBox = Classregistry::init('MediaBox');
		$MediaPlaytime = Classregistry::init('MediaPlaytime');
		$Format = Classregistry::init('MediaFormat');
		$Fs = Classregistry::init('FileStore');
		
		if (empty($this->data['Medias']['convert'])){
			$this->data['Medias']['convert'] = 1;
		}
		
		// save for later otherwise will be reset!
		if (isset($this->data['Medias']['days'])) $days = $this->data['Medias']['days'];
		if (isset($this->data['Medias']['playtimes'])) $playtimes = $this->data['Medias']['playtimes'];
		
		// save media formats
		if (!empty($this->data['Medias']['boxes']) && $id>0){  // && ($created || $this->data['Medias']['convert']==1)
			
			$bid = explode(',', $this->data['Medias']['boxes']);
			
			$this->read(null, $id);
			
			$boxes = $MediaBox->find('all', array(
				'conditions' => array(
					'MediaBox.id' => $bid
				),
				'recursive' => 0,
				'fields' => array('id','width','height','format')
			));

			$formats = array();
			
			if (!empty($boxes)){
				
				foreach($boxes as $b){
					
					$old = $Format->find('first',array(
						'conditions'=>array('media_id'=>$id,'media_box_id'=>$b['MediaBox']['id']),
						'fields' => array('id')
					));
					
					$w = $b['MediaBox']['width'];
					$h = $b['MediaBox']['height'];
					$formats = $b['MediaBox'];
					
					$formats['file_id'] = $this->data['Medias']['file_id'];
					$formats['file_name'] = $this->data['FileStore']['file_name'];
					$ext = $Fs->getExtension($formats['file_name']);
					$fname = str_replace('.'.$ext, '-'.$w.'x'.$h.'.'.$formats['format'], $formats['file_name']);
					$formats['file_name'] = $fname;
					$formats['ready'] = file_exists(WWW_ROOT.'/files/media/'.$fname) ? 1 : 0;
					
					if (empty($old['MediaFormat'])){
						$formats['downloaded'] = 0;
						$formats['media_box_id'] = $b['MediaBox']['id'];
						$formats['media_id'] = $id;
						$formats['id'] = null;
						$Format->create();
						fb('create ..');
					} else {
						$formats['id'] = $Format->id = $old['MediaFormat']['id'];
						fb('saving old ..');	
					}
					
					fb($formats);
					fb($Format->save($formats));
				}
				
				// delete deselected old fromats
				 $Format->deleteAll(array('media_id'=>$id, 'NOT'=>array('media_box_id'=> $bid)), false);
				
			} else {
				// delete all old formats
				$Format->deleteAll(array('media_id'=>$id), false);
			}
		}

		// save playtime
		if ((!empty($playtimes) || !empty($days)) && $id>0){
			$pts = explode(',', $playtimes);
			$days = explode(',', $days);
			$MediaPlaytime->deleteAll(array('media_id'=>$id));
			$ptf = $MediaPlaytime->getPlaytimeFormat();
			// @todo: pridat prazdny cas ak bol zadany iba den! tj od 00:00 do 24:00 ~ alebo zohladnit v selete playlistu...
			
			if (empty($days)){
				$days = array(0);
			}
			
			foreach ((array) $days as $day){
				fb('day');
				foreach ((array) $pts as $pt){
					fb('timer');
					if (isset($ptf[$pt])){
						$in = $ptf[$pt];
					}
					
					$MediaPlaytime->create();
					$data = array(
								'media_id' => $id,
								'time_from' => isset($in[1]) ? $in[1] : 0,
								'time_to' => isset($in[2]) ? $in[2] : 24,
								'day' => $day
					);
					fb($data);
					$MediaPlaytime->save($data);
				}
			}
		}
			
	}
	
	/**
	 * Place any pre-deletion logic in this function. This function should return true if you want the deletion to continue, and false if you want to abort. 
	 * The value of $cascade will be true if records that depend on this record will also be deleted.
	 * @param object $cascade
	 * @return boolean 
	 */
	function beforeDelete($cascade){
		
		if ($this->id>0 && $cascade){
			$MediaPlaytime = Classregistry::init('MediaPlaytime');
			$MediaPlaytime->deleteAll(array('media_id'=>$this->id), false);
			$Format = Classregistry::init('MediaFormat');
			$Format->deleteAll(array('media_id'=>$this->id), false);
			$MediaPlaytimeLog = Classregistry::init('MediaPlaytimeLog');
			$MediaPlaytimeLog->deleteAll(array('media_id'=>$this->id), false);
		}
		
		return true;
	}
	/**
	 * Place any logic that you want to be executed after every deletion in this callback method.
	 * @return void 
	 */
	function afterDelete(){
		
	}

	function getByCompany($id, $fields = null){
		return $this->find('all', array('conditions'=>array('Medias.company_id'=>intval($id)), 'fields'=>$fields));
	}
	
	/**
	 * Called if any problems occur.
	 * Can be used allso for errors loging and etc.
	 * @return void
	 */
	function onError(){
		LogError('Error in model Medias!');
	}
}