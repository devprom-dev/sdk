<?php
/* Taken from http://www.php.net/manual/en/function.xml-parse.php#52567
	Modified by Martin Guppy <http://www.deadpan110.com/>
Usage
 Grab some XML data, either from a file, URL, etc. however you want.
 Assume storage in $strYourXML; 

 $arrOutput = new xml2Array($strYourXML);
 print_r($arrOutput); //print it out, or do whatever!
*/
class xml2Array {
   
	var $arrOutput = array();
	var $resParser;
	var $strXmlData;

	function xmlParse($strInputXML) {
		$this->resParser = xml_parser_create ();

		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
       
		$this->strXmlData = xml_parse($this->resParser,$strInputXML,true);
		if(!$this->strXmlData) {
			trigger_error(sprintf("XML error: %s at line %d at column %d",
				xml_error_string(xml_get_error_code($this->resParser)),
				xml_get_current_line_number($this->resParser),
				xml_get_current_column_number($this->resParser)
			));
		}

		xml_parser_free($this->resParser);
		// Changed by Deadpan110
		//return $this->arrOutput;
		return $this->arrOutput[0];
	}

	function tagOpen($parser, $name, $attrs) {
		$tag=array("name"=>$name,"attrs"=>$attrs);
		array_push($this->arrOutput,$tag);
	}

	function tagData($parser, $tagData) {
		if(trim($tagData)) {
			if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
				$this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
			} else {
				$this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
			}
		}
		elseif ( $tagData == chr(10) )
		{
			if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
				$this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
			}
		}
	}

	function tagClosed($parser, $name) {
		$this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
		array_pop($this->arrOutput);
	}
}
?>
