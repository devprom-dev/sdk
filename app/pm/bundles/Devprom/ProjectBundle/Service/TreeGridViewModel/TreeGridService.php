<?php
namespace Devprom\ProjectBundle\Service\TreeGridViewModel;
include_once SERVER_ROOT_PATH."pm/classes/product/FeatureModelExtendedBuilder.php";

class TreeGridService
{
    private $object = null;

    function __construct( $className )
    {
        getSession()->addBuilder( new \FeatureModelExtendedBuilder() );
        $this->object = getFactory()->getObject($className);
    }

    function getTreeGridJsonView( $listView, $view, $titleField, $childrenField, $parentField )
    {
        $traceAttributes = $this->object->getAttributesByGroup('trace');

        $listView->setRenderView($view);
        $listView->retrieve();
        $json = array();

        $it = $listView->getIteratorRef();
        $parentIds = $it->idsToArray();

        while( !$it->end() )
        {
            $cells = array();
            foreach( array_merge(array('UID'), array_keys($this->object->getAttributes())) as $key ) {
                if ( $key == 'Children' ) continue;
                ob_start();
                $this->object->IsReference($key)
                    ? $listView->drawRefCell( $listView->getFilteredReferenceIt($key, $it->get($key)), $it, $key)
                    : $listView->drawCell( $it, $key );
                $html = ob_get_contents();
                ob_end_clean();

                $cells[strtolower($key)] = $html;
            }
            $cells['caption'] = $it->getDisplayName();
            $cells['title'] = $cells[strtolower($titleField)];
            $cells['key'] = $it->getId();
            $cells['id'] = $it->getId();
            $cells['folder'] = $it->get($childrenField) != '';
            $cells['parent'] = in_array($it->get($parentField), $parentIds) ? $it->get($parentField) : '';
            $cells['icon'] = false;
            $cells['class'] = strtolower(get_class($this->object));
            $cells['checkbox-field'] = '<input class=checkbox tabindex="-1" type="checkbox" name="to_delete_'.$it->getId().'">';
            $cells['modified'] = $it->get('AffectedDate');

            ob_start();
            ?>
            <div class="btn-group btn-group-actions operation">
                <a class="btn btn-xs dropdown-toggle actions-button btn-secondary" data-toggle="dropdown" href="#" data-target="#actions<?=$it->getId()?>">
                    <i class="icon-pencil icon-white"></i>
                    <span class="caret"></span>
                </a>
            </div>
            <div class="btn-group dropdown-fixed last" id="actions<?=$it->getId()?>">
                <?php
                echo $view->render('core/PopupMenu.php', array (
                    'items' => $listView->getActions($it->getCurrentIt())
                ));
                ?>
            </div>
            <?php
            $html = ob_get_contents();
            ob_end_clean();

            $cells['actions'] = $html;
            $json[$it->getId()] = $cells;
            $widgets = $this->getWidgets();

            foreach( $traceAttributes as $attribute ) {
                if ( $it->get($attribute) == '' ) continue;

                $refIt = $listView->getFilteredReferenceIt($attribute, $it->get($attribute));
                $selfKey = ($this->object->getAttributeOrderNum($attribute) * 100000) + $it->getId();

                $widget_it = $widgets[get_class($refIt->object)];
                $ids = $refIt->idsToArray();

                $cells = array();
                $cells['caption'] = $this->object->getAttributeUserName($attribute) . ' ('.$refIt->count().')';
                if ( $widget_it->getId() != '' ) {
                    $url = $widget_it->getUrl('filter=skip&'.strtolower(get_class($refIt->object)).'='.\TextUtils::buildIds($ids));
                    $cells['caption'] .= ' &nbsp; <a class="dashed" target="_blank" href="'.$url.'">'.translate('список').'</a>';
                }
                $cells['title'] = $cells['caption'];
                $cells['icon'] = false;
                $cells['key'] = $selfKey;
                $cells['folder'] = true;
                $cells['parent'] = $it->getId();
                $json[$selfKey] = $cells;

                $json = array_merge($json,
                    $this->getTraceCells($view, $refIt, $selfKey)
                );
            }

            $it->moveNext();
        }

        return \JsonWrapper::buildJSONTree($json, '');
    }

    protected function getTraceCells( $view, $refIt, $parentId, $limit = 10 )
    {
        $uidService = new \ObjectUID();
        $projectIt = getSession()->getProjectIt();

        $json = array();
        while( !$refIt->end() && count($json) <= $limit ) {
            $cells = array();
            $key = get_class($refIt->object).$refIt->getId();
            $cells['caption'] = $uidService->getUidWithCaption($refIt, 15, '', $refIt->get('VPD') != $projectIt->get('VPD'));
            $cells['title'] = $cells['caption'];
            $cells['icon'] = false;
            $cells['key'] = $key;
            $cells['folder'] = false;
            $cells['parent'] = $parentId;
            $cells['class'] = strtolower(get_class($refIt->object));
            $cells['id'] = $refIt->getId();

            $method = new \ObjectModifyWebMethod($refIt);
            $method->setRedirectUrl('donothing');
            $actions['modify'] = array(
                'name' => $method->getCaption(),
                'url' => $method->getJSCall()
            );
            ob_start();
            ?>
            <div class="btn-group btn-group-actions operation">
                <a class="btn btn-xs dropdown-toggle actions-button btn-secondary" data-toggle="dropdown" href="#" data-target="#actions<?=$key?>">
                    <i class="icon-pencil icon-white"></i>
                    <span class="caret"></span>
                </a>
            </div>
            <div class="btn-group dropdown-fixed last" id="actions<?=$key?>">
                <?php
                echo $view->render('core/PopupMenu.php', array (
                    'items' => $actions
                ));
                ?>
            </div>
            <?php
            $html = ob_get_contents();
            ob_end_clean();

            $cells['actions'] = $html;
            $json[$key] = $cells;
            $refIt->moveNext();
        }

        if ( count($json) < $refIt->count() )
        {
            $widgets = $this->getWidgets();
            $widget_it = $widgets[get_class($refIt->object)];
            $ids = $refIt->idsToArray();

            $url = $widget_it->getUrl('filter=skip&'.strtolower(get_class($refIt->object)).'='.\TextUtils::buildIds($ids));
            $text = count($ids) > $limit
                ? str_replace('%1', count($ids) - $limit, text(2028))
                : text(2034);

            $key = get_class($refIt->object).'0';
            $cells = array();
            $cells['caption'] = '<a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
            $cells['title'] = $cells['caption'];
            $cells['icon'] = false;
            $cells['key'] = $key;
            $cells['folder'] = false;
            $cells['parent'] = $parentId;
            $json[$key] = $cells;
        }

        return $json;
    }

    protected function getWidgets()
    {
        if ( is_array($this->reference_widgets) ) return $this->reference_widgets;

        $report = getFactory()->getObject('PMReport');
        $report_it = $report->getAll();
        $module = getFactory()->getObject('Module');
        $module_it = $module->getAll();

        $it = getFactory()->getObject('ObjectsListWidget')->getAll();
        while( !$it->end() )
        {
            switch( $it->get('ReferenceName') ) {
                case 'PMReport':
                    $widget_it = $report_it->moveToId($it->getId());
                    break;
                case 'Module':
                    $widget_it = $module_it->moveToId($it->getId());
                    break;
                default:
                    $it->moveNext();
                    continue;
            }
            $this->reference_widgets[$it->get('Caption')] = $widget_it->copy();
            $it->moveNext();
        }
        return $this->reference_widgets;
    }
}