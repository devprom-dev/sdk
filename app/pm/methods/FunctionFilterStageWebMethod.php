<?php

class FunctionFilterStageWebMethod extends FilterAutoCompleteWebMethod
{
    function getCaption()
    {
        return translate('Стадия проекта');
    }

    function FunctionFilterStageWebMethod()
    {
        global $model_factory;

        $this->object = $model_factory->getObject('Stage');
        parent::FilterAutoCompleteWebMethod( $this->object, $this->getCaption() );
    }

    function getStyle()
    {
        return 'width:140px;';
    }

    function getValueParm()
    {
        return 'stage';
    }

    function hasAccess()
    {
        return getSession()->getProjectIt()->getMethodologyIt()->HasReleases();
    }
}