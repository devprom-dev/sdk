<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class StateExFilterWebMethod extends FilterWebMethod
{
 	private $iterator = null;

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
 		
        if ( $default == '' ) {
            $this->setDefaultValue('N,I');
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

        $values['-'] = '';
		$values['N,I'] = translate('Не завершено');
        $values['Y'] = translate('Завершено');

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
	
	function getStyle()
	{
		return 'width:185px;';
	}
}