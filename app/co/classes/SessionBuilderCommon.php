<?php
include "COSession.php";

class SessionBuilderCommon extends SessionBuilder
{
    protected function buildSession(array $parms, $cacheService = null)
    {
        return new COSession(null, null, null, $cacheService);
    }
}