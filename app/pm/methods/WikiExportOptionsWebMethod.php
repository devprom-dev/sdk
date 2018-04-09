<?php
include_once SERVER_ROOT_PATH.'pm/methods/WikiExportBaseWebMethod.php';

class WikiExportOptionsWebMethod extends WikiExportBaseWebMethod
{
    function url( $page_it = null, $class, $title = '', $baseline = '' )
    {
        $parms = array(
            get_class($this),
            "class=".$class,
            "entity=".strtolower(get_class($page_it->object)),
            'objects=%ids%',
            'baseline='.$baseline,
            'ExportTemplate'
        );
        $parms = array_merge($parms, array_keys($this->getCheckOptions($class)));
        if ( in_array($class, array('WikiConverterPanDocMSWord','WikiConverterPanDocODF')) ) {
            $parms[] = 'File';
        }
        return "javascript:processBulk('".translate('Экспорт').': '.$title."','?formonly=true&operation=Method:".join(":",$parms)."', '".(is_object($page_it) ? $page_it->getId() : '%ids%')."', '');";
    }

    function getCheckOptions( $class )
    {
        $options = array(
            'UseNumbering' => 'numbering',
            'ExportChildren' => 'children',
        );
        if ( !in_array($class, array('WikiConverterPanDocMSWord')) ) {
            $options['paging'] = 'UsePaging';
        }
        return $options;
    }

    function execute_request()
    {
        ob_start();
        parent::execute_request();
        $url = ob_get_contents();
        ob_end_clean();

        $options = array();

        if ( $_REQUEST['ExportTemplate'] != '' ) {
            $template_it = getFactory()->getObject('ExportTemplate')->getExact($_REQUEST['ExportTemplate']);
            if ( $template_it->getId() > 0 ) {
                $options = explode('-', $template_it->getHtmlDecoded('Options'));
                $options[] = 'template='.$template_it->getId();
            }
        }

        if ( count($options) < 1 ) {
            foreach( $this->getCheckOptions($_REQUEST['class']) as $requestKey => $optionName ) {
                if ( in_array($_REQUEST[$requestKey], array('Y','on')) ) {
                    $options[] = $optionName;
                }
            }
            if ( $_REQUEST['baseline'] != '' ) {
                $options[] = 'baseline,'.$_REQUEST['baseline'];
            }
            if ( file_exists($_FILES['File']['tmp_name']) ) {
                $template_it = getFactory()->getObject('ExportTemplate')->
                getRegistry()->Create(
                    array (
                        'Caption' => array_shift(explode('.',$_FILES['File']['name'])),
                        'Options' => join('-',$options)
                    )
                );
                $options[] = 'template='.$template_it->getId();
            }
        }

        $url = '?'.$url.'&options='.join('-',$options);

        echo JsonWrapper::encode(
            array (
                'state' => 'redirect',
                'message' => '',
                'object' => $url
            )
        );
        exit();
    }
}