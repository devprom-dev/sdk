<?php

class RepairDatabase extends Installable
{
    function skip()
    {
    	return !in_array(INSTALLATION_UID, array('55466c9f57053966b3d0c3c3accd1426'));
    }

    // checks all required prerequisites
    function check()
    {
    	return true;
    }

    function install()
    {
    	DAL::Instance()->Query("REPAIR TABLE cms_EntityCluster USE_FRM");
    }
}
