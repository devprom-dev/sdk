<?php
include_once "TransitionStateMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ObjectModifyWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ModifyAttributeWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php';

class ModifyStateWebMethod extends TransitionStateMethod
{
    function getUrl( $parms_array = array() )
    {
        $source_it = $this->transition_it->getRef('SourceState');
        $target_it = $this->transition_it->getRef('TargetState');

        return WebMethod::getUrl(
            array ( 'object' => $this->object_it->getId(),
                'class' => get_class($this->object_it->object),
                'source' => $source_it->get('ReferenceName'),
                'target' => $target_it->get('ReferenceName') )
        );
    }

    function execute_request()
    {
        $this->execute( $_REQUEST );
    }

    function execute( $parms )
    {
        global $session;

        $class_name = getFactory()->getClass($parms['class']);
        if ( !class_exists($class_name) ) throw new Exception('Unknown class name: '.$parms['class']);

        $object = getFactory()->getObject($class_name);
        if ( $parms['object'] > 0 )
        {
            $object_it = $object->getExact( $parms['object'] );
        }
        else
        {
            $index = $object->getRecordCount() + 1;
            $parms['Caption'] = $object->getDisplayName().' '.$index;
            $object_it = $object->getExact( $object->add_parms( $parms ) );
            echo '{"message":"ok","object":"'.$object_it->getId().'"}';
            return;
        }

        if ( $parms['attribute'] != '' ) {
            if ( $object_it->get($parms['attribute']) != $parms['value'] ) {
                if ( !getFactory()->getAccessPolicy()->can_modify_attribute($object, $parms['attribute']) ) {
                    echo JsonWrapper::encode(array (
                        "message" => "denied",
                        "description" => text(1062)
                    ));
                    return;
                }
            }
        }

        try {
            $session = new PMSession(
                getFactory()->getObject('Project')->getByRef('VPD', $object_it->get('VPD')),
                getSession()->getAuthenticationFactory()
            );

            if ( $parms['attribute'] == 'Project' ) {
                $object_it = getFactory()->modifyEntity($object_it, array(
                    'Project' => $parms['value']
                ));

                $session = new PMSession(
                    getFactory()->getObject('Project')->getExact($parms['value']),
                    getSession()->getAuthenticationFactory()
                );
            }
        }
        catch( Exception $e ) {
            echo JsonWrapper::encode(array (
                "message" => "denied",
                "description" => $e->getMessage()
            ));
            return;
        }

        if ( !getFactory()->getAccessPolicy()->can_modify_attribute($object, 'State') ) {
            $result = array (
                "message" => "denied",
                "description" => text(707)
            );
            echo JsonWrapper::encode($result);
            return;
        }

        $state_object = getFactory()->getObject($object->getStateClassName());

        $source_it = $state_object->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('ReferenceName', $object_it->get('State')),
                new \FilterVpdPredicate($object_it->get('VPD')),
                new \SortOrderedClause()
            )
        );

        if ( $source_it->getId() < 1 ) {
            $source_it = $state_object->getRegistry()->Query(
                array (
                    new \FilterVpdPredicate($object_it->get('VPD')),
                    new \SortOrderedClause()
                )
            );
        }

        $target_it = $state_object->getRegistry()->Query(
            array (
                new FilterAttributePredicate('ReferenceName', preg_split('/,/', trim($parms['target']))),
                new FilterBaseVpdPredicate(),
                new SortOrderedClause()
            )
        );

        $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
            $parms['transition'] > 0
                ? array (
                        new FilterInPredicate($parms['transition'])
                    )
                : array (
                    new FilterAttributePredicate('SourceState', $source_it->getId()),
                    new FilterAttributePredicate('TargetState', $target_it->getId()),
                    new TransitionStateClassPredicate($object->getStatableClassName()),
                    new FilterBaseVpdPredicate(),
                    new SortOrderedClause()
                )
        );

        if ( $transition_it->count() < 1 )
        {
            $method = new ObjectModifyWebMethod($source_it);
            $result = array (
                "message" => "denied",
                "description" => str_replace('%1', $method->getJsCall(), text(1860))
            );

            echo JsonWrapper::encode($result);
            return;
        }

        $this->setObjectIt( $object_it );

        $reason = '';

        while( !$transition_it->end() )
        {
            $this->setTransitionIt( $transition_it );

            if ( !$this->hasAccess() )
            {
                $reason = $this->getReasonHasNoAccess();
                $transition_it->moveNext();
                continue;
            }

            // extend model to get visible|required attributes
            $tobeData = array_merge(
                $object_it->getData(),
                $parms,
                array (
                    $parms['attribute'] => $parms['value']
                )
            );
            $model_builder = new WorkflowTransitionAttributesModelBuilder(
                $transition_it,
                array(),
                $tobeData
            );
            $model_builder->build( $object );

            $attributes = array();
            foreach( $object->getAttributes() as $attribute => $data )
            {
                if ( !$object->IsAttributeVisible($attribute) ) continue;
                $attributes[] = $attribute;
            }

            if ( count($attributes) > 0 )
            {
                $required = array();
                foreach( $object->getAttributes() as $attribute => $info ) {
                    if ( $parms[$attribute] != '' ) $required[$attribute] = $parms[$attribute];
                    if ( $parms['attribute'] != '' ) $required[$parms['attribute']] = $parms['value'];
                }

                $url = '&'.http_build_query(
                        array_map(function($value) {
                            return SanitizeUrl::parseUrl($value);
                        },
                            array_merge(
                                $required,
                                array (
                                    'Transition' => $transition_it->getId(),
                                    'formonly' => 'true'
                                )
                            )
                        )
                    );

                if ( $object instanceof Request && in_array('Tasks', $attributes, true) && !in_array('TransitionComment', $attributes) ) {
                    $url = getSession()->getApplicationUrl($object_it).'issues/board?mode=group&ChangeRequest='.$object_it->getId().$url;
                }
                else {
                    $url = $object_it->getEditUrl().$url;
                }

                echo '{"message":"redirect","url":"'.$url.'"}';
                return;
            }
            else
            {
                $alerts = $transition_it->getUserAlerts();
                if ( count($alerts) > 0 && !array_key_exists('suppress-alert',$_REQUEST) ) {
                    echo JsonWrapper::encode(array (
                        "message" => "alert",
                        "description" => sprintf(text(3312), ' - '.join('<br/> - ',$alerts))
                    ));
                    return;
                }

                try {
                    $method = new TransitionStateMethod( $transition_it, $object_it );

                    unset($parms['class']);
                    unset($parms['object']);
                    unset($parms['target']);
                    unset($parms['source']);

                    $method->execute(
                        $transition_it->getId(), $object_it->getId(), get_class($object_it->object), $parms
                    );

                    echo '{"message":"ok"}';
                }
                catch( \Exception $e ) {
                    echo JsonWrapper::encode(array (
                            "message" => "denied",
                            "description" => $e->getMessage()
                        ));
                }
                return;
            }

            $transition_it->moveNext();
        }

        $transition_it->moveFirst();
        $method = new ObjectModifyWebMethod($transition_it);
        $method->setObjectUrl(
            getSession()->getApplicationUrl().'project/workflow/'.$object->getStateClassName().$transition_it->getEditUrl()
        );

        $result = array (
            "message" => "denied",
            "description" =>
                str_replace('%1', $method->getJsCall(),
                    str_replace('%2', $reason, $reason == '' ? text(1012) : text(2018))
                )
        );

        echo JsonWrapper::encode($result);
    }
}
