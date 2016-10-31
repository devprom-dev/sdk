<?php

  include('common.php');
  include('design.php');
  include('c_model.php');
  include('c_package_view.php');
  include('c_businessfunction.php');

  getFactory()->enableVpd(false);
  
 $package = getFactory()->getObject('Package');
 $entity = getFactory()->getObject('Entity');
 $function = getFactory()->getObject('BusinessFunction');
 
 if ( $_REQUEST['mode'] == 'cache' )
 {
	// make a model cache
	makeCache($entity);
 }
  
  beginPage('Модель сайта');
?>
	<table width=100% height=100%>
		<tr>
			<td align=left style="border-bottom:.5pt solid silver;padding-bottom:10pt;" height=30>
				<table>
					<Tr>
						<td><a href="<? echo $package->getPageName(); ?>">Пакеты</a></td>
						<td width=20></td>
						<td><a href="<? echo $entity->getPageName(); ?>">Сущности</a></td>
						<td width=20></td>
						<td><a href="<? echo $function->getPageName(); ?>">Бизнес-функции</a></td>
						<td width=20></td>
						<td><a href="model.php?mode=cache">Закешировать</a></td>
					</Tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding-top:10pt;" valign=top>
				<table cellpadding=2 cellspacing=2>
				<?
					$packages_it = $package->getAll();
					for($i = 0; $i < $packages_it->count(); $i++) {
					?>
						<tr>
							<td>Пакет: <? echo $packages_it->getCaption(); ?></td>
						</tr>
						<tr>
							<td style="padding-left:15pt;">
								<table cellpadding=4 cellspacing=4>
							<?
								$entities_it = $entity->getByRef('packageId', $packages_it->getId());
								for($j = 0; $j < $entities_it->count(); $j++)
								{
								?>
									<tr>
										<td>
											<? echo $entities_it->getCaption(); ?> 
											(таблица: <? echo $entities_it->get('ReferenceName'); ?>)
											<? echo $entities_it->getEditLink(); ?>
										</td>
									</tr>	
									<tr>
										<td style="padding-left:20pt;">
											<table cellpadding=2 cellspacing=2>
											<?
												$attribute = getFactory()->getObject2('Attribute', $entities_it);
												$attr_it = $attribute->getAll();
												
												for($k = 0; $k < $attr_it->count(); $k++)
												{
												?>
													<tr>
														<td><? echo $attr_it->get("Caption"); ?>
															[ <? echo $attr_it->get('ReferenceName'); ?> ]
														</td>
													</tr>
												<?
													$attr_it->moveNext();
												}
											?>
											</table>
										</td>
									</tr>
								<?
									$entities_it->moveNext();
								}

								$fnc_it = $function->getByRef('packageId', $packages_it->getId());
								for($j = 0; $j < $fnc_it->count(); $j++)
								{
								?>
									<tr>
										<td>Бизнес-функция: <? echo $fnc_it->getCaption(); ?> 
											(класс: <? echo $fnc_it->get('ReferenceName'); ?>)</td>
									</tr>	
								<?
									$fnc_it->moveNext();
								}
							?>
								</table>
							</td>
						</tr>
					<?
						$packages_it->moveNext();
					}
				?>
				</table>
			</td>
		</tr>
		<tr>
			<td height=60>
				Пакеты предназначены для группировки сущностей модели сайта. Сущность обязательно должна быть размещена в 
				каком либо пакете.
			</td>
		</tr>
	</table>
<?

endPage();
	
function makeCache( $entity )
{
	$cache = fopen ( dirname(__FILE__).'/c_generated.php', 'w+' );
	
	$it = $entity->getAll();
	$entities = array();
	$entities_hash = array();
	$entities_attributes = array();
	$dictionaries = array();
	$i = 0;
	
	$model_reference = new ModelReferenceRegistry(new CacheEngine());
	
	while ( !$it->end() )
	{
	    if ( $it->get_native('ReferenceName') == 'Задача' )
	    {
	        $it->moveNext();
	        
	        continue;
	    }
	    
		$attributes = array();
		
		$keys = array_keys($entity->attributes);
		
		array_push($attributes, "'entityId' => '".$it->getId()."' ");
		
		for ( $j = 0; $j < count($keys); $j++ )
		{
			array_push($attributes, "'".$keys[$j]."' => '".$it->get_native($keys[$j])."' ");
			
			if ( $keys[$j] == 'ReferenceName' )
			{
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
			$keys = array_keys($attribute->attributes);
			
			for ( $j = 0; $j < count($keys); $j++ )
			{
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
	
	file_put_contents(dirname(__FILE__).'/references.php', $references);
	
	makeDomainModel( $entity );
	
	makeDatabaseModel( $entity );
}

function makeDomainModel( $entity )
{
    $all_entities = array();

    $it = $entity->getAll();
    
	while ( !$it->end() )
	{
	    $all_entities[$it->get('ReferenceName')] = $it->getDisplayName();
	    
	    $it->moveNext();
	}

    $package = getFactory()->getObject('Package');
	
    $package_it = $package->getAll();
    
	while ( !$package_it->end() )
    {
    	$it = $entity->getByRef('packageId', $package_it->getId());
    	
    	$plantuml = '';
    	
    	$entities = array();
    	
    	$references = array();
    	
    	while ( !$it->end() )
    	{
    	    $entities[$it->get('ReferenceName')] = $it->getDisplayName();
    	    
    	    $plantuml .= 'class "'.$it->getDisplayName().'" {'.PHP_EOL;
    	     
    		$attribute = new Attribute( $it );
    		
    	    $attr_it = $attribute->getAll();
    		
    		while ( !$attr_it->end() )
    		{
    		    $type = $attr_it->get('AttributeType');
    		    
        	    if ( strpos($type, 'REF_') !== false )
        	    {
        	        $type = substr($type, 4, strlen($type) - 6);
        	        
        	        $references[$it->get('ReferenceName')][] = array (
                        'ref_entity' => $type,
                        'ref_name' => $attr_it->getDisplayName(),
                        'ref_cardinality' => $attr_it->get('IsRequired') == 'Y' ? '1' : '0..1'
                    );
    
        	        $plantuml .= '  '.$attr_it->getDisplayName().': Object'.PHP_EOL;
        	    }
        	    else
        	    {
        	        $plantuml .= '  '.$attr_it->getDisplayName().': '.strtolower($type).PHP_EOL;
        	    }
        	    
        	    $attr_it->moveNext();
    		}
    
    		$plantuml .= '}'.PHP_EOL.PHP_EOL;
    		
    		$it->moveNext();
    	}
    	
    	foreach( $references as $entity_ref => $ref_entities )
    	{
    	    foreach( $ref_entities as $reference)
    	    {
    	        $plantuml .= '"'.$all_entities[$entity_ref].'" "*" -- "'.$reference['ref_cardinality'].'" "'.$all_entities[$reference['ref_entity']].'": '.$reference['ref_name'].PHP_EOL;
    	    }
    	}
    	
    	$cache = fopen ( dirname(__FILE__).'/domain.'.$package_it->getDisplayName().'.plantuml', 'w+' );
    		
    	fwrite( $cache, $plantuml );
    	
    	fclose( $cache );
    	
    	$package_it->moveNext();
    }
}

function makeDatabaseModel( $entity )
{
    $all_entities = array();

    $it = $entity->getAll();
    
	while ( !$it->end() )
	{
	    $all_entities[$it->get('ReferenceName')] = $it->get('ReferenceName');
	    
	    $it->moveNext();
	}

    $package = getFactory()->getObject('Package');
	
    $package_it = $package->getAll();
    
	while ( !$package_it->end() )
    {
    	$it = $entity->getByRef('packageId', $package_it->getId());
    	
    	$plantuml = '!define TABLE (T,#FFAAAA)'.PHP_EOL.PHP_EOL;
    	
    	$entities = array();
    	
    	$references = array();
    	
    	while ( !$it->end() )
    	{
    	    $entities[$it->get('ReferenceName')] = $it->getDisplayName();
    	    
    	    $plantuml .= 'class "'.$it->get('ReferenceName').'" << TABLE >> {'.PHP_EOL;
    	     
    		$attribute = new Attribute( $it );
    		
    	    $attr_it = $attribute->getAll();
    		
    		while ( !$attr_it->end() )
    		{
    		    $type = $attr_it->get('AttributeType');
    		    
        	    if ( strpos($type, 'REF_') !== false )
        	    {
        	        $type = substr($type, 4, strlen($type) - 6);
        	        
        	        $references[$it->get('ReferenceName')][] = array (
                        'ref_entity' => $type,
                        'ref_name' => $attr_it->get('ReferenceName'),
                        'ref_cardinality' => $attr_it->get('IsRequired') == 'Y' ? '1' : '0..1'
                    );
    
        	        $plantuml .= '  '.$attr_it->get('ReferenceName').': INTEGER'.PHP_EOL;
        	    }
        	    else
        	    {
        	        $plantuml .= '  '.$attr_it->get('ReferenceName').': '.$type.PHP_EOL;
        	    }
        	    
        	    $attr_it->moveNext();
    		}
    
    		$plantuml .= '}'.PHP_EOL.PHP_EOL;
    		
    		$it->moveNext();
    	}
    	
    	foreach( $references as $entity_ref => $ref_entities )
    	{
    	    foreach( $ref_entities as $reference)
    	    {
    	        $plantuml .= '"'.$all_entities[$entity_ref].'" << TABLE >> "*" -- "'.$reference['ref_cardinality'].'" "'.$all_entities[$reference['ref_entity']].'" << TABLE >> : '.$reference['ref_name'].PHP_EOL;
    	    }
    	}
    	
    	$cache = fopen ( dirname(__FILE__).'/datamodel.'.$package_it->getDisplayName().'.plantuml', 'w+' );
    		
    	fwrite( $cache, $plantuml );
    	
    	fclose( $cache );
    	
    	$package_it->moveNext();
    }
}
