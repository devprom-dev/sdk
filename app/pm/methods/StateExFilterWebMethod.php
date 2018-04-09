<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class StateExFilterWebMethod extends FilterWebMethod
{
 	private $iterator = null;
 	private $non_terminal_it = null;
 	private $terminal_it = null;
 	
 	function __construct( $iterator = null, $parm = 'state', $default = '' )
 	{
 		parent::__construct();

        $this->iterator = $iterator;
 		$this->setValueParm($parm);
 		
 		$data = $this->iterator->getRowset();
 		foreach( $data as $key => $row ) {
 			$data[$key]['ReferenceName'] = str_replace(',','-',$data[$key]['ReferenceName']);  
 		}
 		$this->iterator = $this->iterator->object->createCachedIterator($data);
 		
 		$this->buildTerminals();
        if ( $default == '' ) {
            $this->setDefaultValue(join(',',$this->non_terminal_it->fieldToArray('ReferenceName')));
        }
        else {
            $this->setDefaultValue($default);
        }
        $this->setCaption(translate('Состояние'));
 	}
 	
 	function getValues()
 	{
  		$values = array ();
 		$values['all'] = translate('Все');

		while ( !$this->iterator->end() )
		{
			$values[$this->iterator->get('ReferenceName')] = $this->iterator->getDisplayName();
			$this->iterator->moveNext();
		}

		$state = join(',',$this->terminal_it->fieldToArray('ReferenceName'));
		if ( count($values) > 1 && !array_key_exists($state, $values) ) $values[$state] = translate('Завершено'); 
		
		$state = join(',',$this->non_terminal_it->fieldToArray('ReferenceName'));
		if ( count($values) > 1 && !array_key_exists($state, $values) ) $values[$state] = translate('Не завершено');

        if ( $this->iterator->object->getPage() != '?' ) {
            $values = array_merge(
                $values,
                array (
                    '_options' => array( 'uid' => 'options', 'href' => $this->iterator->object->getPage() )
                )
            );
        }

		return $values;
	}
	
	protected function buildTerminals()
	{
		$this->non_terminal_it = $this->iterator->object->createCachedIterator(
					array_values(array_filter($this->iterator->getRowset(), function($row) {
							return $row['IsTerminal'] != 'Y';
					}))
			);
			
		$this->terminal_it = $this->iterator->object->createCachedIterator(
					array_values(array_filter($this->iterator->getRowset(), function($row) {
							return $row['IsTerminal'] == 'Y';
					}))
			);
	}
	
	function getStyle()
	{
		return 'width:185px;';
	}
}