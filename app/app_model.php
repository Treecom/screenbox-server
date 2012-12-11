<?php
/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 * 
 * @author Martin Bucko, Treecom s.r.o.
 * @copyright     Copyright 2010 - 2011 Treecom s.r.o.
 * @package       cake
 * @subpackage    cake.app
 */
class AppModel extends Model {
 	
	/**
	 * locale settings
	 * @var locale
	 */
	var $locale = 'eng';
	
	/**
	 * Cached value from value $locale. Performance optimized.
	 * @var string
	 */
	var $localeCached = null;
	
	/**
	 * @var object Session 
	 */
	var $session;
	
	/**
	 * Persist all models, using for caching models on production server.
	 * If is changed database fields it`s needed to clean persitent cache folder. 
	 * @var boolean
	 */
	// var $persistModel = true;	
	/**
	 * constructor	 
	 * add locale for supported models I18n by domain language
	 */	 
	function __construct($id = false, $table = null, $ds = null){	
  		
		// sessions is not available in models, need to import
		App::import('Core', 'CakeSession');	
		$this->session = new CakeSession();
		
		// set language settings for all models
		$this->setLanguage();
		
		parent::__construct($id, $table, $ds);				
	}
	
	/**
	 * Set current language(locale) in I18n supported models. Result is for performance cached if resused more time as empty.
	 * @param string locale
	 * @return string locale
	 */
	function setLanguage($locale = null) { 
		if (empty($locale)){
			if (empty($this->localeCached)){
				if (class_exists('DomainLoaderComponent')){
					App::import('Component','DomainLoader');
				}
				if (class_exists('DomainLoaderComponent')){
					$loc = DomainLoaderComponent::mapLocale(true);			
					if (!empty($loc['locale'])){
						$this->localeCached = $locale = $loc['locale'];		
					}
				}
			} else {
				$locale = $this->localeCached;
 			}
		}		
		return $this->locale = $locale;
	}
	
	/**
	 * parentNode
	 * get parent node witch is UserGroup 
	 * @return mixed parent node
	 */
	function parentNode() {
	    return null;
	}
	
	/**
	 * getFields
	 * Get current model fields (columns) from model schema with optional parameter for exclude fields.
	 * @param mixed $exclude [optional] array or string if only one field needed to exclude 
	 * @return array fields
	 */
	function getFields($exclude = null){
		
		$fields = $this->_schema;
 		
		if (is_array($fields)){
			$fieldsModel = array_keys($fields);
			
			// compatibility with TranslateIt Behavior
			if (!empty($this->actsAs['TranslateIt'])){
				if (is_array($this->actsAs['TranslateIt'])){
					foreach($this->actsAs['TranslateIt'] as $val){
						array_push($fieldsModel, $val);
					}
				}
			}
			
			foreach ($fieldsModel as $key=>$val){
				$nkey = $this->name .'.'. $val;
				if (is_string($exclude)){
					if ($val!=$exclude && $nkey!=$exclude){
						$fieldsModel[$key] = $nkey;
					} else {
						unset($fieldsModel[$key]);
					}
				}
				if (is_array($exclude)){
					if (!in_array($exclude, $val) && !in_array($exclude, $nkey)){
						$fieldsModel[$key] = $nkey;
					} else {
						unset($fieldsModel[$key]);
					}					
				}
			}
			return $fieldsModel;
		}
		
		return array();						
	}
	
	/**
	 * setLimit
	 * Set limit, offset, order by or sorting for find queries
	 * @param array $set
	 * @param int $count [optional]
	 * @return array limit & offset
	 */
	function setLimit($set, $count = 30, $auto_replace = true){
		if (isset($set['limit'])) 
			$limit = intval($set['limit']) > 0 ? $set['limit'] : $count;
		else 
			$limit = $count;
		
		if (isset($set['start'])) 
			$start = intval($set['start']) > 0 ? $set['start'] : 0;
		else 
			$start = 0;
		
		$order = null;
		if (!empty($set['sort']) && !empty($set['dir'])) {
			
			// auto replace fields 
			if ($auto_replace){
				$set['sort'] = str_replace('created_user_name', 'created_user_id', $set['sort']); 
				$set['sort'] = str_replace('modified_user_name', 'modified_user_id', $set['sort']);
			}
			
			if (strpos($set['sort'], '.')<1){
				$set['sort'] = $this->alias . '.'. $set['sort']; 
			}
			$order =  $set['sort'].' ' . $set['dir'];
		}
		
	   	return array ('limit' => $limit, 'offset' => $start, 'order' => $order);  
	}
 	/**
	 * addCUTime
	 * add Create & Update time by user session in actual model
	 * @param string $model [optional]
	 * @return boolean 	 
	 */	
	function addCUTime($model = false){
 		if (empty($model)){
		   $model = $this->name; 
		}
		if (!empty($this->data[$model])){
	  		if (is_array($this->data[$model])) {
				$user = intval($this->session->read('Auth.User.id'));
				if (empty($this->data[$model]['id']) && empty($this->id)){	
					if (empty($this->data[$model]['created_time'])) 
						$this->data[$model]['created_time'] = time();		
					if (empty($this->data[$model]['created_user_id'])) 	
						$this->data[$model]['created_user_id'] = $user;
				}
				
				if (empty($this->data[$model]['modified_time']))
					$this->data[$model]['modified_time'] = time();
	  			if (empty($this->data[$model]['modified_user_id'])) 
	  				$this->data[$model]['modified_user_id'] = $user;	
				return true;
			}  
		}
		
		return false;
		 		
	}
	
	/**
	 * getTimePublicArray
	 * get item public time field as array for find query
	 * @param integer $publicFrom [optional]
	 * @param integer $publicTo [optional]
	 * @return array query
	 */
	function getTimePublicArray($publicFrom = null, $publicTo = null){
		
		$publicTo = intval($publicTo)>0 ? $publicTo : time();
		$publicFrom = intval($publicFrom)>0 ? $publicFrom : time();
		
		return array(
						array ('OR'=>array($this->name.'.public_from_time <' => $publicFrom, $this->name.'.public_from_time' => null)),
						array ('OR'=>array($this->name.'.public_to_time >' => $publicTo, $this->name.'.public_to_time' => null))
						
		);
	}
	
	/**
	 * itemUsers
	 * convert/add fields from associated user models
	 * required binded CreatedByUser ModifiedByUser and fields: created_user_id,created_time,modified_user_id,modified_time in table
 	 * @param array $data
	 * @param string $format [optional]
	 * @param string $model [optional]
	 * @return array data result
	 */
	function itemUsers($data, $format = false, $model = null){
		
		if ($format===true)	$format = 'Y-m-d H:i';
		
		if (empty($model)){
		   $model = $this->name; 
		}
		if (is_object($model)){
		   $model = $model->name; 
		}
		
		if (!empty($model) && is_array($data)){
 			if (Set::check($data, "0.".$model)){
 				foreach ($data as $key=>$val){
					$data[$key] = $this->itemUsers($val, $format, $this->name);
				}
			} 
			
			if (!empty($data[$model])){
				if (!empty($data['CreatedByUser']) && !empty($data['ModifiedByUser'])){
					$data[$model]['created_user_name']  = $data['CreatedByUser']['first_name']  . ' ' . $data['CreatedByUser']['last_name'];
					$data[$model]['modified_user_name'] = $data['ModifiedByUser']['first_name'] . ' ' . $data['ModifiedByUser']['last_name'];  
				}
				if (is_string($format)){
					if (!empty($data[$model]['created_time'])){
						$data[$model]['created_time'] = date($format, intval($data[$model]['created_time']));
					}
					if (!empty($data[$model]['modified_time'])){
						$data[$model]['modified_time'] = date($format, intval($data[$model]['modified_time']));
					}
				}
			}
		}
		return $data;
	}
 	
	/**
	 * bindUsers
	 * bind associated user models to compatible models
	 * required fields: created_user_id and modified_user_id in table
	 * @return boolean ?
	 */
	function bindUsers(){
 		return $this->bindModel(
			array('belongsTo'=>
					array(
							'CreatedByUser' =>
												array(
													'className'=>'User',
													'foreignKey'=>'created_user_id',
													'fields' => array('id','first_name','last_name','email')												
												),
							'ModifiedByUser' =>
												array(
													'className'=>'User',
													'foreignKey'=>'modified_user_id',
													'fields' => array('id','first_name','last_name','email')													
												)
							)
			), 
			true
		);
	}
	
	/**
	 * unbindUsers
	 * Unbind associated User model in compatible models
	 * @return boolean
	 */
	function unbindUsers(){
		return $this->unbindModel(array('belongsTo'=>array('CreatedByUser','ModifiedByUser')));
	}

	/**
	 * @todo implement AppModel::atachContainable 
	 * @param object $options [optional]
	 * @return boolean ?
	 */
	function atachContainable($options = null){		 
		return $this->Behaviors->attach('Containable',$options);
	}
	
	/**
	 * @todo FIXME
	 * @return boolean ?
	 */
	function detachContainable(){
		return $this->Behaviors->detach('Containable');
	}
	
	/**
	 * Clean one or more fields in model data. Useful for clean data before saving. Function use Sanitize::clean(). 
	 * @param object $model
	 * @param array fields
	 * @return void  
	 */
	function cleanFields(&$model, $fields = array()){
		App::import('Sanitize');
		$defaultCharset = Configure::read('App.encoding');
		$sanitizeOptions = array('remove_html'=>true);
		
		// if not set fiels clean all!
		if (empty($fields) && isset($model->data[$model->name])){ 
			$fields = array_keys($model->data[$model->name]);
		}
			
		foreach ((array)$fields as $f){
			if (isset($model->data[$model->name][$f])){
				if (is_string($model->data[$model->name][$f])){
					$model->data[$model->name][$f] = Sanitize::clean($model->data[$model->name][$f], $sanitizeOptions);
					$model->data[$model->name][$f] = html_entity_decode($model->data[$model->name][$f], ENT_QUOTES, $defaultCharset); // fix
				} 
			}			
		}		
	}
	
	
	/**
	 * seoUrl
	 * Create from string seo url link. 
	 * @param string url
	 * @return string url in seo
	 */
	function seoUrl($url, $separator = '-') {
	    $url = trim($url);
	  	$url = str_replace(' ', $separator, $url);
		$url = str_replace('.', $separator, $url);
		$url = preg_replace('(\-{2,})','',$url);
	    $url = trim($url, "-");
		 if (function_exists('iconv')){
		 	// pozor na setlocale !
 			$url = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $url);
		 }
	    $url = strtolower($url);
	    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
	    return $url;
	}
	
	/**
	 * See Helper::Text
	 */
	function truncateText($text, $length, $options = array()){
		App::import('Helper', 'Text');
		$Text = new TextHelper;
		return $Text->truncate($text, $length, $options);
	}
}
 