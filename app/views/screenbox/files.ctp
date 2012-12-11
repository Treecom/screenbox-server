<?php 

$form->create('FileStore', array(
		'url' => '/files_upload'
));

echo $form->input('FileStore.file',  array('label'=> __('Media file', true), 'type'=> 'file'));
echo $form->button('<span class="icon-save"></span> '. __('Save', true), array('class' => 'btn btn-primary'));
$form->end();

?>

<?php if (!empty($data['Files'])): ?>
	<ul>
		<?php foreach($data['Files'] as $file): ?>
			<li><a href="#file" data-fileid="<?php echo $file['id']?>"><?php echo $file['name']?></li>
		<?php endforeach; ?>
	<ul>
<?php endif; ?>