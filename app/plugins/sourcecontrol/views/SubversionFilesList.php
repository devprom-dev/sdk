<?php

include 'SubversionList.php';

class SubversionFilesList extends SubversionList
{
    var $connector;

    function getIterator()
    {
        $table = $this->getTable();
        $repo_it = $table->getSubversionIt();

        $this->connector = $repo_it->getConnector();

        return $this->connector->getFiles(IteratorBase::wintoutf8($_REQUEST['path']) );
    }

    function IsNeedToModify( $object_it ) { return false; }
    
    function getColumns()
    {
        $this->object->addAttribute('File', '', translate('Имя каталога или файла'), true);
        $this->object->addAttribute('Author', '', translate('Автор'), true);
        $this->object->addAttribute('Size', '', translate('Размер'), true);
        $this->object->addAttribute('Type', '', translate('Тип'), true);
        $this->object->addAttribute('LastModification', '', translate('Последнее изменение'), true);

        return parent::getColumns();
    }

    function drawCell( $object_it, $attr )
    {
        global $_REQUEST, $project_it;

        $table = $this->getTable();
        $repo_it = $table->getSubversionIt();

        if ( $attr == 'File' )
        {
            $path = $this->connector->buildPath(
                    $_REQUEST['path'], $object_it->utf8towin($object_it->get('Path')) );

            switch ( $object_it->get('Type') )
            {
                case 'directory':
                    echo '<a href="?path='.urlencode($path).'&subversion='.$repo_it->getId().'">'.
                            $object_it->utf8towin($object_it->get('Name')).'/</a>';
                            break;

                default:
                    $content = preg_split('/\//', $object_it->get('ContentType'));
                    if ( $content[0] == 'text' )
                    {
                        echo '<a href="/pm/'.$project_it->get('CodeName').
                        '/module/sourcecontrol/files?path='.urlencode($path).'&subversion='.$repo_it->getId().
                        '&name='.urlencode($object_it->get('Name')).'">'.
                        $object_it->utf8towin($object_it->get('Name')).'</a>';
                    }
                    else
                    {
                        echo '<a href="?export=download&path='.urlencode($path).'&subversion='.$repo_it->getId().
                        '&name='.urlencode($object_it->get('Name')).'">'.
                        $object_it->utf8towin($object_it->get('Name')).'</a>';
                    }
                    break;
            }
        }

        if ( $attr == 'LastModification' )
        {
            echo $object_it->get('RecordModified');
        }

        if ( $attr == 'Type' )
        {
            if ( $object_it->get('Type') != 'directory' )
            {
                echo $object_it->get('ContentType');
            }
        }

        if ( $attr == 'Size' )
        {
            if ( $object_it->get('Length') != '' )
            {
                echo $object_it->get('Length').' '.translate('байт');
            }
        }

        if ( $attr == 'Author' )
        {
            echo $object_it->get('Creator');
        }
    }
}