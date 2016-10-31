<!DOCTYPE html>    
<html class="<?=($inside ? 'inside' : '' )?>">
  <head>
	  <meta http-equiv="Content-Type" content="text/html; charset=<?=APP_ENCODING?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<title><?=($title == '' ? $navigation_title : $title)?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&asset=1&type=css"/>
	  <?php if ( getSession()->getApplicationUrl() != '/' ) { ?>
	<link rel="stylesheet" href="<?=getSession()->getApplicationUrl()?>scripts/css/?v=<?=$current_version?>" type="text/css" media="screen">
	  <?php } ?>
	<link title="" type="application/rss+xml" rel="alternate" href="/rss"/>
	<!--[if IE]>
    	<link href="/styles/jquery-ui/jquery.ui.1.8.16.ie.css" rel="stylesheet">
	<![endif]-->
	  <script src="/cache/?v=<?=$current_version?>&asset=1&l=<?=$language_code?>&dpl=<?=$datelanguage?>" type="text/javascript" charset="UTF-8"></script>
	  <?php if (is_array($javascript_paths)) foreach( $javascript_paths as $path ) { ?>
		  <script src="<?=$path?>?v=<?=$current_version?>&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	  <?php } ?>
	  <? if ( TextUtils::versionToString($_SERVER['APP_VERSION']) < TextUtils::versionToString("3.5.36") ) { ?>
	  <script src="/cache/?v=<?=$current_version?>&asset=2&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	  <script src="/cache/?v=<?=$current_version?>&asset=3&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	  <? } ?>
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
	
	<? if ( TextUtils::versionToString($_SERVER['APP_VERSION']) < TextUtils::versionToString("3.5.39") ) { ?>
   		<script src="/cache-after?v=<?=$current_version?>&l=<?=$language_code?>&dpl=<?=$datelanguage?>" type="text/javascript" charset="UTF-8"></script>
	<? } ?>
	<script src="/scripts/zeroclipboard/ZeroClipboard.min.js?v=<?=$current_version?>" type="text/javascript" charset="UTF-8"></script>
	<?php if ( !defined('SEND_BUG_REPORTS') || SEND_BUG_REPORTS ) { ?>
   	<script src="/scripts/raven/raven.min.js?v=<?=$current_version?>" type="text/javascript" charset="UTF-8"></script>
	<?php }?>
   	<script type="text/javascript">
    
        devpromOpts.language = '<?=$language_code?>';
        devpromOpts.datepickerLanguage = '<?=$datelanguage?>';
        devpromOpts.dateformat = '<?=$dateformat?>';
		devpromOpts.datejsformat = '<?=$datejsformat?>';
        devpromOpts.template = '<?=$project_template?>';
		devpromOpts.project = '<?=$project_code?>';
        devpromOpts.mathJaxLib = '<?=(defined('MATH_JAX_LIB_SRC') ? MATH_JAX_LIB_SRC : "")?>';
        devpromOpts.plantUMLServer = '<?=(defined('PLANTUML_SERVER_URL') ? PLANTUML_SERVER_URL : "")?>';
        
		<?php if ( !defined('METRICS_CLIENT') || METRICS_CLIENT ) { ?>
		devpromOpts.url = window.location.protocol+"//devprom.ru/rx";
		devpromOpts.iid = "<?=$public_iid?>";
		devpromOpts.version = "<?=$current_version?>";
        <?php } ?>
        
		initializeApp();
		<?php if ( !defined('UI_EXTENSION') || UI_EXTENSION ) { ?> completeUIExt( $(document) ); <?php } ?>
    	<?php if ( !defined('SEND_BUG_REPORTS') || SEND_BUG_REPORTS ) { ?>
		setUXData();
        Raven.config(window.location.protocol+'//<?=(defined('DEVOPSKEY') ? DEVOPSKEY : 'af4078b6e4630da32f3c164d121ea2b1')?>@api.devopsboard.com/sentry/1', {
        	logger: 'Devprom Front',
        	release: '<?=$current_version?>',
        	extra: {
        		name: '<?=EnvironmentSettings::getServerName()?>',
        		site: '<?=EnvironmentSettings::getServerAddress()?>'
        	}
        }).install();
		Raven.setUserContext({
			id: <?=($user_id < 1 ? 0 : $user_id)?>
		});
		<?php }?>
    </script>

	</body>
</html>