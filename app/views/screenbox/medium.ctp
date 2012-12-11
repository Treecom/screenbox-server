<header class="jumbotron subhead" id="overview">
<?php if(!empty($data['Media']['id'])): ?>
  <h1><?php __("Edit Media") ?></h1>
  <p class="lead"><?php echo ' '.$data['Media']['name']; ?></p>
<?php else: ?>
	<h1><?php __("Add Media") ?></h1>
  <p class="lead"><?php __("Add new Media to your network.") ?></p>
 <?php endif; ?>
</header>

 
<div class="medium-form">

<?php if (isset($data['success']) && $data['success']==true): ?>
<div class="alert alert-success">
	<span class="icon-ok"></span>
	<?php __("User data was successfuly saved!") ?>
</div>
<?php endif; ?>

<?php 

	$days = array(
		"1" => __("Monday", true),
		"2" => __("Tuesday", true),
		"3" => __("Wednesday", true),
		"4" => __("Thursday", true),
		"5" => __("Friday", true),
		"6" => __("Saturday", true),
		"7" => __("Sunday", true)		
	);

	$hours = array();
	for ($i=0; $i < 24; $i++) { 		
		$hours[$i] = $i . ":00 - " .($i+1).":00";
	}

	echo $form->create('Media', array('url'=>'/medium/', 'class' => 'form-horizontal well'));
	echo $form->hidden('Media.id');
	 
	echo $form->input('Media.name',  array('label'=> __('Media name', true)));
	echo $form->input('Media.company_id',  array('label'=> __('From company', true), 'options' => $data['Companies']));	

	echo $form->input('Media.priority',  array('label'=> __('Priority (0-9999)', true)));

	echo $form->input('Media.file',  array('label'=> __('Media file', true), 'type'=> 'file'));

	echo $form->input('Media.screenboxes',  array('label'=> __('Play in Screenboxes', true), 'type'=>'select','multiple'=>'true', 'options'=>$data['Screenboxes']));	

	echo $form->input('Media.days',  array('label'=> __('Play in days', true), 'type'=>'select','multiple'=>'true', 'options'=>$days));	

	echo $form->input('Media.hours',  array('label'=> __('Play in hours', true), 'type'=>'select','multiple'=>'true', 'options'=>$hours));	

	echo $form->input('Media.play', array('type'=>'checkbox', 'label'=> __('Play', true)));
	echo $form->input('Media.public', array('type'=>'checkbox', 'label'=> __('Public', true)));

	echo $form->button('<span class="icon-save"></span> '. __('Save', true), array('class' => 'btn btn-primary'));
	echo $form->end();
?>
</div>
 
 