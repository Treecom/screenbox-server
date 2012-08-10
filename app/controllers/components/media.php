<?php
 
/**
 *  MediaComponent
 * 
 *  Component description
 *
 *  @author Martin Bucko (bucko@oneclick.sk)
 *  @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 *  @version 1.0
 *  @created
 *  @modified
 */

class MediaComponent extends Object {
	/**
	 * @var string Component name
	 */
    var $name = "Media";
    
	var $contextLimit = 10;
	/**
     * __construct
     * Constructor load model Gallery 
     * @return void 
     */
	function __construct(){
		$this->Gallery =& ClassRegistry::init('Gallery');
	}
	
	/**
     * init
     * Used to initialize the components for current controller.
     * @params object Controller with components to load
     * @return void
     */
    function init(&$controller) {
        // load components used by this component
        // $this->_loadComponents(array());
    }
    
    
    
    /* Elements */
    
	/**
	 * getGalleryById
	 * Get gallery by id
	 * @param object $controller
	 * @param object $element [optional]
	 * @return array 
	 */
    function getGalleryById(&$controller, &$element = array()){
		
    	if (!empty($controller->params['route']['item_id'])){
    		$element['properties']['pageId'] = intval($controller->params['route']['item_id']);
    	}
		
		if (!empty($controller->params['named']['id'])){
    		$element['properties']['pageId'] = intval($controller->params['named']['id']);
    	}
		if (empty($element['properties']['thumbSize'])){
			$element['properties']['thumbSize'] = 'small';
		}
		
    	if (!empty($element['properties']['pageId'])){
    		$controller->loadModel('Gallery');
			$controller->loadModel('FileStore');
			
			$assoc = array(
			 	'hasOne' =>
				 array(
			        'FilesRelation' =>
			            array(
			                'className'  => 'FilesRelation',			                			                
			            )
				)
	    	);
			
			if (!empty($element['properties']['verticalItems']) && !empty($element['properties']['horizontalItems'])){
				if ($element['properties']['verticalItems']>0 && $element['properties']['horizontalItems']>0){
					$element['properties']['limit'] = $element['properties']['verticalItems'] * $element['properties']['horizontalItems']; 
				}
			} else {
				$element['properties']['verticalItems'] = 0;
				$element['properties']['horizontalItems'] = 4;				
			}
			
			$element['properties']['limit'] = $limit = !empty($element['properties']['limit']) ? $element['properties']['limit'] : 12;
			
			$cond = array(
				'conditions' => array(
					'FilesRelation.table_id' => $element['properties']['pageId'], 
					'FilesRelation.table' => 'galleries'
				),
				'limit'=>$limit
			);
			$controller->FileStore->bindModel($assoc, false);		
			$controller->paginate['FileStore'] = $cond;
    		
			$element['data'] = $controller->Gallery->findById($element['properties']['pageId']);
			$element['data']['Files'] = $controller->paginate($controller->FileStore); //$controller->Gallery->getGalleryFiles($element['properties']['pageId']);
			$element['data']['Files'] = $controller->FileStore->addThumbs($element['data']['Files'], $element['properties']['thumbSize']);
    	}
		return $element['data'];
    }
 	
	/**
	 * getGalleriesByContext
	 * @param object $controller
	 * @param object $element [optional]
	 * @return array data galleries paged list
	 * @todo: rename to getNewsListingByContext
	 */
    function getGalleriesByContext(&$controller, &$element = array()){
    	$data = array();
    	if (isset($controller->params['context_id'])){
    		if (empty($element['properties']['context_id'])){
    			$element['properties']['context_id']  = $controller->params['context_id'];
    		}
			$controller->loadModel('Gallery');
 			$controller->Gallery->bindIcon();
			
			if (!empty($element['properties']['verticalItems']) && !empty($element['properties']['horizontalItems'])){
				if ($element['properties']['verticalItems']>0 && $element['properties']['horizontalItems']>0){
					$element['properties']['limit'] = $element['properties']['verticalItems'] * $element['properties']['horizontalItems']; 
				}
			} else {
				$element['properties']['verticalItems'] = 0;
				$element['properties']['horizontalItems'] = 3;				
			}
			
			$element['properties']['limit'] = $limit = !empty($element['properties']['limit']) ? $element['properties']['limit'] : $this->contextLimit;
			
			$controller->loadModel('ContextCpRelation');
			$rel = $controller->ContextCpRelation->getContextRelations($element['properties']['context_id'], 'galleries');
			
			if (!empty($rel)){
				$controller->paginate['Gallery'] = array(
					'conditions' => array('Gallery.id' => $rel, 'Gallery.public' => true),
					'limit' => $limit
				);
				$data = $controller->paginate('Gallery');
			}  
    	} 
		
		return $data;
    }
	
	
	
	/**
	 * getGalleryItem
	 * Get gallery item by file id. Retrun result with current Gallery and any numbers of next and previous files defined in element properties as limitDown and limitUp.
	 * @param object $controller
	 * @param object $element [optional]
	 * @return array result
	 */
    function getGalleryItem(&$controller, &$element = array()){
		
		if (!empty($controller->params['route']['item_id'])){
    		$element['properties']['pageId'] = intval($controller->params['route']['item_id']);
    	}
		
		$galId = 0;
		$limitDown = $element['properties']['limitDown'] = !empty($element['properties']['limitDown']) ? $element['properties']['limitDown'] : 2;
		$limitUp = $element['properties']['limitUp'] = !empty($element['properties']['limitUp']) ? $element['properties']['limitUp'] : 2;
		$element['properties']['limit'] = $limitDown + $limitUp + 1;
		
		if (!empty($controller->params['named']['id'])){
    		$galId = intval($controller->params['named']['id']);
    	}
		
    	if (!empty($element['properties']['pageId'])){
    		$controller->loadModel('Gallery');
			$controller->loadModel('FileStore');
			$controller->loadModel('FilesRelation');
			
			// gallery
			$element['data'] = $controller->Gallery->findById($galId);
			
			// check current relation id
			$currentRelId = $controller->FilesRelation->find(
				'first', 
				array(
					'conditions' => 
						array(
							'FilesRelation.file_id' => intval($element['properties']['pageId']),
							'FilesRelation.table' => 'galleries',
							'FilesRelation.table_id' => $galId
						), 
					'order'=> 'FilesRelation.id ASC'
				)
			);
			
			if (!empty($currentRelId['FilesRelation']['id'])){
				$currentRelId = $currentRelId['FilesRelation']['id'];
			}

			// associate Model FilesRelation to FilesStore
			$assoc = array(
			 	'hasOne' =>
				 array(
			        'FilesRelation' =>
			            array(
			                'className'  => 'FilesRelation',			                			                
			            )
				)
	    	);
			$controller->FileStore->bindModel($assoc, false);
			$element['data']['Files'] = array();

			// files down selected
			if ($limitDown>0){
			 	$cond = array(
					'conditions' => array(
						'FilesRelation.table_id' => $galId, 
						'FilesRelation.table' => 'galleries',
						'FilesRelation.id <' => intval($currentRelId)
					),
					'limit' => $limitDown,
					'order' => 'FilesRelation.id DESC'
				);

				$add = $controller->FileStore->find('all',$cond);
				if (!empty($add) && is_array($add)){
					$add = array_reverse($add);
					foreach ($add as $arr){
						$element['data']['Files'][] = $arr;
					}
				}
			}
			
			 // files up selected + current file
			if ($limitUp<1){
				$limitUp = 1;
			} else {
				$limitUp += 1;
			}
			if ($limitUp>0){
				$cond = array(
					'conditions' => array(
						'FilesRelation.table_id' => $galId, 
						'FilesRelation.table' => 'galleries',
						'FilesRelation.id >=' => intval($currentRelId)
						
					),
					'limit' => $limitUp,
					'order' => 'FilesRelation.id ASC'
				);
				$add = $controller->FileStore->find('all',$cond);
				if (!empty($add) && is_array($add)){
					foreach ($add as $arr){
						$element['data']['Files'][] = $arr;
					}
				}
			}
		
			// extract and add current gallery item 
			if (!empty($element['data']['Files'])){
				$element['data']['GalleryItem'] = Set::extract('/FileStore[id='.$element['properties']['pageId'].']',$element['data']['Files']);
				if (!empty($element['data']['GalleryItem'][0])){
					
					$element['data']['GalleryItem'] = $element['data']['GalleryItem'][0]['FileStore'];
					$element['data']['GalleryItem'] = $controller->FileStore->addThumbs($element['data']['GalleryItem'],'big');
				}
				$element['data']['Files'] = $controller->FileStore->addThumbs($element['data']['Files'], 'icon');	
			}
    	}
		return $element['data'];
    }
	
	/**
	 * Get one image by id. 
	 * @param object $controller
	 * @param array $element [optional]
	 * @return array data
	 */
	function getImage(&$controller, &$element = array()){
			if (!empty($element['properties']['fileId'])){
				
				if (empty($element['properties']['fileId'])){
					$element['properties']['thumbSize'] = 'small';
				}
				
				$controller->loadModel('FileStore');
				$cond = array(
					'conditions' => array(
						'FilesStore.id' => intval($element['properties']['fileId'])
					),
					'order' => 'FilesStore.id DESC'
				);
				$element['data'] = $controller->FileStore->find('first',array('FilesStore.id' => intval($element['properties']['fileId'])));
				$element['data'] = $controller->FileStore->addThumbs($element['data'], $element['properties']['thumbSize']);	
				return $element['data'];
			}
			return array();
	}
	
	/**
	 * Get Video file item by id 
	 * @param object $controller
	 * @param array $element [optional]
	 * @return array data 
	 */
	function getVideoItem(&$controller, &$element = array()){
		$data = array();
		
		// video resolution		
		if (isset($controller->params['url']['high'])){
			$controller->Session->write('Media.player.resolution', 'high');
		}
		if (isset($controller->params['url']['low'])){
			$controller->Session->write('Media.player.resolution', 'low');
		}
	
		if (!empty($controller->params['route']['item_id'])){
    		$element['properties']['pageId'] = intval($controller->params['route']['item_id']);
    	}
		
		$controller->loadModel('FileStore');
		$data = $controller->FileStore->findById($element['properties']['pageId']);
		$data = $controller->FileStore->addThumbs(array($data),'small');	
		$data = isset($data[0]) ? $data[0] : $data;	
		return $data;
	}
	
	/**
	 * Get Video listing by context
	 *  
	 * @param object $controller
	 * @param object $element [optional]
	 * @return 
	 */
	function getContextVideos(&$controller, &$element = array()){
		$data = array();
		
		if (isset($controller->params['context_id'])){
			$controller->loadModel('FileStore');
			$controller->loadModel('ContextCpRelation');
			$limit = 4*4;
    		if (empty($element['properties']['contextId'])){
    			$element['properties']['contextId']  = $controller->params['context_id'];
    		}
			
			if (!empty($element['properties']['verticalItems']) && !empty($element['properties']['horizontalItems'])){
				if ($element['properties']['verticalItems']>0 && $element['properties']['horizontalItems']>0){
					$limit = $element['properties']['limit'] = $element['properties']['verticalItems'] * $element['properties']['horizontalItems']; 
				}
			} else {
				$element['properties']['verticalItems'] = 4;
				$element['properties']['horizontalItems'] = 4;				
			}
			
			if (empty($element['properties']['thumbSize'])){
				$element['properties']['thumbSize'] = 'small';
			}
			
			$asoc = array('belongsTo' => array(
		                'FileStore' => array(
		                    'className' => 'FileStore',		
							'foreignKey' => 'table_id',											
		                )
		            )
		    );
			
			$controller->ContextCpRelation->bindModel($asoc, false);
			$controller->paginate['ContextCpRelation'] = array(
				'limit' => $limit,
				'order' => array ('FileStore.created_time' => 'DESC')										
			);
			$data = $controller->paginate($controller->ContextCpRelation,  array(
						'ContextCpRelation.context_id'=> $element['properties']['contextId'], 
						'ContextCpRelation.table'=>'files',
						'FileStore.extension' => array('flv','mp4')	
			));
			$data = $controller->FileStore->addThumbs($data, $element['properties']['thumbSize']);
		}
				
		return $data;
	}
	/* Media Admin */
	
	function admin_getGalleries(&$controller){
		$opt = $q = $qc = array();
		$opt = $this->Gallery->setLimit($controller->params['form']);
		$this->Gallery->bindUsers();
		$opt['conditions'] = array();
		
		if (!empty($controller->params['form']['q'])){
			$q = array('lower(I18n__title.content) like'=>'%'.low($controller->params['form']['q']).'%');
			$qc = array('lower(GalleryI18n.content) like'=>'%'.low($controller->params['form']['q']).'%');
		}   
		
		if (!empty($controller->params['form']['context_id'])){
			$ctid = intval($controller->params['form']['context_id']);
				$controller->loadModel('ContextCpRelation');
				$ids = $controller->ContextCpRelation->getContextRelations($ctid, 'galleries');
				if (!empty($ids)){
					$opt['conditions'] = array('Gallery.id'=>$ids);
				} else {
					// dirty hack
					$opt['conditions'] = array('Gallery.id'=>null);
				}
		}
		
		$q['conditions'] = am($opt['conditions'], $q);	
		$data = $this->Gallery->find('all', $q);
		$data = $this->Gallery->itemUsers($data);
		$data = Set::extract($data, '{n}.Gallery');
		
		$q['conditions'] = am($opt['conditions'], $qc);
    	return array(
    			'count' => $this->Gallery->find('count', $q),
    			'Galeries' => $data
    		);
	}
	
	function admin_getGallery(&$controller){
		if ($controller->params['form']['id']>0){
 			$result =  $this->Gallery->findById(intval($controller->params['form']['id']));
 			if (isset($controller->params['form']['contexts'])){
				$controller->loadModel('ContextCpRelation');
				$result['Gallery']['contexts'] = $controller->ContextCpRelation->find(
								'all',
								array('conditions'=>
										array('table_id'=>$controller->params['form']['id'], 'table'=>'galleries')
								)
				);
				
				if (!empty($result['Gallery']['contexts'])){
					$result['Gallery']['contexts'] = Set::extract($result['Gallery']['contexts'], '{n}.ContextCpRelation.context_id' );
				}
			}
    		return array('success'=>true,'data'=>$result['Gallery']);
		} else {
			return array();
		}
	}
	
	function admin_setGallery(&$controller){
		
		if (!empty($controller->params['form']['id'])){
			if (!$controller->isAuthorized('Controllers/Admin/Media','update')){
				return $controller->unAuth();
			}
		} else {
			if (!$controller->isAuthorized('Controllers/Admin/Media','create')){
				return $controller->unAuth();
			}
		}
		
		if (isset($controller->params['form']['public'])){
			if (!$controller->isAuthorized('Controllers/Admin/Media/GalleryPublic','update')){
				unset($controller->params['form']['public']);
			}
		}
		
		$out = array('success'=>false,'errorMessage'=>__('There was an error saving data to server...', true));
    	$tableId = 0;
		 
     	if (!empty($controller->params['form']['title']) || isset($controller->params['form']['public'])){
    		if (!empty($controller->params['form']['id'])){
    			$this->Gallery->id = intval($controller->params['form']['id']);				
    		}			
    		if ($this->Gallery->save($controller->params['form'])){
    			if (isset($controller->params['form']['contexts'])){
    				$contexts = $controller->params['form']['contexts'];    				
   					// create ContextCpRelations
	      			if (isset($contexts)){
	   					if (!empty($contexts)){   						
	  						$pct  = is_array($contexts) ? $contexts : $controller->Json->decode($contexts); 	
	  						$tableId = !empty($controller->params['form']['id']) ? $controller->params['form']['id'] : $this->Gallery->getInsertID();						 		
	   						if (is_array($pct) && $tableId>0){ 						 							
	 							$controller->loadModel('ContextCpRelation');
								$controller->ContextCpRelation->makeRelations($pct, 'galleries', $tableId);
	  						}
	 					}
	 				}
 				}
    			$out = array('success'=>true,'id'=>$tableId);
    		} 
    	}
    	
    	return $out;    
	}
	
 
	function admin_setGalleryFiles(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/Media','update')){
			return $controller->unAuth();
		}
		
		$out = array('success'=>false,'errorMessage'=>__('There was an error saving data to server...', true));
    	  
    	if (!empty($controller->params['form']['id']) && !empty($controller->params['form']['files'])){
    		$id = intval($controller->params['form']['id']);
			$files  = $controller->Json->decode($controller->params['form']['files']);		
			
			if (is_array($files) && $id>0){
				$controller->loadModel('FilesRelation');
				foreach ($files as $file){
					$file = intval($file);
					if ($file>0) $controller->FilesRelation->createRelation($file, 'galleries', $id);
				}
				$out = array('success'=>true);
			}
    	}
    	return $out;    
	}
	
	
	function admin_deleteGallery(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/Media','delete')){
			return $controller->unAuth();
		}
		
		$out = array('success'=>false);
		$controller->loadModel('FileRelation');
		$id = intval($controller->params['form']['id']);
		if ($id>0){
			if ($this->Gallery->delete($id)){
				$controller->FileRelation->deleteAll(array('table'=>'gallery','table_id'=>$id));
				$out = array('success'=>true);
			}
		}
		return $out;
	}
	
	function admin_getGalleryFiles(&$controller){
		// @todo: fix msg and translation
		$out = array('success'=>false,'error'=>true,'errorMessage'=>__('There was an error saving data to server...', true));
		
		$opt = array();	 
		$opt = $this->Gallery->setLimit($controller->params['form'], 50);
		
		if (!empty($controller->params['form']['q'])){
			$opt['conditions'] = array('lower(FileStore.name) like'=>'%'.low($controller->params['form']['q']).'%');
		}  
		
		if (!empty($controller->params['form']['id'])){
			$opt['id'] = intval($controller->params['form']['id']);
			return $this->Gallery->getGalleryFilesCounted($opt);
 		}
				
		return $out;
	}
	
	/**
	 * admin_deleteGalleryFiles
	 * Delete one or more files form galleries by ids in params "files" or "items".
	 * @param object $controller
	 * @return array operation result 
	 */
	function admin_deleteGalleryFiles(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/Media','update')){
			return $controller->unAuth();
		}
		
		$out = array('success'=>false);
 
		if (!empty($controller->params['form']['files'])){
			$in = 'files';
			$by = 'file_id';
		}
		
		if (!empty($controller->params['form']['items'])){
			$in = 'items';
			$by = 'table_id';
		}
		
		if (!empty($controller->params['form'][$in])){
			if ($controller->Json->detectJson($controller->params['form'][$in])){
				$controller->params['form'][$in] = $controller->Json->decode($controller->params['form'][$in]);
			}
			if (!is_array($controller->params['form'][$in])){
				$id = intval($controller->params['form'][$in]);	
			} else {
				$id = $controller->params['form'][$in];
			}	
		}
		
		if ($id>0 || is_array($id)){
			if ($this->Gallery->deleteFiles($id, $by)){
				$out = array('success'=>true);
			}
		}
		return $out;
	}
}