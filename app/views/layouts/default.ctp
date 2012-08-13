<?php

/**
 * Default layout template 
 */

?><!DOCTYPE html>
<html lang="en">
<head>
	<?= $html->charset(); ?>
  <title><?= $title_for_layout; ?></title>		  
  <meta name="viewport" content="width=device-width, initial-scale=1.0">    
  <meta name="author" content="Treecom s.r.o">    
  <meta name="copyright" content="Copyright (c) 2012 Treecom s.r.o">    
  <?= $meta_for_layout; ?>

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
<?php 
  if ($this->Session->read('Auth.User.active')==1){
    echo $this->element('navigation');
  }
?>
<div class="container top">
<?= $session->flash(); ?>
<?= $content_for_layout; ?>
</div>

</body>
</html>