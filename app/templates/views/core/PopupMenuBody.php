<?php

$language = getLanguage();

$last_month = $language->getPhpDate( strtotime('-1 month', strtotime(date('Y-m-j'))) );

$last_week = $language->getPhpDate( strtotime('-1 week', strtotime(date('Y-m-j'))) );

foreach( $items as $key => $action )
{
	if ( is_object($action[0]) )
	{
		$method = $action[0];
		
		$items[$key]['url'] = $method->getParametersUrl($action[1]);
		
		if ( $items[$key]['url'] == '' )
		{
			$items[$key]['url'] = $method->getUrl($action[1]);
		}
		
		$items[$key]['name'] = $method->getCaption();
		
		continue;
	}
	
	if ( !isset($action[0]) ) continue;
	
	if ( !isset($action['name']) )
 	{
 		$items[$key]['url'] = $action[0];
 		$items[$key]['name'] = $action[1];
 	}
 	
 	$items[$key]['url'] = preg_replace('/last-month/', $last_month,
 	        preg_replace('/last-week/', $last_week, $items[$key]['url']));
}
if ( count($items) < 1 ) return;

?>

<ul class="dropdown-menu" role="menu" uid="<?=$uid?>">

<?php foreach ( $items as $action ) { ?> 

	<?php
	if ( is_array($action['items']) )
	{
		$first_item = array_pop(array_values($action['items']));
		if ( $first_item['name'] != '' ) {
			?>
			<li class="dropdown-submenu">
				<a href="#"><?= $action['name'] ?></a>
				<?php echo $view->render('core/PopupMenuBody.php', array('items' => $action['items'])); ?>
			</li>

			<?php
		}
		else {
			$action['name'] = ''; // mark empty sub-menu as separator
		}
	}
	else if ( $action['uid'] == 'search' ) {
	?>
		<li uid="search" class="dropdown-item-search">
			<input class="" type="text" placeholder="<?=text(2186)?>">
		</li>
		<li class="divider"></li>
	<?php
	}
	else
	{
		?>
		<?php  if ( $action['url'] == '' && $action['click'] == '' && $action['name'] == '' ) { ?>
			<? if ( $last_action['name'] != '') { ?>
				<li class="divider"></li>
			<? } ?>
		<?php } else { ?>
		
		<?php $class = (isset($action['multiselect']) ? "checkable" : (isset($action['radio']) ? "radio" : "") ); ?>
		
		<?php $class = ($action['checked'] ? $class.' checked' : $class); ?>
		
		<li uid="<?=$action['uid']?>">

		    <?php if ( $action['click'] != '' ) { ?>
		    
    			<a id="<?=$action['uid']?>" onclick="<?=$action['click']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
		    
		    <?php } else { ?>
		    
    		    <?php if ( $class == '' ) { ?>
    			
    			<a id="<?=$action['uid']?>" class="<?=$action['class']?>" alt="<?=$action['alt']?>" target="<?=$action['target']?>" href="<?=$action['url']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
    		    
    		    <?php } else { ?>
    			
    			<a id="<?=$action['uid']?>" class="<?=$class?>" radio-group="<?=$action['radio-group']?>" onkeydown="<?=$action['url']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
    			
    			<?php } ?>
    			
			<?php } ?>
			
		</li>
		
		<?php } ?>
	<?php } ?>
<?php

$last_action = $action;
} ?>

</ul>