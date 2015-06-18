<?php

include "ResourceList.php";
 
class ResourceTable extends PMPageTable
{
	function getList()
	{
		$method = new ResourceFilterScaleWebMethod();
		$method->setFilter( $this->getFiltersName() );
		
		$scale = $method->getValue();
		if ( $scale == '' )
		{
			$scale = 'month'; 
		}
		
		return new ResourceList( $this->getObject(), $scale );
	}

	function getFilters()
	{
		$filters = array(
			new ResourceFilterFormatWebMethod(),
			new ResourceFilterViewWebMethod(),
			new ResourceFilterDividerWebMethod(),
			new ResourceFilterUserWebMethod(),
			new ResourceFilterScaleWebMethod(),
			new ResourceFilterYearWebMethod(),
			new ResourceFilterMonthWebMethod()
		);
		
		return $filters; 		
	}

	function getFiltersDefault()
	{
		return array('format', 'view', 'divider', 'scale', 'year', 'month');
	}
	
	function drawFooter()
	{
		if ( !$this->getListRef()->HasRows() )
		{
			return;
		}
		
		$values = $this->getFilterValues();
		switch ( $values['format'] )
		{
			case 'hours':
				break;
				
			default:
				echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
					echo ' - '.text('ee39');
				echo '</div>';
				echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
					echo '<div class="progress_bar_frame" style="width:12px;height:12px;">';
						echo '<div class="progress_bar" style="background:red;width:100%;height:12px;">&nbsp;</div>';
					echo '</div>';
				echo '</div>';
		
				echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
					echo ' - '.text('ee40');
				echo '</div>';
				echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
					echo '<div class="progress_bar_frame" style="width:12px;height:12px;">';
						echo '<div class="progress_bar" style="background:#EBE614;width:100%;height:12px;">&nbsp;</div>';
					echo '</div>';
				echo '</div>';
		
				echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
					echo ' - '.text('ee41');
				echo '</div>';
				echo '<div style="float:right;padding-left:6px;margin-top:6px;">';
					echo '<div class="progress_bar_frame" style="width:12px;height:12px;">';
						echo '<div class="progress_bar" style="width:100%;height:12px;">&nbsp;</div>';
					echo '</div>';
				echo '</div>';
		}
	}
	
	function getSortFields()
	{
		return array();
	}

	function getColumnFields()
	{
		return array();
	}
	
	function getNewActions()
	{
		return array();
	}

	function getActions()
	{
		return array();
	}

	function getDeleteActions()
	{
		return array();
	}
} 

