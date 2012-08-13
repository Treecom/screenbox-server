<!-- navigation start -->
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand logo" href="/">Screenbox <?php __('Server') ?></a>
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
                <b class="icon icon-group"></b>
                <?php  __('Users') ?>
                <b class="caret"></b>
              </a>
              <ul class="dropdown-menu">                  
                <li class="<?php echo (strpos($this->here, '/users/')>-1 ? 'active' : '') ?>"><a href="/users/"><b class="icon icon-group"></b> <?php  __('Users listing') ?></a></li>
                <li class="<?php echo (strpos($this->here, '/user/')>-1 ? 'active' : '') ?>"><a href="/user/"><b class="icon icon-user"></b> <?php  __('User add') ?></a></li>
                <li class="divider"></li>
                <li class="<?php echo (strpos($this->here, '/companies/')>-1 ? 'active' : '') ?>"><a href="/companies/"><b class="icon icon-group"></b> <?php  __('Companies listing') ?></a></li>
                <li class="<?php echo (strpos($this->here, '/company/')>-1 ? 'active' : '') ?>"><a href="/company/"><b class="icon icon-user"></b> <?php  __('Company add') ?></a></li>
                <li class="divider"></li>
                <li class="<?php echo (strpos($this->here, '/user_groups/')>-1 ? 'active' : '') ?>"><a href="/user_groups/"><b class="icon icon-group"></b> <?php  __('Users groups') ?></a></li>
                <li class="<?php echo (strpos($this->here, '/user_group/')>-1 ? 'active' : '') ?>"><a href="/user_group/"><b class="icon icon-group"></b> <?php  __('Users group add') ?></a></li>
                <li class="divider"></li>
                <li class="<?php echo (strpos($this->here, '/user_rights/')>-1 ? 'active' : '') ?>"><a href="/user_rights/"><b class="icon icon-group"></b> <?php  __('Users rights') ?></a></li>
              </ul>
          </li>
          <li class="<?php echo (strpos($this->here, '/settings')>-1 ? 'active' : '') ?>">
            	<a href="/settings/">
            		<b class="icon icon-cogs"></b>
            		<?php  __('Settings') ?>
            	</a>
          </li>  
          <li>
            <a href="/login/off"><b class="icon icon-off"></b><?php  __('Logout') ?></a>
          </li>           
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- navigation end -->
