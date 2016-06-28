<a action="reset" class="btn pull-left" href="<?=$url?>/reset"><?=text(1908)?></a>
<?php if( $lead_role ) { ?>
<div class="span8">
<a action="makedefault" class="btn btn-danger pull-left" href="<?=$url?>/makedefault"><?=text(1909)?></a>
    <? if ( $template != '' ) { ?>
    <a action="resettodefault" class="btn btn-danger pull-left" style="margin-left:16px;" href="<?=$url?>/resettodefault"><?=text(2120)?></a>
    <? } ?>
</div>
<?php } ?>
<span class="clearfix"></span>