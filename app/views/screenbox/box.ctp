<header class="jumbotron subhead" id="overview">
<?php if(!empty($data['User']['id'])): ?>
  <h1><?php __("Edit Screenbox") ?></h1>
  <p class="lead"><?php echo ' '.$data['Screenbox']['name']; ?></p>
<?php else: ?>
	<h1><?php __("Add Screenbox") ?></h1>
  <p class="lead"><?php __("Add new Screenbox to your network.") ?></p>
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

	echo $form->create('Screenbox', array('url'=>'/box', 'class' => 'form-horizontal well'));
	echo $form->hidden('Screenbox.id');
	
	echo $form->input('Screenbox.company_id',  array('label'=> __('Company', true), 'options'=>$data['Companies']));

	echo $form->input('Screenbox.name',  array('label'=> __('Name', true)));
	echo $form->input('Screenbox.description',  array('label'=> __('Description', true)));
	echo $form->input('Screenbox.key',  array('label'=> __('Key', true)));	

	echo $form->input('Screenbox.width',  array('label'=> __('Width', true)));	
	echo $form->input('Screenbox.height',  array('label'=> __('Height', true)));	

	echo $form->input('Screenbox.type',  array('label'=> __('Type', true)));
	echo $form->input('Screenbox.city',  array('label'=> __('City', true)));
	echo $form->input('Screenbox.street',  array('label'=> __('Street', true)));
	echo $form->input('Screenbox.latitude',  array('label'=> __('Latitude', true)));
	echo $form->input('Screenbox.longtitude',  array('label'=> __('Longtitude', true)));

	echo $form->input('Screenbox.shared', array('type'=>'checkbox', 'label'=> __('Shared', true)));
	echo $form->input('Screenbox.public', array('type'=>'checkbox', 'label'=> __('Public', true)));

	echo $form->button('<span class="icon-save"></span> '. __('Save', true), array('class' => 'btn btn-primary'));
	echo $form->end();
?>
</div>
