<?php

/**
 * FileStore
 * Files storage data Model. Model use file upload behavior. Bahavior can be detached if you need.
 * Features:
 *  - automagic human readeble name
 *  - upload and save file
 *  - parse file info (mimeType, size, extension, etc) and store to table on create/save
 *  - automatic folder creation for new files
 *  - automatic various thumbnails size creation from images 
 * 
 * NOTE: If is file upload not complete or error look to size of uploaded file! Maximum is defined in upload behavior and php.ini.
 *  
 * @author Martin Bucko, Treecom s.r.o. (bucko at treecom dot net)
 * @copyright Copyright 2009 - OneClick s.r.o., 2010 - 2011 Treecom s.r.o. 
 */

class FileStore extends AppModel {
	/**
	 * Model name
	 * @var string 
	 */
	var $name = 'FileStore';
	/**	 
	 * @var string
	 */
	var $useTable = 'files';
	
	/**
	 * @var string (or array) The column name(s) and direction(s) to order find results by default.
	 */
	var $order = 'FileStore.id DESC'; 
	
	/**
	 * Acts as TranslateIt and Upload behavior.
	 * NOTE: This fields is saved in i18ns table and dont have fields in file_store table!
	 * @var array
	 */
	var $actsAs  = array(
 			'Upload' => array(
				'file_name' => array(				
             		'create_directory' => true,
					'dir'=>'files/',
					'folderAsField' => 'path',				
					'allowedMime' => null,
					'allowedExt'=>null,
					'useImageMagick' => false,
					'lowerCaseFileNames' => true,
					'uploadName' => 'name',
					'thumbsizes' => null,
					 'fields' => array(
						'dir' => 'path',
						'filesize' => 'filesize',
						'mimetype' => 'type'
					),				
				)				
			),
			'TranslateIt' => array(
								'name', 
								'description',
								'keywords'
			),
			
	);
	
	/**
	 * File direcotry relative to WWW_ROOT
	 * @var string 
	 */
	var $filesDir = 'files'; 
	
	/**
	 * @var string thumbs dir name 
	 */
	var $thumbsDir = 'thumb';
	
	/**
	 * @var string default images icons dir in thumbs folder 
	 */
	var $defaultIconsDir = 'icon';
	
	/**
	 * @var array default image sizes to create thumbs. Variable set at constructor from domain configuration.  
	 */
	var $defaultThumbSizes = array(
						'icon' => array('width'=>64, 'height'=>64),
                		'small' => array('width'=>100, 'height'=>100),
						'big' => array('width'=>640, 'height'=>480)
	);
	
	
	/**
	 * @var array Hold known mime type mappings. Taken from Cake MediaView.	 
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
 	 * defaultMimeType
 	 * @var string
 	 */
 	var $defaultMimeType = 'aplication/octet-stream';
	
	/**
	 * filesGroups
	 * Groups of standart/clasic file extensions. Mostly compatible with browsers and standard web players.
	 * @var string	 
	 */								
	var $filesGroups = array(
			'image' => array('jpg','jpe','jpeg','gif', 'bmp', 'png'),
			'video' => array('avi','mpg','mpe','mpeg','mp4','mov','flv','qt','ram','ra','rm','mkv'),
			'audio' => array('au', 'wav','mp2','mp3','mpga','ogg','aac','mid','midi'),
			'archive' => array('zip','rar','7z','bz2','tar','gtar','gzip'),
			'text' => array('txt','js','css','html','htm','xml','ini','ctp','tpl','rtf'),
			'document' => array('pdf','doc','docx','xls','xlsx','xlm','ppt','rtf'),
			'flash' => array('swf')
	);	
	
	/**
	 * @var array
	 */
	var $disabledFiles = array('php');
	
	/**
	 * propertiesInResults
	 * Allow file properties array in results. Can setup before result for bether perfomance in output.
	 * @var boolean
	 */
	var $propertiesInResults = false;
	
	/**
	 * Link to tmp file class.
	 */
	var $tmpFile = null;
	
	/**
	 * constructor	
	 * See cake docs in Model::__construct. Constructor set up model properties from domain configuration. 
	 * @return void 
	 */	 
	function __construct($id = false, $table = null, $ds = null){	
		
		$val = Configure::read('Domain.thumbSizes');
		if (!empty($val)){
			$this->defaultThumbSizes = $val;
			foreach($this->defaultThumbSizes as $key=>$val){
				$val = explode(',', $val); 
				$v = explode('x', $val[0]);
				$this->defaultThumbSizes[$key] = array();
				$this->defaultThumbSizes[$key]['width'] = $v[0];
				$this->defaultThumbSizes[$key]['height'] = $v[1];
				if (!empty($val[1])){
					$this->defaultThumbSizes[$key]['thumbnailQuality'] = $val[1];
				}
				if (!empty($val[2])){
					$this->defaultThumbSizes[$key]['zoomCrop'] = ($val[2]==1);
				}
			}
			if (isset($this->actsAs['Upload'])){
				$this->actsAs['Upload']['file_name']['thumbsizes'] = $this->defaultThumbSizes;
			}
		}
		
		$val = Configure::read('Domain.filesGroups');
		if (!empty($val)){
			$this->filesGroups = $val;
			foreach($this->filesGroups as $key=>$val){
				$this->filesGroups[$key] = array();
				$this->filesGroups[$key] = explode(',', $val);
			}
		}
		
		$val = Configure::read('Domain.disabledFiles');
		if (!empty($val)){
			$this->disabledFiles = $val;
		}
		
		parent::__construct($id, $table, $ds);		
	}
	
	/**
	 * afterFind
	 * @return array results 
	 */	
	function afterFind($results, $primary = false){
		
		if (!empty($results)){
			foreach ($results as $key => $val) {
				if (!empty($results[$key][$this->name]['properties'])){
					if ($this->propertiesInResults===true){
						$results[$key][$this->name]['properties'] = @unserialize($results[$key][$this->name]['properties']);
					} else {
						unset($results[$key][$this->name]['properties']);
					}
				}
			}
		}
		
		return $results;
	}
	
	/**
	 * beforeValidate
	 * Callback before validate.  
	 * @return void
	 */
	function beforeValidate(){	
		// LogError(print_r($this->data['FileStore'], true));	
 		// work only with one item save. Not working with saveAll (@todo maybe need solve this problem)
		if (is_array($this->data['FileStore'])){
			
			// download file if link property found
			$this->data = $this->downloadFile($this->data);
			
			 // add file name as name, later converted to human readable name  
			 if (empty($this->data['FileStore']['name']) && empty($this->id)){			 	
				$this->data['FileStore']['name'] = $this->getFileName();
			 }
			 			
			// add extension
			if (empty($this->data['FileStore']['extension'])){
				$this->data['FileStore']['extension'] = $this->getExtension();
			}
			
			// check disabled files extensions
			if (!empty($this->data['FileStore']['extension'])){
				$ext = $this->getExtension();
				if (in_array($ext, $this->disabledFiles)){
					return false;
				}
			}
			
			// add mime type (fixed default flash upload mime type) 	
 			$this->data['FileStore']['type'] = $this->getMime();
			if (!empty($this->data['FileStore']['file_name'])){
				if (is_array($this->data['FileStore']['file_name'])){
					$this->data['FileStore']['file_name']['type'] = $this->data['FileStore']['type']; 
					$this->data['FileStore']['file_name']['mimetype'] = $this->data['FileStore']['type'];
				}
			}
			// add folder name if is not set 			
			if (empty($this->data['FileStore']['path'])){
				$this->data['FileStore']['path'] = date("Ym");		
			}
			
			// set empty translated fields on create 
			if (isset($this->actsAs['TranslateIt']) && empty($this->id) && empty($this->data['FileStore']['id'])){
				foreach($this->actsAs['TranslateIt'] as $v){
					if (is_string($v) && empty($this->data['FileStore'][$v])){
						$this->data['FileStore'][$v] = '';	
					}
				}
			}
			
			// poripade riesit context relation auto creation! :)	
			// LogError('----');		
			// LogError(print_r($this->data['FileStore'], true));
			// fb($this->data['FileStore']); 
 		}
		return true;
	}
	
	/**
	 * beforeSave
	 * Callback before save file data.
	 * @return boolean 
	 */
	function beforeSave(){
 
		// work only with one item save. Not working with saveAll (@todo solve problem)
		if (is_array($this->data['FileStore'])){
			// new record 
			if (empty($this->id)){
				// add human file name on create record. 
				$this->data['FileStore']['name'] = $this->getHumanName($this->data['FileStore']['name']);
				 
				// change path from '\' to '/' and add before and after '/'
				if (!empty($this->data['FileStore']['path'])){
						$this->data['FileStore']['path'] = '/'.$this->data['FileStore']['path'].'/';
						$this->data['FileStore']['path'] = str_replace('\\', '/', $this->data['FileStore']['path']); // one "/" to \
 						$this->data['FileStore']['path'] = str_replace('//', '/', $this->data['FileStore']['path']); // double
 						$this->data['FileStore']['path'] = str_replace('//', '/', $this->data['FileStore']['path']); // double
				}
				$this->addCUTime();
				$this->createProperties();
			}
			// set again translated fields on create, this is fix for temp. data in TranslateIt behavior 
			if (isset($this->actsAs['TranslateIt']) && empty($this->id) && empty($this->data['FileStore']['id'])){
				if ($this->Behaviors->attached('TranslateIt')){
					$this->Behaviors->TranslateIt->dispatchMethod($this, 'beforeValidate');
					$this->Behaviors->TranslateIt->dispatchMethod($this, 'beforeSave');
				}
			}
		}
		return true;
	}
 	
	/**
	 * Callback after save
	 * @param boolean created 
	 */	
	function afterSave($created){
		if (is_object($this->tmpFile)){
			$this->tmpFile->delete();
		}
	}
	/**
	 * getByContext
	 * get files by context_id from ContextCpRelations
	 * @param number $id
	 * @return array result
	 */
	function getByContext($id){
		if ($id>0){
			$relations =& ClassRegistry::init('ContextCpRelation');
			$rel = $relations->find('all', array('conditions'=>array('context_id'=>$id, 'table'=>'files')));
			$rel = Set::extract('/ContextCpRelation/table_id', $rel);
  			if (is_array($rel) && !empty($rel)){				
				return $this->find('all',
					 array('conditions'=>
					 	array('id'=> $rel), 
						'order'=>'name ASC'
					)
				);
			}
		}
		return array();
 	}
	
	/**
	 * getByContextFiltred
	 * Get files by context_id from ContextCpRelations. Is possbile to use find options. Options are merged with default options.
	 * @param number $id
	 * @param array $conditions [optional]
	 * @return array result
	 * @todo sketch only ... not tested!
	 */
	function getByContextFiltred($id, $options = array(), &$controller = null){
		if ($id>0){
			$relations =& ClassRegistry::init('ContextCpRelation');
			
			$asoc = array('belongsTo' => array(
		                'FileStore' => array(
		                    'className' => 'FileStore',		
							'foreignKey' => 'table_id',											
		                )
		            )
		    );
			$relations->bindModel($asoc);
			
			$default_options = array(				
					'conditions'=> array(
						'ContextCpRelation.context_id'=>$id, 
						'ContextCpRelation.table'=>'files',
					 ),
					 'order' => array(
					 	'FileStore.created_time DESC'
					 )
			);
			
			$options = array_merge_recursive($default_options, $options);
			
			if (is_object($controller)){										 	
				return $relations->find('all', $options);
			} else {
				return $relations->find('all', $options);
			}			
		}
		return array();
 	}
	
	/**
	 * getFileRelations
	 * Get file relations to context tree by file id.
	 * @param object $id
	 * @return array
	 */
	function getFileRelations($id){
		if ($id>0){
			$relations =& ClassRegistry::init('ContextCpRelation');
			$rel = $relations->find('all', array('conditions' => array('table_id'=>$id, 'table'=>'files')));
			$rel = Set::extract('/ContextCpRelation/context_id', $rel);
  			if (is_array($rel) && !empty($rel)){				
				return $rel;
			}
		}
		return array();
	}
	
	/**
	 * setFileRelations
	 * Get file relations to context tree by file id.
	 * @param mixed $contextId
	 * @return int $tableId
	 */
	function setFileRelations($contextId, $tableId){
 		if (!empty($contextId) && $tableId>0){
			$relations =& ClassRegistry::init('ContextCpRelation');
			if (is_array($contextId)){
				return $relations->makeRelations($contextId, 'files', $tableId, true);
			}
			if (is_numeric($contextId)){
				return $relations->makeRelation($contextId, 'files', $tableId);
			} 
		}
	}
	
	/**
	 * getFileById
	 * Get file info by file id
	 * @param object $id
	 * @return array file data 
	 */
	function getFileById($id, $fields = false){
		if (intval($id)>0){
			$options = array(
				'conditions' => array('FileStore.id' => $id),
				'fields' => $fields ? $fields : array('id', 'name', 'path', 'file_name','extension')
			);
			$data = array();
			if ($data = $this->find('first', $options)){
 				$data = $data['FileStore'];
				$data['full_path'] = $data['path'].$data['file_name'];
				return $data;
			}
			 
		}	
		return array();
	}
	
	/**
	 * getExtension
	 * Get file extension. 
	 * @param mixed filename [optional]
	 * @return string file extension 
	 * @todo This function require some exceptions for files like name.tar.gz etc. 
	 */
	function getExtension($filename = null){
 		$filename  = $this->getFileName($filename, true); 		
 		return pathinfo($filename, PATHINFO_EXTENSION);
	}
	
	function getLinkExtension($filename = null){
		$ext = '';
 		$filename  = $this->getFileName($filename, true);
		if (preg_match('%\.([a-z0-9]{2,})$%', $filename, $ext)>0){
			if ($ext[1]){
				$ext = $ext[1];
			}
		} 		
 		return $ext;
	}
	
	/**
	 * Return human readable file name. Use full for text naming or file description.   
	 * @param mixed file [optional]
	 * @return string
	 */
	function getHumanName($file = null){
		
		if (is_array($file)){
			if (is_array($file[$this->name])){
				$file = $file[$this->name]['name'];
			}
			if (is_array($file['name'])){
				$file = $file['name'];
			}  
		}
		
		$file = $this->getFileName($file);
		$ext  = $this->getExtension($file);		
		$file = str_replace('.'.$ext, '', $file);
		$file = urldecode($file);
		$file = Inflector::humanize($file);  
		$file = str_replace('-',' ', $file);
		$file = str_replace('.',' ', $file);
		
		return $file;
	}
	
	/**
	 * Get file mime type by file name. Optional add mime to model.
	 * @param mixed $file [optional]
	 * @param boolean $add [optional]
	 * @return string mimeType
	 */
	function getMime($file = null, $add = false){
		
		$ext = null;
		$mime = null;
		
		if (!empty($file['FileStore']['extension'])){
 			$ext = $file['FileStore']['extension'];	
		} else {
			$ext = $this->getExtension($file);
		}
		
		if (!empty($this->mimeType[$ext])){
		 		$mime = $this->mimeType[$ext];				 
		}
		
		$mime = empty($mime) ? $this->defaultMimeType : $mime;
		
		if ($add==true && is_array($this->data['FileStore'])){
			$this->data['FileStore']['type'] = $mime;
			if (isset($this->data['FileStore']['file_name']['type'])){
				$this->data['FileStore']['file_name']['type'] = $mime;
			}			 
		}
		
		return $mime;			
	}
	
	/**
	 * Retrun file name from mixed imput or from model data 
	 * @param mixed $file [optional]
	 * @param boolean $low [optional] return lower string file name
	 * @return string
	 */
	function getFileName($file = null, $lower = false){
 
		if ($file===null){
			if (!empty($this->data[$this->name]['file_name'])){
				$file = $this->data[$this->name]['file_name'];
			}
			if (!empty($this->data[$this->name]['file_name']['name'])){
				$file = $this->data[$this->name]['file_name']['name'];
			}
			if (!empty($this->data['file_name'])){
				$file = $this->data['file_name'];
			}
		}
 		
		if (is_array($file)){
			if (!empty($file[$this->name]['file_name']) && !is_array($file[$this->name]['file_name'])){
				$file = $file[$this->name]['file_name'];
			}
			if (!empty($file['file_name']) && !is_array($file['file_name'])){
				$file = $file['file_name'];
			}
			if (!empty($file['file_name']['name'])){
				$file = $file['file_name']['name'];
			}
		}
		
		if ($lower===true && is_string($file)) { 
			$file = strtolower($file);
		}
		
		return trim($file);
	}
	
	/**
	 * addThumbs
	 * Add path to image thumbs. Thumbs can be in simple array, one item model array or more items with model array. 
	 * @param array $data various format of data ($data, $data['FileStore'], $data[n]['FileStore'])
	 * @param string $size [optional] size named folder 
	 * @return array changed data 
	 */
	function addThumbs($data, $size = false, $model = null){
		
		if (is_object($model)){
			$model = $model->name;
		}
		if  (empty($model)){
			$model = 'FileStore';
		}
		$size = !empty($size) ? $size : $this->defaultIconsDir;
		$th = $this->thumbsDir .'/'. $size .'/';
		
		if (!empty($data[$model]['file_name']) && !empty($data[$model]['path'])){
			$data[$model]['thumb'] = $data[$model]['path'] . $th . $data[$model]['file_name'];
			$data[$model]['thumb'] = r("//","/",$data[$model]['thumb']);
		}
		
		if (!empty($data['file_name']) && !empty($data['path'])){
			$data['thumb'] =  '/'.$data['path'] . $th . $data['file_name'];
			$data['thumb'] = r("//","/",$data['thumb']);
		}
			
		if (is_array($data) && isset($data[0][$model]['file_name'])){
			foreach ($data as $key=>$val){
				if (is_numeric($key) && !empty($data[$key][$model]['extension'])){
					$ext = $data[$key][$model]['extension'];
				
					if (in_array($ext, $this->filesGroups['image'])) {
						$data[$key][$model]['thumb'] = '/'.$data[$key][$model]['path'] . '/'.$th . '/'.$data[$key][$model]['file_name'];
					}
					
					if (in_array($ext, $this->filesGroups['video'])){
						$data[$key][$model]['thumb'] = str_replace('.'.$ext, '.jpg', $data[$key][$model]['file_name']);	
						$data[$key][$model]['thumb'] = '/'.$data[$key][$model]['path'] . '/'.$th . '/'.$data[$key][$model]['file_name'];
						
						if (!file_exists(WWW_ROOT . $data[$key][$model]['thumb'])){
							$data[$key][$model]['thumb'] = '';
						}						
					}
					fb($data[$key][$model]['thumb']);
					if (($size=="small" || $size=="icon") && empty($data[$key][$model]['thumb'])) {
						$gr = array_keys($this->filesGroups);
						foreach($gr as $k){ 
							if (in_array($ext, $this->filesGroups[$k]) && $k!='image') {
								$data[$key][$model]['thumb'] = '/img/default_thumbs/'.$k.'_'.$size.'.png';
							} 			
						}
						if (empty($data[$key][$model]['thumb'])){
							$data[$key][$model]['thumb'] = '/img/default_thumbs/file_'.$size.'.png';
						}
					}
					$data[$key][$model]['thumb'] = r("//","/",$data[$key][$model]['thumb']);
				}
			}
		}
		
		return $data; 
	}
	
	/**
	 * getThumbSizes
	 * @param string key [optional]
	 * @return mixed sizes or null if not found
	 */
	function getThumbSizes($key = NULL){
		if (isset($this->actsAs['Upload'])){
			if ($key===null){
				return $this->defaultThumbSizes;
			}
			if (!empty($key)){
				if (!empty($this->defaultThumbSizes[$key])){
					return $this->defaultThumbSizes[$key];
				}
				
			}
		}
		return null;
	}
	
	/**
	 * getThumbSizesList
	 * @param object $key [optional]
	 * @return array or null if not found  
	 */
	function getThumbSizesList($key = NULL){
		if (!empty($this->defaultThumbSizes)){
		 	return array_keys($this->defaultThumbSizes);
		}
		return null;
	}
	
	/**
	 * Download file to temp folder. Detected by link parameter in file data.
	 * @param array file data (with FileStore.link in array)
	 * @return array modified file data
	 */
	function downloadFile($file){
		
		if (!empty($file[$this->name]['link'])){
				
			$link = trim($file[$this->name]['link']);
			$link = str_replace(' ', '%20', $link);
			
			if (preg_match('%^(http)%i', $link)>0){
					
				App::import('Core', 'HttpSocket');
				$HttpSocket = new HttpSocket();
				$tmp = $HttpSocket->request($link);
				
				if (preg_match('%/([a-z0-9_\-\.\%]*)$%i', $link, $fname)>0){
					if (!empty($fname[1])){
						$fname = $fname[1];	
					}
				}
				
				if (empty($fname)){
					$fname = time();
				}
				
				if (empty($file[$this->name]['name'])){
					$file[$this->name]['name'] = $fname;
				}
				$tmpf = TMP . $fname;
				
				$this->tmpFile = new File($tmpf, true);
				if ($this->tmpFile->write($tmp)){
					$this->tmpFile->close();	
					$file[$this->name]['extension'] = $this->getLinkExtension($file[$this->name]['link']);
					$file[$this->name]['file_name'] = array(
						'name' => $fname,
						'tmp_name' => $tmpf,
						'size' => $this->tmpFile->size(),
						'error' => UPLOAD_ERR_OK
					);
				} else {
					$this->tmpFile->close();
				}
				 
				// tmp file is later deleted, see method afterSave
			}
		}
		
		return $file;
	}
	/**
	 * createProperties
	 * Create file properties value for save to table. Value contain informations about dimensions or other like exif from images. 
	 * @param array file data, if not taken from model data [optional]
	 * @return array changed file data
	 */
	function createProperties($file = null){
		if ($file===null && !empty($this->data['FileStore'])){
			$file = $this->data;
		}
 			
		if (empty($file['FileStore']['properties'])){ // only for create
 			if (!empty($file['FileStore']['extension'])){
				$ext = $file['FileStore']['extension'];
			} else {
				$ext = $this->getExtension();
			}
  	
			// images properties
			if (in_array($ext, $this->filesGroups['image']) 
				&& !empty($file['FileStore']['path'])
				&& !empty($file['FileStore']['file_name'])){
					
				$path = WWW_ROOT . $file['FileStore']['path'] . DS . $file['FileStore']['file_name'];
 				$info = null;	
  				list($w,$h) = getimagesize($path, $info);
				$prop = array('w'=>$w,'h'=>$h);		
				
				// ipt info
				if (!empty($info['APP13'])) {
					 $iptc = iptcparse($info['APP13']);
					 $info = array();
 					 if (is_array($iptc)) {
 					 	$tags = array(
 					 		'caption' => '2#120',
 					 		'graphic_name'=>'2#005',
 					 		'urgency'=>'2#010',
 					 		'category'=>'2#015',
 					 		'supp_categories'=>'2#020',
 					 		'spec_instr'=>'2#040',
 					 		'creation_date'=>'2#055',
 					 		'photog'=>'2#080',
 					 		'credit_byline_title'=>'2#085',
 					 		'city'=>'2#090',
 					 		'state'=>'2#095',
 					 		'country'=>'2#101',
 					 		'otr'=>'2#103',
 					 		'headline'=>'2#105',
 					 		'source'=>'2#110',
 					 		'photo_source'=>'2#115',
 					 		'caption'=>'2#120',
 					 		
						); 
						// note that sometimes supp_categories contans multiple entries 
						foreach($tags as $key=>$val){
							if (isset($iptc[$val][0])) {
								$info[$key] = $iptc[$val][0];
							}
						} 
					 }
					 $prop = am((array)$info, (array)$prop);
				}
				
				if (function_exists('exif_read_data')){
					
					/*
					// this exif method return buggy array for serialize 
					if (in_array($ext, array('jpg','jpeg','jpe','tif'))){
						$info = @exif_read_data($path, 'EXIF,IFD0,COMMENT', false, false);
						if ($info){							
							if (isset($info['FILE'])) {								
								$info['COMPUTED']['FileDateTime'] = $info['FILE']['FileDateTime'];								
								unset($info['FILE'], $info['THUMBNAIL']);
							}
							$prop = am((array)$info, (array)$prop);
						}
					}
					*/
					
					// @todo FIXME if photoshop file is uploaded crash!!!
					/*
				 	try {
						App::import('Vendor','phpExifReader',array('file' => 'Exif'.DS.'ExifReader.php'));
						$er = new phpExifReader($path);
						$er->processFile();
						$info = $er->getImageInfo();
											
						// clean
						if (is_array($info) && !empty($info)){
							$remove = array('FileName','Thumbnail','ThumbnailSize','FileSize','colorSpace');
							foreach ($info as $key=>$val){
								if (is_array($val)){
									$val = serialize($val);
								}
								$val = trim($val);
								if (empty($val) || !ctype_alnum($key) || in_array($key, $remove)) { 
									unset($info[$key]);
								}
								if (!is_array($val) && !is_string($val) && !ctype_print($val) && isset($info[$key])){
									unset($info[$key]);								
								}							
							}
						}
						$prop = am((array)$info, (array)$prop);
					} catch (Exception $e){
						LogError('phpExifReader error - ' . $e->getMessage());
					}
				 	*/
				}
				
				
				$file['FileStore']['properties'] = $prop;
			}
			
			// video/audio properties with php_ffmpeg 
			if ((in_array($ext, $this->filesGroups['video']) || in_array($ext, $this->filesGroups['audio'])) 
				&& !empty($file['FileStore']['path'])
				&& !empty($file['FileStore']['file_name'])
				&& class_exists('ffmpeg_movie')){
					
				$path = WWW_ROOT . $file['FileStore']['path'] . DS . $file['FileStore']['file_name'];
				$movie = new ffmpeg_movie($path);	
				
				if ($movie->hasVideo() || $movie->hasAudio()){
					$prop = array(
							'duration' 		=> $movie->getDuration(),
							'frame_count' 	=> $movie->getFrameCount(),	
							'frame_rate' 	=> $movie->getFrameRate(),	
							'comment' 		=> $movie->getComment(),	
							'title' 		=> $movie->getTitle(),	
							'author' 		=> $movie->getAuthor(), 
							'copyright' 	=> $movie->getCopyright(),
							'artist' 		=> $movie->getArtist(),	
							'genre' 		=> $movie->getGenre(),	
							'track_number' 	=> $movie->getTrackNumber(),	
							'year' 			=> $movie->getYear(),	
							'width' 		=> $movie->getFrameWidth(),	
							'height' 		=> $movie->getFrameHeight(),
							'w' 			=> $movie->getFrameWidth(), // app format width  	
							'h'	 			=> $movie->getFrameHeight(),	 // app format height
							'pixel_format' 	=> $movie->getPixelFormat(),	
							'bit_rate' 		=> $movie->getBitRate(),	
							'video_bit_rate'=> $movie->getVideoBitRate(),	
							'audio_bit_rate'=> $movie->getAudioBitRate(),	
							'audio_sample_rate' => $movie->getAudioSampleRate(),	
							'video_codec' 	=> $movie->getVideoCodec(),	
							'audio_codec' 	=> $movie->getAudioCodec(),	
							'audio_channels'=> $movie->getAudioChannels(),	
							'has_audio' 	=> $movie->hasAudio(),
							'has_video' 	=> $movie->hasVideo()
					);
					
					foreach ($prop as $key => $val){
						if (empty($val)){
							unset($prop[$key]);
						}
					}	
					$file['FileStore']['properties'] = $prop;
				}
			}	
			
			// @todo find some more file types infos
		}
		
		if (is_array($file['FileStore']['properties'])){
			try {
				foreach ($file['FileStore']['properties'] as $k=>$v){
					if (is_string($v)){
						$file['FileStore']['properties'][$k] =  @iconv('UTF-8', 'ASCII//TRANSLIT', $v);
					}
				}
				$file['FileStore']['properties'] = serialize($file['FileStore']['properties']);
			} catch (Exception $e){
				LogError('serialize file properties error - ' . $e->getMessage());
			}
		}
		
		$this->data['FileStore']['properties'] = $file['FileStore']['properties'];
		return $file;
	}
	
	function beforeDelete($cascade = true){
		
		if ($this->id>0){
			$cprel =& ClassRegistry::init('ContextCpRelations');
			$cprel->deleteAll(array('ContextCpRelations.table_id' => $this->id,'ContextCpRelations.table' => 'files'), $cascade);
		
			$rel =& ClassRegistry::init('FilesRelations');
			$rel->deleteAll(array('FilesRelations.file_id'=>$this->id), $cascade);
		}
		
		return true;
	}
	
	function onError(){
		 LogError('Error on:' . print_r($this->data['FileStore'], true));
		 if (is_object($this->tmpFile)){
			$this->tmpFile->delete();
		}
	}
}