<?php
include "AdminSession.php";

class SessionBuilderAdmin extends SessionBuilder
{
    protected function buildSession(array $parms, $cacheService = null)
    {
        return new AdminSession(null, null, null, $cacheService);
    }
}