<?php

$custom_tiles = array_filter($tiles, function($value) {
	return $value['kind'] == 'process';
});
$builtin_tiles = array_filter($tiles, function($value) {
	return $value['kind'] != 'process' && !in_array($value['file'], array('reqs_ru.xml','sdlc_ru.xml'));
});
$design_tiles = array_filter($tiles, function($value) {
    return in_array($value['file'], array('reqs_ru.xml','sdlc_ru.xml'));
});

?>

<?php $view->extend('core/PageBody.php'); ?>

<?php $view['slots']->start('_header'); ?>
<?php $view['slots']->stop(); ?>

<script>
	function heightTemplateBlock() {
		var maxHeight = 0;
		$('.template-list.first .template').css('height', 'auto');
		$('.template-list.first .template').each(function(){
			if ($(this).outerHeight() > maxHeight) { maxHeight = $(this).height(); }
		});
		$('.template-list.first .template').css('height', maxHeight);
	}
        
    $(document).ready(function(){
        $('.accordion-title span').on('click', function(){
            var curEl = $(this).closest('.accordion-item'),
                curElHeight = curEl.find('.accordion-dscr').outerHeight();

            $('.accordion-item').not(curEl).find('.accordion-cont').css('height', 0);
            curEl.find('.accordion-cont').css('height', curElHeight);

            $('.accordion-item').not(curEl).removeClass('active');
            curEl.addClass('active');

            return false;
        });

        $('.checkbox input').change( function() {
            runMethod(
                '?method=SettingsWebMethod',
                {
                    'setting' : 'projects-welcome-page',
                    'value' : $(this).is(':checked') ? 'off' : 'on'
                },
                function() {},
                ''
            );
        });
        heightTemplateBlock();
    });

    $(window).resize(function(){
        heightTemplateBlock();
    });
</script>
<div class="form-container">

        <div class="create-project-header">
            <div class="pull-left">
                <h4 class="bs"><?=text('co23')?></h4>
            </div>

            <?php if ( $custom_template_url != '' ) { ?>
            <div class="pull-right">
                <a href="/admin/templates.php"><?=text('co53')?></a>
            </div>
            <?php } ?>

            <? if ( count($languages) > 1 ) { ?>
            <div class="pull-right language-buttons">
                <? foreach( $languages as $language ) { ?>
                <a href="?language=<?=htmlentities($language['cms_LanguageId'])?>" class="btn btn-sm <?=($language_selected == $language['cms_LanguageId'] ? "btn-primary" : "")?>"><?=translate($language['Caption'])?></a>
                <? } ?>
            </div>
            <? } ?>
        </div>

        <?php if ( count($custom_tiles) > 0 ) { ?>
            <div class="create-project-info">
                <p>
                    <i class="icon-info"></i> <?=text('co56')?>
                </p>
            </div>
            <ul class="template-list first">
                <?php foreach ( $custom_tiles as $tile ) { ?>
                    <?php if ( $tile['kind'] == 'methodology' ) continue; ?>

                    <li class="template-list-item">
                        <div class="template green <?=(!$tile['active'] ? 'locked' : '')?>">
                            <?php if ( !$tile['active'] ) { ?>
                                <i class="icon-lock"></i>
                            <?php } else if ( $tile['url'] != '' ) { ?>
                                <a target="_blank" href="<?=htmlentities($tile['url'])?>" title="<?=text('co28')?>"><i class="icon-info"></i></a>
                            <?php } ?>
                            <h6 class="bs"><?=$tile['name']?></h6>
                            <p>
                                <?=$tile['description']?>
                            </p>
                            <?php if ( $tile['active'] ) { ?>
                                <a href="/projects/new?Template=<?=htmlentities($tile['id'])?>" class="template-action"><?=text('co30')?></a>
                            <?php } else { ?>
                                <a href="<?=htmlentities($tile['url'])?>" target="_blank" class="template-action"><?=text('co29')?></a>
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>

                <?php if ( !$custom_template_exists && $custom_template_url != '' ) { ?>
                    <li class="template-list-item">
                        <div class="template grey">
                            <i class="icon-lock"></i>
                            <h6 class="bs"><?=text('co26')?></h6>
                            <p><?=text('co27')?></p>
                            <a href="<?=$custom_template_url?>" target="_blank" class="template-action"><?=text('co28')?></a>
                        </div>
                    </li>
                <?php $custom_template_exists = true; } ?>
            </ul>
        <?php } ?>

		<div class="create-project-info">
			<p>
				<i class="icon-info"></i> <?=text('co52')?>
			</p>
		</div>

		<ul class="template-list">
			<?php foreach ( $builtin_tiles  as $tile ) { ?>
				<li class="template-list-item">
					<div class="template <?=(!$tile['active'] ? 'locked' : 'orange')?>">
						<?php if ( !$tile['active'] ) { ?>
							<i class="icon-lock"></i>
						<?php } else if ( $tile['url'] != '' ) { ?>
							<a target="_blank" href="<?=htmlentities($tile['url'])?>" title="<?=text('co28')?>"><i class="icon-info"></i></a>
						<?php } ?>
						<h6 class="bs"><?=$tile['name']?></h6>
						<p>
							<?=$tile['description']?>
						</p>
						<?php if ( $tile['active'] ) { ?>
						<a href="/projects/new?Template=<?=htmlentities($tile['id'])?>" class="template-action"><?=text('co30')?></a>
						<?php } else { ?>
						<a href="<?=htmlentities($tile['url'])?>" target="_blank" class="template-action"><?=text('co29')?></a>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		</ul>

		<?php if ( count($design_tiles) > 0 ) { ?>
			<div class="create-project-info">
				<p>
					<i class="icon-info"></i> <?=text('co31')?>
				</p>
			</div>
			<ul class="template-list">
			<?php foreach ( $design_tiles as $tile ) { ?>
                <li class="template-list-item">
                    <div class="template <?=(!$tile['active'] ? 'locked' : '')?>">
                        <?php if ( !$tile['active'] ) { ?>
                            <i class="icon-lock"></i>
                        <?php } else if ( $tile['url'] != '' ) { ?>
                            <a target="_blank" href="<?=htmlentities($tile['url'])?>" title="<?=text('co28')?>"><i class="icon-info"></i></a>
                        <?php } ?>
                        <h6 class="bs"><?=$tile['name']?></h6>
                        <p>
                            <?=$tile['description']?>
                        </p>
                        <?php if ( $tile['active'] ) { ?>
                            <a href="/projects/new?Template=<?=htmlentities($tile['id'])?>" class="template-action"><?=text('co30')?></a>
                        <?php } else { ?>
                            <a href="<?=htmlentities($tile['url'])?>" target="_blank" class="template-action"><?=text('co29')?></a>
                        <?php } ?>
                    </div>
                </li>
			<?php } ?>

            <?php if ( !$custom_template_exists && $custom_template_url != '' ) { ?>
                <li class="template-list-item">
                    <div class="template grey">
                        <i class="icon-lock"></i>
                        <h6 class="bs"><?=text('co26')?></h6>
                        <p><?=text('co27')?></p>
                        <a href="<?=$custom_template_url?>" target="_blank" class="template-action"><?=text('co28')?></a>
                    </div>
                </li>
            <?php } ?>

			</ul>
		<?php } ?>
</div>