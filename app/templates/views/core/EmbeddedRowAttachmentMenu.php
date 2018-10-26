<?php
$deleteItem = array_shift($items);
?>

<span class="badge-file" objectid="<?=$id?>" objectclass="<?=$class?>">
    <? if ( is_array($deleteItem) ) { ?>
    <a class="att-act" href="<?=$deleteItem['url']?>" title="<?=$deleteItem['name']?>">
        &#10006;
    </a>
    <? } ?>

    <?=$title?>
    <span class="att-info">
        <?=$info?>
    </span>
</span>
