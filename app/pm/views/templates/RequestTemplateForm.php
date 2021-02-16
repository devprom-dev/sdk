<?php
include_once SERVER_ROOT_PATH . 'pm/views/issues/RequestForm.php';

class RequestTemplateForm extends RequestForm
{
    private $templateObject = null;

    function __construct($object)
    {
        $this->templateObject = $object;
        parent::__construct(getFactory()->getObject('Request'));
        $templateId = $_REQUEST[$this->templateObject->getIdAttribute()];
        if ( $templateId > 0 ) {
            $this->setTemplate($templateId);
        }
        $templateAction = $_REQUEST[$this->templateObject->getEntityRefName() . 'action'];
        if ( $templateAction != '' ) {
            $this->setAction($templateAction);
        }
    }

    function extendModel()
    {
        parent::extendModel();
        $request = $this->getObject();
        $requestIt = $request->getExact($_REQUEST['items']);

        $templatedAttributes = array_merge(
                $this->templateObject->getAttributesTemplated(),
                array( 'Project' )
            );
        foreach( $request->getAttributes() as $attribute => $info ) {
            if ( !in_array($attribute, $templatedAttributes) ) {
                $request->setAttributeVisible($attribute, false);
            }
            if ( !array_key_exists($attribute, $_REQUEST) ) {
                $_REQUEST[$attribute] = $requestIt->getHtmlDecoded($attribute);
            }
            $request->setAttributeRequired($attribute, false);
        }

        $idAttribute = $this->templateObject->getIdAttribute();
        if ( $_REQUEST[$idAttribute] > 0 ) {
            $request->addAttribute($idAttribute, 'INTEGER', $idAttribute, false, false);
            $request->setAttributeDefault($idAttribute, $_REQUEST[$idAttribute]);
            $request->setAttributeRequired($idAttribute, true);
        }
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'entity' => $this->templateObject->getEntityRefName()
            )
        );
    }

    function process()
    {
        $objectIt = $this->getObject()->createCachedIterator(
            array(
                array_merge(
                    $_REQUEST,
                    array (
                        $this->getObject()->getIdAttribute() => '1'
                    )
                )
            )
        );
        $this->setObjectIt($objectIt);

        return parent::process();
    }

    function persist()
    {
        $objectIt = $this->getObjectIt();
        $id = $_REQUEST[$this->templateObject->getIdAttribute()];

        if ( $this->getAction() == 'add') {
            $id = $this->templateObject->add_parms(
                array(
                    'Caption' => $_REQUEST['Caption'],
                    'ListName' => $this->templateObject->getListName(),
                    'ObjectClass' => get_class($this->getObject())
                )
            );
            $this->templateObject->persistSnapshot($id, $objectIt);
            return true;
        }

        if ( $this->getAction() == 'modify') {
            $this->templateObject->modify_parms( $id,
                array(
                    'Caption' => $_REQUEST['Caption']
                )
            );
            $this->templateObject->persistSnapshot($id, $objectIt);
            return true;
        }

        if ( $this->getAction() == 'delete') {
            $this->templateObject->delete( $id );
            return true;
        }
    }

    function redirectOnAdded( $object_it, $redirect_url = '' )
    {
        echo \JsonWrapper::encode(
            array(
                'Id' => 0,
                'Url' => $this->templateObject->getPage()
            )
        );
        exit();
    }
}