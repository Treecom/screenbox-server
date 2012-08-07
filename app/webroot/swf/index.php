<?php
/**
 * Forward to site index and protect folder 
 */
header("HTTP/1.1 301 Moved Permanently");
if (!empty($_SERVER['HTTP_HOST'])){
	header("Location: http://".$_SERVER['HTTP_HOST']);
} else {
	header("Location: /");
}
exit();
?>