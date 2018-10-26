<?php 

if ( !$tableonly )
{
    $view->extend('core/PageBody.php'); 
    $view['slots']->output('_content');
}
else {
    if ( $context_template != '' ) echo $view->render($context_template, $context);
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
    'nearest_title' => $nearest_title,
	'title' => $title,
    'changed_ids' => $changed_ids,
	'filterMoreActions' => $filterMoreActions,
	'details' => $details,
	'details_parms' => $details_parms,
	'widget_id' => $widget_id,
	'placeholderClass' => 'placeholder-fixed',
    'hint' => $hint,
    'hint_open' => $hint_open,
    'page_uid' => $page_uid,
    'list_slider' => $list_slider,
    'sliderClass' => $sliderClass
));

