<?php 
/** 
 * The majority of code in this file is based on CakePHP's Debugger. 
 * This code is designed to work with CakePHP 1.2RC2 and no warranty is given nor implied.
 * @link http://www.cakephp.org 
 * @link http://www.firephp.org 
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License 
 */ 
 
if(!class_exists('Debugger')) { 
    App::import('Core', 'Debugger'); 
} 
if(!class_exists('FirePHP')) { 
    App::import('Vendor', 'FirePHP', array('file' => 'FirePHP'.DS.'FirePHP.class.php')); 
} 
if(!function_exists('fb')) { 
    function fb() {

		$debug = Configure::read('debug');

		if ($debug>0) {	
			ob_start();			 
			$instance = FirePHP::getInstance(true);
			$args = func_get_args();
            return call_user_func_array(array($instance, 'fb'), $args);
		} else {
			return true;
		}
	} 
}

class FirePHPDebugger extends Debugger { 

/** 
 * holds current output format 
 * 
 * @var string 
 * @access private 
 */ 
    var $__outputFormat = 'fb'; 

/** 
 * FirePHP error level 
 * 
 * @var string 
 * @access public 
 */ 
    var $FirePHPLevel = ''; 

/** 
 * Gets a reference to the Debugger object instance 
 * 
 * @return object 
 * @access public 
 */ 
    function &getInstance() { 
        static $instance = array(); 

        if (!isset($instance[0]) || !$instance[0]) { 
            $instance[0] =& new FirePHPDebugger(); 
            if (Configure::read() > 0) { 
                Configure::version(); // Make sure the core config is loaded 
                $instance[0]->helpPath = Configure::read('Cake.Debugger.HelpPath'); 
            } 
        } 
        return $instance[0]; 
    } 

/** 
 * Overrides PHP's default error handling 
 * 
 * @param integer $code Code of error 
 * @param string $description Error description 
 * @param string $file File on which error occurred 
 * @param integer $line Line that triggered the error 
 * @param array $context Context 
 * @return boolean true if error was handled 
 * @access public 
 */ 
    function handleError($code, $description, $file = null, $line = null, $context = null) { 
        if (error_reporting() == 0 || $code === 2048 || $code === 8192) { 
            return; 
        } 

        $_this = FirePHPDebugger::getInstance(); 

        if (empty($file)) { 
            $file = '[internal]'; 
        } 
        if (empty($line)) { 
            $line = '??'; 
        } 
        $path = $_this->trimPath($file); 

        $info = compact('code', 'description', 'file', 'line'); 
        if (!in_array($info, $_this->errors)) { 
            $_this->errors[] = $info; 
        } else { 
            return; 
        } 

        $level = LOG_DEBUG; 
        switch ($code) { 
            case E_PARSE: 
            case E_ERROR: 
            case E_CORE_ERROR: 
            case E_COMPILE_ERROR: 
            case E_USER_ERROR: 
                $error = 'Fatal Error'; 
                $level = LOG_ERROR; 
                $this->FirePHPLevel = FirePHP::ERROR; 
            break; 
            case E_WARNING: 
            case E_USER_WARNING: 
            case E_COMPILE_WARNING: 
            case E_RECOVERABLE_ERROR: 
                $error = 'Warning'; 
                $level = LOG_WARNING; 
                $this->FirePHPLevel = FirePHP::WARN; 
            break; 
            case E_NOTICE: 
            case E_USER_NOTICE: 
                $error = 'Notice'; 
                $level = LOG_NOTICE; 
                $this->FirePHPLevel = FirePHP::INFO; 
            break; 
            default: 
                return false; 
            break; 
        } 

        $helpCode = null; 
        if (!empty($_this->helpPath) && preg_match('/.*\[([0-9]+)\]$/', $description, $codes)) { 
            if (isset($codes[1])) { 
                $helpCode  = $helpID = $codes[1]; 
                $description = trim(preg_replace('/\[[0-9]+\]$/', '', $description)); 
            } 
        } 

        // echo $_this->__output($level, $error, $code, $helpCode, $description, $file, $line, $context);
		$data = compact(
			'level', 'error', 'code', 'helpID', 'description', 'file', 'path', 'line', 'context'
		);
		
		// echo $_this->_output($data); 
		$this->__output($data);

        if (Configure::read('log')) { 
          //  CakeLog::write($level, "{$error} ({$code}): {$description} in [{$file}, line {$line}]"); 
        } 

        if ($error == 'Fatal Error') { 
            die('Fatal Error'); 
        } 
        return true; 
    } 

/** 
 * Handles object conversion to debug string 
 * 
 * @param string $var Object to convert 
 * @access private 
 */ 
    function __output($data) { 
        $_this = FirePHPDebugger::getInstance(); 
        if($_this->__outputFormat !== 'fb') {  			
            return Debugger::__output($data);			
        } 		
		extract($data); 
        $files = $_this->trace(array('start' => 2, 'format' => 'points')); 
        $listing = $_this->fbFormat($_this->excerpt($files[0]['file'], $files[0]['line'] - 1, 1)); 
        $trace = $_this->fbFormat($_this->trace(array('start' => 2, 'depth' => '20'))); 
        $_context = '<br />'; 
        foreach ((array)$context as $var => $value) { 
            $_context.= "\${$var} = " . $_this->exportVar($value, 1)."\n"; 
        } 
        $_context = $_this->fbFormat($_context); 
        $instance = FirePHP::getInstance(true); 
        $message = "{$error} ({$code}): {$description} [{$path}{$file}, line {$line}"; 
        $out = array( 
            'trace' => $trace, 
            'code' => $listing, 
            'context' => $_context, 
        ); 
        call_user_func_array(array($instance,'fb'),array($out, $message,  $this->FirePHPLevel)); 
		fb($data);
    } 

/** 
 * Function to format data to look purdy in FireBug 
 * 
 * @param mixed $data Data to be formatted for FireBug 
 * @return string Formatted FireBug data 
 */ 
    function fbFormat($data = '') { 

        if(is_array($data)) { 
            $data = join($data); 
        } 
        $data = strip_tags($data); 
        $data =  str_ireplace("\t", '&nbsp;&nbsp;', str_ireplace("\n", '<br />', $data)) ; 
        return $data; 
    } 
} 

if (!defined('DISABLE_DEFAULT_ERROR_HANDLING')) { 
    FirePHPDebugger::invoke(FirePHPDebugger::getInstance()); 
} 
