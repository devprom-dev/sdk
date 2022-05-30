<?php
$full_volume = $data['capacity'];
$used_volume = $data['leftwork'];
$left_volume = $full_volume - $used_volume;

if ( $full_volume > 0.0 ) {
    $filled_volume = round(($used_volume / $full_volume) * 100, 0);
}

$overload = false;
if($left_volume < 0) {
    $overload = true;
    if ( $filled_volume > 0.0 ) {
        $filled_volume = round((100 / $filled_volume) * 100, 0);
    }
    else {
        $filled_volume = 0;
    }
}
$title =
    preg_replace(
        array('/%0/', '/%1/', '/%2/', '/%3/'),
        array(
            $measure->getDimensionText(round($used_volume, 1)),
            $measure->getDimensionText(round($used_volume, 1)),
            $measure->getDimensionText(round($full_volume, 1)),
            $measure->getDimensionText(abs(round($left_volume,2)))
        ),
        $overload ? text(2170) : text(2169)
    );

$cell = array (
    'name' => mb_substr($data['number'], 0, 20),
    'progress' => $overload
        ? '<div class="progress progress-text"><div class="bar bar-text bar-danger" style="width: 100%;"></div><span>'.$title.'</span></div>'
        : '<div class="progress progress-text progress-success"><div class="bar bar-text bar-success" style="width:'.$filled_volume.'%;"></div><span>'.$title.'</span></div>',
    'url' => $data['url']
);

?>
<div class="planning-details">
    <div class="progress-cell" title="<?=$cell['title']?>">
        <? if ( $cell['url'] != '' ) { ?>
            <a href="javascript:<?=$cell['url']?>">
                <?=$cell['progress']?>
            </a>
        <? } else { ?>
            <?=$cell['progress']?>
        <? } ?>
    </div>
</div>