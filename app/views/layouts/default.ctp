<?php

/**
 * Default layout template 
 */

?><!DOCTYPE HTML>
<html lang="<?= str_replace("eng","en", Configure::read('Domain.locale')); ?>" dir="ltr">
<head>
	<title><?= $title_for_layout; ?></title>
	<?= $html->charset(); ?>
	<?= $html->meta('icon') . "\n"; ?>
	<?= $meta_for_layout; ?>	
 	<?=	$html->css('site'). "\n"; ?>
	<?=	$html->css('text'). "\n";?>	
	<?= $css_for_layout; ?>	
 	<?= $scripts_for_layout; ?>
	<?= $javascript->link('jquery/jquery.js'). "\n";  ?>
	<?= $javascript->link('site.js'). "\n";  ?>
</head>
<body>
<?  $session->flash(); ?>
<?= $content_for_layout; ?>
</body>
</html>