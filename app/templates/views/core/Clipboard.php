<?php
    if ( $icon == '' ) $icon = "icon-share";
?>
<button type="button" class="btn-link clipboard" data-clipboard-text="<?=$url?>" data-message="<?=text(2029)?>" tabindex="-1" uid="<?=$btnuid?>">
    <i class="<?= $icon ?>"></i> <?=$uid?>
</button>
