<?php 
	
/**
 *  Media Listing 
 */
 	
if (!empty($data)): 

	// paginator options
	$options = array('model'=>'Media');
	$pgPrev = '« '.__("Previous",true).' ';
	$pgNext = ' '.__("Next",true).' »';
	
	// current url def.
	$url= "/";
	
	if (!empty($this->params['route']['context_url'])){
		$url .= $paginator->options['url'] = $this->params['route']['context_url'];		
	}
	
	if (!empty($properties['previewSubContext'])){
		$url .= $properties['previewSubContext']."/";		
	}
	if (empty($properties['limit'])){
		$properties['limit'] = 10;
	}
?>
<div class="news-listing">
	<div class="news-listing-title">
	<?php
		if (!empty($properties['title'])){
			e($properties['title']);
		} elseif (!empty($this->params['context']['name']) && empty($properties['title'])){
			e($this->params['context']['name']);
		} else{ 
			__('Media');
		}
	?>
	</div>
	<div class="news-listing-content">
		<ul class="news-listing-ul">
			<?php 
			
			foreach($data as $line): 
				// need to move this to db-table and create beforeSave
				$custom_url = $url.$navigation->seoUrl($line['Media']['id'].'_'.$line['Media']['title']).'.html';
			?>
				<li><div class="news-listing-item-title"><a href="<?= $custom_url ?>"><?= $line['Media']['title'] ?></a><br />
						<span class="news-listing-item-date"><?= date('H:i d.m.Y', $line['Media']['created_time']) ?></span>
					</div>
					<div class="news-listing-item-anotation"><?= $line['Media']['description'] ?></div>
				</li>
			<?php
			 endforeach; 
			?>
		</ul>
	</div> 
</div>
<?php 
	// pagination
	if ($paginator->counter('%count%')>$properties['limit']): 
?>
	<div class="pagin-canvas">
		<div class="pagin-np"><?= $paginator->prev($pgPrev, $options, null, array('class' => 'pagin-disabled')) ?></div>
		<div class="pagin-nums"><?= $paginator->numbers($options) ?></div>
		<div class="pagin-np"><?= $paginator->next($pgNext, $options, null, array('class' => 'pagin-disabled')) ?></div> 
	</div>
	<?= "<!-- ". $paginator->counter($options) ."-->" ?> 
<?php 	
	endif; 
else: 
	echo $this->element('error_data_missing');
endif;  
?>