<?php

$language = getLanguage();
$last_month = $language->getPhpDate( strtotime('-1 month', strtotime(date('Y-m-j'))) );
$last_week = $language->getPhpDate( strtotime('-1 week', strtotime(date('Y-m-j'))) );
$hasSearchOption = false;
$optionsUrl = '';

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

	if ( EnvironmentSettings::getBrowserIE() && strpos($items[$key]['url'], 'javascript:') !== false ) {
        $items[$key]['click'] = $items[$key]['url'];
        unset($items[$key]['url']);
    }
}

if ( EnvironmentSettings::getBrowserIE() ) {
    foreach( $items as $key => $action ) {
        if ( strpos($items[$key]['url'], 'javascript:') !== false ) {
            $items[$key]['click'] = $items[$key]['url'];
            unset($items[$key]['url']);
        }
    }
}

if ( count($items) < 1 ) return;
$itemsCount = count($items);

?>

<ul class="dropdown-menu text-left" role="menu" uid="<?=$uid?>">

<?php foreach ( array_values($items) as $index => $action ) { ?>

	<?php
	if ( is_array($action['items']) )
	{
		$realActions = array_filter(array_values($action['items']), function($item) {
		    return $item['name'] != '';
        });
		if ( count($realActions) < 1 ) {
			$action['name'] = ''; // mark empty sub-menu as separator
		}
		else {
			if ( count($action['items']) > 1 ) {
				?>
				<li class="dropdown-submenu">
					<a><?= $action['name'] ?></a>
                        <?php echo $view->render('core/PopupMenuBody.php', array('items' => $action['items'], 'uid' => $action['uid'])); ?>
				</li>
				<?php
				$last_action = $action;
				continue;
			}
			else {
			    $prefix = $action['name'] . ': ';
				$action = array_shift($action['items']);
                $action['name'] = $prefix . $action['name'];
			}
		}
	}

	if ( $action['uid'] == 'search' ) {
        $hasSearchOption = true;
	    ?>
		<li uid="search" class="dropdown-item-search">
			<input class="" type="text" placeholder="<?=text(2186)?>">
		</li>
		<li class="divider"></li>
	    <?php
		continue;
	}

    if ( $action['uid'] == 'options' ) {
        $optionsUrl = $action['href'];
        continue;
    }

		?>
		<?php  if ( $action['name'] == '' ) { ?>
			<? if ( $last_action['name'] != '' && $index < $itemsCount - 1) { ?>
				<li class="divider"></li>
			<? } ?>
		<?php } else { ?>
		
		<?php $class = (isset($action['multiselect']) ? "checkable" : (isset($action['radio']) ? "radio" : "") ); ?>
		
		<?php $class = ($action['checked'] ? $class.' checked' : $class); ?>
		
		<li uid="<?=$action['uid']?>">

		    <?php
                if ( $action['click'] != '' ) $action['url'] = $action['click'];
    		    if ( $class == '' ) {
                    if ( $action['class'] == 'image_attach' ) {
                        $attributes = 'data-fancybox="gallery"';
                    }
                ?>
    			
    			<a id="<?=$action['uid']?>" class="<?=$action['class']?>" <?=$attributes?> alt="<?=$action['alt']?>" <?=($action['target'] != '' ? 'target="'.$action['target'].'"' : '')?> <?=($action['url'] != '' ? 'href="'.$action['url'].'"' : '')?> title="<?=$action['title']?>"><?=$action['name']?></a>
    		    
    		    <?php } else { ?>
    			
    			<a id="<?=$action['uid']?>" class="<?=$class?>" radio-group="<?=$action['radio-group']?>" onkeydown="<?=$action['url']?>" title="<?=$action['title']?>"><?=$action['name']?></a>
    			
    			<?php } ?>
    			
		</li>
		
		<?php } ?>
<?php

$last_action = $action;
}

if ( $optionsUrl != '' ) {
    ?>
    <li uid="options" class="dropdown-item-options">
        <a target="_blank" href="<?=$optionsUrl?>"><?=text(2491)?></a>
    </li>
    <?php
}
?>

</ul>