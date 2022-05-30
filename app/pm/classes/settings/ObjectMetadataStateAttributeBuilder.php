<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ObjectMetadataStateAttributeBuilder extends ObjectMetadataEntityBuilder
{
    private $stateAttributeIt  = null;

    function __wakeup() {
        unset($this->stateAttributeIt);
    }

    public function build( ObjectMetadata $metadata )
    {
        if ( $metadata->getObject()->getEntityRefName() == 'pm_StateAttribute' ) return;

        if ( !is_object($this->stateAttributeIt) ) {
            $registry = new ObjectRegistrySQL(new Metaobject('pm_StateAttribute'));
            $this->stateAttributeIt = $registry->Query(
                        array(
                            new FilterBaseVpdPredicate(),
                            new FilterAttributeNullPredicate('State'),
                            new FilterAttributeNullPredicate('Transition'),
                            new SortAttributeClause('Entity')
                        )
                    );
        }

        $classes = array(
            get_class($metadata->getObject()),
            get_parent_class($metadata->getObject())
        );
        foreach( $classes as $className ) {
            $classFound = strtolower($className);
            $this->stateAttributeIt->moveTo('Entity', $classFound);
            if ( $this->stateAttributeIt->getId() != '' ) break;
        }
        if ( $classFound == '' ) return;

        while( $this->stateAttributeIt->get('Entity') == $classFound ) {
            $attribute = $this->stateAttributeIt->get('ReferenceName');

            if ( $this->stateAttributeIt->get('IsVisible') != '' ) {
                $metadata->setAttributeVisible($attribute, $this->stateAttributeIt->get('IsVisible') == 'Y');
            }
            if ( $this->stateAttributeIt->get('IsRequired') != '' ) {
                $metadata->setAttributeRequired($attribute, $this->stateAttributeIt->get('IsRequired') == 'Y');
            }
            if ( $this->stateAttributeIt->get('IsReadonly') != '' ) {
                $metadata->setAttributeEditable($attribute, $this->stateAttributeIt->get('IsReadonly') != 'Y');
            }

            if ( $this->stateAttributeIt->get('IsMainTab') == 'Y' ) {
                $metadata->addAttributeGroup($attribute, 'tab-main');
            }

            if ( $this->stateAttributeIt->get('Description') != '' ) {
                $metadata->setAttributeDescription($attribute, $this->stateAttributeIt->get('Description'));
            }
            if ( $this->stateAttributeIt->get('DefaultValue') != '' ) {
                $metadata->setAttributeDefault($attribute, $this->stateAttributeIt->get('DefaultValue'));
            }
            if ( $this->stateAttributeIt->get('AttributeOrderNum') != '' ) {
                $metadata->setAttributeOrderNum($attribute, $this->stateAttributeIt->get('AttributeOrderNum'));
            }
            if ( $this->stateAttributeIt->get('Groups') != '' ) {
                $groups = $metadata->getAttributeGroups($attribute);
                if ( !is_array($groups) ) $groups = array();
                $metadata->setAttributeGroups($attribute,array_merge(
                    $groups, \TextUtils::parseItems($this->stateAttributeIt->get('Groups'))
                ));
            }
            if ( $this->stateAttributeIt->get('IsVisibleOnEdit') == 'Y' ) {
                $metadata->addAttributeGroup($attribute, 'form-column-skipped');
            }

            $this->stateAttributeIt->moveNext();
        }
    }
}