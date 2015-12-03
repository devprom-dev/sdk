<?php

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class ApplyServicedeskMigrations extends Installable {
    function check()
    {
        return true;
    }


    function install()
    {
        $cmd = $this->getPhpExecutable() . ' "' . SERVER_ROOT_PATH . 'servicedesk/console" --no-interaction --document-root="'.DOCUMENT_ROOT.'" doctrine:migrations:migrate 2>&1';
        $this->info('Executing Servicedesk database migration: ' . $cmd);
        exec($cmd, $output, $retCode);
        $this->info('Result: ' . $retCode . ', Output: ' . var_export($output, true));
        return true;
    }

    /**
     * @return string
     */
    public function getPhpExecutable()
    {
        if ($this->checkWindows()) {
            return '"'.SERVER_ROOT . '/php/php"';
        } else {
            return 'php';
        }
    }

}
