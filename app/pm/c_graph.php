<?

 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class GraphImage
 {
	var $y_num, $x_num, $grid_x_offset, $grid_y_offset,
		$graph_width, $graph_height,
		$im, $background_color, $xs, $ys,
		$lines, $x_names, $max_value;
 	
	function GraphImage( $x_names, $width = 300, $height = 100 ) 
	{
		$this->lines = array();
		$this->x_num = count($x_names);
		$this->x_names = $x_names;
		$this->graph_width = $width;
		$this->graph_height = $height;
		$this->y_num = $this->x_num > 0 
		    ? $this->graph_width > 0 
		        ? round($this->graph_height / ($this->graph_width / $this->x_num))
		        : 0
		    : 0;

		$this->grid_x_offset = 20;
		$this->grid_x_num = min($this->x_num, 15);
		$this->grid_y_num = $this->grid_x_num > 0
		    ? $this->graph_width > 0
		        ? round($this->graph_height / ($this->graph_width / $this->grid_x_num))
		        : 0
		    : 0;
 		
 		if ( strlen($x_names[0]) > 4 )
 		{
 			$this->grid_y_offset = 30;
 		}
 		else
 		{
 			$this->grid_y_offset = 20;
 		}

 		$this->im = @imagecreate ($this->graph_width, $this->graph_height) or die ("Cannot Initialize new GD image stream");
 		$this->background_color = $this->getBackgroundColor();
		
        $this->ys = array();
        for($i = 0; $i < $this->y_num; $i++) {
        	array_push($this->ys, $i * ($this->graph_height / $this->y_num ));
        }
        
        $this->grid_ys = array();
        for($i = 0; $i < $this->grid_y_num; $i++) {
        	array_push($this->grid_ys, $i * ($this->graph_height / $this->grid_y_num ));
        }

        $this->xs = array();
        for($i = 0; $i < $this->x_num; $i++ ) {
         	array_push($this->xs, $i * ($this->graph_width / $this->x_num));
        }
		
        $this->grid_xs = array();
        for($i = 0; $i < $this->grid_x_num; $i++ ) {
         	array_push($this->grid_xs, $i * ($this->graph_width / $this->grid_x_num));
        }

        $grid_color = ImageColorAllocate ($this->im, 235, 235, 235);
         
        for($i = 0; $i < count($this->grid_ys); $i++ ) {
         	imagerectangle($this->im, $this->grid_x_offset + 0, $this->grid_ys[$i] - $this->grid_y_offset, 
        		$this->graph_width, $this->grid_ys[$i] - $this->grid_y_offset, $grid_color);
        }
        for($i = 0; $i < count($this->grid_xs); $i++) {
         	imagerectangle($this->im, $this->grid_x_offset + $this->grid_xs[$i], 0, $this->grid_x_offset + $this->grid_xs[$i], 
        		$this->graph_height - $this->grid_y_offset, $grid_color);
        }

 		$border_color = ImageColorAllocate ($this->im, 192, 192, 192);
 		imagesetthickness($this->im, 1);
 		imagerectangle($this->im, $this->grid_x_offset + 0, 0, 
 			$this->graph_width - 1, $this->graph_height - $this->grid_y_offset, 
			$border_color);
	}
	
	function addGraphLine( $graph_line )
	{
		array_push($this->lines, $graph_line);
	}
	
	function getMaxValue()
	{
		return $this->max_value;
	}
	
	function getImage()
	{
		return $this->im;
	}
	
	function draw() 
	{
		$this->max_value = 0;
		
		for($i = 0; $i < count($this->lines); $i++) {
			$max_y_value = is_array($this->lines[$i]->y_values) ? max($this->lines[$i]->y_values) : 0;
			
			if(is_array($this->lines[$i]->y_values) && $max_y_value > $this->max_value) {
				$this->max_value = $max_y_value;
			}
		}
		for($i = 0; $i < count($this->lines); $i++) 
		{
			$this->lines[$i]->setStyle($this->im);

            for($j = 0; $j < count($this->lines[$i]->y_values) - 1; $j++) 
    		{
    			$first_x = $this->grid_x_offset + $this->xs[$this->lines[$i]->x_values[$j]];
    			
    			if ( $this->max_value == 0 )
    			{
    				$first_y = 0;
    			}
    			else
    			{
	    			$first_y = $this->graph_height - ($this->lines[$i]->y_values[$j] / $this->max_value) * 
							($this->graph_height - 10 - $this->grid_y_offset) - $this->grid_y_offset;
    			}
						
    			$second_x = $this->grid_x_offset + $this->xs[$this->lines[$i]->x_values[$j] + 1] - 1;
    			
    			if ( $this->max_value == 0 )
    			{
    				$second_y = 0;
    			}
    			else
    			{
	    			$second_y = $this->graph_height - ($this->lines[$i]->y_values[$j + 1] / $this->max_value) * 
							($this->graph_height - 10 - $this->grid_y_offset) - $this->grid_y_offset;
    			}
				
				if ( $second_y > $this->graph_height - $this->grid_y_offset )
				{
					$outstanding = $second_y - ($this->graph_height - $this->grid_y_offset);
					$second_x -= (($second_x - $first_x) / abs($first_y - $second_y)) * $outstanding;
					$second_y = $this->graph_height - $this->grid_y_offset;
				}
				
				if ( $first_y == 0 && $second_y == 0 )
				{
					continue;
				}
				
             	imageline(
             		$this->im, 
             		$first_x, 
    				$first_y, 
            		$second_x, 
    				$second_y, 
    				$this->lines[$i]->getColor($this->im));
            }
		}

 		$text_color = ImageColorAllocate ($this->im, 192, 192, 192);
		
		for($i = 0; $i < $this->max_value; $i += $this->getYStep()) {
			$y_pos = (($this->graph_height - 7 - $this->grid_y_offset) / ceil($this->max_value)) * $i + 10;

			imagettftext( $this->im, 7, 0, 0, $y_pos, $text_color, SERVER_ROOT_PATH.'/pm/fonts/arial.ttf', 
				str_pad(round($this->max_value, 0) - $i, 3, " ", STR_PAD_LEFT) );
		}
		
		for($i = 0; $i < $this->x_num; $i += $this->getXStep()) 
		{
			if ( strlen($this->x_names[$i]) > 4 )
			{
				imagettftext( $this->im, 7, 90,
					$this->grid_x_offset + $this->xs[$i] - 4, $this->graph_height - $this->grid_y_offset + 27,
					$text_color, SERVER_ROOT_PATH.'/pm/fonts/arial.ttf', $this->x_names[$i] );
			}
			else
			{
				imagettftext( $this->im, 7, 0,
					$this->grid_x_offset + $this->xs[$i] - 6, $this->graph_height - $this->grid_y_offset + 12,
					$text_color, SERVER_ROOT_PATH.'/pm/fonts/arial.ttf', $this->x_names[$i] );
			}
		}

		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
 		header("Content-type: image/png");

		imagepng($this->im);
	}
	
	function getXStep() {
		return $this->x_num > 10 ? round($this->x_num / 10) : 1;
	}

	function getYStep() {
		return $this->max_value > 5 ? round($this->max_value / 5) : 1;
	}
	
	function getBackgroundColor() {
		return imagecolorallocate ($this->im, 255, 255, 255);
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class GraphLine
 {
 	var $x_values, $y_values, $color, $thickness;
 	
 	function GraphLine($x_values, $y_values, $color, $thickness = 1) 
	{
		$this->x_values = $x_values;
		$this->y_values = $y_values;
		$this->color = $color;
		$this->thickness = $thickness;
	}
	
	function setStyle( &$im )
	{
   		imagesetthickness($im, $this->thickness);
	}
	
	function getColor( $im )
	{
		return imagecolorallocate($im, $this->color[0], $this->color[1], $this->color[2]);
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class GraphLineDashed extends GraphLine
 {
 	function GraphLineDashed($x_values, $y_values, $color, $thickness = 1) 
	{
		parent::GraphLine($x_values, $y_values, $color, $thickness);
	}

	function setStyle( &$im )
	{
   		parent::setStyle( $im );
   		
		$w = imagecolorallocate($im, 255, 255, 255);
		$u = imagecolorallocate($im, $this->color[0], $this->color[1], $this->color[2]);
		
		/* Draw a dashed line, 5 red pixels, 5 white pixels */
		$style = array($u, $u, $u, $u, $u, $w, $w, $w, $w, $w);
		imagesetstyle($im, $style);
	}

	function getColor( $im )
	{
		return IMG_COLOR_STYLED;
	}
 }

?>