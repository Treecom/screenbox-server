<?php 
/**
 *  Screenboxserver component
 *
 *  Screen box server component for controller.
 *
 *  @author Martin Bucko, Treeecom s.r.o.
 *  @copyright Copyright (c) 2011-2012 Treecom s.r.o.
 *  @created 11.10.2011
 */
 
ignore_user_abort(true);
set_time_limit(1800);
App::import('Core','File');

class ScreenboxserverComponent extends Object {

    /**
     * @var string Component name
     */
    var $name = "Screenboxserver";
    
    /**
     * @var object Screenboxserver model object
     */
    var $Screenboxserver = null;
    
    /**
     * @var array Load other components for use
     */
    var $components = array();
	
	/**
	 * @var integer deafult pagination limit 
	 */
	var $paginationLimit = 10;
    
    /**
     * __construct
     * Constructor load model page
     * @return
     */
    function __construct() {
    }
    
    /**
     * initilize
     * The initialize method is called before the controller's beforeFilter method.
     * @params object $controller with components to initialize
     * @return void
     */
    function initialize(&$controller, $settings = array()) {
    }
    
    /**
     *  startup
     *  The startup method is called after the controller's beforeFilter method but before the controller executes the current action handler.
     *  @params object $controller
     *  @return void
     */
    function startup(&$controller) {
    }
    
    
    /* ------------ Elements functions --------------------- */
    
    /**
     *  getScreenboxserver
     *  [Element method]
     *  
     *  Screenboxserver item view element method.
     *  
     *  @params object Controller with components to shutdown
     *  @return array
     */
    function getPlaylist(&$controller, &$element) {
    	$data = $opt = array();
		$box = null;
		
		$controller->loadModel('Screenbox');
		$controller->loadModel('Medias');
		$controller->loadModel('ScreenboxLog');
		$controller->loadModel('ScreenboxPlaytimeLog');
		
		// set id from elements custom properties or from route id (/1234_example.html)
		if (!empty($controller->params['named']['box'])){
    		$box = strtoupper($controller->params['named']['box']);
    	}
		
		if (!empty($box)){

			$opt = array(
				'conditions'=>array('Screenbox.key'=>"$box"),
				'fields'=>array('Screenbox.id','Screenbox.name','Screenbox.company_id','Screenbox.width','Screenbox.height', 'Screenbox.public', 'Screenbox.config'),
				'recusive'=>3
			);
			
			$controller->Screenbox->unbindUsers();
			
			$data = $controller->Screenbox->find('first', $opt);
						
			if (!empty($data['Screenbox']['company_id'])){
				
				// LogError(print_r($controller->data, true));
				// log box status
				if (!empty($controller->data['ScreenboxLog'])){
					$controller->data['ScreenboxLog']['screenbox_id'] = $data['Screenbox']['id'];
					$controller->data['ScreenboxLog']['log_time'] = time(); 
					$controller->ScreenboxLog->import($controller->data['ScreenboxLog']);
				}
				
				// log media
				if (!empty($controller->data['ScreenboxPlaytimeLog'])){
					$controller->ScreenboxPlaytimeLog->import($controller->data['ScreenboxPlaytimeLog'], $data['Screenbox']['id']);
				}
 				
				$opt = array(
					'conditions'=>array('Medias.company_id'=>$data['Screenbox']['company_id'],'Medias.public'=>1),
					'recusive'=>2
				);
				
				$opt['conditions'] = array_merge($controller->Medias->getTimePublicArray(), $opt['conditions']);
				
				$controller->Medias->unbindUsers();
				$controller->Medias->bindModel(
					array(
						'hasMany' => array(
							'ScreenboxPlaytime' => array()
						),
						'hasOne' => array(
							'MediaFormat' => array(
								'conditions'=> array('MediaFormat.ready'=>1, 'MediaFormat.screenbox_id'=>$data['Screenbox']['id'])
							)
						))
				);
				$media = $controller->Medias->find('all', $opt);
 
 				$data['Media'] = array();
				$domain = 'http://' . Configure::read('Domain.name');
				
				foreach($media as $m){
					if (!empty($m['MediaFormat']['file_name'])){
						$data['Media'][] = array(
							'id' => $m['Medias']['id'],
							'file_id' => $m['FileStore']['id'],
							// 'file_name' => $m['FileStore']['file_name'],
							'file_name' => $m['MediaFormat']['file_name'],
							// 'file_path' =>  $domain . $m['FileStore']['path'] . $m['FileStore']['file_name'],
							'file_path' =>  $domain . '/files/media/'. $m['MediaFormat']['file_name'],
							'play' => $m['Medias']['play'],
							'public' => $m['Medias']['public'],
							'priority' => $m['Medias']['priority'],
							// 'width' => $m['Medias']['width'],
							// 'height' => $m['Medias']['height'],
							// 'format' => $m['Medias']['format']
							'ScreenboxPlaytime' => $m['ScreenboxPlaytime']
						);
					}
				}
			}
 		}
		return $data;
    }
	
	
	/**
	 * example link:  /down/box:00012E1FDA91/media:42/get.json
	 */
	function getDownloader(&$controller, &$element) {
		
		$data = array();
		$controller->loadModel('MediaFormat');
		$controller->loadModel('Screenbox');
		$id = $box = null;
		$error = true;
		
		// set id from elements custom properties or from route id (/1234_example.html)
		if (!empty($controller->params['named']['box'])){
    		$box = strtoupper($controller->params['named']['box']);
    	}

		if (!empty($controller->params['named']['media'])){
    		$id = intval($controller->params['named']['media']);
    	}

		$opt = array(
			'conditions'=>array('Screenbox.key'=>"$box","Screenbox.public"=>1),
			'fields'=>array('Screenbox.id'),
			'recusive'=>-1
		);
		
		$controller->Screenbox->unbindUsers();
		$result = $controller->Screenbox->find('first', $opt);
		
		if (!empty($result['Screenbox']['id']) && $id>0){
			$opt = array(
				'conditions'=>array('MediaFormat.media_id'=>$id, 'MediaFormat.ready'=>1, 'MediaFormat.screenbox_id'=>$result['Screenbox']['id']),
				'fields' => array('MediaFormat.id', 'MediaFormat.file_name'),
				'recusive'=>-1
			);	
			$result = $controller->MediaFormat->find('first', $opt);
			
			if (!empty($result['MediaFormat']['file_name'])){
				// redirec to media
				$controller->MediaFormat->id = $result['MediaFormat']['id']; 
				$controller->MediaFormat->saveField('downloaded', 1);
				$controller->redirect('/files/media/'.$result['MediaFormat']['file_name']);
				$error = false;
				exit();
			} else {
				$error = true;
			}
		}
		
		if ($error){
			// 404!
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		
		return $data;
	}
	
	function getUpload(&$controller, &$element) {
		$data = array();
		$controller->loadModel('Miedias');
		return $data;
	}
    
	function getEditMedia(&$controller, &$element) {
		$data = array();
		$controller->loadModel('Miedias');
		if (!empty($controller->params['named']['id'])){
			$id = intval($controller->params['named']['id']);
		}
		
		if ($controller->data){
			if ($controller->Miedias->save($controller->data)){
				// ok
			}
		}
		
		if ($id>0){
			$data = $controller->Miedias->findById($id);
		}
		return $data;
	}
	
	function getMedia(&$controller, &$element) {
		
		$controller->loadModel('Miedias');
		
		$element['properties']['limit'] = $limit = !empty($element['properties']['limit']) ? $element['properties']['limit'] : $this->paginationLimit;

		$data = array();
		$conditions = array();
		
		// setup pagination properties
		$controller->paginate['Medias'] = array(
			'conditions' => array(),
			'limit' => $limit,
			// 'recursive'=>1
		);
		
		$data = $controller->paginate('Miedias'); 
		return $data;
	}
	
	function getMediaPreview(&$controller, &$element) {
		$data = array();
		$controller->loadModel('Miedias');
		if (!empty($controller->params['named']['id'])){
			$id = intval($controller->params['named']['id']);
		}
		if ($id>0){
			$data = $controller->Miedias->findById($id);
		}
		return $data;
	}
	
	function getScreenboxes(&$controller, &$element) {
		$data = array();
		$controller->loadModel('MiediaBox');
		
		$element['properties']['limit'] = $limit = !empty($element['properties']['limit']) ? $element['properties']['limit'] : $this->paginationLimit;

		$data = array();
		$conditions = array();
		
		// setup pagination properties
		$controller->paginate['MiediaBox'] = array(
			'conditions' => array(),
			'limit' => $limit,
			// 'recursive'=>1
		);
		
		$data = $controller->paginate('MiediaBox'); 
		return $data;
	}
	
	function getEditScreenbox(&$controller, &$element) {
		$data = array();
		$controller->loadModel('Screenbox');
		$id = 0;
		if (!empty($controller->params['named']['id'])){
			$id = intval($controller->params['named']['id']);
		}
		if ($id>0){
			$data = $controller->Screenbox->findById($id);
		}
		return $data;
	}
	
	function convertMedia(&$controller, &$element) {
		$data = array('nothing to convert');
		
		$controller->loadModel('MediaFormat');
		$controller->loadModel('FileStore');
		
		$data = $controller->MediaFormat->query('
			SELECT width, height, file_name, file_id, format 
			FROM media_formats as MediaFormat 
			WHERE ready = 0 
			GROUP BY width, height, file_name, file_id, format 
			LIMIT 1
		');
		
		if (!empty($data)){
			foreach($data as $m){
				$m = $m[0];
				if ($m['file_id']){
					$file = $controller->FileStore->findById($m['file_id']);
					if (!empty($file)){
						
						$up = array('width'=>$m['width'], 'height'=>$m['height'], 'file_id'=>$m['file_id']);
						$controller->MediaFormat->updateAll(array('ready'=>2),$up);

						$wh = $m['width'].'x'.$m['height'];
						$wh2 = $m['width'].':'.$m['height'];
						//$wh2 = $m['width'].':-1'; // ratio in height! 
						
						$ftout = WWW_ROOT.'/files/media/tmp_'. $m['file_name'];
						$fout = WWW_ROOT.'/files/media/'. $m['file_name'];
						$fin = WWW_ROOT.$file['FileStore']['path'].$file['FileStore']['file_name'];
						
						// $vo = ' -vcodec libx264 -vpre lossless_fast -maxrate 120000M';
						// $vo = ' -vcodec libx264 -vpre fast -cqp 3 -crf 15 -maxrate 80000 ';
						// $vo = ' -vcodec libx264 -vpre lossless_fast -cqp 5 -b 8000 ';
						// $vo = ' -vcodec libx264 -vpre veryfast -cqp 5 -crf 15 -maxrate 80000 ';
						// $vo = ' -vcodec libx264 -vpre lossless_fast -maxrate 8000 ';
						
						// $vo = ' -vcodec libx264 -vpre fast -crf 15 ';
						// $vo = ' -vcodec libx264 -vpre lossless_ultrafast -crf 22 ';
						
						$vo = ' -vcodec libx264 -vpre veryfast -crf 22 ';
						
						$scale = ' -vf "scale='.$wh2.'" ';
						// $ao = ' -acodec libfaac -ac 2 -ar 48000 -ab 192k ';
						$ao = ' -acodec libfaac ';
						
						$cmd = 'nice ffmpeg -y -i '. $fin . $vo . $scale . $ao . $fout; // -crf 0 -qcomp 0
						$ready = $out = 0;
					 
						$log = system($cmd, $out);
						
						fb($cmd);
						fb($out);
						fb($log);
						
						if ($out==0 && file_exists($fout)){
							if (filesize($fout)>1){
								$data = $up;
								$controller->MediaFormat->updateAll(array('ready'=>1),$up);
								$ready = 1;
							 }
						} 
						if ($ready != 1){
							$controller->MediaFormat->updateAll(array('ready'=>0),$up);
						}
					}	
				}
			}
		}
				
		return $data;
	}


	function getMakeStats(&$controller, $element = null){
			
		$controller->loadModel('Medias');
		$controller->loadModel('Screenbox');	
		$controller->loadModel('ScreenboxLog');
		$controller->loadModel('ScreenboxPlaytimeLog');
	
		
	} 
	
    /* ------------ Admin functions --------------------- */

    	/**
	 * admin_upload
	 * Upload one file from admin. Data is parsed from POST in $controller->params['form']['Filedata']. Return 1 if successfull or 0 if not.
	 * @param object $controller
	 * @return 
	 */
	function upload(&$controller, &$element = null) {
 		$data = array();
		if (!empty($controller->params['form']['file'])){
			$controller->loadModel('FileStore');					
			$contextId = empty($controller->params['form']['context_id']) ? 0 : intval($controller->params['form']['context_id']);
			$file['FileStore'] = $controller->params['form']['file'];		
			$file['FileStore']['file_name'] = $file['FileStore']; // must be array!		
			$file['FileStore']['context_id'] = $contextId;
		
			$controller->FileStore->create();
			if ($controller->FileStore->save($file)){
				// save Context File Relation 
				$id = $controller->FileStore->getInsertId();
				if ($contextId>0 && $id>0){
					$controller->loadModel('ContextCpRelation');
					$controller->ContextCpRelation->makeRelation($contextId, 'files', $id);
				}
				
				if ($id>0){
					return $controller->FileStore->read(null, $id);
				} 
	 		}
			
			if (!empty($controller->FileStore->validationErrors)){
				LogError('Error on File::upload:' . print_r($controller->FileStore->validationErrors,true));
			}
		}
 			
		return $data;
	}

    
    /**
     * admin_getScreenboxservers
     * @param object $controller
     * @return array
     */
    function admin_getMedia(&$controller) {
    
    /*
        // ACL list check for user permissions
       	if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'read')) {
        	return $controller->unAuth();
        }
	*/	
        $opt = array();
		$controller->loadModel('Medias');
		$controller->Medias->bindUsers();
		// $controller->Medias->bindCompany();
		
		// set limit for query
        $opt = $controller->Medias->setLimit($controller->params['form']);
        
		// query search conditions
        if (!empty($controller->params['form']['q'])) {
            $opt['conditions'] = array('lower(Medias.name) like'=>'%'.low($controller->params['form']['q']).'%');
        } else {
            $opt['conditions'] = array();
        }
        
		$controller->Medias->virtualFields['formats_ready'] = '(SELECT count(*) FROM media_formats  WHERE media_id = Medias.id  AND ready = 1)';
		$controller->Medias->virtualFields['formats_count'] = '(SELECT count(*) FROM media_formats  WHERE media_id = Medias.id)';
		$controller->Medias->virtualFields['formats_downloaded'] = '(SELECT count(*) FROM media_formats  WHERE media_id = Medias.id  AND downloaded = 1)';
		
		// find query
        $data = $controller->Medias->find('all', $opt);
		
		// modify users info
        $data = $controller->Medias->itemUsers($data);
		// $data = $controller->Medias->itemCompany($data);
		
		$data = Set::extract($data, '{n}.Medias');
		
		// extract only Media item, no other like users ets.
		$data = array('Media' => $data);
		// add counter
		$data['count'] = $controller->Medias->find('count', array('conditions'=>$opt['conditions']));
		 
        return $data;
    }
    /**
     * admin_getScreenboxserverById
     * @param object $controller
     * @return array
     */
    function admin_getMediaById(&$controller) {
    	
        // ACL list check for user permissions
        if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'read')) {
            return $controller->unAuth();
        }
		
		$controller->loadModel('Medias');
		$controller->loadModel('MediaFormat');
		$controller->loadModel('ScreenboxPlaytime');
		$id = intval($controller->params['form']['id']);
		
        if ($id > 0) {
            $result = $controller->Medias->findById($id);          
			
			$f = $controller->MediaFormat->find('all',
				array('conditions' =>
					array('MediaFormat.media_id'=>$id),
				array('fields'=>'screenbox_id')
			));
			if (!empty($f)){
				$result['Medias']['boxes-tmp'] = join(',', (array) Set::extract('/MediaFormat/screenbox_id', $f));
			}
			
			/* play time */
			$pt = $controller->ScreenboxPlaytime->getFormated($id);
			if (!empty($pt)){
				$result['Medias']['playtime-tmp'] = join(',', $pt);
			}
			
			/* play days */
			$f = $controller->ScreenboxPlaytime->find('all',
				array(
					'conditions' => array('ScreenboxPlaytime.media_id'=>$id),
					'fields'=>'ScreenboxPlaytime.day',
					'group'=>'ScreenboxPlaytime.day',
					'order' => 'ScreenboxPlaytime.day ASC'
			));
			if (!empty($f)){
				$result['Medias']['days'] = join(',', (array) Set::extract('/ScreenboxPlaytime/day', $f));
			}
			$controller->data['Media']  = $result['Medias'];
            return array('success'=>true, 'data'=>$result['Medias']);
        } else {
            return array('success'=>false);
        }
    }
	
	
	
    /**
     * admin_setScreenboxserver
     * @param object $controller
     * @return array
     */
    function admin_setMedia(&$controller) {
        
		$out = array('succes'=>false, 'msg'=>__('There was an error saving data to server...', true));
		/*
        // ACL list check for user permissions
        if (! empty($controller->params['form']['id'])) {
            if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'update')) {
                return $controller->unAuth();
            }
        } else {
            if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'create')) {
                return $controller->unAuth();
            }
        }
		*/

		$controller->loadModel('Medias');
        $out = array('succes'=>false, 'errorMessage'=>__('There was an error saving data to server...', true));
        $tableId = 0;
        // save data
        if (!empty($controller->params['form']['id']) || isset($controller->params['form']['name'])) {
        	
        	if (!empty($controller->params['form']['file']['name'])){
         		$controller->params['form']['file_id'] = $this->upload($controller);
         	}
			if (isset($controller->params['form']['public_from_time']) && empty($controller->params['form']['public_from_time'])) $controller->params['form']['public_from_time'] = null;
			if (isset($controller->params['form']['public_to_time']) && empty($controller->params['form']['public_to_time'])) $controller->params['form']['public_to_time'] = null; 
			
            // save data
            if ($controller->Medias->save($controller->params['form'])) {
                // return out with new id
                $tableId = empty($controller->params['form']['id']) ? $controller->Medias->getLastInsertId() : $controller->params['form']['id'];
                $out = array('success'=>true, 'id'=>intval($tableId));
            }
        }
        return $out;
    }

   /**
     * admin_deleteScreenboxserver
     * @param object $controller
     * @return array
     */
    function admin_deleteMedia(&$controller) {
        
		if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'delete')){
            return $controller->unAuth();
        }
		$controller->loadModel('Medias');
        $out = array('success'=>false);
        $id = intval($controller->params['form']['id']);
		
        if ($id > 0) {
            $out['success'] = $controller->Medias->delete($id, true);
        }
        return $out;
    }

/**
     * admin_getScreenboxservers
     * @param object $controller
     * @return array
     */
    function admin_getScreenboxes(&$controller) {
    
    /*
        // ACL list check for user permissions
       	if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'read')) {
        	return $controller->unAuth();
        }
        */
		
		$controller->loadModel('Screenbox');
		$controller->Screenbox->bindUsers();
		// $controller->Screenbox->bindCompany();
        $opt = array();
		
		// set limit for query
        $opt = $controller->Screenbox->setLimit($controller->params['form']);
        
		// query search conditions
        if (!empty($controller->params['form']['q'])) {
            $opt['conditions'] = array('lower(Screenbox.id) like'=>'%'.low($controller->params['form']['q']).'%');
        } else {
            $opt['conditions'] = array();
        }
		
		$controller->Screenbox->virtualFields['last_sync'] = '(SELECT log_time FROM screenbox_logs  WHERE  screenbox_id = Screenbox.id ORDER BY log_time DESC LIMIT 1)';
        
		// find query
        $data = $controller->Screenbox->find('all', $opt);
		
		// modify users info
        $data = $controller->Screenbox->itemUsers($data);
		//$data = $controller->Screenbox->itemCompany($data);
        
		// extract only Screenbox item, no other like users ets.
		$data = array('Screenbox' => Set::extract($data, '{n}.Screenbox'));
		// add counter
		$data['count'] = $controller->Screenbox->find('count', array('conditions'=>$opt['conditions']));
		 
        return $data;
    }
    /**
     * admin_getScreenboxserverById
     * @param object $controller
     * @return array
     */
    function admin_getScreenboxById(&$controller) {
    	
    	/*
        // ACL list check for user permissions
        if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'read')) {
            return $controller->unAuth();
        }
		*/

		$controller->loadModel('Screenbox');
		
        if ($controller->params['form']['id'] > 0) {
            $result = $controller->Screenbox->findById(intval($controller->params['form']['id']));
            $controller->data = $result;
            return array('success'=>true, 'Screenbox'=>$result['Screenbox']);
        } else {
            return array('success'=>false);
        }
    }
	
    /**
     * admin_setScreenboxserver
     * @param object $controller
     * @return array
     */
    function admin_setScreenbox(&$controller) {
        
		$out = array('succes'=>false, 'msg'=>__('There was an error saving data to server...', true));
		
        // ACL list check for user permissions
        if (! empty($controller->params['form']['id'])) {
            if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'update')) {
                return $controller->unAuth();
            }
        } else {
            if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'create')) {
                return $controller->unAuth();
            }
        }
		
		$controller->loadModel('Screenbox');
        $out = array('succes'=>false, 'errorMessage'=>__('There was an error saving data to server...', true));
        $tableId = 0;
		
		if (!empty($controller->params['form']['config'])){
			$controller->params['form']['config'] = $controller->Json->decodeToArray($controller->params['form']['config']);
		}
		
        // save data
        if (!empty($controller->params['form']['id']) || isset($controller->params['form']['company_id'])) {
            // save data
            if ($controller->Screenbox->save($controller->params['form'])) {
            	 $tableId = empty($controller->params['form']['id']) ? $controller->Screenbox->getLastInsertId() : $controller->params['form']['id'];
				
                // return out with new id
                $out = array('success'=>true, 'id'=>$tableId);
            }
        }
        return $out;
    }
	
    /**
     * admin_deleteScreenboxserver
     * @param object $controller
     * @return array
     */
    function admin_deleteScreenbox(&$controller) {
        
		if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'delete')){
            return $controller->unAuth();
        }
        $out = array('success'=>false);
        $id = intval($controller->params['form']['id']);
		$controller->loadModel('Screenbox');
		
        if ($id > 0) {
            $out['success'] = $controller->Screenbox->delete($id, true);
        }
        return $out;
    }


	/**
     * admin_getScreenboxserverById
     * @param object $controller
     * @return array
     */
    function admin_getCompanyBoxes(&$controller) {
    	
        // ACL list check for user permissions
        if (!$controller->isAuthorized('Controllers/Admin/Screenboxserver', 'read')) {
            return $controller->unAuth();
        }
		
		$controller->loadModel('Screenbox');
		
        if ($controller->params['form']['company_id'] > 0) {
            $result = $controller->Screenbox->getByCompany($controller->params['form']['company_id'], array('id','name'));
			$result = Set::classicExtract( $result, '{n}.Screenbox');
			// $result = array('Screenboxes'=>$result);
            return array('success'=>true, 'data'=> $result,'count'=>1);
        } else {
            return array('success'=>false);
        }
    }
}
