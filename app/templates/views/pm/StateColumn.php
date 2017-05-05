<?php 
$text_rgb = array(255,255,255);
if ( $color == 'false' || $color == '' ) {
	$color_class = $terminal ? 'label-success' : 'label-warning';
}

$title = '<span class="label-state label '.$color_class.'" id="'.$id.'" style="background-color:'.$color.';'.ColorUtils::getTextStyle($color).'">'.$name.'</span>';

if ( count($actions) > 0 ) {
    echo $view->render('core/EmbeddedRowTitleMenu.php', array(
        'title' => $title.' <span class="label">...</span>',
        'items' => $actions,
        'id' => $id
    ));
}
else {
    echo $title;
}
