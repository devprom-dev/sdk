<?php 

if ( !$tableonly )
{
    if ( !$document_mode ) $view->extend('core/PageBody.php'); 
    $view['slots']->output('_content');
}

$bodySections = array(new DetailsInfoSection());
if ( $_COOKIE['toggle-structure-panel-' . $table->getDocumentIt()->getId()] == 'false' ) {
    array_unshift($bodySections, new DocumentStructureSection());
}
$placementClass = $_COOKIE['document-tree-placement'] != 'right' ? 'left' : 'right';

?>
<div class="wiki-page <?=$placementClass?>">
    <?php
        if ( !$tableonly && count($sections) > 0 && $placementClass != 'right' ) {
            echo $view->render('pm/WikiDocumentTree.php', array(
                'sections' => $sections,
                'object_class' => $object_class,
                'object_id' => $object_id,
                'page_uid' => $page_uid,
                'document_hint' => $document_hint,
                'docs_url' => $docs_url,
                'docs_title' => $docs_title,
                'placement_text' => text(2209),
                'placement_icon' => 'icon-arrow-right',
                'placement_script' => "javascript: toggleDocumentTreePlacement('right')",
                'placement_class' => 'left'
            ));
        }
    ?>
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
                'details' => $details,
                'details_parms' => $details_parms,
                'widget_id' => $widget_id,
                'sections' => $bodySections,
                'placeholderClass' => '',
                'hint' => $hint,
                'hint_open' => $hint_open,
                'page_uid' => $page_uid,
                'sliderClass' => 'list-slider-1'
            ));
            if ( $hint_open )
            {
                echo '<span class="clearfix"></span>';
                echo $view->render('core/Hint.php', array('title' => $document_hint, 'name' => $page_uid, 'open' => true));
            }
        ?>
    </div>
    <?php
        if ( !$tableonly && count($sections) > 0 && $placementClass == 'right' ) {
            echo $view->render('pm/WikiDocumentTree.php', array(
                'sections' => $sections,
                'object_class' => $object_class,
                'object_id' => $object_id,
                'page_uid' => $page_uid,
                'document_hint' => $document_hint,
                'docs_url' => $docs_url,
                'docs_title' => $docs_title,
                'placement_text' => text(2208),
                'placement_icon' => 'icon-arrow-left',
                'placement_script' => "javascript: toggleDocumentTreePlacement('left')",
                'placement_class' => 'right'
            ));
        }
    ?>
</div>