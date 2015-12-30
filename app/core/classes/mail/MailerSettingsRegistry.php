<?php

class MailerSettingsRegistry extends ObjectRegistrySQL
{
    function getQueryClause( $sql ) {
        return " (SELECT 1 entityId, 1 OrderNum) ";
    }
}