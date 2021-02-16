<?php 

if ( !$tableonly )
{
    if ( !$document_mode ) $view->extend('core/PageBody.php'); 
    $view['slots']->output('_content');
}

$comparisonMode = $_REQUEST['comparemode'] == 'modified';
$structureVisible = in_array($_COOKIE['toggle-docstruct'], array('','true')) && $has_hierarchy;
$filter_actions = $table->getFilterActions();

?>
<div class="treeview-push pull-left <?=($structureVisible? 'invisible' : '')?>" onclick="toggleDocumentStructure()">
    <i class="icon-chevron-right"></i>
</div>
<div class="wiki-page left">
    <?php
        if ( !$tableonly && count($sections) > 0 ) {
            echo $view->render('pm/WikiDocumentTree.php', array(
                'sections' => $sections,
                'object_class' => $object_class,
                'object_id' => $object_id,
                'page_uid' => $page_uid,
                'document_hint' => $document_hint,
                'docs_url' => $docs_url,
                'registry_url' => $registry_url,
                'registry_title' => $registry_title,
                'docs_title' => $docs_title,
                'documentId' => $widget_id,
                'structureVisible' => $structureVisible
            ));
        }
    ?>
    <div class="wiki-page-document">
        <?php
            $filter_settings = array(
                array(
                    'html' => '<button id="filter-settings" class="btn dropdown-toggle btn btn-sm btn-light"><i class="icon-cog icon-gray"></i></button>'
                )
            );
            if ( $comparisonMode ) {
                array_unshift(
                    $filter_settings,
                    array(
                        'html' => '<a class="btn btn-sm btn-light" href="'.$document_url.'">&#8592; '.translate('Закрыть').'</a>'
                    )
                );
            }

            echo $view->render('core/PageTableBody.php', array (
                'table' => $table,
                'caption' => $caption,
                'description' => $description,
                'tableonly' => $tableonly,
                'filter_visible' => $filter_visible,
                'filter_items' => $filter_items,
                'filter_buttons' => $filter_buttons,
                'filter_search' => $filter_search,
                'actions' => $actions,
                'additional_actions' => $additional_actions,
                'list' => $list,
                'filter_modified' => $filter_modified,
                'navigation_url' => $navigation_url,
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
                'list_slider' => $list_slider,
                'sliderClass' => $sliderClass,
                'filterMoreActions' => $filterMoreActions,
                'filter_settings' => $filter_settings
            ));
        ?>
    </div>
</div>