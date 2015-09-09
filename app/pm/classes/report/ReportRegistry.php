<?php

class ReportRegistry extends ObjectRegistrySQL
{
 	var $reports = array();
	
 	function addReport( $array, $sort_method = "report_category_sort" )
 	{
 	    foreach( $this->reports as $key => $report )
 	    {
 	        if ( $report['name'] == $array['name'] )
 	        {
 	            $this->reports[$key] = $array;
 	            
 	            return;
 	        }
 	    }
 	    
 		array_push( $this->reports, $array );
 		
 		usort( $this->reports, $sort_method );
 	}

 	function getReports()
 	{
 		return $this->reports;
 	}
 	
 	function setReports( $reports, $sort_method = "report_category_sort" )
 	{
 		$this->reports = $reports;
 		
 		usort( $this->reports, $sort_method );
 	}
 	
 	function getReport( $name )
 	{
 		foreach( $this->reports as $report )
 		{
 			if ( $report['name'] == $name )
 			{
 				return $report;
 			}
 		}
 		
 		return array();
 	}
 	
 	function setReport( $name, $report_attrs )
 	{
 		foreach( $this->reports as $key => $report )
 		{
 			if ( $report['name'] == $name )
 			{
 				$this->reports[$key] = $report_attrs;
 			}
 		}
 	}

 	function createSQLIterator( $sql )
 	{
 	    $data_array = array();
 	    
 	    $vpd_value = array_shift($this->getObject()->getVpds());
 	    
 	    foreach( $this->reports as $report )
 	    {
 	        $data = array();
 	        
    		$data['cms_ReportId'] = $report['name'];
    		$data['Caption'] = $report['title'];
    		$data['Description'] = $report['description'];
    		$data['Url'] = $report['url'];
    		$data['QueryString'] = $report['query'];
    		$data['Category'] = $report['category'];
    		$data['Type'] = $report['type'];
    		$data['Module'] = $report['module'];
    		$data['Report'] = $report['report'];
    		$data['Author'] = $report['author'];
    		$data['WidgetClass'] = $report['widget'];
    		$data['IsCustomized'] = $report['custom'] ? 'Y' : 'N';
    		$data['VPD'] = $vpd_value;
    		
    		$data_array[] = $data;
 	    }

 	    return $this->createIterator($data_array);
 	}
}

function report_category_sort( $left, $right )
{
 	if ( $left['category-index'] == $right['category-index'] ) {
 		if ( $left['type'] == $right['type'] ) {
            if ( $left['title'] == $right['title'] ) {
                return 0;
            }
            return $left['title'] > $right['title'] ? 1 : -1;
		}
 		return $left['type'] > $right['type'] ? 1 : -1;
 	}
 	return $left['category-index'] > $right['category-index'] ? 1 : -1;
}
 