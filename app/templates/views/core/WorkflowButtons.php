<div class="filter-actions">
<?php foreach( $actions as $item ) { ?>
    <? if ( strpos($item['uid'], 'workflow-') === false ) continue; ?>
    <div class="btn-group pull-left">
        <a id="<?=$item['uid']?>" class="btn btn-small btn-warning" href="<?=$item['url']?>">
            <?=$item['name']?>
        </a>
    </div>
<?php } ?>
</div>
<div class="clearfix"></div>