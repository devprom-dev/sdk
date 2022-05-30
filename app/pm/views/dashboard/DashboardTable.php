<?php

class DashboardTable extends PMPageTable
{
    function getTemplate() {
		return 'pm/Dashboard.php';
    }

    function getRenderParms($parms)
    {
        $cells = array();

        $moduleIt = getFactory()->getObject('Module')->getAll();
        $reportIt = getFactory()->getObject('PMReport')->getAll();

        $cellIt = getFactory()->getObject('DashboardItem')->getAll();
        while( !$cellIt->end() ) {
            $moduleIt->moveToId($cellIt->get('WidgetUID'));
            if ( $moduleIt->getId() == '' ) {
                $reportIt->moveToId($cellIt->get('WidgetUID'));
                if ( $reportIt->getId() == '' ) {
                    $cellIt->moveNext();
                    continue;
                }
                else {
                    $url = $reportIt->getUrl();
                }
            }
            else {
                $url = $moduleIt->getUrl();
            }

            $modifyMethod = new ObjectModifyWebMethod($cellIt);
            $deleteMethod = new DeleteObjectWebMethod($cellIt);

            $cells[] = array(
                'id' => $cellIt->getId(),
                'order' => $cellIt->get('OrderNum'),
                'title' => $cellIt->getDisplayName(),
                'url' => $url,
                'height' => $cellIt->get('Height'),
                'width' => $cellIt->get('Width'),
                'modifyUrl' => $modifyMethod->hasAccess() ? $modifyMethod->getJSCall() : '',
                'deleteUrl' => $deleteMethod->hasAccess() ? $deleteMethod->getJSCall() : ''
            );
            $cellIt->moveNext();
        }

        $appendUrl = new ObjectCreateNewWebMethod($cellIt->object);
        $appendUrl->doSelectProject(false);

        $reorderUrl = new ModifyAttributeWebMethod(
            $cellIt->object->createCachedIterator(
                array(
                    array('pm_DashboardItemId' => '%id%')
                )
            ),
            'OrderNum',
            '%value%'
        );

        $resizeUrl = new ModifyAttributeWebMethod(
            $cellIt->object->createCachedIterator(
                array(
                    array('pm_DashboardItemId' => '%id%')
                )
            ),
            'Height',
            '%height%'
        );

        return parent::getRenderParms(
            array_merge(
                $parms,
                array(
                    'cells' => $cells,
                    'appendUrl' => $appendUrl->hasAccess() ? $appendUrl->getJSCall() : "",
                    'reorderUrl' => $reorderUrl->hasAccess() ? $reorderUrl->getUrl() : "",
                    'resizeUrl' => $resizeUrl->hasAccess() ? $resizeUrl->getUrl(array('parms[Width]' => '%width%')) : ""
                )
            )
        );
    }
}
