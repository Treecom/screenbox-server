<header class="jumbotron subhead" id="overview">
  <h1>Users accounts</h1>
  <p class="lead">Users list with access to your network.</p>
</header>


<div class="users-list">
<table class="table table-bordered">
    <tr>
		<th width="32"> </th>
		<th><?php __("Email") ?></th>
		<th><?php __("First name") ?></th>
		<th><?php __("Last name") ?></th>	
		<th><?php __("Active") ?></th>		
    </tr>
    <?php foreach((array)$data['Users'] as $line): ?>
    <tr>
    	<td>
			<i class="edit icon-edit" data-id="<?php echo $line['id'] ?>" rel="tooltip" title="<?php __('Edit User') ?>"> </i>
			<i class="delete icon-remove" data-id="<?php echo $line['id'] ?>" rel="tooltip" title="<?php __('Remove User') ?>"> </i>
		</td>
		<td><?php echo $line['email'] ?></td>
		<td><?php echo $line['first_name'] ?></td>
		<td><?php echo $line['last_name'] ?></td>
		<td><a href="/users/active/<?php echo $line['id'] .'/'. ($line['active']==1 ? 0:1) ?>"><?php echo ($line['active']==1 ? __('Yes', true) : __('No', true)) ?></a></td>		
    </tr>
	<?php endforeach; ?>
</table>
</div>


<script type="text/javascript">
	$('.edit,.delete').css('cursor','pointer').tooltip({'placement':'right'});
	$('.edit').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/user/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/users/delete/'+id;
		}
	});
</script>