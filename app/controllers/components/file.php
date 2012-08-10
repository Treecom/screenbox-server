<?php
 

// use Cake File Class
uses('File');

/**
 *  FilesComponent
 * 
 *  Component for file managment.
 *  
 *  Features:
 *   - upload/download/delete files
 *   - get/set files listings
 *   - get/set files metadata
 *   - get/set files relations (multiply) to context tree  
 *
 *  @author Martin Bucko (bucko@oneclick.sk)
 *  @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o.
 *  @version 1.0
 *  @created
 *  @modified
 */
class FileComponent extends Object {
	
	/**
	 * @var string $name
	 */
    var $name = "File";
	
	/**
	 * Model reference
	 * @var object
	 */
	var $File = null;
    
	/**
	 * Upload dir relative to WWW_ROOT
	 * @var string $uploadDir
	 */
	var $uploadDir = 'files';
	
	/**
	 * Enable slug rename filenames after upload
	 * @var boolean $slugEnabled
	 */
	var $slugEnabled = true;
	
	/**
	 * Existing file renaming
	 * @todo deprecated - unused!
	 */
	var $renameBefore = "_";
	var $renameAfter = ""; 
	
	/**
	 * Holds known mime type mappings. Taken from Cake MediaView.
	 * @var array $mimeType	 
	 */
	var $mimeType = array('ai' => 'application/postscript', 'bcpio' => 'application/x-bcpio', 'bin' => 'application/octet-stream',
								'ccad' => 'application/clariscad', 'cdf' => 'application/x-netcdf', 'class' => 'application/octet-stream',
								'cpio' => 'application/x-cpio', 'cpt' => 'application/mac-compactpro', 'csh' => 'application/x-csh',
								'csv' => 'application/csv', 'dcr' => 'application/x-director', 'dir' => 'application/x-director',
								'dms' => 'application/octet-stream', 'doc' => 'application/msword', 'drw' => 'application/drafting',
								'dvi' => 'application/x-dvi', 'dwg' => 'application/acad', 'dxf' => 'application/dxf', 'dxr' => 'application/x-director',
								'eps' => 'application/postscript', 'exe' => 'application/octet-stream', 'ez' => 'application/andrew-inset',
								'flv' => 'video/x-flv', 'gtar' => 'application/x-gtar', 'gz' => 'application/x-gzip',
								'bz2' => 'application/x-bzip', '7z' => 'application/x-7z-compressed', 'hdf' => 'application/x-hdf',
								'hqx' => 'application/mac-binhex40', 'ips' => 'application/x-ipscript', 'ipx' => 'application/x-ipix',
								'js' => 'application/x-javascript', 'latex' => 'application/x-latex', 'lha' => 'application/octet-stream',
								'lsp' => 'application/x-lisp', 'lzh' => 'application/octet-stream', 'man' => 'application/x-troff-man',
								'me' => 'application/x-troff-me', 'mif' => 'application/vnd.mif', 'ms' => 'application/x-troff-ms',
								'nc' => 'application/x-netcdf', 'oda' => 'application/oda', 'pdf' => 'application/pdf',
								'pgn' => 'application/x-chess-pgn', 'pot' => 'application/mspowerpoint', 'pps' => 'application/mspowerpoint',
								'ppt' => 'application/mspowerpoint', 'ppz' => 'application/mspowerpoint', 'pre' => 'application/x-freelance',
								'prt' => 'application/pro_eng', 'ps' => 'application/postscript', 'roff' => 'application/x-troff',
								'scm' => 'application/x-lotusscreencam', 'set' => 'application/set', 'sh' => 'application/x-sh',
								'shar' => 'application/x-shar', 'sit' => 'application/x-stuffit', 'skd' => 'application/x-koan',
								'skm' => 'application/x-koan', 'skp' => 'application/x-koan', 'skt' => 'application/x-koan',
								'smi' => 'application/smil', 'smil' => 'application/smil', 'sol' => 'application/solids',
								'spl' => 'application/x-futuresplash', 'src' => 'application/x-wais-source', 'step' => 'application/STEP',
								'stl' => 'application/SLA', 'stp' => 'application/STEP', 'sv4cpio' => 'application/x-sv4cpio',
								'sv4crc' => 'application/x-sv4crc', 'svg' => 'image/svg+xml', 'svgz' => 'image/svg+xml',
								'swf' => 'application/x-shockwave-flash', 't' => 'application/x-troff',
								'tar' => 'application/x-tar', 'tcl' => 'application/x-tcl', 'tex' => 'application/x-tex',
								'texi' => 'application/x-texinfo', 'texinfo' => 'application/x-texinfo', 'tr' => 'application/x-troff',
								'tsp' => 'application/dsptype', 'unv' => 'application/i-deas', 'ustar' => 'application/x-ustar',
								'vcd' => 'application/x-cdlink', 'vda' => 'application/vda', 'xlc' => 'application/vnd.ms-excel',
								'xll' => 'application/vnd.ms-excel', 'xlm' => 'application/vnd.ms-excel', 'xls' => 'application/vnd.ms-excel',
								'xlw' => 'application/vnd.ms-excel', 'zip' => 'application/zip', 'aif' => 'audio/x-aiff', 'aifc' => 'audio/x-aiff',
								'aiff' => 'audio/x-aiff', 'au' => 'audio/basic', 'kar' => 'audio/midi', 'mid' => 'audio/midi',
								'midi' => 'audio/midi', 'mp2' => 'audio/mpeg', 'mp3' => 'audio/mpeg', 'mpga' => 'audio/mpeg',
								'ra' => 'audio/x-realaudio', 'ram' => 'audio/x-pn-realaudio', 'rm' => 'audio/x-pn-realaudio',
								'rpm' => 'audio/x-pn-realaudio-plugin', 'snd' => 'audio/basic', 'tsi' => 'audio/TSP-audio', 'wav' => 'audio/x-wav',
								'asc' => 'text/plain', 'c' => 'text/plain', 'cc' => 'text/plain', 'css' => 'text/css', 'etx' => 'text/x-setext',
								'f' => 'text/plain', 'f90' => 'text/plain', 'h' => 'text/plain', 'hh' => 'text/plain', 'htm' => 'text/html',
								'html' => 'text/html', 'm' => 'text/plain', 'rtf' => 'text/rtf', 'rtx' => 'text/richtext', 'sgm' => 'text/sgml',
								'sgml' => 'text/sgml', 'tsv' => 'text/tab-separated-values', 'tpl' => 'text/template', 'txt' => 'text/plain',
								'xml' => 'text/xml', 'avi' => 'video/x-msvideo', 'fli' => 'video/x-fli', 'mov' => 'video/quicktime',
								'movie' => 'video/x-sgi-movie', 'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg', 'mp4' => 'video/mp4',
								'qt' => 'video/quicktime', 'viv' => 'video/vnd.vivo', 'vivo' => 'video/vnd.vivo', 'gif' => 'image/gif',
								'ief' => 'image/ief', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg',
								'pbm' => 'image/x-portable-bitmap', 'pgm' => 'image/x-portable-graymap', 'png' => 'image/png',
								'pnm' => 'image/x-portable-anymap', 'ppm' => 'image/x-portable-pixmap', 'ras' => 'image/cmu-raster',
								'rgb' => 'image/x-rgb', 'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'xbm' => 'image/x-xbitmap',
								'xpm' => 'image/x-xpixmap', 'xwd' => 'image/x-xwindowdump', 'ice' => 'x-conference/x-cooltalk',
								'iges' => 'model/iges', 'igs' => 'model/iges', 'mesh' => 'model/mesh', 'msh' => 'model/mesh',
								'silo' => 'model/mesh', 'vrml' => 'model/vrml', 'wrl' => 'model/vrml',
								'mime' => 'www/mime', 'pdb' => 'chemical/x-pdb', 'xyz' => 'chemical/x-pdb');
	
	
	/**
	 * Default icon for files
	 * @var string
	 */
	var $defaultIcon = '/js/ext/resources/images/default/icons/file-page-4.png';
	
	/**
	 * __construct
	 * Constructor load FileStore model as default model for this component
	 * @return 
	 */
 	function __construct(){		
		// $this->File =& ClassRegistry::init('FileStore');
	}
							
	 /**
     * init
     * Used to initialize the components for current controller.
     * @params object Controller with components to load
     * @return void
     */
    function init(&$controller){}
    
    /**
     * initilize
     * Called before the Controller::beforeFilter().
     * @params object Controller with components to initialize
     * @return void
     */
    function initialize(&$controller){}
    
    /**
     *  startup 
     *  Called after the Controller::beforeFilter() and before the controller action
     *  @params object Controller with components to startup
     *  @return void
     */
    function startup(&$controller){}
    
     /**
     *  beforeRender 
     *  Called after the Controller::beforeRender(), after the view class is loaded, and before the Controller::render()
     *  @params object controller
     *  @return void
     */
    function beforeRender(&$controller) {}
    
    /**
     *  shutdown
     *  Called after Controller::render() and before the output is printed to the browser.
     *  @params object  	Controller with components to shutdown
     *  @return void
     */
    function shutdown(&$controller){}
	
	 /**
 	 * fileInfo
 	 * Get file info and return file type, size, extension, dirname, basename, extension and filename.
 	 * @param string $file path
 	 * @return array
 	 */
	function fileInfo($file_name){
 					
 		$info = array(
			'type' => 'application/octet-stream',
			'extension'=>null,
			'size'=>null,
			'dirname'=>null,
			'basename'=>null,
			'filename'=>null
		);		
		
		$file = new File($file_name);
		$info['size'] = $file->size();
		$info['extension'] = low($info['extension']);
		$info = am($info, $file->info());						
 		 
		if (!empty($info['extension'])){
 			if (!empty($this->mimeType[($info['extension'])])){
		 		$info['type'] = $this->mimeType[($info['extension'])]; 
			}		
		}
		 
		return $info;
	}
 
    /* ------ Elements -------- */
    
	/**
     *  showImageThumb
     *  .....
     *  @params object Controller with components to shutdown
     *  @return void
     */
    function showImage(&$controller, &$element) {    	
    	return 'showImage (FIXME)';
    }

	/**
     *  download
     *  Download any registered file
     *  @params object Controller with components to shutdown
     *  @return void
     */
    function download(&$controller) {    	    	
		// @todo: forward or download file by id/name 		
    }
	
	/**
     *  getFilesList
     *  Get basic files list by context  
     *  @params object Controller with components to shutdown
     *  @return void
     */
    function getFilesList(&$controller, &$element) {
		 
		$element['properties']['title'] = __('Files by Context', true);
		if (!empty($controller->params['context_id'])){
    		if (empty($element['properties']['contextId'])){
    			$element['properties']['contextId']  = intval($controller->params['context_id']);
    		}
			$id = intval($element['properties']['contextId']);
 			if ($id>0) $data = $controller->FileStore->getByContext($id);
    	} 
		  	
    	return $data;
    }

	/**
	 * admin_upload
	 * Upload one file from admin. Data is parsed from POST in $controller->params['form']['Filedata']. Return 1 if successfull or 0 if not.
	 * @param object $controller
	 * @return 
	 */
	function upload(&$controller, &$element = null) {
 		$data = array();
		if (!empty($controller->params['form']['Filedata'])){
			$controller->loadModel('FileStore');					
			$contextId = empty($controller->params['form']['context_id']) ? 0 : intval($controller->params['form']['context_id']);
			$file['FileStore'] = $controller->params['form']['Filedata'];		
			$file['FileStore']['file_name'] = $file['FileStore']; // must be array!		
			$file['FileStore']['context_id'] = $contextId;
		
			fb('in file::upload ');
			
			$controller->FileStore->create();
			if ($controller->FileStore->save($file)){
				fb('SAVED! file::upload '); 											
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

	/* ------ Admin -------- */
	
	/**
	 * admin_upload
	 * Upload one file from admin. Data is parsed from POST in $controller->params['form']['Filedata']. Return 1 if successfull or 0 if not.
	 * @param object $controller
	 * @return 
	 */
	function admin_upload(&$controller) {
		$result = $this->upload($controller);
		return !empty($result['FileStore']['id']) ? 1 : 0;
	}
	 
	/**
	 * admin_setFileInfo
	 * Edit file name, context and other info
	 * @param object $controller
	 * @return array
	 */
	function admin_setFileInfo(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/File','update')){
			return $controller->unAuth();
		}
	 
		$controller->loadModel('FileStore');		
		$out = array('success'=>false);
		
		$id = (empty($controller->params['form']['id'])) ? 0 : $controller->params['form']['id'];
		
		if ($id>0){
			
			$un = array('file_name','type','extension');
			foreach ($un as $key){
				if (isset($controller->params['form'][$key])){
					unset($controller->params['form'][$key]);
				}	
			}
			
			$controller->FileStore->id = $id;			
 			$controller->FileStore->save($controller->params['form'], array('fieldList' => array('name','description','properties','modified_time','modified_user_id')));	
		}
						
		return $out;
	}
	
	/**
	 * admin_getFiles
	 * Get files list
	 * @param object $controller
	 * @return array files list 
	 */
	function admin_getFiles(&$controller){
		
		$controller->loadModel('FileStore');
 		$controller->FileStore->bindUsers();
		$controller->FileStore->propertiesInResults = true;
		
 		$opt = array();	 	
		$opt = $controller->FileStore->setLimit($controller->params['form']);
		$opt['conditions'] = array();	
		
		// query	
		if (!empty($controller->params['form']['q'])){
			// @todo more parameters to search ...
			$opt['conditions']['OR'] = array(
				array('lower(name) like'=>'%'.low($controller->params['form']['q']).'%'),
				array('file_name like'=>'%'.low($controller->params['form']['q']).'%')
			);	
		} 
		
		// by filter group
		if (!empty($controller->params['form']['filter_group'])){
			$gruops = explode(',',$controller->params['form']['filter_group']);
			if (!empty($gruops) && is_array($gruops)){
				if (empty($controller->params['form']['extension'])) $controller->params['form']['extension'] = array();
				foreach($gruops as $key=>$val){
					if (!empty($controller->FileStore->filesGroups[$val])){
						$controller->params['form']['extension'] = am($controller->params['form']['extension'],(array) $controller->FileStore->filesGroups[$val]);
					}
				}
			}			
 		} 
		
		// by extensions
		if (!empty($controller->params['form']['extension'])){
			array_push($opt['conditions'], array('extension' => $controller->params['form']['extension']));
 		} 
		
		// by context relations
		if (!empty($controller->params['form']['context_id'])){
			if ($controller->params['form']['context_id']>1){
				$foundRel = false;
				$controller->loadModel('ContextCpRelation');
				$ctFiles = $controller->ContextCpRelation->find('all', 
						array('conditions' => array('context_id'=>$controller->params['form']['context_id'], 'table'=>'files'),
							 'order' => 'table_id desc',
							 'limit' => $opt['limit']
						)
				);
	 			
				if (!empty($ctFiles)){
					$ctFilesIds = Set::extract($ctFiles, '{n}.ContextCpRelation.table_id');	
					if (!empty($ctFilesIds)) {
						array_push($opt['conditions'], array('FileStore.id' => $ctFilesIds));
						$foundRel = true;
					}
				}
				// releations requested but not found 
				if (!$foundRel){
					array_push($opt['conditions'], array('FileStore.id' => 0));
				}
			}
		}
		
		// by more ids
		if (!empty($controller->params['form']['id'])){
			if ($controller->Json->detectJson($controller->params['form']['id'])){
				$controller->params['form']['id'] = $controller->Json->decode($controller->params['form']['id']);
			} else {
				$controller->params['form']['id'] = array($controller->params['form']['id']);
			}			
			array_push($opt['conditions'], array('FileStore.id' => $controller->params['form']['id']));
		}
		
		$data = $controller->FileStore->find('all', $opt);
		$data = $controller->FileStore->itemUsers($data);
				
		if(!empty($data)){
			$data = $controller->FileStore->addThumbs($data);
  			$data = Set::extract($data,'{n}.FileStore');
		}
		
		unset($opt['limit'], $opt['offset']);
		
    	return array(
    			'count' => $controller->FileStore->find('count', $opt),
     			'Files' => $data
    	);	
	}
	
	/**
	 * admin_getFile
	 * Get File by id 
	 * @param object $controller
	 * @return array 
	 */
	function admin_getFile(&$controller){
		if (!empty($controller->params['form']['id'])){
			$controller->loadModel('FileStore');
			$controller->FileStore->bindUsers();
			$id = intval($controller->params['form']['id']);
			$data = $controller->FileStore->findById($id);
 			if (!empty($data)){
				$data = $controller->FileStore->itemUsers($data);
				$data = $data['FileStore'];
				if (in_array($data['extension'], $controller->FileStore->filesGroups['image'])){
					$data['thumb'] = $data['path'] . $data['file_name'];
				} else {
					$data['thumb'] = '/js/ext/resources/images/default/icons/file-page-1.png';
				}
 			}
			if (isset($controller->params['form']['clear'])){
 				return $data;
			}
			return array('success' => true, 'data' => $data);			
		} else {
			return array('success' => false, 'errors' => array('msg'=>__( 'Missing file ID', true)));
		}		
	}
	
	/**
	 * admin_getFileRelations
	 * Get file relations to context tree
	 * @param object $controller
	 * @return array
	 */
	function admin_getFileRelations(&$controller){
		$data = array();
 		if (!empty($controller->params['form']['id'])){
			$controller->params['form']['id'] = intval($controller->params['form']['id']);
			if ($controller->params['form']['id']>1){
				$controller->loadModel('FileStore');
				$data = $controller->FileStore->getFileRelations($controller->params['form']['id']);
				if (!empty($data)){
					foreach($data as $key=>$val){
						$data[$key] = intval($val);
					}
				}
				return array('success' => true, 'data' => $data);
			}
		}
				
		return array('success' => false, 'errors' => array('msg'=>__('Missing file ID', true)));	
	}
	
	/**
	 * admin_getFileRelations
	 * Get file relations to context tree
	 * @param object $controller
	 * @return array
	 */
	function admin_setFileRelations(&$controller){
		
		if (!$controller->isAuthorized('Controllers/Admin/File','update')){
			return $controller->unAuth();
		}
		
  		if (!empty($controller->params['form']['id']) && !empty($controller->params['form']['context_id'])){
 			if ($controller->Json->detectJson($controller->params['form']['context_id'])){
 				$controller->params['form']['context_id'] = $controller->Json->decode($controller->params['form']['context_id']);
 			}			
			if ($controller->Json->detectJson($controller->params['form']['id'])){
				$controller->params['form']['id'] = $controller->Json->decode($controller->params['form']['id']);
			} else {
				$controller->params['form']['id'] = array($controller->params['form']['id']);
			}
			
			if (is_array($controller->params['form']['id'])){
				
				$out = array();
				$controller->loadModel('FileStore');			
				foreach ($controller->params['form']['id'] as $id) {
					if (intval($id)>0){
						$out[] = $controller->FileStore->setFileRelations($controller->params['form']['context_id'], $id, false);
					}
				}
			}
			return array('success' => (count($out)>0));
		}
				
		return array('success' => false, 'errors' => array('msg'=>__('Missing file ID', true)));	
	}
	
 	/**
	 * admin_deleteFiles
	 * Delete one or more files by IDs
	 * @param object $controller
	 * @return array
	 */
	function admin_deleteFiles(&$controller){

		if (!$controller->isAuthorized('Controllers/Admin/File','delete')){
			return $controller->unAuth();
		}
		
		$out = array();
		
		if (!empty($controller->params['form']['id'])){
			
			$controller->loadModel('FileStore');
			$id = $controller->params['form']['id'];		
 			
			if (strpos($id, '[')>-1){
				$id = $controller->Json->decode($id);
			}	
 			if ($id>0 && !is_array($id)) {						
				$id = array($id);				
			}
 			if (is_array($id)){
 				foreach ($id as $idDel){
					$out[] = $controller->FileStore->delete(intval($idDel), true);
				}
			}
			return array('success' => (!in_array(false, $out) && !empty($out)));
		}
		return array('success' => false, 'errors' => array('msg'=>__('Missing file ID', true)));
	}
	
	/**
	 * admin_deleteFile
	 * Delete one file. Used/forwarded to function deleteFiles.
	 * @param object $controller
	 * @return array
	 */
	function admin_deleteFile(&$controller){
		return admin_deleteFiles($controller);
	}
	
	/**
	 * Regenerate thumbs of one or more selected file by id.
	 * @param object $controller
	 * @return array 
	 */
	function admin_regenerateThumbs(&$controller){
		$out = array('success'=>true);
		$controller->loadModel('FileStore');
		if (!empty($controller->params['form']['id'])){
			
			if ($controller->Json->detectJson($controller->params['form']['id'])){
				$controller->params['form']['id'] = $controller->Json->decode($controller->params['form']['id']);
			} else {
				$controller->params['form']['id'] = array($controller->params['form']['id']);
			}
			foreach ((array) $controller->params['form']['id'] as $fid){
				$fid =  intval($fid);
				if ($fid>0){
					$controller->FileStore->id = $fid;
					$controller->FileStore->regenerateThumbs();
				}
			}
		}
		return $out;
	}
	
 	/**
 	 * Install elements for this components
 	 * @param object $controller
 	 * @return array
 	 */
	function admin_install(&$controller){
		$element = array(
			'elements_group' => 'Files',
			'name' => 'files_basic_list',
			'name_full' => 'Files basic list',
			'description' => '',
			'component' => 'Files.getFilesList',
			'cache' => null,
			'plugins' => null, 
			'helpers' => null,
			'acl' => null,
			'request_action' => null,					
			'properties' => 
			// name, value, type (number, string, select, boolean, array string), typeValues (select: [0:one, 1:two], array string:one,two)
				array(
					array(							
							'key' => 'enabled',
							'value' => true,
							'type' => 'boolean',
 	 					)
				)
		);
		
		$controller->loadModel('Element');
		return array('result' => $controller->Element->addElements($element));
	}
}
