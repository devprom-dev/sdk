<!DOCTYPE html>    
<html class="<?=($inside ? 'inside' : '' )?>">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></meta>
  	<title><?=($title == '' ? $navigation_title : $title)?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&type=css"/>
	<!--[if IE]>
    	<link href="/styles/jquery-ui/jquery.ui.1.8.16.ie.css" rel="stylesheet">
	<![endif]-->
	<link title="" type="application/rss+xml" rel="alternate" href="/rss"/>
	<link rel="stylesheet" href="<?=getSession()->getApplicationUrl()?>scripts/css/?v=<?=$current_version?>" type="text/css" media="screen">
	<?php $view['slots']->output('_header'); ?>
   	<script src="/cache?v=<?=$current_version?>&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
  </head>

  <body>
	<div class="container-fluid wrapper-all <?=($inside ? 'container-fluid-internal' : '')?>">
	  
	  <?php $view['slots']->output('_content'); ?>
	  
	  <footer class="<?=($inside ? 'internal' : '')?>">
		<ul>
			<?php 
	 		
		 	if ( defined('METRICS_VISIBLE') && METRICS_VISIBLE ) 
		 	{
		 	 	$metrics_text = str_replace('%1', MetricsServer::Instance()->getDuration(), text(1067));
	 
			 	$metrics_text = str_replace('%2', MetricsClient::Instance()->getDuration('clscript'), $metrics_text);
		 	}
			
			?>
			<li><? echo '<a target="_blank" href="http://devprom.ru">'.$license_name.'</a> '.$current_version.' '.$metrics_text; ?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
			<li><a target="_blank" href="http://support.devprom.ru/issue/new"><?=translate('Сообщить о проблеме')?></a></li>
		</ul>
	    <p><?=text(1286)?></p>
		<ul>
			<li><a target="_blank" href="http://devprom.ru/docs"><?=translate('документация')?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
			<li><a target="_blank" href="http://devprom.ru/news"><?=translate('новости')?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
			<li><a target="_blank" href="http://devprom.ru/download"><?=translate('обновления')?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
			<li><a target="_blank" href="http://support.devprom.ru"><?=translate('поддержка')?></a></li>
		</ul>
	  </footer>
	</div>	<!-- end container -->

	<?=$scripts?>
	
	<?php if (is_array($javascript_paths)) foreach( $javascript_paths as $path ) { ?>
	<script src="<?=$path?>?v=<?=$current_version?>&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	<?php } ?>
	
   	<script src="/cache-after?v=<?=$current_version?>&l=<?=$language_code?>&dpl=<?=$datelanguage?>" type="text/javascript" charset="UTF-8"></script>

   	<script type="text/javascript">
    
        devpromOpts.language = '<?=$language_code?>';
        devpromOpts.datepickerLanguage = '<?=$datelanguage?>';
        devpromOpts.dateformat = '<?=$dateformat?>';
        devpromOpts.saveButtonName = '<?=translate('Сохранить')?>';
        devpromOpts.closeButtonName = '<?=translate('Отменить')?>';
        devpromOpts.deleteButtonName = '<?=translate('Удалить')?>';
        devpromOpts.template = '<?=$project_template?>';
        devpromOpts.mathJaxLib = '<?=(defined('MATH_JAX_LIB_SRC') ? MATH_JAX_LIB_SRC : "")?>';
        devpromOpts.plantUMLServer = '<?=(defined('PLANTUML_SERVER_URL') ? PLANTUML_SERVER_URL : "")?>';
        <?php if ( !defined('METRICS_CLIENT') || METRICS_CLIENT ) { $global_url = parse_url(_getServerUrl()); ?>
		devpromOpts.url = "<?=$global_url['scheme']?>://devprom.ru/rx";
		devpromOpts.iid = "<?=INSTALLATION_UID?>";
		devpromOpts.version = "<?=$current_version?>";
        <?php } ?>
        
		$.fn.colorPicker.defaults.showHexField = false;

    	$(document).ready( function() {
    		if ( devpromOpts.dateformat != '' ) {
    			$.datepicker.regional[ devpromOpts.datepickerLanguage ].dateFormat = devpromOpts.dateformat; 
    		}

    		$(window).on('beforeunload', function() {
    			return beforeUnload();
    		});

    		$.cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
    		$.cookies.set('devprom-client-tz', jstz.determine().name());
    	});
        
		<?php if ( !defined('UI_EXTENSION') || UI_EXTENSION ) { ?>

		completeUIExt( $(document) );
		
		completeChartsUI( $(document) );

		setUXData();
		
        <?php } ?>
	</script>

	</body>
</html>