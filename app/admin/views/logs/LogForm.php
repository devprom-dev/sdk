<?php

class LogForm extends PageForm
{
    function extendModel()
    {
        parent::extendModel();
        $object = $this->getObject();
        $object->setAttributeVisible('Caption', true);
        $object->removeAttribute('OrderNum');
        $object->removeAttribute('BackupFileName');
    }

    function createFieldObject( $attr )
    {
        switch ( $attr )
        {
            case 'Caption':
                $field = new FieldText();
                $field->setRows(40);
                $field->setText($this->getFileTail( SERVER_LOGS_PATH.'/'.$this->getObjectIt()->get('Caption') ));
                return $field;

            default:
                return parent::createFieldObject( $attr );
        }
    }

    function getActions() {
        return array();
    }

    function getDeleteActions($objectIt) {
        return array();
    }

    function getBodyTemplate() {
        return 'core/SimpleFormBody.php';
    }

    private function getFileTail( $file_path, $lines = 1820 )
    {
        $fp = fopen($file_path, 'r');

        if ( $fp === false ) return '';

        $pos = -1; $line = ''; $c = '';

        $passed = 0;

        do {
            $line = $c . $line;

            if ( fseek($fp, $pos--, SEEK_END) < 0 ) break;

            $c = fgetc($fp);

            if ( $c === false ) break;

            if ( $c == chr(10) || $c == chr(13) ) $passed++;
        }
        while ($passed < $lines);

        fclose($fp);

        return '<code class="accesslog hljs" style="color:inherit;background-color:inherit;white-space:pre-wrap;border:none;">'.
            $line .'</code>';
    }
}
