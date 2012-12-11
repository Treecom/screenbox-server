<header class="jumbotron subhead" id="overview">
<?php if(!empty($data['User']['id'])): ?>
  <h1><?php __("Edit user") ?></h1>
  <p class="lead"><?php echo ' '.$data['User']['first_name'] . ' ' . $data['User']['last_name']; ?></p>
<?php else: ?>
	<h1><?php __("User add") ?></h1>
  <p class="lead"><?php __("Add new user to your network.") ?></p>
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

	echo $form->create('User', array('url'=>'/user', 'class' => 'form-horizontal well'));
	echo $form->hidden('User.id');
	echo $form->input('User.email',  array('label'=> __('Email', true)));
	echo $form->input('User.first_name',  array('label'=> __('First Name', true)));
	echo $form->input('User.last_name',  array('label'=> __('Last Name', true)));	
	echo $form->input('User.user_group_id', array('label'=> __('User Group', true), 'options' => $data['UserGroup']));
	echo $form->input('User.password',  array('label'=> __('Password', true)));
	echo $form->input('User.password2', array('type'=>'password','label'=>__('Repeat Password', true)));
	echo $form->input('User.active', array('type'=>'checkbox', 'label'=> __('Active', true)));
	echo $form->button('<span class="icon-save"></span> '. __('Save', true), array('class' => 'btn btn-primary'));
	echo $form->end();
?>
</div>
 
<script type="text/javascript">
// bootstrap workarounds
$('.error-message').addClass('label label-important');
$('.input').addClass('control-group');
$(".alert").alert()
</script>