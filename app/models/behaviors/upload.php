<?php
/**
 * MeioUpload Behavior
 * 
 * ------------
 * Martin Bucko NOTE: this file is patched and is not same as original 
 * ------------
 * NOTE: If is file upload not complete or error look to size of uploaded file! Maximum is defined in upload behavior and php.ini.
 * ------------
 * 
 * This behavior is based on Vincius Mendes'  MeioUpload Behavior
 *  (http://www.meiocodigo.com/projects/meioupload/)
 * Which is in turn based upon Tane Piper's improved uplaod behavior
 *  (http://digitalspaghetti.tooum.net/switchboard/blog/2497:Upload_Behavior_for_CakePHP_12)
 *
 * @author Jose Diaz-Gonzalez (support@savant.be)
 * @author Juan Basso (jrbasso@gmail.com)
 * @author Martin Bucko (bucko@treecom.net)
 * @package app
 * @subpackage app.models.behaviors
 * @filesource http://github.com/jrbasso/MeioUpload/tree/master
 * @link 
 * @version 2.0.1
 * @lastmodified 2010-01-03
 */

 
App::import('Core', array('File', 'Folder'));
 
class UploadBehavior extends ModelBehavior {
/**
 * The default options for the behavior
 */
	var $defaultOptions = array(
		'useTable' => true,
		'lowerCaseFileNames' => true,
		'createDirectory' => true,
		'dir' => 'files{DS}{ModelName}{DS}{fieldName}',
		'folderAsField' => null, // Can be the name of any field in $this->data
		'uploadName' => null, // Can also be the tokens {ModelName} or {fieldName}
		'maxSize' => null, // null = auto parse from value of upload_max_filesize in php.ini
		'allowedMime' => array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/bmp', 'image/x-icon', 'image/vnd.microsoft.icon'),
		'allowedExt' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp', '.ico'),
		'file_mode' => 0777,
		'folder_mode' => 0777,
		'default' => false, // Not sure what this does
		'zoomCrop' => true, // Whether to use ZoomCrop or not with PHPThumb
		'thumbsizes' => array(
			// Place any custom thumbsize in model config instead,
		),
		'thumbnailQuality' => 75, // Global Thumbnail Quality
		'maxDimension' => 'w', // Can be null, h, or w
		'useImageMagick' => true,
		'imageMagickPath' => null,
		'imageMagickPath' => '/usr/bin/convert', // Path to imageMagick on your server
		// 'imageMagickPath' => 'c:/Program Files/ImageMagick/convert.exe', // Path to imageMagick on your server
		'ffmpegthumb' =>true,
		'phpffmpeg' => false,
		'fields' => array(
			'dir' => 'dir',
			'filesize' => 'filesize',
			'mimetype' => 'mimetype'
		),
		'length' => array(
			'minWidth' => 0, // 0 for not validates
			'maxWidth' => 0,
			'minHeight' => 0,
			'maxHeight' => 0
		),
		'validations' => array()
	);
 
	var $defaultValidations = array(
		'FieldName' => array(
			'rule' => array('uploadCheckFieldName'),
			'check' => true,
			'last' => true
		),
		'Dir' => array(
			'rule' => array('uploadCheckDir'),
			'check' => true,
			'last' => true
		),
		'Empty' => array(
			'rule' => array('uploadCheckEmpty'),
			'check' => true,
			'on' => 'create',
			'last' => true
		),
		'UploadError' => array(
			'rule' => array('uploadCheckUploadError'),
			'check' => true,
			'last' => true
		),
		'MaxSize' => array(
			'rule' => array('uploadCheckMaxSize'),
			'check' => true,
			'last' => true
		),
		'InvalidMime' => array(
			'rule' => array('uploadCheckInvalidMime'),
			'check' => true,
			'last' => true
		),
		'InvalidExt' => array(
			'rule' => array('uploadCheckInvalidExt'),
			'check' => true,
			'last' => true
		),
		'MinWidth' => array(
			'rule' => array('uploadCheckMinWidth'),
			'check' => true,
			'last' => true
		),
		'MaxWidth' => array(
			'rule' => array('uploadCheckMaxWidth'),
			'check' => true,
			'last' => true
		),
		'MinHeight' => array(
			'rule' => array('uploadCheckMinHeight'),
			'check' => true,
			'last' => true
		),
		'MaxHeight' => array(
			'rule' => array('uploadCheckMaxHeight'),
			'check' => true,
			'last' => true
		),
	);
 
/**
 * The array that saves the $options for the behavior
 */
	var $__fields = array();
 
/**
 * Patterns of reserved words
 */
	var $patterns = array(
		'thumb',
		'default'
	);
 
/**
 * Words to replace the patterns of reserved words
 */
	var $replacements = array(
		't_umb',
		'd_fault'
	);
 
/**
 * Array of files to be removed on the afterSave callback
 */
	var $__filesToRemove = array();
 
/**
 * Array of all possible images that can be converted to thumbnails
 *
 * @var array
 **/
	var $_imageTypes = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/bmp', 'image/x-icon', 'image/vnd.microsoft.icon');

/**
 * Array of all possible video that can be converted to thumbnails
 *
 * @var array
 **/
	var $_videoTypes = array('video/x-flv','video/mpeg','video/mp4','video/quicktime','video/x-ms-wmv','video/webm','video/x-msvideo');
	
/**
 * Current frame for video image. Using if video uploaded.
 * 
 * @var integer
 */
	var $_currentFrame = null;
 
/**
 * Constructor
 *
 * @author Juan Basso
 */
	function __construct() {
		$messages = array(
			'FieldName' => array(
				'message' => __('This field has not been defined between the parameters of UploadBehavior.', true)
			),
			'Dir' => array(
				'message' => __('The directory where the file would be placed there or is protected against writing.', true)
			),
			'Empty' => array(
				'message' => __('The file can not be empty.', true)
			),
			'UploadError' => array(
				'message' => __('There were problems in uploading the file.', true)
			),
			'MaxSize' => array(
				'message' => __('The maximum file size is exceeded.', true)
			),
			'InvalidMime' => array(
				'message' => __('Invalid file type.', true)
			),
			'InvalidExt' => array(
				'message' => __('Invalid file extension.', true)
			),
			'MinWidth' => array(
				'message' => __('Image width is smaller than minimum allowed.', true)
			),
			'MinHeight' => array(
				'message' => __('Image height is smaller than minimum allowed.', true)
			),
			'MaxWidth' => array(
				'message' => __('Image width is larger than maximum allowed.', true)
			),
			'MaxHeight' => array(
				'message' => __('Image height is larger than maximum allowed.', true)
			)
		);
		$this->defaultValidations = $this->_arrayMerge($this->defaultValidations, $messages);
		$this->defaultOptions['validations'] = $this->defaultValidations;
	}
 
/**
 * Setup the behavior. It stores a reference to the model, merges the default options with the options for each field, and setup the validation rules.
 *
 * @param $model Object
 * @param $settings Array[optional]
 * @return null
 * @author Vinicius Mendes
 */
	function setup(&$model, $settings = array()) {	 
		$this->__fields[$model->alias] = array();
		foreach ($settings as $field => $options) {
			// Check if they even PASSED IN parameters
			if (!is_array($options)) {
				// You jerks!
				$field = $options;
				$options = array();
			}
 
			// Inherit model's lack of table use if not set in options
			// regardless of whether or not we set the table option
			if (!$model->useTable) {
				$options['useTable'] = false;
			}
 
			// Merge given options with defaults
			$options = $this->_arrayMerge($this->defaultOptions, $options);
            
			// Check if given field exists
			if ($options['useTable'] && !$model->hasField($field)) {
				trigger_error(sprintf(__('Upload Error: The field "%s" doesn\'t exists in the model "%s".', true), $field, $model->alias), E_USER_WARNING);
			}
 
			// Including the default name to the replacements
			if ($options['default']) {
				if (strpos($options['default'], '.') !== false) {
					trigger_error(__('Upload Error: The default option must be the filename with extension.', true), E_USER_ERROR);
				}
				$this->_includeDefaultReplacement($options['default']);
			}
 
			// Verifies if the thumbsizes names is alphanumeric
			foreach ($options['thumbsizes'] as $name => $size) {
				if (empty($name) || !ctype_alnum($name)) {
					trigger_error(__('Upload Error: The thumbsizes names must be alphanumeric.', true), E_USER_ERROR);
				}
			}
 
			// Process the max_size if it is not numeric
			if (empty($options['maxSize'])){
				$options['maxSize'] = str_replace('M','mb', ini_get('upload_max_filesize'));
			}
			$options['maxSize'] = $this->_sizeToBytes($options['maxSize']);
 
			// Replace tokens of the dir and field, check it doesn't have a DS on the end
			$tokens = array('{ModelName}', '{fieldName}', '{DS}', '/', '\\');
			$options['dir'] = rtrim($this->_replaceTokens($model, $options['dir'], $field, $tokens), DS);
			$options['uploadName'] = rtrim($this->_replaceTokens($model, $options['uploadName'], $field, $tokens), DS);
 
 			// Create the folders for the uploads
			//$this->_createFolders($options['dir'], array_keys($options['thumbsizes']));
 
			// Replace tokens in the fields names
			if ($options['useTable']) {
				foreach ($options['fields'] as $fieldToken => $fieldName) {
					$options['fields'][$fieldToken] = $this->_replaceTokens($model, $fieldName, $field, $tokens);
				}
			}
			$this->__fields[$model->alias][$field] = $options;
		}
	}
 
/**
 * Sets the validation rules for each field.
 *
 * @param $model Object
 * @return true
 */
	function beforeValidate(&$model) {
		foreach ($this->__fields[$model->alias] as $fieldName => $options) {
			$this->_setupValidation($model, $fieldName, $options);
		}
		return true;
	}
 
/**
 * Initializes the upload
 *
 * @param $model Object
 * @return boolean Whether the upload completed
 * @author Jose Diaz-Gonzalez
 **/
	function beforeSave(&$model) {
		return $this->upload($model, null);
	}
 
/**
 * Deletes the files marked to be deleted in the save method.
 * A file can be marked to be deleted if it is overwriten by
 * another or if the user mark it to be deleted.
 *
 * @param $model Object
 * @author Vinicius Mendes
 */
	function afterSave(&$model) {
		foreach ($this->__filesToRemove as $file) {
			if ($file['name']) {
				$this->_deleteFiles($model, $file['field'], $file['name'], $file['dir']);
			}
		}
		// Reset the filesToRemove array
		$this->__filesToRemove = array();
	}
 
/**
 * Performs a manual upload
 *
 * @param $model Object
 * @param $data Array data to be saved
 * @return boolean Whether the upload completed
 * @author Jose Diaz-Gonzalez
 **/
	function upload(&$model, $data) {
		$result = $this->_uploadFile($model, $data);
		if (is_bool($result)) {
			return $result;
		} elseif (is_array($result)) {
			if ($result['return'] === false) {
				// Upload failed, lets see why
				switch($result['reason']) {
					case 'validation':
						$model->validationErrors[$result['extra']['field']] = $result['extra']['error'];
						break;
				}
				return false;
			} else {
				$this->data = $result['data'];
				return true;
			}
		} else {
			return false;
		}
	}
 
/**
 * Deletes all files associated with the record beforing delete it.
 *
 * @param $model Object
 * @author Vinicius Mendes
 */
	function beforeDelete(&$model) {
		$model->read(null, $model->id);
		if (isset($model->data)) {
			foreach ($this->__fields[$model->alias] as $field => $options) {
				$file = $model->data[$model->alias][$field];
				if ($file && $file != $options['default']) {
					$dir = !empty($options['folderAsField']) ? $model->data[$model->alias][$options['folderAsField']] : $options['dir'];
					// LogError(print_r(array($field, $file, $dir), true));
					$this->_deleteFiles($model, $field, $file, $dir);
				}
			}
		}
		return true;
	}
 
/**
 * Checks if the field was declared in the MeioUpload Behavior setup
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Vinicius Mendes
 */
	function uploadCheckFieldName(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['FieldName']['check']) {
				return true;
			}
			if (isset($this->__fields[$model->alias][$fieldName])) {
				return true;
			} else {
				$this->log(sprintf(__( 'Upload Error: The field "%s" wasn\'t declared as part of the UploadBehavior in model "%s".', true), $fieldName, $model->alias));
				return false;
			}
		}
		return true;
	}
 
/**
 * Checks if the folder exists or can be created or writable.
 *
 * @return boolean
 * @param $model Object
 * @param $data Array
 * @author Vinicius Mendes
 */
	function uploadCheckDir(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['Dir']['check']) {
				return true;
			}
			$options = $this->__fields[$model->alias][$fieldName];
			if (empty($field['remove']) || empty($field['name'])) {
				// Check if directory exists and create it if required
				if (!is_dir($options['dir'])) {
					if ($options['createDirectory']) {
						$folder = &new Folder();
						if (!$folder->mkdir($options['dir'])) {
							trigger_error(sprintf(__( 'Upload Error: The directory %s does not exist and cannot be created.', true), $options['dir']), E_USER_WARNING);
							return false;
						}
					} else {
						trigger_error(sprintf(__( 'Upload Error: The directory %s does not exist.', true), $options['dir']), E_USER_WARNING);
						return false;
					}
				}
 
				// Check if directory is writable
				if (!is_writable($options['dir'])) {
					trigger_error(sprintf(__( 'Upload Error: The directory %s isn\'t writable.', true), $options['dir']), E_USER_WARNING);
					return false;
				}
			}
		}
		return true;
	}
 
/**
 * Checks if the filename is not empty.
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Vinicius Mendes
 */
	function uploadCheckEmpty(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['Empty']['check']) {
				return true;
			}
			if (empty($field['remove'])) {
				if (!is_array($field) || empty($field['name'])) {
					return false;
				}
			}
		}
		return true;
	}
 
/**
 * Checks if ocurred erros in the upload.
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Vinicius Mendes
 */
	function uploadCheckUploadError(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['UploadError']['check']) {
				return true;
			}
			if (!empty($field['name']) && $field['error'] > 0) {
				return false;
			}
		}
		return true;
	}
 
/**
 * Checks if the file isn't bigger then the max file size option.
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Vinicius Mendes
 */
	function uploadCheckMaxSize(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['MaxSize']['check']) {
				return true;
			}
			$options = $this->__fields[$model->alias][$fieldName];
			if (!empty($field['name']) && $field['size'] > $options['maxSize']) {
				return false;
			}
		}
		return true;
	}
 
/**
 * Checks if the file is of an allowed mime-type.
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Vinicius Mendes
 */
	function uploadCheckInvalidMime(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['InvalidMime']['check']) {
				return true;
			}
			$options = $this->__fields[$model->alias][$fieldName];
			if (!empty($field['name']) && count($options['allowedMime']) > 0 && !in_array($field['type'], $options['allowedMime'])) {
				return false;
			}
		}
		return true;
	}
 
/**
 * Checks if the file has an allowed extension.
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Vinicius Mendes
 */
	function uploadCheckInvalidExt(&$model, $data) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName]['InvalidExt']['check']) {
				return true;
			}
			$options = $this->__fields[$model->alias][$fieldName];
			if (!empty($field['name'])) {
				if (count($options['allowedExt']) > 0) {
					$matches = 0;
					foreach ($options['allowedExt'] as $extension) {
						if (strtolower(substr($field['name'], -strlen($extension))) == strtolower($extension)) {
							$matches++;
						}
					}
 
					if ($matches == 0) {
						return false;
					}
				}
			}
		}
		return true;
	}
 
/**
 * Checks if the min width is allowed
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Juan Basso
 */
	function uploadCheckMinWidth(&$model, $data) {
		return $this->_uploadCheckSize($model, $data, 'minWidth');
	}
 
/**
 * Checks if the max width is allowed
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Juan Basso
 */
	function uploadCheckMaxWidth(&$model, $data) {
		return $this->_uploadCheckSize($model, $data, 'maxWidth');
	}
 
/**
 * Checks if the min height is allowed
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Juan Basso
 */
	function uploadCheckMinHeight(&$model, $data) {
		return $this->_uploadCheckSize($model, $data, 'minHeight');
	}
 
/**
 * Checks if the max height is allowed
 *
 * @param $model Object
 * @param $data Array
 * @return boolean
 * @author Juan Basso
 */
	function uploadCheckMaxHeight(&$model, $data) {
		return $this->_uploadCheckSize($model, $data, 'maxHeight');
	}
 
/**
 * Check generic to size of image
 *
 * @param $model Object
 * @param $data Array
 * @param $type String Values: maxWidth, minWidth, maxHeight, minHeight
 * @return boolean
 * @author Juan Basso
 */
	function _uploadCheckSize(&$model, &$data, $type) {
		foreach ($data as $fieldName => $field) {
			if (!$model->validate[$fieldName][ucfirst($type)]['check'] || empty($field['tmp_name'])) {
				return true;
			}
			$options = $this->__fields[$model->alias][$fieldName];
			list($imgWidth, $imgHeight) = getimagesize($field['tmp_name']);
			$imgType = 'img' . substr($type, 3);
			if (substr($type, 0, 3) === 'min') {
				if (!empty($field['name']) && $options['length'][$type] > 0 && $$imgType < $options['length'][$type]) {
					return false;
				}
			} else {
				if (!empty($field['name']) && $options['length'][$type] > 0 && $$imgType > $options['length'][$type]) {
					return false;
				}
			}
		}
		return true;
	}
 
/**
 * Uploads the files
 *
 * @param $model Object
 * @param $data Array Optional Containing data to be saved
 * @return array
 * @author Vinicius Mendes
 */
	function _uploadFile(&$model, $data = null) {
		if (!isset($data) || !is_array($data)) {
			$data =& $model->data;
		}
		foreach ($this->__fields[$model->alias] as $fieldName => $options) {
			// Take care of removal flagged field
			// However, this seems to be kind of code duplicating, see line ~711
			if (!empty($data[$model->alias][$fieldName]['remove'])) {
				$this->_markForDeletion($model->alias, $model->primaryKey, $fieldName, $data, $options['default']);
				$data = $this->_unsetDataFields($model->alias, $fieldName, $data, $options);
				$result = array('return' => true, 'data' => $data);
				continue;
			}
			// If no file was selected we do not need to proceed
			if (empty($data[$model->alias][$fieldName]['name'])) {
				unset($data[$model->alias][$fieldName]);
				$result = array('return' => true, 'data' => $data);
				continue;
			}
			$pos = strrpos($data[$model->alias][$fieldName]['type'], '/');
			$sub = substr($data[$model->alias][$fieldName]['type'], $pos+1);
			list(,$ext) = $this->_splitFilenameAndExt($data[$model->alias][$fieldName]['name']);
 
			// Put in a subfolder if the user wishes it
			if (isset($options['folderAsField']) && !empty($options['folderAsField']) && is_string($options['folderAsField'])) {
				$ff = $data[$model->alias][$options['folderAsField']];
				$options['dir'] = str_replace($ff, '', $options['dir']); // avoid duplicates
				$options['dir'] = str_replace(DS.DS, DS, $options['dir']); // remove double DS
				$options['dir'] = $options['dir'] . DS . $ff;
				$this->__fields[$model->alias][$fieldName]['dir'] = $options['dir'];
			}
			// Create the folders for the uploads
			$this->_createFolders($options['dir'], array_keys($options['thumbsizes']), $options['file_mode']);
  
  
			// Check whether or not the behavior is in useTable mode
			if ($options['useTable'] == false) {
				$this->_includeDefaultReplacement($options['default']);
				$this->_fixName($model, $fieldName, false, $options);
				$saveAs = $options['dir'] . DS . $data[$model->alias][$options['uploadName']] . '.' . $sub;
 
				// Attempt to move uploaded file
				$copyResults = $this->_copyFileFromTemp($data[$model->alias][$fieldName]['tmp_name'], $saveAs, isset($data[$model->alias]['link']), $options['file_mode']);
				if ($copyResults !== true) {
					$result = array('return' => false, 'reason' => 'validation', 'extra' => array('field' => $field, 'error' => $copyResults));
					continue;
				}
 
				// If the file is an image, try to make the thumbnails
				if ((count($options['thumbsizes']) > 0) && count($options['allowedExt']) > 0 && in_array($data[$model->alias][$fieldName]['type'], $this->_imageTypes)) {
					$this->_createThumbnails($model, $data, $fieldName, $saveAs, $ext, $options);
				}
			
				$data = $this->_unsetDataFields($model->alias, $fieldName, $model->data, $options);
				$result = array('return' => true, 'data' => $data);
				continue;
			} else {
				// if the file is marked to be deleted, use the default or set the field to null
				if (!empty($data[$model->alias][$fieldName]['remove'])) {
					if ($options['default']) {
						$data[$model->alias][$fieldName] = $options['default'];
					} else {
						$data[$model->alias][$fieldName] = null;
					}
					//if the record is already saved in the database, set the existing file to be removed after the save is sucessfull
					if (!empty($data[$model->alias][$model->primaryKey])) {
						$this->_setFileToRemove($model, $fieldName);
					}
				}
 
				// If no file has been upload, then unset the field to avoid overwriting existant file
				if (!isset($data[$model->alias][$fieldName]) || !is_array($data[$model->alias][$fieldName]) || empty($data[$model->alias][$fieldName]['name'])) {
					if (!empty($data[$model->alias][$model->primaryKey]) || !$options['default']) {
						unset($data[$model->alias][$fieldName]);
					} else {
						$data[$model->alias][$fieldName] = $options['default'];
					}
				}
 
				//if the record is already saved in the database, set the existing file to be removed after the save is sucessfull
				if (!empty($data[$model->alias][$model->primaryKey])) {
					$this->_setFileToRemove($model, $fieldName);
				}
 
				// Fix the filename, removing bad characters and avoiding from overwriting existing ones
				if ($options['default'] == true) {
					$this->_includeDefaultReplacement($options['default']);
				}
				
				$this->_fixName($model, $fieldName, true, $options);

				// Also save the original image as uploadName if that option is not empty
				if (!empty($options['uploadName'])) {
					$saveAs = $options['dir'] . DS . $data[$model->alias][$options['uploadName']];
				} else {
					$saveAs = $options['dir'] . DS . $data[$model->alias][$fieldName]['name'];
				}
				
				//	LogError('islink: '. isset($data[$model->alias]['link']));
				// Attempt to move uploaded file
				$copyResults = $this->_copyFileFromTemp($data[$model->alias][$fieldName]['tmp_name'], $saveAs,  isset($data[$model->alias]['link']), $options['file_mode']);
				if ($copyResults !== true) {
					$result = array('return' => false, 'reason' => 'validation', 'extra' => array('field' => $field, 'error' => $copyResults));
					continue;
				}

				// If the file is an image, try to make the thumbnails
				if (count($options['thumbsizes']) > 0 && in_array($data[$model->alias][$fieldName]['type'], $this->_imageTypes)) {
					$this->_createThumbnails($model, $data, $fieldName, $saveAs, null, $options);
				}
				
				// If the file is an video, try to make the thumbnails
				if (count($options['thumbsizes']) > 0 && in_array($data[$model->alias][$fieldName]['type'], $this->_videoTypes)) {
					$this->_createVideoThumbnails($model, $data, $fieldName, $saveAs, null, $options);
				}
 
				// Update model data
				$data[$model->alias][$options['fields']['dir']] = $options['dir'];
				$data[$model->alias][$options['fields']['mimetype']] = $data[$model->alias][$fieldName]['type'];
				$data[$model->alias][$options['fields']['filesize']] = $data[$model->alias][$fieldName]['size'];
				
				if (isset($options['uploadName']) && !empty($options['uploadName'])) {
					$data[$model->alias][$fieldName] = $data[$model->alias][$options['uploadName']];
				} else {
					$data[$model->alias][$fieldName] = $data[$model->alias][$fieldName]['name'];
				}
				
				$result = array('return' => true, 'data' => $data);
				continue;
			}
		}
		if (isset($result)) {
			return $result;
		} else {
			return true;
		}
	}
 
/**
 * Create all the thumbnails
 *
 * @return void
 * @author Jose Diaz-Gonzalez
 **/
	function _createThumbnails(&$model, $data, $fieldName, $saveAs, $ext, $options) {
		
		foreach ($options['thumbsizes'] as $key => $value) {
			
			// Generate the name for the thumbnail
			if (isset($options['uploadName']) && !empty($options['uploadName'])) {
 				$thumbSaveAs = $this->_getThumbnailName($saveAs, $options['dir'], $key, $data[$model->alias][$options['uploadName']], $ext);
			} else {
				$thumbSaveAs = $this->_getThumbnailName($saveAs, $options['dir'], $key, $data[$model->alias][$fieldName]['name']);
			}
			$params = array(
				'thumbWidth' => $value['width'],
				'thumbHeight' => $value['height']
			);
			if (isset($value['maxDimension'])) {
				$params['maxDimension'] = $value['maxDimension'];
			}
			if (isset($value['thumbnailQuality'])) {
				$params['thumbnailQuality'] = $value['thumbnailQuality'];
			}
			if (isset($value['zoomCrop'])) {
				$params['zoomCrop'] = $value['zoomCrop'];
			}
			$this->_createThumbnail($model, $saveAs, $thumbSaveAs, $fieldName, $params);
		}
	}
 
/**
 * Function to create Thumbnail images
 *
 * @author Jose Diaz-Gonzalez
 * @param String source file name (without path)
 * @param String target file name (without path)
 * @param String path to source and destination (no trailing DS)
 * @param Array
 * @return void
 */
	function _createThumbnail(&$model, $source, $target, $fieldName, $params = array()) {
		$params = array_merge(
			array(
				'thumbWidth' => 150,
				'thumbHeight' => 225,
				'maxDimension' => '',
				'thumbnailQuality' => 75,
				'zoomCrop' => false
			),
			$params);

		// Import phpThumb class
		App::import('Vendor','phpthumb', array('file' => 'phpThumb'.DS.'phpthumb.class.php'));

		// Configuring thumbnail settings
		$phpThumb = new phpthumb;
		$phpThumb->setSourceFilename($source);
 
		if ($params['maxDimension'] == 'w') {
			$phpThumb->w = $params['thumbWidth'];
		} else if ($params['maxDimension'] == 'h') {
			$phpThumb->h = $params['thumbHeight'];
		} else {
			$phpThumb->w = $params['thumbWidth'];
			$phpThumb->h = $params['thumbHeight'];
		}
 
		$phpThumb->setParameter('zc', $this->__fields[$model->alias][$fieldName]['zoomCrop']);
		if (isset($params['zoomCrop'])){
			$phpThumb->setParameter('zc', $params['zoomCrop']);
		}
		$phpThumb->q = $params['thumbnailQuality'];
 
		$imageArray = explode(".", $source);
		$phpThumb->config_output_format = $imageArray[1];
		unset($imageArray);
 
		$phpThumb->config_prefer_imagemagick = $this->__fields[$model->alias][$fieldName]['useImageMagick'];
		$phpThumb->config_imagemagick_path = $this->__fields[$model->alias][$fieldName]['imageMagickPath'];
 
		// Setting whether to die upon error
		$phpThumb->config_error_die_on_error = true;
				
		// Creating thumbnail
		if ($phpThumb->GenerateThumbnail()) {			
			if (!$phpThumb->RenderToFile($target)) {				
				$this->_addError('Could not render image to: '.$target);
			} else {
				return true;
			}
		}
		
		return false;
	}
 
 /**
  * Create more videothumbs by defined thumb sizes 
  * @author Martin Bucko 
  */
	 function _createVideoThumbnails(&$model, $data, $fieldName, $saveAs, $ext, $options) {
	 	    $movieClass = null;
		 	foreach ($options['thumbsizes'] as $key => $value) {
					
					// Generate the name for the thumbnail
					if (isset($options['uploadName']) && !empty($options['uploadName'])) {
		 				$thumbSaveAs = $this->_getThumbnailName($saveAs, $options['dir'], $key, $data[$model->alias][$options['uploadName']], $ext);
					} else {
						$thumbSaveAs = $this->_getThumbnailName($saveAs, $options['dir'], $key, $data[$model->alias][$fieldName]['name']);
					}
					$params = array(
						'thumbWidth' => $value['width'],
						'thumbHeight' => $value['height']
					);
					if (isset($value['maxDimension'])) {
						$params['maxDimension'] = $value['maxDimension'];
					}
					if (isset($value['thumbnailQuality'])) {
						$params['thumbnailQuality'] = $value['thumbnailQuality'];
					}
					if (isset($value['zoomCrop'])) {
						$params['zoomCrop'] = $value['zoomCrop'];
					}
					$this->_createVideoThumbnail($model, $saveAs, $thumbSaveAs, $fieldName, $params, $movieClass);
			}
	 }
 
 /**
  * Create one video thumb by using php ffmpeg
  * @author Martin Bucko
  */
 	function _createVideoThumbnail(&$model, $source, $target, $fieldName, $params = array(), $movieClass = null) {
		$params = array_merge(
			array(
				'thumbWidth' => 150,
				'thumbHeight' => 225,
				'maxDimension' => '',
				'thumbnailQuality' => 75,
				'zoomCrop' => false
			),
			$params);

		$targetImage = explode(".", $target);
		unset($targetImage[(count($targetImage)-1)]);
		$target = join ('.', $targetImage) . '.jpg';
 		
 		error_log('here 0');
	
		if (class_exists('ffmpeg_movie') && $this->defaultOptions['phpffmpeg']){
			if (!is_object($movieClass)){
				$movie = new ffmpeg_movie($source);
			} else {
				$movie = $movieClass;
			}

			$frames = $movie->getFrameCount();
			if (!($this->_currentFrame > 0) && $frames > 0){
				$this->_currentFrame = rand(1, $frames);
			}
			$frame = $movie->getFrame($this->_currentFrame);
			if ($frame){
				// frame size must be even number
				if ($params['thumbWidth']%2 != 0){
					$params['thumbWidth'] += 1;
				}
				if ($params['thumbHeight']%2 != 0){
					$params['thumbHeight'] += 1;
				}
				// $frame->resize($params['thumbWidth'], $params['thumbHeight']);
				$im = imagejpeg($frame->toGDImage(), $target, $params['thumbnailQuality']);
				return $im;
			}
		}

		if ($this->defaultOptions['ffmpegthumb']==true){
			system('ffmpegthumbnailer -s'. $params['thumbWidth'] .' -q'.$params['thumbnailQuality'].'  -i '.$source.' -o '.$target, $out);
			return ($out==0) ? true: false;
		}		
		
		$this->_addError('Could not render video image from: '. $source .' to: '. $target);
		return false;				
	}
	
	/**
	 * Regenerate record thumbnails, create folder if new sizes defined.
	 * @param object $model
	 * @param array $data [optional]
	 * @return boolean
	 */
	function regenerateThumbs(&$model, $data = array()){

		if (!isset($data) || !is_array($data)) {
			$data =& $model->data;
		}	
	
		if (empty($data) && !empty($model->id)) {
			$model->read(null, $model->id);
			$data =& $model->data;
		}
		 	
		if (empty($data[$model->alias])){
			return false;
		}
		
		foreach ($this->__fields[$model->alias] as $fieldName => $options) {
		
			if (!empty($data[$model->alias][$fieldName])) {
				$options['uploadName'] = null;
				$this->__fields[$model->alias]['uploadName'] = $fieldName;
				$fn = $data[$model->alias][$fieldName];
				$data[$model->alias][$fieldName] = array();
				$data[$model->alias][$fieldName]['name'] = $fn;
				if (!empty($options['fields']['mimetype'])){
					if (!empty($data[$model->alias][($options['fields']['mimetype'])])){
						$data[$model->alias][$fieldName]['type'] = $data[$model->alias][($options['fields']['mimetype'])];
					}
				}
			} else {
				continue;
			}
		
			// Put in a subfolder if the user wishes it
			if (isset($options['folderAsField']) && !empty($options['folderAsField']) && is_string($options['folderAsField'])) {
				$options['dir'] = $data[$model->alias][$options['folderAsField']];
				if (strpos($options['dir'], DS)==0){
					$options['dir'] = substr($options['dir'], 1);
				}
				$this->__fields[$model->alias][$fieldName]['dir'] = $options['dir'];
			}
			
			// Create the folders for the uploads
			$this->_createFolders($options['dir'], array_keys($options['thumbsizes']));
  			 
			$saveAs = $options['dir'] . $data[$model->alias][$fieldName]['name'];
			// If the file is an image, try to make the thumbnails
			if (count($options['thumbsizes']) > 0 && in_array($data[$model->alias][$fieldName]['type'], $this->_imageTypes)) {
				$this->_createThumbnails($model, $data, $fieldName, $saveAs, null, $options);
			}
			
			// If the file is an video, try to make the thumbnails
			if (count($options['thumbsizes']) > 0 && in_array($data[$model->alias][$fieldName]['type'], $this->_videoTypes)) {
				$this->_createVideoThumbnails($model, $data, $fieldName, $saveAs, null, $options);
			}
			
			if (!empty($data[$model->alias][$fieldName]['name'])) {
				$data[$model->alias][$fieldName] = $data[$model->alias][$fieldName]['name']; 
			}
		}
		return true;
	} 
	
/**
 * Merges two arrays recursively
 * primeminister / 2009-11-13 : Added fix for numeric arrays like allowedMime and allowedExt.
 * These values will remain intact even if the passed options were shorter.
 * Solved that with array_splice to keep intact the previous indexes (already merged)
 *
 * @param $arr Array
 * @param $ins Array
 * @return array
 * @author Vinicius Mendes
 */
	function _arrayMerge($arr, $ins) {
		if (is_array($arr)) {
			if (is_array($ins)) {
				foreach ($ins as $k => $v) {
					if (isset($arr[$k]) && is_array($v) && is_array($arr[$k])) {
						$arr[$k] = $this->_arrayMerge($arr[$k], $v);
					} elseif (is_numeric($k)) {
						array_splice($arr, $k, count($arr));
						$arr[$k] = $v;
					} else {
						$arr[$k] = $v;
					}
				}
			}
		} elseif (!is_array($arr) && (strlen($arr) == 0 || $arr == 0)) {
			$arr = $ins;
		}
		return $arr;
	}
 
/**
 * Replaces some tokens. {ModelName} to the underscore version of the model name
 * {fieldName} to the field name, {DS}. / or \ to DS constant value.
 *
 * @param $string String
 * @param $fieldName String
 * @return string
 * @author Vinicius Mendes
 */
	function _replaceTokens(&$model, $string, $fieldName, $tokens = array()) {
		return str_replace(
			$tokens,
			array(Inflector::underscore($model->name), $fieldName, DS, DS, DS),
			$string
		);
	}
 
/**
 * Removes the bad characters from the $filename and replace reserved words. It updates the $model->data.
 *
 * @param $fieldName String
 * @return void
 * @author Vinicius Mendes
 */
	function _fixName(&$model, $fieldName, $checkFile = true, $options = false) {
		
		// updates the filename removing the keywords thumb and default name for the field.
		if (!empty($options['uploadName'])){
			$filename = $model->data[$model->alias][$options['uploadName']];
		} else {
			$filename = $model->data[$model->alias][$fieldName]['name'];	
		}
		
		if (isset($options['lowerCaseFileNames'])){
			if ($options['lowerCaseFileNames']){
				$filename = strtolower($filename);	
			}				
		}			
				
 		list ($filename, $ext) = $this->_splitFilenameAndExt($filename);
		$filename = str_replace($this->patterns, $this->replacements, $filename);
		$filename = Inflector::slug($filename);
		$i = 1;
		$newFilename = $filename;
		if ($checkFile) {
			while (file_exists(WWW_ROOT . $this->__fields[$model->alias][$fieldName]['dir'] . DS . $newFilename . '.' . $ext)) {
				$newFilename = $filename . '-' . $i++;
			}
		}
		$newFilename = $model->seoUrl($newFilename);
		
		$filename = $newFilename . '.' . $ext;
		
		if (!empty($options['uploadName'])){
			$model->data[$model->alias][$options['uploadName']] = $filename;
		} else {
			$filename = $model->data[$model->alias][$fieldName]['name'] = $filename;	
		}
		
		return $filename;
	}
 
/**
 * Include a pattern of reserved word based on a filename, and it's replacement.
 *
 * @param $default String
 * @return void
 * @author Vinicius Mendes
 */
	function _includeDefaultReplacement($default) {
		$replacements = $this->replacements;
		list ($newPattern, $ext) = $this->_splitFilenameAndExt($default);
		if (!in_array($newPattern, $this->patterns)) {
			$this->patterns[] = $newPattern;
			$newReplacement = $newPattern;
			if (isset($newReplacement[1])) {
				if ($newReplacement[1] != '_') {
					$newReplacement[1] = '_';
				} else {
					$newReplacement[1] = 'a';
				}
			} elseif ($newReplacement != '_') {
				$newReplacement = '_';
			} else {
				$newReplacement = 'a';
			}
			$this->replacements[] = $newReplacement;
		}
	}
 
/**
 * Splits a filename in two parts: the name and the extension. Returns an array with it respectively.
 *
 * @param $filename String
 * @return array
 * @author Juan Basso
 */
	function _splitFilenameAndExt($filename) {
		extract(pathinfo($filename));
		if (!isset($filename)) {
			$filename = substr($basename, 0, -1 - count($extension)); // Remove extension and .
		}
		if (!isset($extension)){
			$extension = '';
		}
	 
		return array($filename, $extension);
	}
 
/**
 * Generate the name for the thumbnail
 * If a 'normal' thumbnail is set, then it will overwrite the original file
 *
 * @param $saveAs String name for original file
 * @param $dir String directory for all uploads
 * @param $key String thumbnail size
 * @param $fieldToSaveAs String field in model to save as
 * @param $sub String substring to append to directory for naming
 * @return string
 * @author Jose Diaz-Gonzalez
 **/
	function _getThumbnailName($saveAs, $dir, $key, $fieldToSaveAs, $sub = null) {
		if ($key == 'normal') {
			return $saveAs;
		}
		// Otherwise, set the thumb filename to thumb.$key.$filename.$ext
		$result = $dir . DS . 'thumb' . DS . $key . DS . $fieldToSaveAs;
		if (isset($sub)) {
			return $result . '.' . $sub;
		}
		$result = str_replace('//','/', $result);
		return $result;
	}
 
/**
 * Convert a size value to bytes. For example: 2 MB to 2097152.
 *
 * @param $size String
 * @return int
 * @author Vinicius Mendes
 */
	function _sizeToBytes($size) {
		if (is_numeric($size)) {
			return $size;
		}
		if (!preg_match('/^([1-9][0-9]*)[ ]*(kb|mb|gb|tb)$/i', $size, $matches)) {
			trigger_error(__('Upload Error: The max_size option format is invalid.', true), E_USER_ERROR);
			return 0;
		}
		switch (strtolower($matches[2])) {
			case 'kb':
				return $matches[1] * 1024;
			case 'mb':
				return $matches[1] * 1048576;
			case 'gb':
				return $matches[1] * 1073741824;
			case 'tb':
				return $matches[1] * 1099511627776;
			default:
				trigger_error(__('Upload Error: The max_size unit is invalid.', true), E_USER_ERROR);
		}
		return 0;
	}
 
/**
 * Sets the validation for each field, based on the options.
 *
 * @param $fieldName String
 * @param $options Array
 * @return void
 * @author Vinicius Mendes
 */
	function _setupValidation(&$model, $fieldName, $options) {
		$options = $this->__fields[$model->alias][$fieldName];
 
		if (isset($model->validate[$fieldName])) {
			if (isset($model->validate[$fieldName]['rule'])) {
				$model->validate[$fieldName] = array(
					'oldValidation' => $model->validates[$fieldName]
				);
			}
		} else {
			$model->validate[$fieldName] = array();
		}
		$model->validate[$fieldName] = $this->_arrayMerge($this->defaultValidations, $model->validate[$fieldName]);
		$model->validate[$fieldName] = $this->_arrayMerge($options['validations'], $model->validate[$fieldName]);
	}
 
 
/**
 * Creates thumbnail folders if they do not already exist
 *
 * @param $dir string Path to uploads
 * @param $key string Name for particular thumbnail type
 * @return void
 * @author Jose Diaz-Gonzalez
 **/
	function _createFolders($dir, $thumbsizes, $permisions = 0755) {
		if ($dir[0] !== '/') {
			$dir = WWW_ROOT . $dir;
		}
		
		$dir = str_replace('//', '/', $dir);
		
		// LogError('to dir: '. $dir); 
		
		$folder = new Folder($dir . DS . 'thumb', true, $permisions);
		/*
		$folder = new Folder();
		if (!$folder->cd($dir)) {
			$folder->create($dir);
		}
		$folder = new Folder($dir . DS . 'thumb', true, 777);
		if (!$folder->cd($dir. DS . 'thumb')) {
			$folder->create($dir . DS . 'thumb');
		}
		 */
		foreach ($thumbsizes as $thumbsize) {
			
			if ($thumbsize != 'normal') { // && !$folder->cd($dir . DS .'thumb' . DS . $thumbsize)
				$folder = new Folder($dir . DS . 'thumb' . DS . $thumbsize, true, $permisions);
				// $folder->create($dir . DS . 'thumb' . DS . $thumbsize);
			}
		}
	}
 
/**
 * Copies file from temporary directory to final destination
 *
 * @param $tmpName string full path to temporary file
 * @param $saveAs string full path to move the file to
 * @return mixed true is successful, error message if not
 * @author Jose Diaz-Gonzalez
 **/
	function _copyFileFromTemp($tmpName, $saveAs, $copy, $permisions = 0644) {
		$results = true;
		
		if (!is_uploaded_file($tmpName) || $copy) {
			if (!file_exists($tmpName) || strpos($tmpName, APP_PATH)<0){
				return false;
			}
		}
		// LogError('to tmpname: '. $tmpName . ' save:'.$saveAs); 
		
		// $file = new File($tmpName, $saveAs);
		
		$file = new File($tmpName, false);
		$temp = new File($saveAs, true, $permisions);
		
		// LogError('to read: '. $file->readable() . ' save:'. $temp->writable()); 
		
		if (!$file->readable()){
			if (!@move_uploaded_file($tmpName, $saveAs)){
				// LogError('OK: '. $saveAs);
				$results = __('Upload Error: Problems in the copy of the file.', true);
			}
		} else {
			if (!$temp->write($file->read())) {
				$results = __('Upload Error: Problems in the copy of the file.', true);
			}	
		}
		
		
		$file->close();
		$temp->close();
		return $results;
	}
 
/**
 * Set a file to be removed in afterSave() callback
 *
 * @param $fieldName String
 * @return void
 * @author Vinicius Mendes
 */
	function _setFileToRemove(&$model, $fieldName) {
		$filename = $model->field($fieldName);
		if (!empty($filename) && $filename != $this->__fields[$model->alias][$fieldName]['default']) {
			if (empty($this->__fields[$model->alias][$fieldName]['thumbsizes'])) {
				$this->__filesToRemove[] = array(
					'field' => $fieldName,
					'dir' => $this->__fields[$model->alias][$fieldName]['dir'],
					'name' => $filename
				);
				return;
			}
			foreach($this->__fields[$model->alias][$fieldName]['thumbsizes'] as $key => $sizes){
				if ($key === 'normal') {
					$subpath = '';
				} else {
					$subpath = DS . 'thumb' . DS . $key;
				}
				$this->__filesToRemove[] = array(
					'field' => $fieldName,
					'dir' => $this->__fields[$model->alias][$fieldName]['dir'] . $subpath,
					'name' => $filename
				);
			}
		}
	}
 
/**
 * Marks files for deletion in the beforeSave() callback
 *
 * @param $modelName string name of the Model
 * @param $modelPrimaryKey string field of the Model that is the primary key
 * @param $fieldName string name of field that holds a reference to the file
 * @param $data array
 * @param $default
 * @return void
 * @author Jose Diaz-Gonzalez
 **/
	function _markForDeletion($modelName, $modelPrimaryKey, $fieldName, $data, $default) {
		if (!empty($data[$modelName][$fieldName]['remove'])) {
			if ($default) {
				$data[$modelName][$fieldName] = $default;
			} else {
				$data[$modelName][$fieldName] = '';
			}
			//if the record is already saved in the database, set the existing file to be removed after the save is sucessfull
			if (!empty($data[$modelName][$modelPrimaryKey])) {
				$this->_setFileToRemove($model, $fieldName);
			}
		}
	}
 
/**
 * Delete the $filename inside the $dir and the thumbnails.
 * Returns true if the file is deleted and false otherwise.
 *
 * @param $filename Object
 * @param $dir Object
 * @return boolean
 * @author Vinicius Mendes
 */
	function _deleteFiles(&$model, $field, $filename, $dir) {
		$dir  = str_replace('/', DS, $dir);
		$saveAs = WWW_ROOT . $dir . DS . $filename;
			
		if (is_file($saveAs) && !unlink($saveAs)) {
			return false;
		}
		if (is_array($this->__fields[$model->alias][$field]['thumbsizes'])){
			foreach ($this->__fields[$model->alias][$field]['thumbsizes'] as $size => &$config) {
				
				$file = WWW_ROOT . $dir . DS . 'thumb' . DS . $size . DS . $filename;
				$fileImage = preg_replace('/^(.*)\.([a-z]{2,3})$/i', '\1.jpg', $file);
				
  				$file = &new File($file);
				if ($file->exists()){
					$file->delete();
				} else {
					$file = &new File($fileImage);
					if ($file->exists()){				
						$file->delete();
					}
				}
			}
		}
		return true;
	}
 
/**
 * Unsets data from $data
 * Useful for no-db upload
 *
 * @param $modelName string name of the Model
 * @param $fieldName string name of field that holds a reference to the file
 * @param $data array
 * @param $options array
 * @return array
 * @author Jose Diaz-Gonzalez
 **/
	function _unsetDataFields($modelName, $fieldName, $data, $options) {
		unset($data[$modelName][$fieldName]);
		unset($data[$modelName][$options['fields']['dir']]);
		unset($data[$modelName][$options['fields']['filesize']]);
		unset($data[$modelName][$options['fields']['mimetype']]);
		return $data;
	}
 
/**
 * Adds an error, legacy from the component
 *
 * @param $msg string error message
 * @return void
 * @author Jose Diaz-Gonzalez
 **/
	function _addError($msg) {
		$this->errors[] = $msg;
		LogError(print_r($msg, true));
	}
}
