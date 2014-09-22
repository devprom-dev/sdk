<?php
/*
***************************************************************************
*   Copyright (C) 2007-2008 by Sixdegrees                                 *
*   cesar@sixdegrees.com.br                                               *
*   "Working with freedom"                                                *
*   http://www.sixdegrees.com.br                                          *
*                                                                         *
*   Modified by Ethan Smith (ethan@3thirty.net), April 2008               *
*      - Added support for non-standard port numbers (rewrote cleanURL)   *
*      - getFileLogs will now include an array of files, if multiple      *
*        have been modified files are                                     *
*      - added setRepository method, to fix mis-spelling of old           *
*        setRespository method                                            *
*      - various bugfixes (out by one error on getFileLogs)               *
*                                                                         *
*   Modified by Ethan Smith (ethan@3thirty.net), June 23 2008             *
*      - Removed references to storeFileLogs as a member variable - it's  *
*        now a local variable within getFileLogs() called $fileLogs       * 
*      - getFile() now checks if you are requesting a directory, and      *
*         will return false if you are.                                   *
*      - Added a new parameter to run getDirectoryTree non- recursively   *
*                                                                         *
*   Modified by Per Soderlind (per@soderlind.no), August 13 2008          *
*      - Added support for LP2:BASELINE-RELATIVE-PATH in                  *
*        storeDirectoryFiles()                                            *
*      - In storeDirectoryFiles(), changed if{} elseif {} to switch {}    *
*        since it's faster :)                                             *
*                                                                         *
*   Modified by Dmitrii Shevchenko (dmitrii.shevchenko@gmail.com),        * 
*                                                 August 17 2008          *
*      - minor change to getDirectoryTree() function                      *
*      - added checkOut() function                                        *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/
define("PHPSVN_DIR",dirname(__FILE__) );

require(PHPSVN_DIR."/http.php");
require(PHPSVN_DIR."/xml_parser.php"); // to be dropped?
require(PHPSVN_DIR."/definitions.php");
//require_once(PHPSVN_DIR."/xml2Array.php");


/**
 *  PHP SVN CLIENT
 *
 *  This class is a SVN client. It can perform read operations
 *  to a SVN server (over Web-DAV). 
 *  It can get directory files, file contents, logs. All the operaration
 *  could be done for a specific version or for the last version.
 *
 *  @author Cesar D. Rodas <cesar@sixdegrees.com.br>
 *  @license BSD License
 */
class phpsvnclient {
	/**
	 *  SVN Repository URL
	 *
	 *  @var string
	 *  @access private
	 */
	var $_url;
	/**
	 *  Cache, for don't request the same thing in a
	 *  short period of time.
	 *
	 *  @var string
	 *  @access private
	 */
	var $_cache;
	/**
	 *  HTTP Client object
	 *
	 *  @var object
	 *  @access private
	 */
	var $_http;
	/**
	 *  Respository Version.
	 *
	 *  @access private
	 *  @var interger
	 */
	var $_repVersion;
	/**
	 *  Password
	 *
	 *  @access private
	 *  @var string
	 */
	var $pass;
	/**
	 *  Password
	 *
	 *  @access private
	 *  @var string
	 */
	var $user;
	/**
	 *  Last error number
	 *
	 *  Possible values are NOT_ERROR, NOT_FOUND, AUTH_REQUIRED, UNKOWN_ERROR
	 *
	 *  @access public
	 *  @var integer
	 */
	var $errNro;

	var $storeDirectoryFiles;
	var $lastDirectoryFiles;

	function phpsvnclient($url = 'http://phpsvnclient.googlecode.com/svn/', $user = false, $pass = false) {
		$this->construct($url, $user, $pass);
		if ( method_exists($this, '__destruct'))
		{
			register_shutdown_function(array(&$this, '__destruct'));
		}
	}

	function construct($url = 'http://phpsvnclient.googlecode.com/svn/', $user = false, $pass = false) {
		$http = & $this->_http;
		$http = new http_class;
		$http->user_agent = "phpsvnclient (http://phpsvnclient.googlecode.com/)";

		$this->storeDirectoryFiles = array();
		$this->_url = $url;
		$this->user = $user;
		$this->pass = $pass;
		//$this->setDebug();
	}
	
	function setDebug()
	{
		$this->_http->debug = 1;
		$this->_http->debug_response_body = 1;
		$this->_http->html_debug = 1;
	}
    
    function getLastError()
    {
    	return $this->_http->error;
    }
/**
 *  Public Functions
 */

	/**
	 *  checkOut
	 */
	function checkOut($folder = '/', $outPath = '.') {
		while($outPath[strlen($outPath) - 1] == '/' && strlen($outPath) > 1)
			$outPath = substr($outPath, 0, -1);
		
		$tree = $this->getDirectoryTree($folder);
		foreach($tree as $file) {
			$path = $file['path'];
			$tmp = strstr(trim($path, '/'), trim($folder, '/'));
			$createPath = $outPath.'/'.($tmp ? substr($tmp, strlen(trim($folder, '/'))) : "");
			if(trim($path, '/') == trim($folder, '/'))
				continue;
			if($file['type'] == 'directory' && !is_dir($createPath))
				mkdir($createPath);
			elseif($file['type'] == 'file') {
				$contents = $this->getFile($path);
				$hOut = fopen($createPath, 'w');
				fwrite($hOut, $contents);
				fclose($hOut);
			}
		}
	}

	/**
	 *  rawDirectoryDump
	 *
	 *  This method dumps SVN data for $folder
	 *  in the version $version of the repository.
	 *
	 *  @param string  $folder Folder to get data
	 *  @param integer $version Repository version, -1 means actual
	 *  @return array SVN data dump.
	 */
	function rawDirectoryDump($folder='/',$version=-1) {
		$actVersion = $this->getVersion();

		if ( $version == -1 ||  $version > $actVersion) {
			$version = $actVersion;
		}
		
		$url = $this->cleanURL($this->_url."/!svn/bc/".$version."/".
			$this->encodePath($folder)."/");
			
		$this->initQuery($args,"PROPFIND",$url);
		$args['Body'] = PHPSVN_NORMAL_REQUEST;
		$args['Headers']['Content-Length'] = strlen(PHPSVN_NORMAL_REQUEST);

		if ( ! $this->Request($args, $headers, $body) ) {
			return false;
		}
		
		$xml2Array = new xml2Array();
		return $xml2Array->xmlParse($body);
	}

	/**
	 *  getDirectoryFiles
	 *
	 *  This method returns all the files in $folder
	 *  in the version $version of the repository.
	 *
	 *  @param string  $folder Folder to get files
	 *  @param integer $version Repository version, -1 means actual
	 *  @return array List of files.	 */
	function getDirectoryFiles($folder='/', $version=-1) 
	{
		if ($arrOutput = $this->rawDirectoryDump($folder, $version)) 
		{
			$files = array();

			foreach ( $arrOutput['children'] as $key => $value ) 
			{
				$properites = $value['children'][1]['children'][0]['children'];
				//print_r($properites);
				$file = array();
				
				foreach ( $properites as $propIndex => $property )
				{
					$var = '';
					switch ( $property['name'] )
					{
						case 'LP1:GETLASTMODIFIED':
							$var = 'last-mod';
							break;
							
						case 'LP1:GETCONTENTTYPE':
							$var = 'content-type';
							break;
							
						case 'LP1:GETCONTENTLENGTH':
							$var = 'length';
							if ( $property['tagData'] == '' )
							{
								$property['tagData'] = 0;
							}
							break;
							
						case 'LP1:CREATOR-DISPLAYNAME':
							$var = 'creator';
							break;
							
						case 'LP2:BASELINE-RELATIVE-PATH':
						case 'LP3:BASELINE-RELATIVE-PATH':
							$var = 'path';
							break;
							
						case 'D:STATUS':
							break;
					}
					
					if ( $var != '' )
					{
						$file[$var] = $property['tagData'];
					}
				}
				
				array_push($files, $file);
			}
/*
			foreach($arrOutput['children'] as $key=>$value) 
			{
				$this->array_walk_recursive('', $value);
				array_push($files, $this->storeDirectoryFiles);
				unset($this->storeDirectoryFiles);
			}
			
	*/		
			for ( $i = 0; $i < count($files); $i++ )
			{
				if ( !isset($files[$i]['length']) )
				{
					$files[$i]['type'] = 'directory';
				}
				else
				{
					$files[$i]['type'] = 'file';
				}
			}

			return $files;
		}
		return false;
	}
	
	function array_walk_recursive ( $key, $value )
	{
		if ( is_array($value) )
		{
			$value_keys = array_keys($value);
			foreach( $value_keys as $value_key )
			{
				$this->array_walk_recursive($value_key, $value[$value_key]);
			}
		}
		else
		{
			$this->storeDirectoryFiles($value, $key);
		}
	}

	/**
	 *  getDirectoryTree
	 *
	 *  This method returns the complete tree of files and directories
	 *  in $folder from the version $version of the repository. Can also be used
	 *  to get the info for a single file or directory
	 *
	 *  @param string  $folder Folder to get tree
	 *  @param integer $version Repository version, -1 means actual
	 *  @param boolean $recursive Whether to get the tree recursively, or just
	 *  the specified directory/file.
	 *
	 *  @return array List of files and directories.
	 */
	function getDirectoryTree($folder='/',$version=-1, $recursive=true) {
		$directoryTree = array();

		if (!($arrOutput = $this->getDirectoryFiles($folder, $version)))
			return false;
			
		if (!$recursive)
			return $arrOutput[0];
		
		while(count($arrOutput) && is_array($arrOutput)) {
			$array = array_shift($arrOutput);
			
			array_push($directoryTree, $array);
			
			if(trim($array['path'], '/') == trim($folder, '/'))
				continue;
			
			if ($array['type'] == 'directory') {
				$walk = $this->getDirectoryFiles($array['path'], $version);
				array_shift($walk);
				//$walk = array_reverse($walk);

				foreach($walk as $step) {
					array_unshift($arrOutput, $step);
				}
			}
		}
		return $directoryTree;
	}

	/**
	 *  Returns file contents
	 *
	 *  @param	string 	$file File pathname
	 *  @param	integer	$version File Version
	 *  @return	string	File content and information, false on error, or if a
	 *  				directory is requested
	 */
	function getFile($file,$version=-1) {
		$actVersion = $this->getVersion();
		if ( $version == -1 ||  $version > $actVersion) {
			$version = $actVersion;
		}

		// check if this is a directory... if so, return false, otherwise we
		// get the HTML output of the directory listing from the SVN server. 
		// This is maybe a bit heavy since it makes another connection to the
		// SVN server. Maybe add this as an option/parameter? ES 23/06/08
		$fileInfo = $this->getDirectoryTree($file, $version, false);
		if ($fileInfo["type"] == "directory")
			return false;

		$url = $this->cleanURL($this->_url."/!svn/bc/".$version."/".
			$this->encodePath($file)."/");
			
		$this->initQuery($args,"GET",$url);
		if ( ! $this->Request($args, $headers, $body) )
			return false;

		return $body;
	}

	/**
	 *  Get changes logs of a file.
	 *
	 *  Get repository change logs between version
	 *  $vini and $vend.
	 *
	 *  @param integer $vini Initial Version
	 *  @param integer $vend End Version
	 *  @return Array Respository Logs
	 */
	function getRepositoryLogs($path = "/", $vini=0,$vend=-1) {
		return $this->getFileLogs($path,$vini,$vend);
	}

	/**
	 *  Get changes logs of a file.
	 *
	 *  Get repository change of a file between version
	 *  $vini and $vend.
	 *
	 *  @param
	 *  @param integer $vini Initial Version
	 *  @param integer $vend End Version
	 *  @return Array Respository Logs
	 */
	function getFileLogs($file, $vini=0,$vend=-1) {
		$fileLogs = array();

		$actVersion = $this->getVersion();
		if ( $vend == -1 || $vend > $actVersion)
			$vend = $actVersion;

		if ( $vini < 0) $vini=0;
		if ( $vini > $vend) $vini = $vend;

		$url = $this->cleanURL($this->_url."/!svn/bc/".$actVersion."/".
			$this->encodePath($file)."/");
			
		$this->initQuery($args,"REPORT",$url);
		$args['Body'] = sprintf(PHPSVN_LOGS_REQUEST,$vini,$vend);
		$args['Headers']['Content-Length'] = strlen($args['Body']);
		$args['Headers']['Depth']=1;

		if ( ! $this->Request($args, $headers, $body) )
			return false;

		$xml2Array = new xml2Array();
		$arrOutput = $xml2Array->xmlParse($body);
		//array_shift($arrOutput['children']);

		if ( !is_array($arrOutput['children']) )
		{
			return $fileLogs;
		}
	
		foreach($arrOutput['children'] as $value) {
			$array=array();
			foreach($value['children'] as $entry) {
				if ($entry['name'] == 'D:VERSION-NAME') $array['version'] = $entry['tagData'];
				if ($entry['name'] == 'D:CREATOR-DISPLAYNAME') $array['author'] = $entry['tagData'];
				if ($entry['name'] == 'S:DATE') $array['date'] = $entry['tagData'];
				if ($entry['name'] == 'D:COMMENT') $array['comment'] = $entry['tagData'];

				if (($entry['name'] == 'S:ADDED-PATH') ||
					($entry['name'] == 'S:MODIFIED-PATH') ||
					($entry['name'] == 'S:DELETED-PATH')) {
						// For backward compatability
						$array['files'][] = $entry['tagData'];

						if ($entry['name'] == 'S:ADDED-PATH') $array['add_files'][] = $entry['tagData'];
						if ($entry['name'] == 'S:MODIFIED-PATH') $array['mod_files'][] = $entry['tagData'];
						if ($entry['name'] == 'S:DELETED-PATH') $array['del_files'][] = $entry['tagData'];
				}
			}
			array_push($fileLogs,$array);
		}

		return $fileLogs;
	}


	/**
	 *  Get the repository version
	 *
	 *  @return integer Repository version
	 *  @access public
	 */
	function getVersion() {
		if ( $this->_repVersion > 0) return $this->_repVersion;

		$this->_repVersion = -1;		
		$this->initQuery($args,"PROPFIND",$this->cleanURL($this->_url."/!svn/vcc/default/") );
		
		$args['Body'] = PHPSVN_VERSION_REQUEST;
		$args['Headers']['Content-Length'] = strlen(PHPSVN_NORMAL_REQUEST);
		$args['Headers']['Depth']=0;

		if ( !$this->Request($args, $tmp, $body) )  {
			return $this->_repVersion;
		}

		$parser=new xml_parser_class;
		$parser->Parse( $body,true);
		$enable=false;

		foreach($parser->structure as $value) {
			if ( $enable && !is_array($value) ) {
				$t = explode("/",$value);

				// start from the end and move backwards until we find a non-blank entry
				$index = count($t) - 1;
				while ($t[$index] == "" && $index >= 0){
					$index--;
				}

				// check the last non-empty element to see if it's numeric. If so, it's the revision number
				if (is_numeric($t[$index])) {
					$this->_repVersion = $t[$index];
					break;
				}
			}
			if ( is_array($value) && $value['Tag'] == 'D:href') $enable = true;
		}

		return $this->_repVersion;
	}

/**
 *  Deprecated functions for backward comatability
 */

	/**
	 *  Set URL
	 *
	 *  Set the project repository URL.
	 *
	 *  @param string $url URL of the project.
	 *  @access public
	 */
	function setRepository($url) {
		$this->_url = $url;
	}
	/**
	 *  Old method; there's a typo in the name. This is now a wrapper for setRepository
	 */
	function setRespository($url) {
		return $this->setRepository($url);
	}
	/**
	 *  Add Authentication  settings
	 *
	 *  @param string $user Username
	 *  @param string $pass Password
	 */
	function setAuth($user,$pass) {
		$this->user = $user;
		$this->pass = $pass;
	}

/**
 *  Private Functions
 */
	/**
	 *  Callback for array_walk_recursive in public function getDirectoryFiles
	 *
	 *  @access private
	 */
	function storeDirectoryFiles($item, $key) {
		if ($key == 'name') 
		{
			if (	($item == 'D:HREF') || 
				($item == 'LP1:GETLASTMODIFIED') ||
				($item == 'LP2:BASELINE-RELATIVE-PATH') ||
				($item == 'LP3:BASELINE-RELATIVE-PATH') ||
				($item == 'D:STATUS') ||
				($item == 'LP1:CREATOR-DISPLAYNAME') ||
				($item == 'LP1:GETCONTENTLENGTH') || 
				($item == 'LP1:GETCONTENTTYPE') 
				) 
			{
				$this->lastDirectoryFiles = $item;
			}
		} 
		elseif (($key == 'tagData') && ($this->lastDirectoryFiles != '') || $this->lastDirectoryFiles == 'LP1:GETCONTENTLENGTH' ) 
		{
			// Unsure if the 1st of two D:HREF's always returns the result we want, but for now...
			if (($this->lastDirectoryFiles == 'D:HREF') && (isset($this->storeDirectoryFiles['type']))) return;

			// Dump into the array 
			switch ( $this->lastDirectoryFiles ) 
			{
				case 'D:HREF':
					$var = 'type';
					break;
				case 'LP1:GETLASTMODIFIED':
					$var = 'last-mod';
					break;
				case 'LP1:GETCONTENTTYPE':
					$var = 'content-type';
					break;
				case 'LP1:GETCONTENTLENGTH':
					$var = 'length';
					if ( $item == '' )
					{
						$item = 0;
					}
					break;
				case 'LP1:CREATOR-DISPLAYNAME':
					$var = 'creator';
					break;
				case 'LP2:BASELINE-RELATIVE-PATH':
				case 'LP3:BASELINE-RELATIVE-PATH':
					$var = 'path';
					break;
				case 'D:STATUS':
					$var = 'status';
					break;
			}
			$this->storeDirectoryFiles[$var] = $item;
			$this->lastDirectoryFiles = '';
/*
			// Detect 'type' as either a 'directory' or 'file'
			if (	(isset($this->storeDirectoryFiles['type'])) &&
				(isset($this->storeDirectoryFiles['last-mod'])) &&
				(isset($this->storeDirectoryFiles['path'])) &&
				(isset($this->storeDirectoryFiles['status'])) ) {

				$len = strlen($this->storeDirectoryFiles['path']);
				if ( substr($this->storeDirectoryFiles['type'],strlen($this->storeDirectoryFiles['type']) - $len) == $this->storeDirectoryFiles['path'] ) {
					$this->storeDirectoryFiles['type'] = 'file';
				} else {
					$this->storeDirectoryFiles['type'] = 'directory';
				}
			}
			*/

		} else {
			$this->lastDirectoryFiles = '';
		}
	}

	/**
	 *  Prepare HTTP CLIENT object
	 *
	 *  @param array &$arguments Byreferences variable.
	 *  @param string $method Method for the request (GET,POST,PROPFIND, REPORT,ETC).
	 *  @param string $url URL for the action.
	 *  @access private
	 */
	function initQuery(&$arguments,$method, $url) {
		$http = & $this->_http;
		$http->GetRequestArguments($url,$arguments);
		if ( isset($this->user) && isset($this->pass)) {
			$arguments["Headers"]["Authorization"] = " Basic ".base64_encode($this->user.":".$this->pass);
		}
		$arguments["RequestMethod"]=$method;
		$arguments["Headers"]["Content-Type"] = "text/xml";
		$arguments["Headers"]["Depth"] = 1;
	}

	/**
	 *  Open a connection, send request, read header
	 *  and body.
	 *
	 *  @param Array $args Connetion's argument
	 *  @param Array &$headers Array with the header response.
	 *  @param string &$body Body response.
	 *  @return boolean True is query success
	 *  @access private
	 */
	function Request($args, &$headers, &$body) {
		$http = & $this->_http;
		$http->Open($args);
		$http->SendRequest($args);
		$http->ReadReplyHeaders($headers);
		if ($http->response_status[0] != 2) {
			switch( $http->response_status ) {
				case 404:
					$this->errNro=NOT_FOUND;
					break;
				case 401:
					$this->errNro=AUTH_REQUIRED;
					break;
				default:
					$this->errNro=UNKNOWN_ERROR;
			}
			$http->close();
			return false;
		}
		$this->errNro = NO_ERROR;
		$body='';
		$tbody='';
		for(;;) {
			$error=$http->ReadReplyBody($tbody,1000);
			if($error!="" || strlen($tbody)==0) break;
			$body.=($tbody);
		}
		$http->close();
		return true;
	}

	/**
	 *  Clean URL
	 *
	 *  Delete "//" on URL requests.
	 *
	 *  @param string $url URL
	 *  @return string New cleaned URL.
	 *  @access private
	 */
	function cleanURL($url) 
	{
		return preg_replace("/((^:)\/\/)/", "//", $url);
	}
	
	function encodePath( $path )
	{
		$parts = preg_split('/\//', $path);
		
		for( $i = 0; $i < count($parts); $i++ )
		{
			$parts[$i] = urlencode($parts[$i]);
		}
		
		$path = join($parts, '/');

		return str_replace('+', '%20', $path);
	}
	
	function preferCurl()
	{
		$this->_http->preferCurl();
	}
}
?>
