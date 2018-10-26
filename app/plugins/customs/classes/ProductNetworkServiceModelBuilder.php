<?php
use Devprom\ProjectBundle\Service\Network\NetworkService;
use Devprom\ProjectBundle\Service\Network\NetworkServiceModelBuilder;
include_once SERVER_ROOT_PATH . "pm/classes/product/FeatureModelExtendedBuilder.php";

class ProductNetworkServiceModelBuilder extends NetworkServiceModelBuilder
{
    public function build( \SessionBase $session, NetworkService $service ) {
        $service->addBuilder(new \FeatureModelExtendedBuilder($session));
    }
}