<?php
namespace Devprom\ProjectBundle\Service\Files;
use Flow;

class UploadFileService
{
    private $tmp = '';
    private $upload = '';

    function __construct()
    {
        $this->tmp = SERVER_FILES_PATH . 'tmp';
        $this->upload = SERVER_FILES_PATH . 'upload';
    }

    function process( $objectId, $objectClass, $attachmentClass )
    {
        mkdir($this->tmp);
        mkdir($this->upload);

        $config = new Flow\Config();
        $config->setTempDir($this->tmp);

        $request = new Flow\Request();
        $uploadFileName = uniqid()."_".$request->getFileName(); // The name the file will have on the server
        $uploadPath = $this->upload . '/' . $uploadFileName;

        if ( Flow\Basic::save($uploadPath, $config, $request) )
        {
            if ( $objectId != '' ) {
                $objectClass = getFactory()->getClass($objectClass);
                if ( class_exists($objectClass) ) {
                    $objectIt = getFactory()->getObject($objectClass)->getExact($objectId);
                    if ( $objectIt->getId() != '' )
                    {
                        $attachment = getFactory()->getObject($attachmentClass);
                        foreach( $attachment->getAttributesByType('file') as $attribute )
                        {
                            $_FILES[$attribute] = $_FILES['file'];
                            $_FILES[$attribute]['tmp_name'] = $uploadPath;

                            $fileIt = $attachment->getRegistry()->Create(
                                array(
                                    'Caption' => $_FILES['file']['name'],
                                    'ObjectClass' => get_class($objectIt->object),
                                    'ObjectId' => $objectIt->getId(),
                                    'WikiPage' => $objectIt->getId()
                                )
                            );
                            return array(
                                'name' => $fileIt->getDisplayName(),
                                'id' => $fileIt->getId(),
                                'class' => get_class($fileIt->object)
                            );
                        }
                    }
                }
            }
            else {
                $_FILES['File'] = $_FILES['file'];
                $_FILES['File']['tmp_name'] = $uploadPath;
                $file_uid = md5(microtime().$uploadPath);

                $fileIt = getFactory()->getObject('cms_TempFile')->getRegistry()->Create(
                    array(
                        'Caption' => $file_uid,
                        'FileName' => $_FILES['file']['name'],
                        'MimeType' => $_FILES['file']['type']
                    )
                );

                return array(
                    'name' => $fileIt->getDisplayName(),
                    'id' => $fileIt->getId(),
                    'class' => get_class($fileIt->object)
                );
            }
        }
        else {
            \Logger::getLogger('System')->error("File wasn't uploaded");
        }

        return array();
    }

    function attachTemporaryFiles( $objectIt, $fileAttribute, $attachmentObject )
    {
        foreach( $_REQUEST as $parm => $value )
        {
            $matches = array();
            if ( preg_match('/file:([^:]+):(\d+)/', $parm, $matches) && $value == 'new' )
            {
                $className = $matches[1];
                $objectId = $matches[2];

                $fileIt = getFactory()->getObject($className)->getExact($objectId);
                if ( $fileIt->getId() != '' ) {
                    $_FILES[$fileAttribute]['tmp_name'] = $fileIt->getFilePath('File');
                    $_FILES[$fileAttribute]['name'] = $fileIt->get('FileName');
                    $_FILES[$fileAttribute]['type'] = $fileIt->get('MimeType');

                    $attachmentIt = $attachmentObject->getRegistry()->Create(
                        array(
                            'Caption' => $fileIt->getDisplayName(),
                            'ObjectClass' => get_class($objectIt->object),
                            'ObjectId' => $objectIt->getId(),
                            'WikiPage' => $objectIt->getId()
                        )
                    );
                    if ( $attachmentIt->getId() != '' ) {
                        $fileIt->object->delete($fileIt->getId());
                    }
                }
            }
        }

        // remove obsolete temporary files
        $file_registry = getFactory()->getObject('cms_TempFile')->getRegistry();
        $file_it = $file_registry->Query(array(new \ObjectRecordAgePersister()));

        while( !$file_it->end() ) {
            if ( $file_it->get('AgeDays') > 0 ) $file_it->delete();
            $file_it->moveNext();
        }
    }

    function deleteFiles() {
        foreach( $_REQUEST as $parm => $value )
        {
            $matches = array();
            if ( preg_match('/file:([^:]+):(\d+)/', $parm, $matches) && $value == 'delete' )
            {
                $className = $matches[1];
                $objectId = $matches[2];

                $fileIt = getFactory()->getObject($className)->getExact($objectId);
                if ( !getFactory()->getAccessPolicy()->can_delete($fileIt) ) continue;

                if ( $fileIt->getId() != '' ) {
                    $fileIt->object->delete($fileIt->getId());
                }
            }
        }
    }
}