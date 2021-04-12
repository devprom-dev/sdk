<!DOCTYPE html>    
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
  	<title><?=($title == '' ? $navigation_title : $title)?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&type=css"/>
	<!--[if IE]>
    	<link href="/styles/jquery-ui/jquery.ui.1.8.16.ie.css" rel="stylesheet">
	<![endif]-->
	<link title="" type="application/rss+xml" rel="alternate" href="/rss"/>
   	<script src="/cache?v=<?=$current_version?>&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
  </head>
  
  <body style="background: white;margin: 0 10px 0 10px;">
		<?php
			echo $view->render('core/PageTableBody.php', array (
			    'table' => $table,
			    'caption' => $caption,
			    'description' => $description,
			    'tableonly' => $tableonly,
			    'filter_items' => $filter_items,
			    'filter_modified' => $filter_modified,
			    'sections' => $sections,
				'object_class' => $object_class,
				'object_id' => $object_id,
			    'actions' => $actions,
			    'new_actions' => $new_actions,
			    'list' => $list,
			    'navigation_url' => $navigation_url,
			    'navigation_title' => $navigation_title,
			    'changed_ids' => $changed_ids
			));
		?>

		<?=$scripts?>

		<script src="/cache-after?v=<?=$current_version?>&l=<?=$language_code?>&dpl=<?=$datelanguage?>" type="text/javascript" charset="UTF-8"></script>

	   	<script type="text/javascript">
	        devpromOpts.language = '<?=$language_code?>';
	        devpromOpts.datepickerLanguage = '<?=$datelanguage?>';
	        devpromOpts.dateformat = '<?=$dateformat?>';
	
	    	$(document).ready( function() {
	    		if ( devpromOpts.dateformat != '' ) {
	    			$.datepicker.regional[ devpromOpts.datepickerLanguage ].dateFormat = devpromOpts.dateformat; 
	    		}
	    	});
	        
			completeUIExt( $(document) );
			completeChartsUI( $(document) );
		</script>
	</body>
</html>