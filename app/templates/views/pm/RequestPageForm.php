<?php
if ( $_REQUEST['attributesonly'] == '' ) {
    $view->extend('core/PageBody.php');
}

$skipFields = array_merge(
        $form->getObject()->getAttributesByGroup('form-column-skipped'),
        array(
            'Caption'
        )
    );
$wordyKeys = array();
foreach( $attributes as $name => $attribute )
{
    if ( $name == 'Description' ) continue;
    if ( !$attribute['visible'] ) continue;
    if ( is_null($attribute['field']) ) continue;

    if ( count(preg_split('/,/',$attribute['value'])) > 20 ) {
        $skipFields[] = $name;
        $wordyKeys[] = $name;
    }
}

// display other fields instead of system ones
if ( array_key_exists('ResponseSLA', $attributes) ) {
    $skipFields[] = 'PlannedResponse';
}
if ( array_key_exists('LeadTimeSLA', $attributes) ) {
    $skipFields[] = 'Estimation';
}

$visibleAttributes = array_filter($attributes,
    function($item, $key) use ($skipFields) {
        return $item['visible'] && !in_array($item['type'], array('wysiwyg')) && !in_array($key, $skipFields);
    }, ARRAY_FILTER_USE_BOTH);

$firstTopKeys = array_intersect(
        $form->getObject()->getAttributesByGroup('form-column-first'),
        array_keys($visibleAttributes)
    );

$secondTopKeys = array_intersect(
    array(
        'Fact', 'Tags'
    ),
    array_keys($visibleAttributes)
);

$thirdTopKeys = array_intersect(
    array(
        'Attachment', 'Tasks'
    ),
    array_keys($visibleAttributes)
);

$traceKeys = array_diff(
                array_intersect(
                    $form->getObject()->getAttributesByGroup('trace'),
                    array_keys($visibleAttributes)
                ),
                $form->getObject()->getAttributesByGroup('form-column-first')
            );

$columnKeys = array_diff(
        array_keys($visibleAttributes),
        array_merge(
            $firstTopKeys, $secondTopKeys, $thirdTopKeys, $traceKeys
        )
    );

$columnKeys = array_merge($firstTopKeys, $columnKeys);

$totalKeys = count($columnKeys) + count($secondTopKeys) +
    ($_REQUEST['attributesonly'] == '' ? count($traceKeys) + count($thirdTopKeys) : 0);

$columns = array_chunk( $columnKeys,
        ceil($totalKeys / ($_REQUEST['attributesonly'] == '' ? 3 : 2))
    );

$columns[0] = array_unique(array_merge($firstTopKeys, $columns[0]));
$columns[1] = array_diff(array_merge($secondTopKeys, $columns[1]), $firstTopKeys);

if ( $_REQUEST['attributesonly'] == '' ) {
    if ( !is_array($columns[2]) ) $columns[2] = array();
    $columns[2] = array_merge($thirdTopKeys, $columns[2], $traceKeys);
}

// attributes to be displayed in third column
if ( $_REQUEST['attributesonly'] == '' ) {
    $section_class['Tasks'] = 'hidden-desktop';
    $section_class['Trace'] = 'hidden-desktop';
}

?>
<div class="history-user">
    <div class="actions hidden-print">
        <?php
        if ( count($actions) > 0 && $action != 'show' ) {
            echo $view->render('core/PageFormButtons.php', array(
                'actions' => $actions,
                'sections' => $sections
            ));
        }
        ?>
    </div> <!-- end actions -->

    <?php
        echo $view->render('core/PageBreadcrumbs.php', array(
            'navigation_url' => $navigation_url,
            'parent_widget_url' => $parent_widget_url,
            'parent_widget_title' => $parent_widget_title,
            'nearest_title' => $nearest_title,
            'has_caption' => $has_caption,
            'caption' => $caption,
            'uid' => $uid,
            'uid_url' => $uid_url,
            'state_name' => $state_name,
            'form' => $form,
            'listWidgetIt' => $listWidgetIt,
            'title' => $title
        ));
    ?>

    <div class="form-container">
    <h4 class="bs page-form-view" style="width:90%;">
        <?
        if ( $attributes['Caption']['field'] instanceof FieldEditable )
        {
            $attributes['Caption']['field']->draw();
        }
        else
        {
            echo $attributes['Caption']['text'];
        }
        ?>
    </h4>

    <?php if ( $warning != '' ) { ?>

    <div class="alert alert-error"><?=$warning?></div>

    <?php } ?>

    <?php if ( $alert != '' ) { ?>

    <div class="alert alert-info"><?=$alert?></div>

    <?php } ?>

    <div class="accordion-wrap">
        <div class="accordion-heading">
          <a class="to-drop-btn" href="#collapseOne" tabindex="-1">
            <span class="caret"></span>
            <?=translate('Свойства')?>
          </a>
        </div>
        <div id="collapseOne" class="accordion-body" tabindex="-1">
            <div class="row">
                <div class="properties-cell-1">
                    <div style="width:100%;display:table;">
                        <div class="properties-column-30">
                            <table class="properties-table">
                            <?php
                            foreach( $columns[0] as $ref_name ) {
                                $attribute = $attributes[$ref_name];
                                ?>
                                <tr name="<?=$ref_name?>">
                                    <th title="<?=htmlentities(strip_tags($attribute['description']))?>">
                                        <?=$attribute['name']?>:
                                    </th>
                                    <td>
                                        <?
                                        if ( count($refs_actions[$ref_name]) > 1 ) {
                                            if ( $ref_name == 'BlockReason' ) {
                                                echo '<div class="alert alert-blocked">';
                                            }
                                            echo $this->render('pm/AttributeButton.php', array (
                                                'data' => $attribute['text'],
                                                'items' => $refs_actions[$ref_name]
                                            ));
                                            if ( $ref_name == 'BlockReason' ) {
                                                echo '</div>';
                                            }
                                        }
                                        else {
                                            echo $view->render('core/PageFormViewAttribute.php', $attribute);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </table>
                        </div>
                        <div class="properties-column-31">
                            <table class="properties-table">
                                <?php
                                foreach( $columns[1] as $ref_name ) {
                                    $attribute = $attributes[$ref_name];
                                    ?>
                                    <tr name="<?=$ref_name?>">
                                        <th title="<?=htmlentities(strip_tags($attribute['description']))?>">
                                            <?php if ( $attribute['type'] != 'char' ) { ?>
                                                <?=$attribute['name']?>:
                                            <?php } ?>
                                        </th>
                                        <td>
                                            <?
                                            if ( count($refs_actions[$ref_name]) > 1 ) {
                                                echo $this->render('pm/AttributeButton.php', array (
                                                    'data' => $attribute['text'],
                                                    'items' => $refs_actions[$ref_name]
                                                ));
                                            }
                                            else {
                                                echo $view->render('core/PageFormViewAttribute.php', $attribute);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>

                    <!--  -->
                    <?
                    $wasToolbar = false;
                    foreach( $attributes as $key => $attribute )
                    {
                        if ( !$attribute['visible'] ) continue;
                        if ( $attribute['type'] != 'wysiwyg' ) continue;
                        ?>
                        <div class="accordion-heading">
                            <a class="to-drop-btn" data-toggle="collapse" tabindex="-1">
                                <span class="caret"></span>
                                <?=$attribute['name']?>
                            </a>
                        </div>
                        <div class="accordion-body in collapse" style="overflow: hidden;">
                            <?
                            if ( is_a($attribute['field'], 'Field') ) {
                                if ( !$wasToolbar && $attribute['field']->contentEditable() && $attribute['editable'] ) { $wasToolbar = true; ?>
                                    <div class="hidden-print documentToolbar sticks-top" style="overflow:hidden;">
                                        <div class="sticks-top-body hidden-print" id="documentToolbar" style="z-index:2;"></div>
                                    </div>
                                <? }
                                $attribute['field']->draw($this);
                            }
                            else {
                                echo '<p>'.$attribute['text'].'</p>';
                            }
                            ?>
                            <br/>
                        </div>
                    <?
                    }
                    ?>
                </div>

                <? if ( is_array($columns[2]) ) { ?>
                    <div class="properties-cell-2">
                        <div class="properties-column">&nbsp;</div>
                        <div class="properties-column-32 file-drop-target">
                            <table class="properties-table">
                                <?php
                                foreach( $columns[2] as $ref_name ) {
                                    $attribute = $attributes[$ref_name];
                                    ?>
                                    <tr name="<?=$ref_name?>">
                                        <th title="<?=htmlentities(strip_tags($attribute['description']))?>">
                                            <?=$attribute['name']?>:
                                        </th>
                                        <td>
                                            <?
                                            if ( count($refs_actions[$ref_name]) > 1 ) {
                                                echo $this->render('pm/AttributeButton.php', array (
                                                    'data' => $attribute['text'],
                                                    'items' => $refs_actions[$ref_name]
                                                ));
                                            }
                                            else {
                                                echo $view->render('core/PageFormViewAttribute.php', $attribute);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>

        <?php if ( $attributes['Attachment']['visible'] ) { ?>
            <div class="accordion-heading <?=$section_class['Trace']?>">
                <a class="to-drop-btn" data-toggle="collapse" href="#collapseFour" tabindex="-1">
                    <span class="caret"></span>
                    <?=$attributes['Attachment']['name']?>
                </a>
            </div>
            <div id="collapseFour" class="accordion-body in collapse <?=$section_class['Trace']?>">
                <? echo $view->render('core/PageFormViewAttribute.php', $attributes['Attachment']); ?>
                <div class="clearfix"></div>
                <br/>
            </div>
        <?php } ?>

        <?php if ( $attributes['Tasks']['visible'] ) { ?>
            <div class="accordion-heading <?=$section_class['Tasks']?>">
              <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseThree" tabindex="-1">
                <span class="caret"></span>
                <?=$attributes['Tasks']['name']?>
              </a>
            </div>
            <div id="collapseThree" class="accordion-body collapse <?=$section_class['Tasks']?>">
                <? echo $view->render('core/PageFormViewAttribute.php', $attributes['Tasks']); ?>
                <br/>
            </div>
        <?php } ?>

        <?php foreach( $wordyKeys as $attribute ) { ?>
            <div class="accordion-heading">
                <a class="to-drop-btn" data-toggle="collapse" href="#collapse<?=$attribute?>" tabindex="-1">
                    <span class="caret"></span>
                    <?=$attributes[$attribute]['name']?>
                </a>
            </div>
            <div id="collapse<?=$name?>" class="accordion-body">
                <? echo $view->render('core/PageFormViewAttribute.php', $attributes[$attribute]); ?>
                <br/>
            </div>
        <?php }	?>

        <?php if ( count($traceKeys) > 0 ) { ?>
            <div class="accordion-heading <?=$section_class['Trace']?>">
              <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseFive" tabindex="-1">
                <span class="caret"></span>
                <?=text(1243)?>
              </a>
            </div>
            <div id="collapseFive" class="accordion-body collapse <?=$section_class['Trace']?>">
                <table class="properties-table">
                <?php foreach( $traceKeys as $attribute ) { ?>
                    <tr>
                        <th><?=$attributes[$attribute]['name']?>:</th>
                        <td>
                            <? echo $view->render('core/PageFormViewAttribute.php', $attributes[$attribute]); ?>
                        </td>
                    </tr>
                <?php } ?>
                </table>
            </div>
        <?php }	?>

        <?php if ( count($sections) > 0 && (!$formonly || $_REQUEST['attributesonly'] != '')) { ?>
            <div class="accordion-heading">
              <a id="comments-section" class="to-drop-btn" tabindex="-1">
                <span class="caret"></span>
                <?=text(1346)?>
                <?=($comments_count > 0 ? ' ('.$comments_count.')' : '')?>
              </a>
            </div>
            <div id="collapseComments" class="accordion-body in collapse" style="overflow: inherit;">
                <?php
                    echo $view->render('core/PageSections.php', array(
                        'sections' => $sections,
                        'object_class' => $object_class,
                        'object_id' => $object_id,
                        'placement' => 'bottom'
                    ));
                ?>
            </div>
        <?php } ?>

        </div> <!-- end accordion -->
    </div>
</div>

<script language="javascript">
	cookies.setOptions({expires:new Date(new Date().getFullYear() + 1, 1, 1)});
	
	$(document).ready(function() {
		$(".accordion-heading > a")
			.slice(1).click(function() {
		      cookies.set('devprom.request.form.section' + $(this).attr('href'), 
		  	      	$(this).hasClass('collapsed') ? '1' : '0');
			});
    });
</script>

<?php
if ( $_REQUEST['attributesonly'] == '' ) {
?>
    <script language="javascript">
        devpromOpts.updateUI = function() { window.location.reload(); };
    </script>
<?php
}
?>