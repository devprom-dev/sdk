<?php

class SubversionRevisionChart extends PMPageChart
{
    function getGroupFields()
    {
        return array_merge(PageList::getGroupFields(), array('Author'));
    }
}
