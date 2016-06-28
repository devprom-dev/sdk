<?php 

if ( !$tableonly )
{
    if ( !$document_mode ) $view->extend('core/PageBody.php'); 
    
    $view['slots']->output('_content');
}

?>
<div class="wiki-page">

    <div class="wiki-page-document">
        <?php
        echo $view->render('core/PageTableBody.php', array (
            'table' => $table,
            'caption' => $caption,
            'description' => $description,
            'tableonly' => $tableonly,
            'filter_items' => $filter_items,
            'actions' => $actions,
            'additional_actions' => $additional_actions,
            'list' => $list,
            'filter_modified' => $filter_modified,
            'navigation_url' => $navigation_url,
            'navigation_title' => $navigation_title,
            'changed_ids' => $changed_ids,
        	'object_id' => $object_id,
        	'object_class' => $object_class,
        	'save_settings_alert' => $save_settings_alert,
            'sections' => array(new FullScreenSection()),
            'placeholderClass' => ''
        ));
        if ( $document_hint != '' )
        {
            echo '<span class="clearfix"></span>';
            echo $view->render('core/Hint.php', array('title' => $document_hint, 'name' => $page_uid));
        }
        ?>
    </div>

    <?php if ( !$tableonly && count($sections) > 0 ) { ?>
    
    <div class="wiki-page-document-divider hidden-tablet">&nbsp;</div>
    
    <div class="wiki-page-tree hidden-tablet">
        <?php
        
        echo $view->render('core/PageSections.php', array(
            'sections' => $sections,
            'object_class' => $object_class,
            'object_id' => $object_id,
            'page_uid' => $page_uid,
            'document_hint' => $document_hint
        ));
    
        ?>
    </div>
    
    <?php } ?>

</div>