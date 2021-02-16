<?php foreach( $attributes as $key => $attribute ) { ?>

    <?php if ( !$attribute['visible'] ) { ?>
        <input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">
        <?php continue; } ?>

    <div class="control-group row-fluid" id="fieldRow<?=$key?>">
        <div class="controls2">
            <? echo $view->render('core/PageFormAttribute.php', $attribute); ?>

            <?php if ( $attribute['description'] != '' ) { ?>
                <span class="help-block"><?=$attribute['description']?></span>
            <?php } ?>
        </div>
    </div>

<?php } ?>
