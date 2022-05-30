<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class UpgradeModel extends Installable
{
    function check() {
        return true;
    }

    function install()
    {
        $this->makeCache(getFactory()->getObject('entity'));
        return true;
    }

    function makeCache( $entity )
    {
        $cache = fopen ( SERVER_ROOT_PATH.'cms/c_generated.php', 'w+' );

        $it = $entity->getAll();
        $entities = array();
        $entities_hash = array();
        $entities_attributes = array();
        $dictionaries = array();
        $i = 0;

        $model_reference = new ModelReferenceRegistry(CacheEngineVar::Instance());

        while ( !$it->end() )
        {
            if ( $it->get_native('ReferenceName') == 'Задача' ) {
                $it->moveNext();
                continue;
            }

            $attributes = array();
            $keys = array_keys($entity->getAttributes());

            array_push($attributes, "'entityId' => '".$it->getId()."' ");
            for ( $j = 0; $j < count($keys); $j++ ) {
                array_push($attributes, "'".$keys[$j]."' => '".$it->get_native($keys[$j])."' ");
                if ( $keys[$j] == 'ReferenceName' ) {
                    array_push($attributes, "'ReferenceNameLC' => '".strtolower($it->get_native($keys[$j]))."' ");
                }
            }

            array_push($entities, '	'.$i.' => array ('.join(', ', $attributes).') ');
            array_push($entities_hash, " '".$it->get('ReferenceName')."' => ".$i );

            $attributes = array();
            $attribute = new Attribute( $it );
            $attr_it = $attribute->getAll();
            $a = 0;

            while ( !$attr_it->end() )
            {
                $fields = array();
                $keys = array_keys($attribute->getAttributes());

                for ( $j = 0; $j < count($keys); $j++ ) {
                    array_push($fields,
                        "'".$keys[$j]."' => '".$attr_it->get_native($keys[$j])."'");
                }

                array_push( $attributes,
                    " ".$a." => Array(".join(',', $fields).") ");

                if ( strpos($attr_it->get('AttributeType'), 'REF_') !== false )
                {
                    $class = substr($attr_it->get('AttributeType'), 4, strlen($attr_it->get('AttributeType')) - 6);
                    $model_reference->addReference($it->get('ReferenceName'), $class, $attr_it->get('ReferenceName'));
                }

                $attr_it->moveNext();
                $a++;
            }

            array_push($entities_attributes,
                " '".$it->get('ReferenceName')."' => array (".join(', ', $attributes).") ");

            if ( false && $it->get('IsDictionary') == 'Y' )
            {
                $data = new Metaobject($it->get('ReferenceName'));
                $data_it = $data->getAll();
                $fields = array();

                while ( !$data_it->end() )
                {
                    array_push($fields,
                        $data_it->getId()." => '".$data_it->getDisplayName()."'");

                    $data_it->moveNext();
                }

                array_push($dictionaries,
                    " '".$it->get('ReferenceName')."' => array (".join(', ', $fields).") ");
            }

            $it->moveNext();
            $i++;
        }

        list( $forward_references, $backward_references ) = $model_reference->getReferences();

        $line = '<?php'.chr(10);

        $line .= '// PHPLOCKITOPT NOENCODE'.chr(10);
        $line .= '// PHPLOCKITOPT NOOBFUSCATE'.chr(10);

        $line .= ' $generated_entities = array ( '.chr(10);
        $line .= join(', '.chr(10), $entities);
        $line .= ');'.chr(10);

        $line .= ' $generated_hash = array ( '.chr(10);
        $line .= join(', '.chr(10), $entities_hash);
        $line .= ');'.chr(10);

        $line .= ' $generated_attributes = array ( '.chr(10);
        $line .= join(', '.chr(10), $entities_attributes);
        $line .= ');'.chr(10);

        $line .= ' $generated_dictionaries = array ( '.chr(10);
        $line .= join(', '.chr(10), $dictionaries);
        $line .= ');'.chr(10);

        $line .= ' function & _getEntities() { global $generated_entities; return $generated_entities; } '.chr(10);
        $line .= ' function & _getHash() { global $generated_hash; return $generated_hash; } '.chr(10);
        $line .= ' function & _getAttributes() { global $generated_attributes; return $generated_attributes; } '.chr(10);

        $line .= '?>'.chr(10);

        fwrite( $cache, $line );
        fclose( $cache );

        $references = "<?php ";
        $references .= ' global $forward_references, $backward_references; $forward_references = \''.serialize($forward_references).'\';'.chr(10);
        $references .= ' $backward_references = \''.serialize($backward_references).'\';'.chr(10);
        $references .= ' function _getForwardReferences() { global $forward_references; return unserialize($forward_references); } '.chr(10);
        $references .= ' function _getBackwardReferences() { global $backward_references; return unserialize($backward_references); } '.chr(10);

        file_put_contents(SERVER_ROOT_PATH.'cms/references.php', $references);
    }
}
