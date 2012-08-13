<?php
/**
 * Serialized Model Behavior 
 *
 * @author Martin Bucko, Treecom s.r.o.
 * @copyright Copyright (c) 2011, Treecom s.r.o.
 */
App::import('Component','Json');

class SerializedBehavior extends ModelBehavior {
	/**
	 * defaultOptions
	 * 	- detectJSON [boolean] detect JSON beforeSave for performance default off/false
	 *  - fields for serialization and unserialization (empty)
	 * @var array
	 */
	var $defaultOptions = array(
			'detectJSON' => false,
			'fields' => array()
	);	
	
	/**
	 * Behavior settings
	 * @var array
	 */
	var $settings = array();
	
	/**
	 * @var object Json 
	 */
	var $Json = false;
	
	/**
	 * setup
	 * Setup behavior options. Merge default options with options in model definition.
	 * @param object $Model
	 * @param object $settings
	 * @return 
	 */
	function setup(&$Model, $settings = array()) {
 		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $this->defaultOptions;				
		}
		$this->settings[$Model->alias] = array_merge((array)$this->settings[$Model->alias], (array)$settings);
	}
	/**
	 * afterFind
	 * Unserialize model fields 
	 * 
	 * @param object $Model 
	 * @param array $results 
	 * @return array
	 * @access public
	 */
    function afterFind(&$Model, $results) {
    	
    	$onlyFields = !empty($this->settings[$Model->alias]['fields']) ? $this->settings[$Model->alias]['fields'] : false;
	 
		if (is_array($onlyFields) && !empty($onlyFields)){
			foreach((array) $results as $key => $val){
				foreach ($onlyFields as $field){
					if (!empty($results[$key][$Model->alias][$field])){				
						$results[$key][$Model->alias][$field] = $this->unserializeItems($results[$key][$Model->alias][$field]);
					}
					if (!empty($results[$key][$field])){
 						$results[$key][$field] = $this->unserializeItems($results[$key][$field]);
					}
				}
			}
		} else {
        	$results = $this->unserializeItems($results);
		}
        return $results;
    }
     
	/**
	 * beforeSave
	 * Saves all fields that do not belong to the current Model into 'with' helper model.
	 *
	 * @param object $Model 
	 * @access public
	 */
    function beforeSave(&$Model) {
    	
		$detectJson = ($this->settings[$Model->alias]['detectJSON']==true);
		$onlyFields = $this->settings[$Model->alias]['fields'];
	 
		if (class_exists('JsonComponent') && $detectJson){
			$Json = new JsonComponent();	
			$Json->startup($this); // initialize fix
		} else {
			$Json = false;
		}
 	 	
		$fields = array();
		
		if (is_array($onlyFields) && !empty($onlyFields)){			
			foreach ($onlyFields as $key) {
				if (isset($Model->data[$Model->alias][$key])){
					$fields[$key] = $Model->data[$Model->alias][$key];
				}
			}
		} else {
        	$fields = $Model->data[$Model->alias];
		}
		
        foreach ($fields as $key => $val) {
        	if ($detectJson==true){
 				if ($Json && is_string($val)){
  					if ($Json->detectJson($val)){
						$val = $Json->decodeToArray($val);	
 					}
				}	
        	}
            if(is_array($val)){
                $val = serialize($val);
            }
            $Model->data[$Model->alias][$key] = $val;
        }
 
		return true;
    }

	/**
	 * unserializeItems
	 * Unserializes the fields of an array (if the value itself was serialized) 
	 *
	 * @param array $arr 
	 * @return array
	 * @access public
	 */
    function unserializeItems($arr){
    	
    	if (!is_array($arr) && is_string($arr)){
    		if (strpos($arr, ':')>0){
    			return unserialize($arr);
			} else {
				return $arr;
			}
    	}
					
        foreach($arr as $key => $val){
            if(is_array($val)){
                $val = $this->unserializeItems($val);
            } elseif($this->isSerialized($val)){
                $val = unserialize($val);
            }
            $arr[$key] = $val;
        }
        return $arr;
    }
	
	/**
	 * isSerialized
	 * Checks if string is serialized array/object
	 * 
	 * @param string string to check
	 * @access public
	 * @return boolean 
	 */
    function isSerialized($str) {
    	if (is_string($str) && !empty($str)){
        	return ($str == serialize(false) || @unserialize($str) !== false);
		}
		return false;
    }
	
	function getJson(){
		if (!is_object($this->Json)){
			if (class_exists('JsonComponent') && $detectJson){
				$this->Json = new JsonComponent();	
				$this->Json->startup($this); // initialize fix
			} 
		}
		return $this->Json;
	}
}
