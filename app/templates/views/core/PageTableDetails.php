<?php
$active = $_COOKIE[$details_id.'-active'];
if ( !array_key_exists($active, $details) ) {
    $active = $details_parms['active'];
    if ( $active == '' ) {
        $active = array_shift(array_keys($details));
    }
}
?>
<div class="page-details" style="border-top:1px solid #dddddd;border-left:1px solid #dddddd;">
    <div class="sticks-top" heightStyle="window"></div>
    <div class="sticks-top-body">
        <div class="details-header">
            <? foreach( $details as $id => $detail ) { ?>
                <a class="btn btn-mini <?=($active == $id ? 'btn-info active' : '')?>" did="<?=$id?>" url="<?=$detail['url']?>" title="<?=$detail['title']?>" default="<?=($id == 'props' ? $default_id : '')?>">
                    <i class="<?=$detail['image']?> <?=($active == $id ? 'icon-white' : '')?>"></i>
                </a>
            <? } ?>
        </div>
        <div class="details-body"><div class="document-empty"></div></div>
    </div>
</div>
<script type="text/javascript">
    var data = [];
    $(document).ready(function() {
        $('.page-details a').click(function() {
            $('.page-details a').removeClass('btn-info').removeClass('active');
            $('.page-details a i').removeClass('icon-white');
            $(this).addClass('btn-info').addClass('active');
            $(this).find('i').addClass('icon-white');
            cookies.set('<?=$details_id.'-active'?>', $(this).attr('did'));
            var url = $(this).attr('url');
            if ( url == '' ) return;
            var url = url.replace(/%id%/, $.isNumeric($(this).attr('default')) ? $(this).attr('default') : '0');
            detailsInitialize($('.details-body'),url,true,$(this).attr('did') != 'props');
        });
        $(document).on("trackerItemSelected", function(e, id, ctrlKey) {
            if ( ctrlKey ) return;
            var btn = $('.page-details a.active[did="props"]');
            if( btn.length > 0 ) {
                var url = btn.attr('url').replace(/%id%/, $.isNumeric(id) ? id : '0');
                detailsInitialize($('.details-body'),url,true,false);
            }
        });
        detailsInitialize(
            $('.details-body'),
            '<?=str_replace('%id%', $default_id, $details[$active]['url'])?>',
            <?=($visible && !in_array($active, array('')) ? 'true' : 'false')?>,
            <?=(in_array($active, array('props')) ? 'false' : 'true')?>
        );
    });
</script>
