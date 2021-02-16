<?php
include_once "FlotChartWidget.php";

class FlotChartDigits extends FlotChartWidget
{
    private $unit = '';

    function getValues()
    {
        $rows = array();
        
		foreach ( $this->getData() as $key => $item ) {
			$rows[] = array(
                'label' => $key,
                'data' => $item['data']
            );
		}
		
		return $rows;
    }

    function setUnit( $value ) {
        $this->unit = $value;
    }
    
    public function draw( $chart_id )
    {
        $values = array_shift($this->getValues());
        echo '<div class="metric-value success">' . $values['data'] . $this->unit . '</div>';
    }
}