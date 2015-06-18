<?php

class MainDevpromTable extends BaseDEVPROMTable
{
 	function __construct()
 	{
 	 	if ( $_REQUEST['mode'] != '' )
 		{
			$obsolete = $this->getObsolete();
			
			$redirect = $obsolete[IteratorBase::utf8towin($_REQUEST['mode'])];
			
			if ( $redirect != '' ) 
			{
				header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
				
				exit(header('Location: '.IteratorBase::wintoutf8($redirect)));
			}
 		}

 		parent::__construct();
 	}
 	
 	function getObsolete()
 	{
 		return array (
 				'Система-управления-требованиями-Devprom-Requirements' => 'http://devprom.ru/features/Система-управления-требованиями-Devprom-Requirements',
 				'Enterprise' => 'http://devprom.ru/features/Система-управления-требованиями-Devprom-Requirements'
 		);
 	}
 	
 	function getTitle()
 	{
 		return 'Devprom ALM - инструмент управления жизненным циклом разработки программного обеспечения';
 	}
 	
	function draw()
 	{
 		global $_REQUEST, $model_factory;
 		
 		$root_it = $this->getObjectIt();
		$this->drawCommon( $root_it );
 		
 		?>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.tabs').each( function( index, value ) {
					$(this).attr('seq', index);
					$(this).find('.menu li').each( function( index, value ) {
						$(this).attr('seq', index);
					});
				});
				
				$('.menu ul li a').attr('href', 'javascript:')
					.click(function() {
						var seq = $(this).parent().attr('seq');
						$('.tabs').hide();
						$('.tabs[seq='+seq+'] menu li[seq='+seq+']').attr('class', 'current');
						$('.tabs[seq='+seq+']').show();
					}); 

				$('.tabs').hide();
				$('.slider .item').hide();
				$('.slider .item').filter(':first').show();
				$('.tabs[seq=0]').show();
		
				$('.slider .item').each( function( index, value ) {
					$(this).attr('seq', index).css( { marginLeft: '0' } );
				});
				
				$('.slider .leftA').attr('href', 'javascript:')
					.click( function () {
						clearInterval( sliderTimer );
						
						if ( $('.slider .item').filter(':visible').length < 1 ) {
							return;
						}
	
						$('.slider .item').filter(':visible').animate(
							{ marginLeft: '200px', opacity: 'toggle' }, 200, '',
							function () { 
								var new_index = parseInt($(this).attr('seq')) - 1;
								if ( new_index < 0 ) new_index = $('.slider .item').length - 1;

								$(this).hide(); 

								$('.slider .item[seq='+new_index+']')
									.css( {marginLeft: '-100px'} )
									.animate( { marginLeft: '0', opacity: 'toggle' }, 200 ); 
							}
						);
					});

				$('.slider .rightA').attr('href', 'javascript:')
					.click( function (e) {
						if ( typeof e.pageX != 'undefined' ) {
							clearInterval( sliderTimer );
						}

						if ( $('.slider .item').filter(':visible').length < 1 ) {
							return;
						}
						
						$('.slider .item').filter(':visible').
							animate( { marginLeft: '-200px', opacity: 'toggle' }, 200, '',
								function () { 
									var new_index = parseInt($(this).attr('seq')) + 1;
									if ( new_index >= $('.slider .item').length ) new_index = 0;
	
									$(this).hide(); 
	
									$('.slider .item[seq='+new_index+']')
										.css( {marginLeft: '100px'} )
										.animate( { marginLeft: '0', opacity: 'toggle' }, 200 ); 
								} );
					});
					
				var sliderTimer = setInterval( function() { $('.slider .rightA').trigger('click'); }, 10000 );

				$('.responses .item').each( function( index, value ) {
					$(this).attr('seq', index);
				});
				
				$('.responses .leftA').attr('href', 'javascript:')
					.click( function () {
						var new_index = parseInt($('.responses .item').filter(':visible').attr('seq')) - 1;
						if ( new_index < 0 ) new_index = $('.responses .item').length - 1;
	
						$('.responses .item').hide();
						$('.responses .item[seq='+new_index+']').show();
					});

				$('.responses .rightA').attr('href', 'javascript:')
					.click( function () {
						var new_index = parseInt($('.responses .item').filter(':visible').attr('seq')) + 1;
						if ( new_index >= $('.responses .item').length ) new_index = 0;
	
						$('.responses .item').hide();
						$('.responses .item[seq='+new_index+']').show();
					});
					
				$('.responses .item').hide();
				$('.responses .item[seq=0]').show();
			});
		</script>
		<?
 	}
 	
 	function drawCommon( $root_it )
 	{
 		global $project_it;
 		
		$parser = new DEVPROMWikiParser( $root_it, $project_it );
		echo $parser->parse();
 	}
}