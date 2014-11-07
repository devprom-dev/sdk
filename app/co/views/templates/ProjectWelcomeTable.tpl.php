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

			<div class="create-project-header">
				<div class="pull-left">
					<h4 class="bs"><?=text('co23')?></h4>
				</div>
				<div class="pull-right">
					<form role="form">
						<div class="checkbox">
							<label> <?=text('co25')?> <input type="checkbox">
							</label>
						</div>
					</form>
				</div>
			</div>

		<div class="create-project-info">
			<p>
				<i class="icon-info"></i> <?=text('co24')?>
			</p>
		</div>

		<ul class="template-list first">
			<?php foreach ( $tiles as $tile ) { ?>
				<?php if ( $tile['kind'] == 'methodology' ) continue; ?>
				
				<li class="template-list-item">
					<div class="template <?=($tile['kind'] == 'process' ? 'green' : '')?> <?=(!$tile['active'] ? 'locked' : '')?>">
						<?php if ( !$tile['active'] ) { ?><i class="icon-lock"></i> <?php } ?>
						<h6 class="bs"><?=$tile['name']?></h6>
						<p>
							<?=$tile['description']?>
						</p>
						<?php if ( $tile['active'] ) { ?>
						<a href="/projects/new?Template=<?=$tile['id']?>" class="template-action"><?=text('co30')?></a>
						<?php } else { ?>
						<a href="<?=$tile['url']?>" target="_blank" class="template-action"><?=text('co29')?></a>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
			
			<?php if ( !$custom_template_exists ) { ?>
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

		<ul class="template-list">
			<?php foreach ( $tiles as $tile ) { ?>
				<?php if ( $tile['kind'] != 'methodology' ) continue; ?>
				
				<li class="template-list-item">
					<div class="template <?=(!$tile['active'] ? 'locked' : 'orange')?>">
						<?php if ( !$tile['active'] ) { ?><i class="icon-lock"></i> <?php } ?>
						<h6 class="bs"><?=$tile['name']?></h6>
						<p>
							<?=$tile['description']?>
						</p>
						<?php if ( $tile['active'] ) { ?>
						<a href="/projects/new?Template=<?=$tile['id']?>" class="template-action"><?=text('co30')?></a>
						<?php } else { ?>
						<a href="<?=$tile['url']?>" target="_blank" class="template-action"><?=text('co29')?></a>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		</ul>

		<?php if ( count($solutions) > 0 ) { ?>
		<div class="accordion-wr">
			<h4 class="bs"><?=text('co31')?></h4>
			<div class="accordion">
				<?php foreach( $solutions as $solution ) { ?>
					<div class="accordion-item">
						<h6 class="accordion-title">
							<span><?=$solution['name']?></span>
						</h6>
						<div class="accordion-cont">
							<div class="accordion-dscr">
								<p><?=$solution['description']?></p>
								<p>
									<a href="<?=$solution['url']?>" target="_blank"><?=text('co34')?></a>
								</p>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
