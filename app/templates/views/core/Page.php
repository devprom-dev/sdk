<!DOCTYPE html>    
<html class="<?=($inside ? 'inside' : '' )?>">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=APP_ENCODING?>"></meta>
  	<title><?=($title == '' ? $navigation_title : $title)?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&asset=1&type=css"/>
	<link rel="stylesheet" href="<?=getSession()->getApplicationUrl()?>scripts/css/?v=<?=$current_version?>" type="text/css" media="screen">
	<link title="" type="application/rss+xml" rel="alternate" href="/rss"/>
	<!--[if IE]>
    	<link href="/styles/jquery-ui/jquery.ui.1.8.16.ie.css" rel="stylesheet">
	<![endif]-->
   	<script src="/cache/?v=<?=$current_version?>&asset=1&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
   	<script src="/cache/?v=<?=$current_version?>&asset=2&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
   	<script src="/cache/?v=<?=$current_version?>&asset=3&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
   	<script src="/scripts/zeroclipboard/ZeroClipboard.min.js?v=<?=$current_version?>" type="text/javascript" charset="UTF-8"></script>
	<?php $view['slots']->output('_header'); ?>
   	</head>
  <body>
	<div class="container-fluid wrapper-all <?=($inside ? 'container-fluid-internal' : '')?>">
	  <?php
		  $view['slots']->output('_content');
		  echo $view->render('PageFooter.php', array(
				'inside' => $inside,
				'license_name' => $license_name,
				'current_version' => $current_version
		  ));
	  ?>
	</div>	<!-- end container -->

	<?=$scripts?>
	
	<?php if (is_array($javascript_paths)) foreach( $javascript_paths as $path ) { ?>
	<script src="<?=$path?>?v=<?=$current_version?>&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	<?php } ?>
	
   	<script src="/cache-after?v=<?=$current_version?>&l=<?=$language_code?>&dpl=<?=$datelanguage?>" type="text/javascript" charset="UTF-8"></script>
	<?php if ( !defined('SEND_BUG_REPORTS') || SEND_BUG_REPORTS ) { ?>
   	<script src="/scripts/raven/raven.min.js?v=<?=$current_version?>" type="text/javascript" charset="UTF-8"></script>
	<?php }?>
   	<script type="text/javascript">
    
        devpromOpts.language = '<?=$language_code?>';
        devpromOpts.datepickerLanguage = '<?=$datelanguage?>';
        devpromOpts.dateformat = '<?=$dateformat?>';
        devpromOpts.saveButtonName = '<?=translate('Сохранить')?>';
        devpromOpts.completeButtonName = '<?=translate('Выполнить')?>';
        devpromOpts.closeButtonName = '<?=translate('Закрыть')?>';
        devpromOpts.deleteButtonName = '<?=translate('Удалить')?>';
        devpromOpts.template = '<?=$project_template?>';
        devpromOpts.mathJaxLib = '<?=(defined('MATH_JAX_LIB_SRC') ? MATH_JAX_LIB_SRC : "")?>';
        devpromOpts.plantUMLServer = '<?=(defined('PLANTUML_SERVER_URL') ? PLANTUML_SERVER_URL : "")?>';
        
		<?php if ( !defined('METRICS_CLIENT') || METRICS_CLIENT ) { ?>
		devpromOpts.url = window.location.protocol+"//devprom.ru/rx";
		devpromOpts.iid = "<?=$public_iid?>";
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

    		cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
    		cookies.set('devprom-client-tz', jstz.determine().name());
    	});
        
		<?php if ( !defined('UI_EXTENSION') || UI_EXTENSION ) { ?>
		completeUIExt( $(document) );
		completeChartsUI( $(document) );
		setUXData();
        <?php } ?>
    	<?php if ( !defined('SEND_BUG_REPORTS') || SEND_BUG_REPORTS ) { ?>
        Raven.config(window.location.protocol+'//<?=(defined('DEVOPSKEY') ? DEVOPSKEY : 'af4078b6e4630da32f3c164d121ea2b1')?>@api.devopsboard.com/sentry/1', {
        	logger: 'Devprom Front',
        	release: '<?=$current_version?>',
        	extra: {
        		name: '<?=EnvironmentSettings::getServerName()?>',
        		site: '<?=EnvironmentSettings::getServerAddress()?>'
        	}
        }).install();
		<?php }?>
    </script>

	</body>
</html>