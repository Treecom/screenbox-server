<header class="jumbotron subhead" id="overview">
<?php if(!empty($data['Company']['id'])): ?>
  <h1><?php __("Edit user") ?></h1>
  <p class="lead"><?php echo ' '.$data['Company']['name']; ?></p>
<?php else: ?>
  <h1><?php __("Company add") ?></h1>
  <p class="lead"><?php __("Add company to your network.") ?></p>
<?php endif; ?>
</header>

 
<div class="company-form">

<?php if (isset($data['success']) && $data['success']==true): ?>
<div class="alert alert-success">
	<span class="icon-ok"></span>
	<?php __("Company data was successfuly saved!") ?>
</div>
<?php endif; ?>

<?php 

	echo $form->create('Company', array('url'=>'/company/', 'class' => 'form-horizontal well'));
	echo $form->hidden('Company.id');
	echo $form->input('Company.name',  array('label'=> __('Company name', true)));
	echo $form->input('Company.address',  array('label'=> __('Full address', true), 'type'=>'textarea'));
	echo $form->input('Company.city',  array('label'=> __('City', true)));
	echo $form->input('Company.country',  array('label'=> __('Country', true)));
	echo $form->input('Company.website',  array('label'=> __('Website', true)));
	echo $form->input('Company.email_1',  array('label'=> __('Email', true)));
	echo $form->input('Company.email_2',  array('label'=> __('Email', true)));
	echo $form->input('Company.email_3',  array('label'=> __('Email', true)));
	echo $form->input('Company.phone_1',  array('label'=> __('Phone', true)));
	echo $form->input('Company.phone_2',  array('label'=> __('Phone', true)));
	echo $form->input('Company.phone_3',  array('label'=> __('Phone', true)));
	echo $form->input('Company.mobile_1',  array('label'=> __('Mobile', true)));
	echo $form->input('Company.mobile_2',  array('label'=> __('Mobile', true)));
	echo $form->input('Company.mobile_3',  array('label'=> __('Mobile', true)));
	echo $form->input('Company.fax_1',  array('label'=> __('Fax', true)));
	echo $form->input('Company.fax_2',  array('label'=> __('Fax', true)));
	echo $form->input('Company.latitude',  array('label'=> __('Latitude', true)));
	echo $form->input('Company.lontitude',  array('label'=> __('Lontitude', true)));
	echo $form->input('Company.icq',  array('label'=> __('ICQ', true)));
	echo $form->input('Company.skype',  array('label'=> __('Skype', true)));
	echo $form->input('Company.jabber',  array('label'=> __('Jabber', true)));
	echo $form->input('Company.twitter',  array('label'=> __('Twitter', true)));
	echo $form->input('Company.fbid',  array('label'=> __('Facebook', true)));
	echo $form->input('Company.vat_1',  array('label'=> __('Vat ID 1', true)));
	echo $form->input('Company.vat_2',  array('label'=> __('Vat ID 2', true)));
	 
	echo $form->button('<span class="icon-save"></span> '. __('Save', true), array('class' => 'btn btn-primary'));
	echo $form->end();
?>
</div>
 