<div class="wiki-page-tree hidden-print <?=$placement_class?>" style="<?=($structureVisible ? '' : 'display:none;')?>">
    <div class="tabs-block" style="display: inline">
        <ul class="nav nav-tabs" id="rightTab">
            <?php foreach ( $sections as $key => $section ) { ?>
                <li class="<?=($key == array_shift(array_keys($sections)) ? 'active' : '')?>">
                    <a href="#<?=$section->getId().$object_id?>" tabindex="-1" data-toggle="tab"><?=$section->getCaption()?></a>
                </li>
            <?php } ?>

            <a class="reg-url" onclick="window.location='<?=$registry_url?>';"><?=$registry_title?></a>
            <?php if ( $docs_url != '' ) { ?>
            <a class="docs-url" onclick="window.location='<?=$docs_url?>';"><?=$docs_title?></a>
            <?php } ?>
        </ul>
        <?php foreach ( $sections as $key => $section ) { ?>
            <div class="tab-pane <?=($key == array_shift(array_keys($sections)) ? 'active' : '')?> <?=$section->getId()?>" id="<?=$section->getId().$object_id?>">
                <?php
                    $section->render( $this, array(
                        'page_uid' => $page_uid,
                        'document_hint' => $document_hint,
                        'document_id' => $documentId,
                        'object_id' => $object_id
                    ));
                ?>
            </div>
        <?php } ?>
    </div>
</div>
