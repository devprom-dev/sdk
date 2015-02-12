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

    public function loadSystemSettings()
    {
        $params = array();
        $params['DB_HOST'] = DB_HOST;
        $params['DB_USER'] = DB_USER;
        $params['DB_PASS'] = DB_PASS;
        $params['DB_NAME'] = DB_NAME;

        $settings = $this->queryDevpromSettings();

        $params['systemLanguage'] = $settings['langCode'];
        $params['adminEmail'] = $settings['adminEmail'];
        $clientName = mb_convert_encoding($settings['clientName'], 'utf-8', 'windows-1251');
        $params['supportName'] = sprintf("%s Support", TextUtil::unescapeHtml($clientName));

        return $params;
    }
    
    public function loadProjectSettings()
    {
    	$data = $this->queryProjectSettings();
        $settings['supportEmail'] = $this->normalizeEmailAddress($data['supportEmail']);
        $settings['supportProjects'] = $data['supportProjects'];
        $settings['supportProjectVpds'] = $data['supportProjectVpds'];
        return $settings;
    }

    /**
     * @return array
     */
    protected function queryDevpromSettings()
    {
    	if (!\DeploymentState::IsInstalled()) return array();
        DALMySQL::Instance()->Connect(new MySQLConnectionInfo(DB_HOST, DB_NAME, DB_USER, DB_PASS));
        $sql = "SELECT LOWER(l.CodeName) langCode, AdminEmail adminEmail, s.Caption clientName
                    FROM cms_SystemSettings s, cms_Language l
                    WHERE s.LANGUAGE = l.cms_languageId";
        $r2 = DAL::Instance()->Query($sql);
        $data = mysql_fetch_assoc($r2);
        return $data;
    }

    protected function queryProjectSettings()
    {
    	if (!\DeploymentState::IsInstalled()) return array();
    	DALMySQL::Instance()->Connect(new MySQLConnectionInfo(DB_HOST, DB_NAME, DB_USER, DB_PASS));
        $sql = "SELECT p.pm_ProjectId, p.VPD, IF(rm.SenderAddress IS NOT NULL,
                              rm.SenderAddress,
                                  IF(rm.EmailAddress IS NOT NULL,
                                 IF(rm.EmailAddress NOT LIKE '%%@%%', CONCAT(rm.EmailAddress, '@', rm.HostAddress), rm.EmailAddress),
                                 (SELECT s.AdminEmail FROM cms_SystemSettings s))
                                  ) supportEmail
                    FROM pm_Project p LEFT JOIN co_RemoteMailbox rm ON rm.Project = p.pm_ProjectId 
        		   WHERE p.IsSupportUsed = 'Y' ";
        $r2 = DAL::Instance()->Query($sql);
        $result = array();
        $ids = array();
        $vpds = array();
        while($data = mysql_fetch_assoc($r2)) {
        	$result['supportEmail'] = $data['supportEmail'];
        	$ids[] = $data['pm_ProjectId'];
        	$vpds[] = $data['VPD'];
        }
        $result['supportProjects'] = $ids; 
        $result['supportProjectVpds'] = $vpds;
        return $result;
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