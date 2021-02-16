<?php
include_once SERVER_ROOT_PATH."cms/views/Field.php";

class FieldEditable extends Field
{
    function hasBorder()
    {
        return false;
    }
}
