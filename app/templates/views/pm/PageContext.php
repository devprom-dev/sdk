<div class="context-container">
    <div class="context-left"></div>
    <div class="context-middle">
        <?php
        $id = md5(uniqid(time().$title,true));
        if ( $titleUrl != '' ) {
            echo '<a class="btn btn-link" href="'.$titleUrl.'">'.$title.'</a>';
        }
        else {
            echo $title;
        }
        ?>
        <div class="btn-group row-group-btn more-actions">
            <div class="btn dropdown-toggle" data-toggle="dropdown" href="" data-target="#textmenu<?=$id?>" title="<?=text(2634)?>">
                <span class="label">...</span>
            </div>
        </div>
        <div class="btn-group dropdown-fixed" id="textmenu<?=$id?>">
            <?php echo $view->render('core/PopupMenu.php', array('items' => $actions)); ?>
        </div>
    </div>
    <div class="context-right"></div>
</div>