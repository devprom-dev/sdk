<?php

class MailerSettingsRegistry extends ObjectRegistrySQL
{
    function getQueryClause(array $parms) {
        return " (SELECT 1 entityId, 1 OrderNum) ";
    }
}