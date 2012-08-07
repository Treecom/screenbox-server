<?php 
/**
 *  Mediaserver component
 *
 *
 *  @author Martin Bucko (bucko at treecom dot net)
 *  @copyright Copyright 2010 - 2011 Treecom s.r.o.
 *  @version 1.0
 *  @created 11.10.2011
 */
 
ignore_user_abort(true);
set_time_limit(1800);
App::import('Core','File');

class MediaserverComponent extends Object {

    /**
     * @var string Component name
     */
    var $name = "Mediaserver";
    
    /**
     * @var object Mediaserver model object
     */
    var $Mediaserver = null;
    
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
     *  getMediaserver
     *  [Element method]
     *  
     *  Mediaserver item view element method.
     *  
     *  @params object Controller with components to shutdown
     *  @return array
     */
    function getPlaylist(&$controller, &$element) {
    	$data = $opt = array();
		$box = null;
		
		$controller->loadModel('MediaBox');
		$controller->loadModel('Medias');
		$controller->loadModel('MediaBoxLog');
		$controller->loadModel('MediaPlaytimeLog');
		
		// set id from elements custom properties or from route id (/1234_example.html)
		if (!empty($controller->params['named']['box'])){
    		$box = strtoupper($controller->params['named']['box']);
    	}
		
		if (!empty($box)){

			$opt = array(
				'conditions'=>array('MediaBox.key'=>"$box"),
				'fields'=>array('MediaBox.id','MediaBox.name','MediaBox.company_id','MediaBox.width','MediaBox.height', 'MediaBox.public', 'MediaBox.config'),
				'recusive'=>3
			);
			
			$controller->MediaBox->unbindUsers();
			
			$data = $controller->MediaBox->find('first', $opt);
						
			if (!empty($data['MediaBox']['company_id'])){
				
				// LogError(print_r($controller->data, true));
				// log box status
				if (!empty($controller->data['MediaBoxLog'])){
					$controller->data['MediaBoxLog']['media_box_id'] = $data['MediaBox']['id'];
					$controller->data['MediaBoxLog']['log_time'] = time(); 
					$controller->MediaBoxLog->import($controller->data['MediaBoxLog']);
				}
				
				// log media
				if (!empty($controller->data['MediaPlaytimeLog'])){
					$controller->MediaPlaytimeLog->import($controller->data['MediaPlaytimeLog'], $data['MediaBox']['id']);
				}
 				
				$opt = array(
					'conditions'=>array('Medias.company_id'=>$data['MediaBox']['company_id'],'Medias.public'=>1),
					'recusive'=>2
				);
				
				$opt['conditions'] = array_merge($controller->Medias->getTimePublicArray(), $opt['conditions']);
				
				$controller->Medias->unbindUsers();
				$controller->Medias->bindModel(
					array(
						'hasMany' => array(
							'MediaPlaytime' => array()
						),
						'hasOne' => array(
							'MediaFormat' => array(
								'conditions'=> array('MediaFormat.ready'=>1, 'MediaFormat.media_box_id'=>$data['MediaBox']['id'])
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
							'MediaPlaytime' => $m['MediaPlaytime']
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
		$controller->loadModel('MediaBox');
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
			'conditions'=>array('MediaBox.key'=>"$box","MediaBox.public"=>1),
			'fields'=>array('MediaBox.id'),
			'recusive'=>-1
		);
		
		$controller->MediaBox->unbindUsers();
		$result = $controller->MediaBox->find('first', $opt);
		
		if (!empty($result['MediaBox']['id']) && $id>0){
			$opt = array(
				'conditions'=>array('MediaFormat.media_id'=>$id, 'MediaFormat.ready'=>1, 'MediaFormat.media_box_id'=>$result['MediaBox']['id']),
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
	
	function getMediaBoxes(&$controller, &$element) {
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
	
	function getEditMediaBox(&$controller, &$element) {
		$data = array();
		$controller->loadModel('MediaBox');
		$id = 0;
		if (!empty($controller->params['named']['id'])){
			$id = intval($controller->params['named']['id']);
		}
		if ($id>0){
			$data = $controller->MediaBox->findById($id);
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
		$controller->loadModel('MediaBox');	
		$controller->loadModel('MediaBoxLog');
		$controller->loadModel('MediaPlaytimeLog');
	
		
	} 
	
    /* ------------ Admin functions --------------------- */
    
    /**
     * admin_getMediaservers
     * @param object $controller
     * @return array
     */
    function admin_getMedia(&$controller) {
    
        // ACL list check for user permissions
       	if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'read')) {
        	return $controller->unAuth();
        }
		
        $opt = array();
		$controller->loadModel('Medias');
		$controller->Medias->bindUsers();
		$controller->Medias->bindCompany();
		
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
		$data = $controller->Medias->itemCompany($data);
		
		$data = Set::extract($data, '{n}.Medias');
		
		// extract only Media item, no other like users ets.
		$data = array('Medias' => $data);
		// add counter
		$data['count'] = $controller->Medias->find('count', array('conditions'=>$opt['conditions']));
		 
        return $data;
    }
    /**
     * admin_getMediaserverById
     * @param object $controller
     * @return array
     */
    function admin_getMediaById(&$controller) {
    	
        // ACL list check for user permissions
        if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'read')) {
            return $controller->unAuth();
        }
		
		$controller->loadModel('Medias');
		$controller->loadModel('MediaFormat');
		$controller->loadModel('MediaPlaytime');
		$id = intval($controller->params['form']['id']);
		
        if ($id > 0) {
            $result = $controller->Medias->findById($id);          
			
			$f = $controller->MediaFormat->find('all',
				array('conditions' =>
					array('MediaFormat.media_id'=>$id),
				array('fields'=>'media_box_id')
			));
			if (!empty($f)){
				$result['Medias']['boxes-tmp'] = join(',', (array) Set::extract('/MediaFormat/media_box_id', $f));
			}
			
			/* play time */
			$pt = $controller->MediaPlaytime->getFormated($id);
			if (!empty($pt)){
				$result['Medias']['playtime-tmp'] = join(',', $pt);
			}
			
			/* play days */
			$f = $controller->MediaPlaytime->find('all',
				array(
					'conditions' => array('MediaPlaytime.media_id'=>$id),
					'fields'=>'MediaPlaytime.day',
					'group'=>'MediaPlaytime.day',
					'order' => 'MediaPlaytime.day ASC'
			));
			if (!empty($f)){
				$result['Medias']['days'] = join(',', (array) Set::extract('/MediaPlaytime/day', $f));
			}
			  
            return array('success'=>true, 'data'=>$result['Medias']);
        } else {
            return array('success'=>false);
        }
    }
	
	
	
    /**
     * admin_setMediaserver
     * @param object $controller
     * @return array
     */
    function admin_setMedia(&$controller) {
        
		$out = array('succes'=>false, 'msg'=>__('There was an error saving data to server...', true));
		
        // ACL list check for user permissions
        if (! empty($controller->params['form']['id'])) {
            if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'update')) {
                return $controller->unAuth();
            }
        } else {
            if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'create')) {
                return $controller->unAuth();
            }
        }
		
		$controller->loadModel('Medias');
        $out = array('succes'=>false, 'errorMessage'=>__('There was an error saving data to server...', true));
        $tableId = 0;
        // save data
        if (!empty($controller->params['form']['id']) || isset($controller->params['form']['name'])) {
        	
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
     * admin_deleteMediaserver
     * @param object $controller
     * @return array
     */
    function admin_deleteMedia(&$controller) {
        
		if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'delete')){
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
     * admin_getMediaservers
     * @param object $controller
     * @return array
     */
    function admin_getMediaBoxes(&$controller) {
    
        // ACL list check for user permissions
       	if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'read')) {
        	return $controller->unAuth();
        }
		
		$controller->loadModel('MediaBox');
		$controller->MediaBox->bindUsers();
		$controller->MediaBox->bindCompany();
        $opt = array();
		
		// set limit for query
        $opt = $controller->MediaBox->setLimit($controller->params['form']);
        
		// query search conditions
        if (!empty($controller->params['form']['q'])) {
            $opt['conditions'] = array('lower(MediaBox.id) like'=>'%'.low($controller->params['form']['q']).'%');
        } else {
            $opt['conditions'] = array();
        }
		
		$controller->MediaBox->virtualFields['last_sync'] = '(SELECT log_time FROM media_box_logs  WHERE  media_box_id = MediaBox.id ORDER BY log_time DESC LIMIT 1)';
        
		// find query
        $data = $controller->MediaBox->find('all', $opt);
		
		// modify users info
        $data = $controller->MediaBox->itemUsers($data);
		$data = $controller->MediaBox->itemCompany($data);
        
		// extract only MediaBox item, no other like users ets.
		$data = array('MediaBox' => Set::extract($data, '{n}.MediaBox'));
		// add counter
		$data['count'] = $controller->MediaBox->find('count', array('conditions'=>$opt['conditions']));
		 
        return $data;
    }
    /**
     * admin_getMediaserverById
     * @param object $controller
     * @return array
     */
    function admin_getMediaBoxById(&$controller) {
    	
        // ACL list check for user permissions
        if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'read')) {
            return $controller->unAuth();
        }
		
		$controller->loadModel('MediaBox');
		
        if ($controller->params['form']['id'] > 0) {
            $result = $controller->MediaBox->findById(intval($controller->params['form']['id']));
            
            return array('success'=>true, 'data'=>$result['MediaBox']);
        } else {
            return array('success'=>false);
        }
    }
	
    /**
     * admin_setMediaserver
     * @param object $controller
     * @return array
     */
    function admin_setMediaBox(&$controller) {
        
		$out = array('succes'=>false, 'msg'=>__('There was an error saving data to server...', true));
		
        // ACL list check for user permissions
        if (! empty($controller->params['form']['id'])) {
            if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'update')) {
                return $controller->unAuth();
            }
        } else {
            if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'create')) {
                return $controller->unAuth();
            }
        }
		
		$controller->loadModel('MediaBox');
        $out = array('succes'=>false, 'errorMessage'=>__('There was an error saving data to server...', true));
        $tableId = 0;
		
		if (!empty($controller->params['form']['config'])){
			$controller->params['form']['config'] = $controller->Json->decodeToArray($controller->params['form']['config']);
		}
		
        // save data
        if (!empty($controller->params['form']['id']) || isset($controller->params['form']['company_id'])) {
            // save data
            if ($controller->MediaBox->save($controller->params['form'])) {
            	 $tableId = empty($controller->params['form']['id']) ? $controller->MediaBox->getLastInsertId() : $controller->params['form']['id'];
				
                // return out with new id
                $out = array('success'=>true, 'id'=>$tableId);
            }
        }
        return $out;
    }
	
    /**
     * admin_deleteMediaserver
     * @param object $controller
     * @return array
     */
    function admin_deleteMediaBox(&$controller) {
        
		if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'delete')){
            return $controller->unAuth();
        }
        $out = array('success'=>false);
        $id = intval($controller->params['form']['id']);
		$controller->loadModel('MediaBox');
		
        if ($id > 0) {
            $out['success'] = $controller->MediaBox->delete($id, true);
        }
        return $out;
    }


	/**
     * admin_getMediaserverById
     * @param object $controller
     * @return array
     */
    function admin_getCompanyBoxes(&$controller) {
    	
        // ACL list check for user permissions
        if (!$controller->isAuthorized('Controllers/Admin/Mediaserver', 'read')) {
            return $controller->unAuth();
        }
		
		$controller->loadModel('MediaBox');
		
        if ($controller->params['form']['company_id'] > 0) {
            $result = $controller->MediaBox->getByCompany($controller->params['form']['company_id'], array('id','name'));
			$result = Set::classicExtract( $result, '{n}.MediaBox');
			// $result = array('MediaBoxes'=>$result);
            return array('success'=>true, 'data'=> $result,'count'=>1);
        } else {
            return array('success'=>false);
        }
    }
}
