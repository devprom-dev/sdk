<div>
    <label class="radio">
        <input type="radio" name="DemoData" value="N" <?=(in_array($value,array('N')) ? 'checked' : '')?>>
        <?=text(2324)?>
    </label>
    <label class="radio">
        <input type="radio" name="DemoData" value="Y" <?=(in_array($value,array('Y','')) ? 'checked' : '')?>>
        <?=text(1869)?>
    </label>
    <? if ( is_object($trackerField) ) { ?>
    <label class="radio">
        <input type="radio" name="DemoData" value="I" <?=(in_array($value,array('I')) ? 'checked' : '')?>>
        <?=text(2325)?>
    </label>
        <?php
            $trackerField->render($view);
        ?>
    <? } ?>
</div>