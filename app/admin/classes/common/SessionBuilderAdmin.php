<?php
include "AdminSession.php";

class SessionBuilderAdmin extends SessionBuilder
{
    protected function buildSession(array $parms, $cacheService)
    {
        return new AdminSession(null, null, null, $cacheService);
    }
}