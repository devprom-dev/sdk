<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewRequestTaskStateWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return text(1108);
    }

    function getValueParm()
    {
        return 'taskstate';
    }

    function getValues()
    {
        $values = array(
            'all' => translate('Все'),
        );

        $state_it = getFactory()->getObject('TaskState')->getAll();
        while ( !$state_it->end() )
        {
            if ( $state_it->get('IsTerminal') == 'Y' ) {
                $values['notresolved'] = translate('Не выполнено');
            }
            $values[$state_it->get('ReferenceName')] = $state_it->getDisplayName();
            $state_it->moveNext();
        }

        return $values;
    }

    function getStyle()
    {
        return 'width:190px;';
    }

    function hasAccess()
    {
        return getSession()->getProjectIt()->getMethodologyIt()->HasTasks();
    }
}
