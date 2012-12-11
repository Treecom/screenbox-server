<?php

/**
 * Blank layout template 
 */

?><!DOCTYPE html>
<html lang="en">
<head>
	<title><?= $title_for_layout; ?></title>
	<?= $html->charset(); ?>	
    <?= $meta_for_layout; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/normalize.css" rel="stylesheet">
	<link href="/css/bootstrap-slate.css" rel="stylesheet">
    <link href="/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/css/jquery-ui-1.8.16.custom.css" rel="stylesheet">
    <link href="/css/font-awesome.css" rel="stylesheet">    
    <link href="/css/site.css" rel="stylesheet">
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.js"></script>
    <script type="text/javascript" src="/js/site.js"></script>    

    <?= $css_for_layout; ?>	
 	<?= $scripts_for_layout; ?> 	
</head>
<body>
<div class="container">
	<?  $session->flash(); ?>
	<?= $content_for_layout; ?>
</div>
</body>
</html>