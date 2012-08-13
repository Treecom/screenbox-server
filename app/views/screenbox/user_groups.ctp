<header class="jumbotron subhead" id="overview">
  <h1>Users groups</h1>
  <p class="lead">Users groups listing in your network.</p>
</header>


<div class="users-list">
<table class="table table-bordered">
    <tr>
		<th width="32"> </th>
		<th><?php __("Name") ?></th>
	 
		<th><?php __("Active") ?></th>		
    </tr>
    <?php foreach((array)$data['UserGroups'] as $line): ?>
    <tr>
    	<td>
			<i class="edit icon-edit" data-id="<?php echo $line['id'] ?>" rel="tooltip" title="<?php __('Edit Group') ?>"> </i>
			<i class="delete icon-remove" data-id="<?php echo $line['id'] ?>" data-name="<?php echo $line['name'] ?>"rel="tooltip" title="<?php __('Remove Group') ?>" data-toggle="modal" data-target="#delModal"> </i>
		</td>
		<td><?php echo $line['name'] ?></td>		
		<td><a href="/user_groups/active/<?php echo $line['id'] .'/'. ($line['active']==1 ? 0:1) ?>"><?php echo ($line['active']==1 ? __('Yes', true) : __('No', true)) ?></a></td>		
    </tr>
	<?php endforeach; ?>
</table>
</div>

<div class="modal hide" id="delModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3>Delete group?</h3>
  </div>
  <div class="modal-body">
    <p>Are you shore to delete group "<b>%name%</b>"?</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Close</a>
    <a href="#" class="btn btn-primary" data-ok="modal">Delete</a>
  </div>
</div>

<script type="text/javascript">
	$('.edit,.delete').css('cursor','pointer').tooltip({'placement':'right'});
	$('.edit').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/user_group/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		var name = $(this).attr('data-name');
		console.log('here..');
		$('#delModal b').html(name);
		$('#delModal [data-ok="modal"]').click(function (){
			if (id>0){
				window.location = '/user_groups/delete/'+id;
			}
			$('#delModal').modal('hide');
			return true;
		});		
	});
</script>