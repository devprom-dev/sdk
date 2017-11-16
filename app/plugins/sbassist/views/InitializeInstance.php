<?php

include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class InitializeInstance extends Page
{
    private $trial_length = 14;
    private $language = 2;

    public function __construct()
    {
    }

    // the page will be available without any authentization required
    function authorizationRequired()
    {
        return false;
    }

    function render()
    {
        $this->language = $_REQUEST['l'] == 'ru' ? 1 : 2;

        if ( getFactory()->getObject('User')->getAll()->count() > 0 ) return;

        $log = 'License given: '.$this->createLicense().PHP_EOL;

        $installation_factory = InstallationFactory::getFactory();

        $user_id = $this->createUser(
            $_REQUEST['username'], $_REQUEST['userlogin'], $_REQUEST['useremail']
        );

        $user_it = getFactory()->getObject('User')->getExact($user_id);

        $log .= 'User created: '.$user_id;

        $this->updateSystemSettings();

        unlink( $this->getKeyFile() );

        $checkpoint_factory = getCheckpointFactory();
        $checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );
        $checkpoint->executeDynamicOnly();

        $this->setupLoggers();
        $this->setupBackgroundTasks();
        $this->setupProjectTemplates();

        getSession()->close();
        getSession()->open($user_it);

        file_put_contents(dirname(SERVER_ROOT_PATH).'/initialize.log', $log);

        $this->sendMail($user_it);

        $clear_cache_action = new ClearCache();
        $clear_cache_action->install();
        PluginsFactory::Instance()->invalidate();

        if ( $_REQUEST['template'] == '' )
        {
            exit(header('Location: /projects/welcome'));
        }
        else {
            $template = getFactory()->getObject('pm_ProjectTemplate');
            $template->setRegistry( new ObjectRegistrySQL() );
            $template_it = $template->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('FileName', $_REQUEST['template'])
                )
            );

            getFactory()->getCacheService()->invalidate();
            exit(header('Location: /module/sbassist/projects?template='.$template_it->get('FileName')));
        }
    }

    protected function createUser( $name, $login, $email )
    {
        $user = getFactory()->getObject('User');
        $user->setNotificationEnabled(false);

        $user_id = $user->add_parms(
            array (
                'Caption' => $name,
                'Login' => $login,
                'Email' => $email,
                'Password' => $login,
                'IsAdmin' => 'Y',
                'Language' => $this->language
            )
        );

        $group_it = getFactory()->getObject('co_UserGroup')->getRegistry()->getAll();

        if ( $group_it->getId() > 0 )
        {
            getFactory()->getObject('co_UserGroupLink')->add_parms(
                array (
                    'SystemUser' => $user_id,
                    'UserGroup' => $group_it->getId()
                )
            );
        }

        $generator = new UserPicSpritesGenerator();
        $generator->storeSprites();

        return $user_id;
    }

    protected function createLicense()
    {
        date_default_timezone_set('UTC');
        $date = new DateTime();
        $date->add(new DateInterval('P14D'));

        $license_value = json_encode(array (
            'timestamp' =>  $date->format('Y-m-d'),
            'days' => $this->trial_length,
            'users' => 10,
            'options' => 'core'
        ));

        openssl_sign($license_value.INSTALLATION_UID, $signature, include($this->getKeyFile()), OPENSSL_ALGO_SHA512);
        $key_value = base64_encode($signature);

        getFactory()->getObject('LicenseInstalled')->getAll()->modify(
            array (
                'LicenseType' => 'LicenseScrumBoard',
                'LicenseValue' => $license_value,
                'LicenseKey' => $key_value
            )
        );
        file_put_contents(SERVER_ROOT_PATH.'/conf/license.dat', serialize(array('leftdays' => $this->trial_length)));

        return $key_value;
    }

    protected function updateSystemSettings()
    {
        getFactory()->getObject('cms_SystemSettings')->getAll()->modify(
            array (
                'Caption' => 'Scrum Board',
                'EmailSender' => 'admin',
                'AdminEmail' => SAAS_SENDER,
                'ServerName' => EnvironmentSettings::getServerName(),
                'ServerPort' => SAAS_PORT,
                'Language' => $this->language
            )
        );
    }

    protected function getKeyFile()
    {
        return dirname(__FILE__).'/key.php';
    }

    protected function setupLoggers()
    {
        $default_path = '/var/log/devprom';

        $local_dir = dirname(SERVER_ROOT_PATH).'/logs';

        mkdir($local_dir, 0755, true);

        $settings_file = DOCUMENT_ROOT.'conf/logger.xml';

        file_put_contents($settings_file, str_replace($default_path, $local_dir, file_get_contents($settings_file)));
    }

    protected function sendMail( $user_it )
    {
        $to_address = $user_it->get('Email');
        $user_name = $user_it->get('Login');
        $user_pass = $user_it->get('Login');
        $host_url = SAAS_SCHEME.'://'.EnvironmentSettings::getServerName();

        $mail = new HtmlMailbox;
        $mail->appendAddress($to_address);

        $lang = $this->language == 1 ? 'ru' : 'en';
        $body = file_get_contents(SERVER_ROOT_PATH.'plugins/sbassist/resources/'.$lang.'/greetings.html');

        $body = preg_replace('/\%user_name\%/', $user_it->get('Caption'), preg_replace('/\%host_url\%/', $host_url, $body));
        $body = preg_replace('/\%password\%/', $user_pass, preg_replace('/\%login\%/', $user_name, $body));
        $body = preg_replace('/\%pass_url\%/', $host_url.'/reset?key='.$user_it->getResetPasswordKey(), $body);

        $mail->setBody($body);
        $mail->setSubject(text('sbassist46'));
        $mail->setFrom(str_replace('%1', SAAS_SENDER, text('sbassist44')));
        $mail->send();
    }

    protected function setupBackgroundTasks()
    {
        $hours = rand(0, 8);
        $minutes = rand(0, 59);

        $job_it = getFactory()->getObject('co_ScheduledJob')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('ClassName',
                    array(
                        'processbackup',
                        'processcheckpoints',
                        'trackhistory'
                    )
                )
            )
        );

        while( !$job_it->end() )
        {
            $isActive = 'Y';
            switch($job_it->get('ClassName'))
            {
                case 'processbackup':
                    $modify_hours = $hours < 1 ? 23 : min($hours, 23);
                    break;

                case 'trackhistory':
                    $modify_hours = '*';
                    break;

                case 'processcheckpoints':
                    $modify_hours = '*';
                    break;
            }

            $job_it->modify(
                array (
                    'Minutes' => min(max($minutes, 0), 59),
                    'Hours' => $modify_hours,
                    'IsActive' => $isActive
                )
            );

            $job_it->moveNext();
        }

        $info_path = DOCUMENT_ROOT.'conf/runtime.info';

        $file = fopen( $info_path, 'w', 1 );
        fwrite( $file, time() );
        fclose( $file );
    }

    protected function setupProjectTemplates()
    {
        if ( $this->language == 2 ) {
            $titles = array (
                'kanban_en.xml' => '',
                'scrum_en.xml' => '',
                'scrumban_en.xml' => ''
            );
            $allowed_templates = array (
                'kanban_en.xml' => '',
                'scrum_en.xml' => '',
                'scrumban_en.xml' => ''
            );
        }
        else {
            $titles = array (
                'kanban_en.xml' => 'Канбан',
                'scrum_en.xml' => 'Скрам',
                'scrumban_en.xml' => 'Скрамбан'
            );
            $allowed_templates = array (
                'kanban_ru.xml' => 'Визуализируйте производственный процесс при помощи Kanban. Улучшайте ваш процесс, чтобы снизить время цикла.',
                'scrum_ru.xml' => 'Часто демонстрируйте результат при помощи Scrum. Используйте простые метрики для управления командой.',
                'scrumban_ru.xml' => 'Справляйтесь с незапланированной работой при помощи Scrumban. Комбинируйте Scrum и Kanban для достижения лучшего результата.'
            );
        }

        $template = getFactory()->getObject('pm_ProjectTemplate');
        $template->setRegistry( new ObjectRegistrySQL() );

        $template_it = $template->getRegistry()->Query();
        $templateIndex = 10;
        while( !$template_it->end() )
        {
            if ( array_key_exists($template_it->get('FileName'), $allowed_templates) ) {
                if ( $allowed_templates[$template_it->get('FileName')] != '' ) {
                    $template_it->object->modify_parms(
                        $template_it->getId(),
                        array (
                            'Caption' => $titles[$template_it->get('FileName')] != ''
                                            ? $titles[$template_it->get('FileName')]
                                            : $template_it->get('Caption'),
                            'Description' => $allowed_templates[$template_it->get('FileName')],
                            'OrderNum' => $templateIndex
                        )
                    );
                    $templateIndex += 10;
                }
                $template_it->moveNext();
                continue;
            }

            $template_it->delete();
            $template_it->moveNext();
        }

        copy( SERVER_ROOT_PATH . "plugins/sbassist/resources/js/scrum-tour.js", SERVER_ROOT_PATH . "plugins/scrum/resources/js/tour.js" );
        copy( SERVER_ROOT_PATH . "plugins/sbassist/resources/js/kanban-tour.js", SERVER_ROOT_PATH . "plugins/kanban/resources/js/tour.js" );
    }
}
