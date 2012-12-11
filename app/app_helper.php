<?php
 
App::import('Core', 'Helper');
// App::import('Core', 'Sanitize'); // !?performance 
/**
 * This is a placeholder class.
 * Create the same file in app/app_helper.php
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package     cake
 * @subpackage  cake.app.app_helper
 * @author	Martin Bucko 2010 
 */
class AppHelper extends Helper {
	
	var $metaDescription = '';
	var $metaKeywords = '';
	var $helpers = array('Html');
	
	function setMetaKeys($content){
		if (!is_string($content)){
			$content = '';	
		}
		if (is_array($content)){
			$content = join(",", $content);
		}
		// $content = Sanitize::paranoid($content,array(',',';'));
		$content = trim($content);
		if (strlen($content)>254){
			$content = substr($content, 0, 254);
		}
		return $this->metaKeywords = $content;
	}
	
	function setMetaDescription($content){
		if (!is_string($content)){
			$content = '';	
		}
		if (is_array($content)){
			$content = join(" ", $content);
		}
		// $content = Sanitize::paranoid($content);
		$content = trim($content);
		if (strlen($content)>254){
			$content = substr($content, 0, 254);
		}
		return $this->metaDescription = $content;
	}
	
	function getMetaKeywords($keys ,$options = false){
		$keys = empty($keys) ? trim($this->metaKeywords) : $this->setMetaDescription($keys);
		if ($options){
			$keys = $this->Html->meta('keywords',$keys);
		}
		return $keys;
	}
	
	function getMetaDescription($desc, $options = false){
		$desc = empty($desc) ? trim($this->metaDescription) : $this->setMetaDescription($desc);
		if ($options){
			$desc = $this->Html->meta('description',$desc);
		}		
		return $desc ;
	}
	
	/**
	 * seoUrl
	 * Create from string seo url link. 
	 * @param string url
	 * @return string url in seo
	 */
	function seoUrl($url, $separator = '-') {
	    $url = trim($url);
	  	$url = str_replace(' ', $separator, $url);
		$url = str_replace('.', $separator, $url);
		$url = preg_replace('(\-{2,})','',$url);
	    $url = trim($url, "-");
		 if (function_exists('iconv')){
		 	// pozor na setlocale !
 			$url = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $url);
		 }
	    $url = strtolower($url);
	    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
	    return $url;
	}
}
