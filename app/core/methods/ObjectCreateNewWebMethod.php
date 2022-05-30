<?php
include_once "WebMethod.php";

class ObjectCreateNewWebMethod extends WebMethod
{
	private $object;
	private $vpd = '';
	private $selectProject = false;

	function __construct( $object = null )
	{
		parent::__construct();

		if ( is_object($object) ) {
            $this->object = getFactory()->getObject(get_class($object));
			$this->setVpd($this->object->getVpdValue());

			if ( $this->object->getVpdValue() != '' && !$this->object instanceof Project && $this->object->getEntityRefName() != 'pm_CustomReport' ) {
                $projectIt = getSession()->getProjectIt();
                $this->doSelectProject($projectIt->IsPortfolio()
                    && $this->object->getVpdValue() == $projectIt->get('VPD'));
            }
		}

		$this->setAsync(false);
        $this->setBeforeCallback('beforeUnload');
		$this->setRedirectUrl( 'function(){devpromOpts.updateUI();}' );
	}

    function getModule() {
        if ( $this->vpd != '' ) $this->object->setVpdContext($this->vpd);
        return getSession()->getApplicationUrl($this->object).'methods.php';
    }

	public function getObject()
	{
		return $this->object;
	}

	public function getCaption()
	{
		return $this->object->getDisplayName();
	}

	public function doSelectProject($value = true)
    {
	    $this->selectProject = $value;
    }

	public function setVpd( $vpd ) {
		$this->vpd = $vpd;
	}

	function getNewObjectUrl()
	{
		if ( $this->vpd != '' ) $this->object->setVpdContext($this->vpd);
		return $this->object->getPageName();
	}
	
	function getJSCall( $parms = array() )
	{
		$uid = new ObjectUID;
		if ( $uid->hasUidObject($this->object) ) {
			$absoluteUrl = getSession()->getApplicationUrl().$uid->getObjectUidInt($uid->getClassName($this->object->getEmptyIterator()), '');
		}
		else {
            $absoluteUrl = $this->object->getPage();
        }

		$formUrl = addslashes(htmlspecialchars($this->getNewObjectUrl(), ENT_COMPAT | ENT_HTML401, APP_ENCODING));
		$method_parms = array (
            get_class($this->object),
            $this->object->getEntityRefName(),
            $absoluteUrl
		);
		foreach( $method_parms as $key => $parm ) {
			$method_parms[$key] = addslashes(htmlspecialchars($parm, ENT_COMPAT | ENT_HTML401, APP_ENCODING));
		}

		if ( $this->selectProject ) {
            $projectIt = getSession()->getProjectIt();
            return "javascript: workflowNewObject('/pm/".$projectIt->get('CodeName')."/widget/project','', '', '', {}, function(project, data) {
                            var formUrl = '".$formUrl."';
                            formUrl = formUrl.replace(/\/pm\/[^\/]+/i, '/pm/' + project);
                            workflowNewObject(formUrl,'".join("','", $method_parms)
                               ."', ".str_replace('"',"'",json_encode($parms, JSON_HEX_APOS)).",".$this->getRedirectUrl().");
                        });";
        }

		return "javascript: workflowNewObject('".$formUrl."','".join("','", $method_parms)
            ."', ".str_replace('"',"'",json_encode($parms, JSON_HEX_APOS)).",".$this->getRedirectUrl().")";
	}

	function url( $parms = array() )
    {
        return parent::getJSCall( array_merge( $parms,
            array (
                'class' => get_class($this->object)
            )
        ));
    }

	function execute_request()
    {
        $class = getFactory()->getClass($_REQUEST['class']);
        if ( !class_exists($class) ) {
            \Logger::getLogger('System')->error('Unknown entity: '.$class);
            return;
        }

        $object = getFactory()->getObject($class);
        $data = array (
            'Caption' => $object instanceof WikiPage
                            ? '<'.$object->getSectionName().'>'
                            : $object->getDisplayName()
        );

        if ( $object instanceof WikiPage ) {
            $type_it = $object->getAttributeObject('PageType')->getExact($object->getDefaultAttributeValue('PageType'));
            if ( $type_it->getId() != '' ) {
                $data['Caption'] = '<'.$type_it->getDisplayName().'>';
            }
            if ( !array_key_exists('PageType', $_REQUEST) ) {
                $data['PageType'] = $type_it->getId();
            }
            if ( $type_it->get('DefaultPageTemplate') != '' ) {
                $data['Content'] = $type_it->getRef('DefaultPageTemplate')->getHtmlDecoded('Content');
            }
        }

        if ( $data['Content'] == '' ) {
            $template_it = getFactory()->getObject('TextTemplate')->getRegistry()->Query(
                array (
                    new FilterVpdPredicate(),
                    new TextTemplateEntityPredicate(get_class($object)),
                    new FilterAttributePredicate('IsDefault', 'Y')
                )
            );
            if ( $template_it->getId() != '' ) {
                $data['Content'] = $template_it->getHtmlDecoded('Content');
            }
        }

        $parms = array_filter(
            array_merge(
                array_intersect_key(
                    $_REQUEST,
                    array_map(
                        function($value) {
                            return '';
                        },
                        array_flip(array_keys($object->getAttributes()))
                    )
                ),
                $data
            ),
            function($value) {
                return $value != '';
            }
        );

        try {
            $object_it = getFactory()->createEntity($object, $parms);
            echo json_encode(
                array(
                    'Id' => $object_it->getId(),
                    'Url' => $object_it->getUidUrl()
                )
            );
        }
        catch( \Exception $e ) {
            echo JsonWrapper::encode(
                array(
                    'message' => 'denied',
                    'description' => $e->getMessage()
                )
            );
            return;
        }
    }

    function hasAccess() {
		return getFactory()->getAccessPolicy()->can_modify($this->object);
	}
}