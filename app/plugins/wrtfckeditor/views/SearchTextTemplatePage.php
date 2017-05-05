<?php
include "SearchTextTemplateForm.php";

class SearchTextTemplatePage extends PMPage
{
    function getObject() {
        return getFactory()->getObject('Request');
    }

    function getForm() {
        return new SearchTextTemplateForm($this->getObject());
    }

    function needDisplayForm() {
        return true;
    }

    function export()
    {
        $data = array();
        $template_it = getFactory()->getObject($this->getFormRef()->getTemplateClassName())->getAll()   ;
        while( !$template_it->end() ) {
            $data[] = array (
                'Id' => $template_it->getHtmlDecoded('Caption'),
                'Caption' => $template_it->getHtmlDecoded('Content'),
            );
            $template_it->moveNext();
        }
        echo json_encode($data);
    }
}