<?php
$color = $stateIt->get('RelatedColor');
$terminal = $stateIt->get('IsTerminal') == 'Y';
$name = $stateIt->get('Caption');
$referenceName = $stateIt->get('ReferenceName');

$text_rgb = array(255,255,255);
if ( $color == 'false' || $color == '' ) {
	$color_class = $terminal ? 'label-success' : 'label-warning';
}

$title = '<span class="label-state label '.$color_class.'" id="'.$id.'" style="background-color:'.$color.';'.ColorUtils::getTextStyle($color).'">'.$name.'</span>';

if ( count($actions) > 0 ) {
    echo $view->render('core/EmbeddedRowTitleMenu.php', array(
        'title' => $title,
        'items' => $actions,
        'id' => $id,
        'group_class' => 'last'
    ));
}
else {
    if ( is_object($listWidgetIt) && $referenceName != '' ) {
        $url = $listWidgetIt->getUrl('state='.$referenceName);
        if ( $url != '' ) {
            echo '<a href="'.$url.'">'.$title.'</a>';
            return;
        }
    }
    echo $title;
}
