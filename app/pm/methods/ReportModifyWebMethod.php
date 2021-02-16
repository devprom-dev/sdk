<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class ReportModifyWebMethod extends WebMethod
{
 	var $report_it;
 	private $values = array();
 	
 	function __construct( $report_it = null )
 	{
 		$this->report_it = $report_it;
 		if ( is_object($this->report_it) ) {
 		    parse_str($this->report_it->getHtmlDecoded('Url'), $this->values);
        }
 		$this->setRedirectUrl("donothing");
 		parent::__construct();
 	}
 	
 	function getCaption() 
	{
		return translate('Сохранить');
	}
	
	function getJSCall( $values = array() ) {
		return parent::getJSCall(
			array( 'report' => $this->report_it->getId(),
				   'items' => join(array_keys($values), ','),
				   'values' => join(array_values($values), ';') )
			);
	}

 	function execute_request()
 	{
	 	if ( $_REQUEST['report'] != '' ) {
	 		$this->execute($_REQUEST['report'], $_REQUEST['items'], $_REQUEST['values']);
	 	}
 	}
 	
 	function execute( $report_id, $items, $values )
 	{
 		$report = getFactory()->getObject('pm_CustomReport');
 		$report_it = $report->getExact( $report_id );

 		if ( !getFactory()->getAccessPolicy()->can_modify($report_it) ) {
 		    throw new \Exception('Access restricted');
        }
 		
		$parms = '';
		$items = preg_split('/,/', $items);
		$values = preg_split('/;/', $values);

		foreach ( $items as $key => $parm ) {
			if ( $values[$key] != '' ) {
				$parms .= $parm.'='.$values[$key].'&';
			}
		}

		$report->modify_parms($report_it->getId(), array ( 'Url' => trim($parms,'&') ));
	}
 	
 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_modify($this->report_it);
 	}

 	function getValue( $parm ) {
 	    return $this->values[$parm];
    }

    function setValues( $values ) {
 	    $this->values = $values;
    }

    function getQueryString() {
        return http_build_query($this->values, '', '&', PHP_QUERY_RFC3986);
    }

    function url() {
    }

    function urlCommon() {
    }
}
