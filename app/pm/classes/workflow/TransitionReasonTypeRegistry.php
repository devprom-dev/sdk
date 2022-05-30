<?php

class TransitionReasonTypeRegistry extends ObjectRegistrySQL
{
	const None = 'N';
	const Visible = 'I';
	const Required = 'Y';

	public function Query($parms = array())
	{
		return $this->createIterator(
		    array (
				array ( 'entityId' => self::None, 'Caption' => text(2213) ),
				array ( 'entityId' => self::Visible, 'Caption' => text(2214) ),
				array ( 'entityId' => self::Required, 'Caption' => text(2215) )
		    )
        );
	}
}