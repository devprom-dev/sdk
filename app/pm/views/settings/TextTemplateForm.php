<?php

class TextTemplateForm extends PMPageForm
{
	function createFieldObject( $attribute )
	{
		switch( $attribute )
		{
			case 'Content':
                $field = new FieldWYSIWYG();

                is_object($this->getObjectIt())
                    ? $field->setObjectIt( $this->getObjectIt() ) : $field->setObject( $this->getObject() );

                $editor = $field->getEditor();
                $editor->setMode( WIKI_MODE_MINIMAL );

                $field->setHasBorder( false );
                $field->setName($attribute);
                return $field;

			default:
				return parent::createFieldObject( $attribute );
		}
	}
}