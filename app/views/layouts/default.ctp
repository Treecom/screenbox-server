<?php

/**
 * Default layout template 
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
	<script type="text/javascript" src="http://twitter.github.com/bootstrap/assets/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.js"></script>
    <script type="text/javascript" src="/js/site.js"></script>    

    <?= $css_for_layout; ?>	
 	<?= $scripts_for_layout; ?> 	
</head>
<body>
<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button"class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">   
	         <span class="icon-bar"></span>
	         <span class="icon-bar"></span>
	         <span class="icon-bar"></span>    
          </button>
          <a class="brand" href="/">Screenbox Server</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="">
                <a href="/boxes">
                	<span class="icon icon-hdd"></span>
                	Boxes 
                 </a>     
              </li>
              <li class="">
                <a href="/media"><span class="icon icon-play-circle"></span>
                	Media</a>
              </li>
             
              <li class="">
                <a href="/stats"><span class="icon icon-bar-chart"></span>
                	Stats</a>
              </li>
              </ul>
              <ul class="nav pull-right">  
              	<li class="dropdown">
                	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon icon-cogs"></span>
                	Settings
                	<b class="caret"></b></a>
                	<ul class="dropdown-menu">
                		<li><a href="/settings"><span class="icon icon-cogs"></span> Edit settings</a></li>
                		<li><a href="/users"><span class="icon icon-group"></span> Users</a></li>
                	</ul>
              </li>  
              <li class="">
                <a href="/login/off" class="dropdown-toggle" data-toggle="dropdown"><span class="icon icon-off"></span>Logout</a>
              </li>           
            </ul>
          </div>
        </div>
      </div>
</div>
<div class="container">
	<?  $session->flash(); ?>
	<?= $content_for_layout; ?>
</div>
</body>
</html>