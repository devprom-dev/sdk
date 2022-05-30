<?php
$defaultTab = $_COOKIE[$details_id.'-active'];

if ( !array_key_exists($defaultTab, $details) ) {
    $defaultTab = $details_parms['active'];
    if ( $defaultTab == '' ) {
        $defaultTab = array_shift(array_keys($details));
    }
}

if ( !defined('UI_EXTENSION') || defined('UI_EXTENSION') && UI_EXTENSION ) {
    $detailsVisible = $_COOKIE['toggle-detailspanel-' . $widget_id];
    if ( $detailsVisible == '' ) {
        $detailsVisible = $details_parms['active'] != '' ? 'true' : 'false';
    }
}

$defaultUrl = str_replace('%class%', strtolower($className), str_replace('%id%', $default_id, $details[$defaultTab]['url']));
?>
<div class="details-button">
    <i class="icon-chevron-left"></i>
</div>
<div class="page-details">
    <div class="details-header">
        <? foreach( $details as $id => $detail ) { ?>
            <a class="btn btn-xs <?=($defaultTab == $id ? 'btn-info active' : '')?>" did="<?=$id?>" url="<?=$detail['url']?>" title="<?=$detail['title']?>" default="<?=($id == 'props' ? $default_id : '')?>">
                <i class="<?=$detail['image']?> <?=($defaultTab == $id ? 'icon-white' : '')?>"></i>
            </a>
        <? } ?>
        <button class="btn btn-xs btn-light pull-right" onclick="setTimeout(function(){toggleMasterDetails(true);},300)">
            <i class="icon-chevron-right"></i>
        </button>
    </div>
    <div class="details-body"><div class="document-empty"></div></div>
</div>
<script type="text/javascript">
    var tableDetailsData = {
        id: '<?=$default_id?>',
        class: '<?=strtolower($className)?>'
    };
    devpromOpts.updateUI = function() {
        detailsRefresh({dummy:true});
    };

    $(document).ready(function() {
        $('.page-details a').click(function() {
            $('.details-header a').removeClass('btn-info').removeClass('active');
            $('.details-header a i').removeClass('icon-white');
            $(this).addClass('btn-info').addClass('active');
            $(this).find('i').addClass('icon-white');
            cookies.set('<?=$details_id.'-active'?>', $(this).attr('did'));
            if ( $(this).attr('did') == 'form' ) {
                detailsWideMode(true);
            }
            else {
                detailsWideMode(false);
            }
            $(document).trigger('trackerItemSelected', [tableDetailsData.id, false, tableDetailsData.class]);
        });

        $(document).on("trackerItemSelected", function(e, id, ctrlKey, className) {
            if ( ctrlKey ) return;
            tableDetailsData.id = id;
            tableDetailsData.class = className;
            var btn = $('a.active[did]:visible');
            if( btn.length > 0 ) {
                var url = btn.attr('url');
                if ( typeof id != 'undefined' ) {
                    url = url.replace(/%id%/, id)
                            .replace(/%class%/, typeof className != 'undefined' ? className : '<?=strtolower($className)?>');
                }
                detailsInitialize($('.details-body'),url,true);
            }
        });

        if ( '<?=$defaultTab?>' == 'form' ) {
            detailsWideMode(true);
        }
        <? if ( $detailsVisible == 'true' ) { ?>
            toggleMasterDetails(false);
        <? } ?>

        function detailsWideMode(on) {
            if ( on ) {
                $('.table-master table.table-inner').css('min-width', $('#tablePlaceholder').width() - 30 );
                $('.table-details').addClass('wide');
            }
            else {
                $('.table-master table.table-inner').css('min-width', '');
                $('.table-details').removeClass('wide');
            }
        }
    });
</script>
