<?php

class StateCommonRegistry extends ObjectRegistrySQL
{
	const Done = 'Y';
	const Progress = 'I';
	const Submitted = 'N';

	public function Query($parms = array())
	{
		return $this->createIterator( array (
				array ( 'entityId' => self::Submitted, 'Caption' => translate('Добавлено') ),
				array ( 'entityId' => self::Progress, 'Caption' => translate('В работе') ),
				array ( 'entityId' => self::Done, 'Caption' => translate('Выполнено') )
		));
	}
}