<?php

class FieldForm extends Field
{
    function getValidator() {
        return new ModelValidatorEmbeddedForm($this->getName());
    }
}
