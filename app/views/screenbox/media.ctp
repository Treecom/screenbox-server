<header class="jumbotron subhead" id="overview">
  <h1><?php __('Media') ?></h1>
  <p class="lead"><?php __('Your media list') ?></p>
</header>


<table class="table table-bordered">
    <tr>
		<th width="35"> </th>
		<th><?php __('Name') ?></th>		
		<th><?php __('Play') ?></th>
		<th><?php __('Public') ?></th>
    </tr>

    <?php foreach((array)$data['Media'] as $line): ?>
    <tr>
		<td>
			<i class="edit icon-edit" title="<?php __('Edit') ?>" data-id="<?php echo $line['id']?>"> </i>			
			<i class="delete icon-remove" title="<?php __('Delete') ?>"  data-id="<?php echo $line['id']?>"> </i>
		</td>
		<td><?php echo $line['name'] ?></td>
		<td><a href="/media/play/<?php echo $line['id'] .'/'. ($line['play']==1 ? 0:1) ?>"><?php echo ($line['play']==1 ? __('Yes', true) : __('No', true)) ?></a></td>
		<td><a href="/media/public/<?php echo $line['id'] .'/'. ($line['public']==1 ? 0:1) ?>"><?php echo ($line['public']==1 ? __('Yes', true) : __('No', true)) ?></a></td>
    </tr>  
    <?php endforeach; ?>
</table>

<script type="text/javascript">
	$('.edit,.delete,.public').css('cursor','pointer').tooltip({placement:'right'});
	 
	$('.edit').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/medium/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/media/delete/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/media/public/'+id;
		}
	});
</script>