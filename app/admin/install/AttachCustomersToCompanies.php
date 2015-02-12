<?php

class AttachCustomersToCompanies extends Installable 
{
    function check()
    {
        return true;
    }

	function skip()
	{
		return getFactory()->getObject('User')->getRegistry()->Count() < 1 
			|| !class_exists('CustomerEventHandler');
	}
    
    function install()
    {
    	$customer_it = getFactory()->getObject('Customer')->getRegistry()->Query(
	    			array (
	    					new FilterAttributePredicate('Company', 'none')
	    			)
		   	);

    	$handler = new CustomerEventHandler();
    	while( !$customer_it->end() )
    	{
    		$handler->attachCompanyByEmail($customer_it);
    		$customer_it->moveNext();
    	}
    	
        return true;
    }
}
