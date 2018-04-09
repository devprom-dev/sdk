<?php

class FieldEmail extends FieldShortText
{
    function getValidator()
    {
        return new ModelValidatorTypeEmail();
    }
}