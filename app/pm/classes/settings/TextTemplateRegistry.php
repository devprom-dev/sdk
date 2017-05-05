<?php

class TextTemplateRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
        $filters = parent::getFilters();
        if ( $this->getObject()->getObjectClass() != '' ) {
            $filters[] = new TextTemplateEntityPredicate($this->getObject()->getObjectClass());
        }
        return $filters;
	}
}