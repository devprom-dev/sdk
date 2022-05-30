<?php

class RecurringForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        $object->addAttribute('Minutes', 'VARCHAR', translate('Минуты'), true);
        $object->addAttribute('Hours', 'VARCHAR', translate('Часы'), true);
        $object->addAttribute('Days', 'VARCHAR', translate('Дни'), true);
        $object->addAttribute('DaysOfWeek', 'VARCHAR', translate('Дни недели'), true);
        $object->addAttribute('Months', 'VARCHAR', translate('Месяцы'), true);
    }

    function validateInputValues($id, $action)
    {
        if ( in_array('*', $_REQUEST['Minutes']) ) {
            $minutes = '*';
        }
        else {
            $minutes = join(',', $_REQUEST['Minutes'] );
        }
        if ( $minutes == '' ) $minutes = '*';

        if ( in_array('*', $_REQUEST['Hours']) ) {
            $hours = '*';
        }
        else {
            $hours = join(',', $_REQUEST['Hours'] );
        }
        if ( $hours == '' ) $hours = '*';

        if ( in_array('*', $_REQUEST['Days']) ) {
            $days = '*';
        }
        else {
            $days = join(',', $_REQUEST['Days'] );
        }
        if ( $days == '' ) $days = '*';

        if ( in_array('*', $_REQUEST['DaysOfWeek']) ) {
            $dow = '*';
        }
        else {
            $dow = join(',', $_REQUEST['DaysOfWeek'] );
        }
        if ( $dow == '' ) $dow = '*';

        if ( in_array('*', $_REQUEST['Months']) ) {
            $months = '*';
        }
        else {
            $months = join(',', $_REQUEST['Months'] );
        }
        if ( $months == '' ) $months = '*';

        $_REQUEST['CronSchedule'] = join(' ', array(
            $minutes,
            $hours,
            $days,
            $dow,
            $months
        ));

        return parent::validateInputValues($id, $action);
    }

    function getFieldValue($field)
    {
        $value = parent::getFieldValue($field);

        $index = array_search($field, $this->getShortAttributes());
        if ( $index !== false ) {
            $matches = explode(' ', parent::getFieldValue('CronSchedule'));
            if ( $matches[$index] == '' ) return '*';
            return $matches[$index];
        }

        return $value;
    }

    function createFieldObject( $attr_name )
	{
		switch( $attr_name )
		{
            case 'Minutes':
                $rowset = array(
                    array(
                        'entityId' => '*',
                        'Caption' => translate('Каждая')
                    )
                );
                foreach( range(0,60) as $dayNumber ) {
                    $rowset[] = array(
                        'entityId' => strval($dayNumber),
                        'Caption' => strval($dayNumber)
                    );
                }
                $entity = new Metaobject('entity');
                $field = new FieldDictionary($entity->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

            case 'Hours':
                $rowset = array(
                    array(
                        'entityId' => '*',
                        'Caption' => translate('Каждый')
                    )
                );
                foreach( range(0,23) as $dayNumber ) {
                    $rowset[] = array(
                        'entityId' => strval($dayNumber),
                        'Caption' => strval($dayNumber)
                    );
                }
                $entity = new Metaobject('entity');
                $field = new FieldDictionary($entity->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

            case 'Days':
                $rowset = array(
                    array(
                        'entityId' => '*',
                        'Caption' => translate('Каждый')
                    )
                );
                foreach( range(1,31) as $dayNumber ) {
                    $rowset[] = array(
                        'entityId' => $dayNumber,
                        'Caption' => $dayNumber
                    );
                }
                $entity = new Metaobject('entity');
                $field = new FieldDictionary($entity->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

            case 'DaysOfWeek':
                $dateRegistry = (new DateWeekday())->getRegistry();
                $rowset = array_merge(
                    array(
                        array(
                            'entityId' => '*',
                            'Caption' => translate('Каждый')
                        )
                    ),
                    $dateRegistry->Query(array())->getRowset()
                );
                $entity = new Metaobject('entity');
                $field = new FieldDictionary($entity->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

            case 'Months':
                $dateMonth = new DateMonth();
                $rowset = array_merge(
                    array(
                        array(
                            'entityId' => '*',
                            'Caption' => translate('Каждый')
                        )
                    ),
                    $dateMonth->getAll()->getRowset()
                );
                $entity = new Metaobject('entity');
                $field = new FieldDictionary($entity->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

			default:
				return parent::createFieldObject( $attr_name );
		}
	}

	function getShortAttributes()
    {
        return array('Minutes','Hours','Days','DaysOfWeek','Months');
    }
}