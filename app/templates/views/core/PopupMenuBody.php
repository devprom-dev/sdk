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

foreach( $items as $key => $action )
{
	if ( $action['name'] == '' && $action['url'] == '' )
	{
		unset($items[$key]); continue;
	}
	
	break;
}

if ( count($items) < 1 ) return;

?>

<ul class="dropdown-menu" role="menu">

<?php foreach ( $items as $action ) { ?> 

	<?php  if ( is_array($action['items']) ) { ?>
		
	<li class="dropdown-submenu">
		<a href="#"><?=$action['name']?></a>
		<?php echo $view->render('core/PopupMenuBody.php', array( 'items' => $action['items'] )); ?>
	</li>
		
	<?php } else { ?>
	
		<?php  if ( $action['url'] == '' && $action['click'] == '' || $action['name'] == '' ) { ?>
		
		<li class="divider"></li>
			
		<?php } else { ?>
		
		<?php $class = (isset($action['multiselect']) ? "checkable" : (isset($action['radio']) ? "radio" : "") ); ?>
		
		<?php $class = ($action['checked'] ? $class.' checked' : $class); ?>
		
		<li uid="<?=$action['uid']?>">

		    <?php if ( $action['click'] != '' ) { ?>
		    
    			<a onclick="<?=$action['click']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
		    
		    <?php } else { ?>
		    
    		    <?php if ( $class == '' ) { ?>
    			
    			<a class="<?=$action['class']?>" href="<?=$action['url']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
    		    
    		    <?php } else { ?>
    			
    			<a class="<?=$class?>" radio-group="<?=$action['radio-group']?>" onkeydown="<?=$action['url']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
    			
    			<?php } ?>
    			
			<?php } ?>
			
		</li>
		
		<?php } ?>
	<?php } ?>
<?php } ?>

</ul>