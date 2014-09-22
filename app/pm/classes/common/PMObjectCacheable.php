<?php

class PMObjectCacheable extends MetaobjectCacheable
{
    function getVpdValue()
    {
        return getSession()->getProjectIt()->get('VPD');
    }
}