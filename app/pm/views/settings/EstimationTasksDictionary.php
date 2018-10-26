<?php

class EstimationTasksDictionary extends FieldDictionary
{
    function __construct()
    {
        parent::__construct( getSession()->getProjectIt()->object );
        $this->setNullOption(false);
    }

    function getOptions()
    {
        return array(
            array(
                'value' => 'N',
                'caption' => text(1097),
                'disabled' => false
            ),
            array(
                'value' => 'Y',
                'caption' => text(1102),
                'disabled' => false
            )
        );
    }
}