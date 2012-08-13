<header class="jumbotron subhead" id="overview">
  <h1><?php __("Companies") ?></h1>
  <p class="lead"><?php __("Companies list connected to your network.") ?></p>
</header>


<div class="companies-list">
<table class="table table-bordered">
    <tr>
		<th width="32"> </th>
		<th><?php __("Name") ?></th>
		<th><?php __("City") ?></th>
		<th><?php __("Country") ?></th>			
    </tr>
    <?php foreach((array)$data['Companies'] as $line): ?>
    <tr>
    	<td>
			<i class="edit icon-edit" data-id="<?php echo $line['id'] ?>" rel="tooltip" title="<?php __('Edit Company') ?>"> </i>
			<i class="delete icon-remove" data-id="<?php echo $line['id'] ?>" rel="tooltip" title="<?php __('Remove Company') ?>"> </i>
		</td>
		<td><?php echo $line['name'] ?></td>
		<td><?php echo $line['city'] ?></td>
		<td><?php echo $line['country'] ?></td>		
    </tr>
	<?php endforeach; ?>
</table>
</div>


<script type="text/javascript">
	$('.edit,.delete').css('cursor','pointer').tooltip({'placement':'right'});
	$('.edit').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/company/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/companies/delete/'+id;
		}
	});
</script>