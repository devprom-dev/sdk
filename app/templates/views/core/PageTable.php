<?php 

if ( !$tableonly )
{
    $view->extend('core/PageBody.php'); 
    
    $view['slots']->output('_content');
}

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
    'additional_actions' => $additional_actions,
	'bulk_actions' => $bulk_actions,
    'list' => $list,
    'navigation_url' => $navigation_url,
    'navigation_title' => $navigation_title,
	'title' => $title,
    'changed_ids' => $changed_ids,
	'save_settings_alert' => $save_settings_alert,
	'module_url' => $module_url
));

