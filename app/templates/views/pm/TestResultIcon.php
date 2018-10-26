<?php
$style = '';
if ( $color != '' ) {
    $style = 'color:white;border-color:'.$color.';background-color:'.$color.';';
}
else {
    switch( $reference ) {
        case 'succeeded':
        case 'passed':
            $class = 'label-success';
            break;
        case 'failed':
            $class = 'label-important';
            break;
        case 'blocked':
            $class = '';
            break;
        default:
            $class = 'label-warning';
    }
}
$id = md5(uniqid(time().$random,true));
?>
<div class="btn-group" style="text-align:left;" title="<?=$title?>">
	<a class="dropdown-toggle actions-button" data-toggle="dropdown" href="#" data-placement="right" data-target="#testresult<?=$id?>">
		<span class="label <?=$class?>" data-toggle="context" style="<?=$style?>">
			<?=$data?>
		</span>
	</a>
</div>
<div class="btn-group dropdown-fixed" id="testresult<?=$id?>">
	<? echo $this->render('core/PopupMenu.php', array ( 'items' => $items ));?>
</div>