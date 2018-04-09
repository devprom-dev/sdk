<div class="wiki-page-tree hidden-tablet <?=$placement_class?>">
    <div class="tabs-block">
        <ul class="nav nav-tabs" id="rightTab">
            <?php foreach ( $sections as $key => $section ) { ?>
                <li class="<?=($key == array_shift(array_keys($sections)) ? 'active' : '')?>">
                    <a href="#<?=$section->getId().$object_id?>" tabindex="-1" data-toggle="tab"><?=$section->getCaption()?></a>
                </li>
            <?php } ?>
            <a class="docs-url" onclick="window.location='<?=$docs_url?>';"><?=$docs_title?></a>
            <a class="tree-placement" onclick="toggleDocumentStructure('<?=$documentId?>')" title="<?=text(2204)?>"><i class="icon-remove"></i></a>
            <a class="tree-placement" onclick="<?=$placement_script?>" title="<?=$placement_text?>"><i class="<?=$placement_icon?>"></i></a>
            <a class="tree-placement" onclick="extendTreeArea('<?=$object_id?>')" title="<?=text(2456)?>"><i class="icon-indent-left"></i></a>
            <a class="tree-placement" onclick="toggleTreeNodes('<?=$object_id?>')" title="<?=text(2455)?>"><i class="icon-plus-sign"></i></a>
        </ul>
        <div class="tab-content right-side-tab">
            <?php foreach ( $sections as $key => $section ) { ?>
                <div class="tab-pane <?=($key == array_shift(array_keys($sections)) ? 'active' : '')?> <?=$section->getId()?>" id="<?=$section->getId().$object_id?>">
                    <?php $section->render( $this, array('page_uid' => $page_uid, 'document_hint' => $document_hint) ); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
