<header class="jumbotron subhead" id="overview">
  <h1><?php __('Screenboxes') ?></h1>
  <p class="lead"><?php __('Your Screenboxes network') ?></p>
</header>

<table class="table table-bordered">
    <tr>
		<th width="35"> </th>
		<th><?php __('Screenbox name') ?></th>
		<th><?php __('Location') ?></th>
		<th><?php __('Public') ?></th>
    </tr>

    <?php foreach((array)$data['Screenbox'] as $line): ?>
    <tr>
		<td>
			<i href="#" class="edit icon-edit" title="<?php __('Edit') ?>" data-id="<?php echo $line['id']?>"> </i>			
			<i href="#" class="delete icon-remove" title="<?php __('Delete') ?>"  data-id="<?php echo $line['id']?>"> </i>
		</td>
		<td class="tt" title="<?php echo strip_tags($line['description']) ?>"><?php echo $line['name'] ?></td>
		<td><?php echo $line['city'] .', '. $line['street'] ?></td>
		<td><a href="/boxes/public/<?php echo $line['id'] .'/'. ($line['public']==1 ? 0:1) ?>"><?php echo ($line['public']==1 ? __('Yes', true) : __('No', true)) ?></a></td>
    </tr>  
    <?php endforeach; ?>
</table>

<script type="text/javascript">
	$('.edit,.delete,.public').css('cursor','pointer').tooltip({placement:'right'});
	$('.tt').tooltip({placement:'bottom',delay:{show:1000}});
	$('.edit').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/box/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/boxes/delete/'+id;
		}
	});
	$('.delete').on('click', function(e){
		var id = $(this).attr('data-id');
		if (id>0){
			window.location = '/boxes/public/'+id;
		}
	});
</script>