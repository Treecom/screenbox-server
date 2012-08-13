<header class="jumbotron subhead" id="overview">
<?php if(!empty($data['Media']['id'])): ?>
  <h1><?php __("Edit Media") ?></h1>
  <p class="lead"><?php echo ' '.$data['Media']['name']; ?></p>
<?php else: ?>
	<h1><?php __("Add Media") ?></h1>
  <p class="lead"><?php __("Add new Media to your network.") ?></p>
 <?php endif; ?>
</header>

 
<div class="screenbox-form">

<?php if (isset($data['success']) && $data['success']==true): ?>
<div class="alert alert-success">
	<span class="icon-ok"></span>
	<?php __("User data was successfuly saved!") ?>
</div>
<?php endif; ?>

<?php 

	echo $form->create('Media', array('url'=>'/medium/', 'class' => 'form-horizontal well'));
	echo $form->hidden('Media.id');
	
	echo $form->input('Media.company_id',  array('label'=> __('Company', true)));	

	echo $form->input('Media.name',  array('label'=> __('Name', true)));
	echo $form->input('Media.priority',  array('label'=> __('Priority', true)));

	echo $form->input('Media.play', array('type'=>'checkbox', 'label'=> __('Play', true)));
	echo $form->input('Media.public', array('type'=>'checkbox', 'label'=> __('Public', true)));

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