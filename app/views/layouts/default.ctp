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
    <meta name="author" content="Treecom s.r.o">    
    <meta name="copyright" content="Copyright (c) 2012 Treecom s.r.o">    
    
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
<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand logo" href="/"> Screenbox <?php __('Server') ?></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="dropdown <?php echo (strpos($this->here, '/box')>-1 ? 'active' : '') ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                	<b class="icon icon-hdd"></b>
                	<?php  __('Boxes') ?>
                	<b class="caret"></b>
                 </a>     
                 <ul class="dropdown-menu">
                 	<li class="<?php echo (strpos($this->here, '/boxes/')>-1 ? 'active' : '') ?>"><a href="/boxes/"><b class="icon icon-hdd"></b> <?php  __('Boxes listing') ?></a></li>
                 	<li class="<?php echo (strpos($this->here, '/box/')>-1 ? 'active' : '') ?>"><a href="/box/"><b class="icon icon-hdd"></b> <?php  __('Box add') ?></a></li>
                 </ul>
              </li>
              <li class="dropdown <?php echo (strpos($this->here, '/media')>-1 ? 'active' : '') ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                	<b class="icon icon-play-circle"></b>
                	<?php  __('Media') ?>
                	<b class="caret"></b>
                </a>

                <ul class="dropdown-menu">
                 	<li class="<?php echo (strpos($this->here, '/media/')>-1 ? 'active' : '') ?>"><a href="/media/"><b class="icon icon-play-circle"></b> <?php  __('Media listing') ?></a></li>
                 	<li class="<?php echo (strpos($this->here, '/medium/')>-1 ? 'active' : '') ?>"><a href="/medium/"><b class="icon icon-play-circle"></b> <?php  __('Medium add') ?></a></li>
                 </ul>
              </li>
             
              <li class="<?php echo (strpos($this->here, '/stats')>-1 ? 'active' : '') ?>">
                <a href="/stats"><b class="icon icon-bar-chart"></b>
                	<?php  __('Stats') ?>
               	</a>
              </li>
              </ul>
              <ul class="nav pull-right">  
              	<li class="dropdown">
                	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                		<b class="icon icon-cogs"></b>
                		<?php  __('Settings') ?>
                		<b class="caret"></b>
                	</a>
                	<ul class="dropdown-menu">
                		<li class="<?php echo (strpos($this->here, '/settings')>-1 ? 'active' : '') ?>"><a href="/settings/"><b class="icon icon-cogs"></b> <?php  __('Edit settings') ?></a></li>
                		<li class="<?php echo (strpos($this->here, '/users/')>-1 ? 'active' : '') ?>"><a href="/users/"><b class="icon icon-group"></b> <?php  __('Users') ?></a></li>
                		<li class="<?php echo (strpos($this->here, '/user/')>-1 ? 'active' : '') ?>"><a href="/user/"><b class="icon icon-group"></b> <?php  __('Add user') ?></a></li>
                		<li class="<?php echo (strpos($this->here, '/user_groups/')>-1 ? 'active' : '') ?>"><a href="/user_groups/"><b class="icon icon-group"></b> <?php  __('User groups') ?></a></li>
                		<li class="<?php echo (strpos($this->here, '/user_rights/')>-1 ? 'active' : '') ?>"><a href="/user_rights/"><b class="icon icon-group"></b> <?php  __('User rights') ?></a></li>
                	</ul>
              </li>  
              <li class="">
                <a href="/login/off"><b class="icon icon-off"></b><?php  __('Logout') ?></a>
              </li>           
            </ul>
          </div>
        </div>
      </div>
</div>
<div class="container top">
	<?  $session->flash(); ?>
	<?= $content_for_layout; ?>
</div>
</body>
</html>