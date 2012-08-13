<header class="jumbotron subhead" id="overview">
<?php if(!empty($data['UserGroup']['id'])): ?>
  <h1><?php __("Edit users group") ?></h1>
  <p class="lead"><?php echo ' '.$data['UserGroup']['name']; ?></p>
<?php else: ?>
	<h1><?php __("Add users group") ?></h1>
  <p class="lead"><?php __("Add new user group to your network.") ?></p>
 <?php endif; ?>
</header>

 
<div class="users-form">

<?php if (isset($data['success']) && $data['success']==true): ?>
<div class="alert alert-success">
	<span class="icon-ok"></span>
	<?php __("User data was successfuly saved!") ?>
</div>
<?php endif; ?>

<?php 

	echo $form->create('UserGroup', array('url'=>'/user_group', 'class' => 'form-horizontal well'));
	echo $form->hidden('UserGroup.id');
	echo $form->input('UserGroup.name',  array('label'=> __('Name', true)));	
	echo $form->input('UserGroup.description',  array('type'=>'textarea','label'=> __('Description', true)));
	echo $form->input('UserGroup.active', array('type'=>'checkbox', 'label'=> __('Active', true)));
	echo $form->button('<span class="icon-save"></span> '. __('Save', true), array('class' => 'btn btn-primary'));
	echo $form->end();
?>
</div>
