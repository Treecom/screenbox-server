<?php

/**
 * Company Model Class
 * 
 * Companies data.
 * 
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 */

class Company extends AppModel {
	/**
	 * Model name
	 * @var string
	 */
	var $name = 'Company';
	
	
	/**
	 * beforeValidate
	 * Use this callback to modify model data before it is validated, or to modify validation rules if required. This function must also return true, otherwise the current save() execution will abort.
	 * @return boolean
	 */
	function beforeValidate(){
		
		// users data to item
		// see app_model.php
		$this->addCUTime();
		
		$this->cleanFields($this, array('name','about'));
		
		return true;
	}
}
