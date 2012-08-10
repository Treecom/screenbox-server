<?php

/*
 * FileRelation Model Class
 * 
 * Files Reletations betwen components. 
 * 
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2011 Treecom s.r.o.
 */

class FileRelation extends AppModel {
	
		/**
		 * Model Name
		 */
		var $name = 'FileRelation';
		
		function addRelation($fileId, $contextId, $domain = null){
 			$this->create();
			$this->bindUsers();
 			$this->data = array(
				'FileRelation' => array(
					'file_id' => $fileId,
					'context_id' => $contextId,
					'domain_id' => $domain
				)
			);
 			$this->addCUTime();							
			return $this->save();
		}
		
		// @todo implement!
		function removeFile($fileId){
			 
		}
		
		// @todo implement!
		function removeContext($contextId){
			 
		}
}
