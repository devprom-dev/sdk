<?php

namespace Devprom\ServiceDeskBundle\Config;

use DAL;
use DALMySQL;
use Devprom\ServiceDeskBundle\Util\TextUtil;
use MySQLConnectionInfo;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class DevpromParametersLoader {

    public function load($supportProjectId)
    {
        $params = array();
        $params['DB_HOST'] = DB_HOST;
        $params['DB_USER'] = DB_USER;
        $params['DB_PASS'] = DB_PASS;
        $params['DB_NAME'] = DB_NAME;

        $settings = $this->queryDevpromSettings($supportProjectId);

        $params['systemLanguage'] = $settings['langCode'];
        $params['adminEmail'] = $settings['adminEmail'];
        $params['supportEmail'] = $this->normalizeEmailAddress($settings['supportEmail']);
        $clientName = mb_convert_encoding($settings['clientName'], 'utf-8', 'windows-1251');
        $params['supportName'] = sprintf("%s Support", TextUtil::unescapeHtml($clientName));

        return $params;
    }

    /**
     * @return array
     */
    protected function queryDevpromSettings($projectId)
    {
        DALMySQL::Instance()->Connect(new MySQLConnectionInfo(DB_HOST, DB_NAME, DB_USER, DB_PASS));
        $r = DAL::Instance()->Query("SHOW COLUMNS FROM `co_RemoteMailbox` LIKE 'SenderAddress'");
        if (mysql_num_rows($r)) {
            $sql = "SELECT LOWER(l.CodeName) langCode, AdminEmail adminEmail, s.Caption clientName,
                        IF(rm.SenderAddress IS NOT NULL,
                              rm.SenderAddress,
                                  IF(rm.EmailAddress IS NOT NULL,
                                 IF(rm.EmailAddress NOT LIKE '%%@%%', CONCAT(rm.EmailAddress, '@', rm.HostAddress), rm.EmailAddress),
                                 adminEmail)
                                  ) supportEmail
                    FROM cms_SystemSettings s, cms_Language l
                    LEFT JOIN (pm_Project p, co_RemoteMailbox rm) ON (p.pm_ProjectId = '%s' AND rm.Project = p.pm_ProjectId)
                    WHERE s.LANGUAGE = l.cms_languageId";
        } else {
            $sql = "SELECT LOWER(l.CodeName) langCode, AdminEmail adminEmail, s.Caption clientName,
                    IF(rm.EmailAddress IS NOT NULL,
                         IF(rm.EmailAddress NOT LIKE '%%@%%', CONCAT(rm.EmailAddress, '@', rm.HostAddress), rm.EmailAddress),
                         adminEmail) supportEmail
                FROM cms_SystemSettings s, cms_Language l
                LEFT JOIN (pm_Project p, co_RemoteMailbox rm) ON (p.pm_ProjectId = '%s' AND rm.Project = p.pm_ProjectId)
                WHERE s.LANGUAGE = l.cms_languageId";
        }
        $sql = sprintf($sql, mysql_real_escape_string($projectId));
        $r2 = DAL::Instance()->Query($sql);
        $data = mysql_fetch_assoc($r2);
        return $data;
    }

    /**
     * @param $supportEmail
     * @return string
     */
    protected function normalizeEmailAddress($supportEmail)
    {
        if (!$supportEmail) {
            return " ";
        } else if (preg_match("/.+<(.+)>/", html_entity_decode($supportEmail), $matches)) {
            return $matches[1];
        }

        return $supportEmail;
    }
}