<?php

class MailerSettingsRegistry extends ObjectRegistrySQL
{
    function getQueryClause() {
        return " (SELECT 1 entityId, 1 OrderNum) ";
    }
}