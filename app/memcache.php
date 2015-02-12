<?php

$IIIIl1IlI1Il='$Id: memcache.php 326707 2012-07-19 19:02:42Z ab $';
define('ADMIN_USERNAME','memcache');
define('ADMIN_PASSWORD','password');
define('DATE_FORMAT','Y/m/d H:i:s');
define('GRAPH_SIZE',200);
define('MAX_ITEM_DUMP',50);
$IIIIl1IlI11l[] = 'localhost:11211';
if (!isset($_SERVER['PHP_AUTH_USER']) ||!isset($_SERVER['PHP_AUTH_PW']) ||
$_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
Header("WWW-Authenticate: Basic realm=\"Memcache Login\"");
Header("HTTP/1.0 401 Unauthorized");
echo "				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>";
exit;
}
function get_host_port_from_server($IIIIl1IllIII){
$IIIIIIlI1l1l = explode(':',$IIIIl1IllIII);
if (($IIIIIIlI1l1l[0] == 'unix') &&(!is_numeric( $IIIIIIlI1l1l[1]))) {
return array($IIIIl1IllIII,0);
}
else {
return $IIIIIIlI1l1l;
}
}
function sendMemcacheCommands($IIIIIII1l1ll){
global $IIIIl1IlI11l;
$IIIIIIIIIlI1 = array();
foreach($IIIIl1IlI11l as $IIIIl1IllIII){
$IIIIl1IllII1 = get_host_port_from_server($IIIIl1IllIII);
$IIIIIII111ll = $IIIIl1IllII1[0];
$IIIIIII111l1 = $IIIIl1IllII1[1];
$IIIIIIIIIlI1[$IIIIl1IllIII] = sendMemcacheCommand($IIIIIII111ll,$IIIIIII111l1,$IIIIIII1l1ll);
}
return $IIIIIIIIIlI1;
}
function sendMemcacheCommand($IIIIl1IllIII,$IIIIIII111l1,$IIIIIII1l1ll){
$IIIIlllI1I11 = @fsockopen($IIIIl1IllIII,$IIIIIII111l1);
if (!$IIIIlllI1I11){
die("Cant connect to:".$IIIIl1IllIII.':'.$IIIIIII111l1);
}
fwrite($IIIIlllI1I11,$IIIIIII1l1ll."\r\n");
$IIIIl1IllIll='';
while ((!feof($IIIIlllI1I11))) {
$IIIIl1IllIll .= fgets($IIIIlllI1I11,256);
if (strpos($IIIIl1IllIll,"END\r\n")!==false){
break;
}
if (strpos($IIIIl1IllIll,"DELETED\r\n")!==false ||strpos($IIIIl1IllIll,"NOT_FOUND\r\n")!==false){
break;
}
if (strpos($IIIIl1IllIll,"OK\r\n")!==false){
break;
}
}
fclose($IIIIlllI1I11);
return parseMemcacheResults($IIIIl1IllIll);
}
function parseMemcacheResults($IIIIIIII1111){
$IIIIIlll11l1 = array();
$IIIIII1l1Ill = explode("\r\n",$IIIIIIII1111);
$IIIIl1IllI1I = count($IIIIII1l1Ill);
for($IIIIIlIIIll1=0;$IIIIIlIIIll1<$IIIIl1IllI1I;$IIIIIlIIIll1++){
$IIIIIIIllllI = $IIIIII1l1Ill[$IIIIIlIIIll1];
$IIIIl1IllI1l = explode(' ',$IIIIIIIllllI,3);
if (count($IIIIl1IllI1l)==3){
$IIIIIlll11l1[$IIIIl1IllI1l[0]][$IIIIl1IllI1l[1]]=$IIIIl1IllI1l[2];
if ($IIIIl1IllI1l[0]=='VALUE'){
$IIIIIlll11l1[$IIIIl1IllI1l[0]][$IIIIl1IllI1l[1]] = array();
list ($IIIII1III1ll,$IIIIl1IllI11)=explode(' ',$IIIIl1IllI1l[2]);
$IIIIIlll11l1[$IIIIl1IllI1l[0]][$IIIIl1IllI1l[1]]['stat']=array('flag'=>$IIIII1III1ll,'size'=>$IIIIl1IllI11);
$IIIIIlll11l1[$IIIIl1IllI1l[0]][$IIIIl1IllI1l[1]]['value']=$IIIIII1l1Ill[++$IIIIIlIIIll1];
}
}elseif($IIIIIIIllllI=='DELETED'||$IIIIIIIllllI=='NOT_FOUND'||$IIIIIIIllllI=='OK'){
return $IIIIIIIllllI;
}
}
return $IIIIIlll11l1;
}
function dumpCacheSlab($IIIIl1IllIII,$IIIIl1IlllIl,$IIIIIl1lI1l1){
list($IIIIIII111ll,$IIIIIII111l1) = get_host_port_from_server($IIIIl1IllIII);
$IIIIl1IlllI1 = sendMemcacheCommand($IIIIIII111ll,$IIIIIII111l1,'stats cachedump '.$IIIIl1IlllIl.' '.$IIIIIl1lI1l1);
return $IIIIl1IlllI1;
}
function flushServer($IIIIl1IllIII){
list($IIIIIII111ll,$IIIIIII111l1) = get_host_port_from_server($IIIIl1IllIII);
$IIIIl1IlllI1 = sendMemcacheCommand($IIIIIII111ll,$IIIIIII111l1,'flush_all');
return $IIIIl1IlllI1;
}
function getCacheItems(){
$IIIIIIIl1II1 = sendMemcacheCommands('stats items');
$IIIIl1Illll1 = array();
$IIIIl1Illl1I = array();
foreach ($IIIIIIIl1II1 as $IIIIl1IllIII=>$IIIIl1Illl1l){
$IIIIl1Illll1[$IIIIl1IllIII] = array();
$IIIIl1Illl1I[$IIIIl1IllIII]=0;
if (!isset($IIIIl1Illl1l['STAT'])){
continue;
}
$IIIIl1Illl11 = $IIIIl1Illl1l['STAT'];
foreach($IIIIl1Illl11 as $IIIIl1Ill1II=>$IIIIIIIlIlll){
if (preg_match('/items\:(\d+?)\:(.+?)$/',$IIIIl1Ill1II,$IIIIIIl1l1II)){
$IIIIl1Illll1[$IIIIl1IllIII][$IIIIIIl1l1II[1]][$IIIIIIl1l1II[2]] = $IIIIIIIlIlll;
if ($IIIIIIl1l1II[2]=='number'){
$IIIIl1Illl1I[$IIIIl1IllIII] +=$IIIIIIIlIlll;
}
}
}
}
return array('items'=>$IIIIl1Illll1,'counts'=>$IIIIl1Illl1I);
}
function getMemcacheStats($IIIIl1Ill1I1=true){
$IIIIl1IlllI1 = sendMemcacheCommands('stats');
if ($IIIIl1Ill1I1){
$IIIIIlll11l1 = array();
foreach($IIIIl1IlllI1 as $IIIIl1IllIII=>$IIIIIll1Ill1){
foreach($IIIIIll1Ill1['STAT'] as $IIIIIIII1l1l=>$IIIIII1l111I){
if (!isset($IIIIIlll11l1[$IIIIIIII1l1l])){
$IIIIIlll11l1[$IIIIIIII1l1l]=null;
}
switch ($IIIIIIII1l1l){
case 'pid':
$IIIIIlll11l1['pid'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'uptime':
$IIIIIlll11l1['uptime'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'time':
$IIIIIlll11l1['time'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'version':
$IIIIIlll11l1['version'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'pointer_size':
$IIIIIlll11l1['pointer_size'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'rusage_user':
$IIIIIlll11l1['rusage_user'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'rusage_system':
$IIIIIlll11l1['rusage_system'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
case 'curr_items':
$IIIIIlll11l1['curr_items']+=$IIIIII1l111I;
break;
case 'total_items':
$IIIIIlll11l1['total_items']+=$IIIIII1l111I;
break;
case 'bytes':
$IIIIIlll11l1['bytes']+=$IIIIII1l111I;
break;
case 'curr_connections':
$IIIIIlll11l1['curr_connections']+=$IIIIII1l111I;
break;
case 'total_connections':
$IIIIIlll11l1['total_connections']+=$IIIIII1l111I;
break;
case 'connection_structures':
$IIIIIlll11l1['connection_structures']+=$IIIIII1l111I;
break;
case 'cmd_get':
$IIIIIlll11l1['cmd_get']+=$IIIIII1l111I;
break;
case 'cmd_set':
$IIIIIlll11l1['cmd_set']+=$IIIIII1l111I;
break;
case 'get_hits':
$IIIIIlll11l1['get_hits']+=$IIIIII1l111I;
break;
case 'get_misses':
$IIIIIlll11l1['get_misses']+=$IIIIII1l111I;
break;
case 'evictions':
$IIIIIlll11l1['evictions']+=$IIIIII1l111I;
break;
case 'bytes_read':
$IIIIIlll11l1['bytes_read']+=$IIIIII1l111I;
break;
case 'bytes_written':
$IIIIIlll11l1['bytes_written']+=$IIIIII1l111I;
break;
case 'limit_maxbytes':
$IIIIIlll11l1['limit_maxbytes']+=$IIIIII1l111I;
break;
case 'threads':
$IIIIIlll11l1['rusage_system'][$IIIIl1IllIII]=$IIIIII1l111I;
break;
}
}
}
return $IIIIIlll11l1;
}
return $IIIIl1IlllI1;
}
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0",false);
header("Pragma: no-cache");
function duration($ts) {
global $IIIIllIl1l1I;
$years = (int)((($IIIIllIl1l1I -$ts)/(7*86400))/52.177457);
$rem = (int)(($IIIIllIl1l1I-$ts)-($years * 52.177457 * 7 * 86400));
$IIIIl1IlIlII = (int)(($rem)/(7*86400));
$IIIIllIIl11l = (int)(($rem)/86400) -$IIIIl1IlIlII*7;
$IIII11I11llI = (int)(($rem)/3600) -$IIIIllIIl11l*24 -$IIIIl1IlIlII*7*24;
$mins = (int)(($rem)/60) -$IIII11I11llI*60 -$IIIIllIIl11l*24*60 -$IIIIl1IlIlII*7*24*60;
$IIIIIIII1111 = '';
if($years==1) $IIIIIIII1111 .= "$years year, ";
if($years>1) $IIIIIIII1111 .= "$years years, ";
if($IIIIl1IlIlII==1) $IIIIIIII1111 .= "$IIIIl1IlIlII week, ";
if($IIIIl1IlIlII>1) $IIIIIIII1111 .= "$IIIIl1IlIlII weeks, ";
if($IIIIllIIl11l==1) $IIIIIIII1111 .= "$IIIIllIIl11l day,";
if($IIIIllIIl11l>1) $IIIIIIII1111 .= "$IIIIllIIl11l days,";
if($IIII11I11llI == 1) $IIIIIIII1111 .= " $IIII11I11llI hour and";
if($IIII11I11llI>1) $IIIIIIII1111 .= " $IIII11I11llI hours and";
if($mins == 1) $IIIIIIII1111 .= " 1 minute";
else $IIIIIIII1111 .= " $mins minutes";
return $IIIIIIII1111;
}
function graphics_avail() {
return extension_loaded('gd');
}
function bsize($IIIIlllI1I11) {
foreach (array('','K','M','G') as $IIIIIlIIIll1 =>$IIIIIlIIIlI1) {
if ($IIIIlllI1I11 <1024) break;
$IIIIlllI1I11/=1024;
}
return sprintf("%5.1f %sBytes",$IIIIlllI1I11,$IIIIIlIIIlI1);
}
function menu_entry($ob,$IIIIII1lllI1) {
global $PHP_SELF;
if ($ob==$_GET['op']){
return "<li><a class=\"child_active\" href=\"$PHP_SELF&op=$ob\">$IIIIII1lllI1</a></li>";
}
return "<li><a class=\"active\" href=\"$PHP_SELF&op=$ob\">$IIIIII1lllI1</a></li>";
}
function getHeader(){
$IIIIl11111I1 = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head><title>MEMCACHE INFO</title>
<style type=\"text/css\"><!--
body { background:white; font-size:100.01%; margin:0; padding:0; }
body,p,td,th,input,submit { font-size:0.8em;font-family:arial,helvetica,sans-serif; }
* html body   {font-size:0.8em}
* html p      {font-size:0.8em}
* html td     {font-size:0.8em}
* html th     {font-size:0.8em}
* html input  {font-size:0.8em}
* html submit {font-size:0.8em}
td { vertical-align:top }
a { color:black; font-weight:none; text-decoration:none; }
a:hover { text-decoration:underline; }
div.content { padding:1em 1em 1em 1em; position:absolute; width:97%; z-index:100; }

h1.memcache { background:rgb(153,153,204); margin:0; padding:0.5em 1em 0.5em 1em; }
* html h1.memcache { margin-bottom:-7px; }
h1.memcache a:hover { text-decoration:none; color:rgb(90,90,90); }
h1.memcache span.logo {
	background:rgb(119,123,180);
	color:black;
	border-right: solid black 1px;
	border-bottom: solid black 1px;
	font-style:italic;
	font-size:1em;
	padding-left:1.2em;
	padding-right:1.2em;
	text-align:right;
	display:block;
	width:130px;
	}
h1.memcache span.logo span.name { color:white; font-size:0.7em; padding:0 0.8em 0 2em; }
h1.memcache span.nameinfo { color:white; display:inline; font-size:0.4em; margin-left: 3em; }
h1.memcache div.copy { color:black; font-size:0.4em; position:absolute; right:1em; }
hr.memcache {
	background:white;
	border-bottom:solid rgb(102,102,153) 1px;
	border-style:none;
	border-top:solid rgb(102,102,153) 10px;
	height:12px;
	margin:0;
	margin-top:1px;
	padding:0;
}

ol,menu { margin:1em 0 0 0; padding:0.2em; margin-left:1em;}
ol.menu li { display:inline; margin-right:0.7em; list-style:none; font-size:85%}
ol.menu a {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	margin-left: 5px;
	}
ol.menu a.child_active {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	border-left: solid black 5px;
	margin-left: 0px;
	}
ol.menu span.active {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:black;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	border-left: solid black 5px;
	}
ol.menu span.inactive {
	background:rgb(193,193,244);
	border:solid rgb(182,182,233) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	margin-left: 5px;
	}
ol.menu a:hover {
	background:rgb(193,193,244);
	text-decoration:none;
	}


div.info {
	background:rgb(204,204,204);
	border:solid rgb(204,204,204) 1px;
	margin-bottom:1em;
	}
div.info h2 {
	background:rgb(204,204,204);
	color:black;
	font-size:1em;
	margin:0;
	padding:0.1em 1em 0.1em 1em;
	}
div.info table {
	border:solid rgb(204,204,204) 1px;
	border-spacing:0;
	width:100%;
	}
div.info table th {
	background:rgb(204,204,204);
	color:white;
	margin:0;
	padding:0.1em 1em 0.1em 1em;
	}
div.info table th a.sortable { color:black; }
div.info table tr.tr-0 { background:rgb(238,238,238); }
div.info table tr.tr-1 { background:rgb(221,221,221); }
div.info table td { padding:0.3em 1em 0.3em 1em; }
div.info table td.td-0 { border-right:solid rgb(102,102,153) 1px; white-space:nowrap; }
div.info table td.td-n { border-right:solid rgb(102,102,153) 1px; }
div.info table td h3 {
	color:black;
	font-size:1.1em;
	margin-left:-0.3em;
	}
.td-0 a , .td-n a, .tr-0 a , tr-1 a {
    text-decoration:underline;
}
div.graph { margin-bottom:1em }
div.graph h2 { background:rgb(204,204,204);; color:black; font-size:1em; margin:0; padding:0.1em 1em 0.1em 1em; }
div.graph table { border:solid rgb(204,204,204) 1px; color:black; font-weight:normal; width:100%; }
div.graph table td.td-0 { background:rgb(238,238,238); }
div.graph table td.td-1 { background:rgb(221,221,221); }
div.graph table td { padding:0.2em 1em 0.4em 1em; }

div.div1,div.div2 { margin-bottom:1em; width:35em; }
div.div3 { position:absolute; left:40em; top:1em; width:580px; }
//div.div3 { position:absolute; left:37em; top:1em; right:1em; }

div.sorting { margin:1.5em 0em 1.5em 2em }
.center { text-align:center }
.aright { position:absolute;right:1em }
.right { text-align:right }
.ok { color:rgb(0,200,0); font-weight:bold}
.failed { color:rgb(200,0,0); font-weight:bold}

span.box {
	border: black solid 1px;
	border-right:solid black 2px;
	border-bottom:solid black 2px;
	padding:0 0.5em 0 0.5em;
	margin-right:1em;
}
span.green { background:#60F060; padding:0 0.5em 0 0.5em}
span.red { background:#D06030; padding:0 0.5em 0 0.5em }

div.authneeded {
	background:rgb(238,238,238);
	border:solid rgb(204,204,204) 1px;
	color:rgb(200,0,0);
	font-size:1.2em;
	font-weight:bold;
	padding:2em;
	text-align:center;
	}

input {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:1em;
	padding:0.1em 0.5em 0.1em 0.5em;
	}
//-->
</style>
</head>
<body>
<div class=\"head\">
	<h1 class=\"memcache\">
		<span class=\"logo\"><a href=\"http://pecl.php.net/package/memcache\">memcache</a></span>
		<span class=\"nameinfo\">memcache.php by <a href=\"http://livebookmark.net\">Harun Yayli</a></span>
	</h1>
	<hr class=\"memcache\">
</div>
<div class=content>";
return $IIIIl11111I1;
}
function getFooter(){
global $IIIIl1IlI1Il;
$footer = '</div><!-- Based on apc.php '.$IIIIl1IlI1Il.'--></body>
</html>
';
return $footer;
}
function getMenu(){
global $PHP_SELF;
echo "<ol class=menu>";
if ($_GET['op']!=4){
echo "    <li><a href=\"$PHP_SELF&op={$_GET['op']}\">Refresh Data</a></li>";
}
else {
echo "    <li><a href=\"$PHP_SELF&op=2}\">Back</a></li>";
}
echo
menu_entry(1,'View Host Stats'),
menu_entry(2,'Variables');
echo "	</ol>
	<br/>";
}
$_GET['op'] = !isset($_GET['op'])?'1':$_GET['op'];
$PHP_SELF= isset($_SERVER['PHP_SELF']) ?htmlentities(strip_tags($_SERVER['PHP_SELF'],'')) : '';
$PHP_SELF=$PHP_SELF.'?';
$IIIIllIl1l1I = time();
foreach($_GET as $IIIIIIII1l1l=>$IIIIllI1lIlI){
$_GET[$IIIIIIII1l1l]=htmlentities($IIIIllI1lIlI);
}
if (isset($_GET['singleout']) &&$_GET['singleout']>=0 &&$_GET['singleout'] <count($IIIIl1IlI11l)){
$IIIIl1IlI11l = array($IIIIl1IlI11l[$_GET['singleout']]);
}
if (isset($_GET['IMG'])){
$memcacheStats = getMemcacheStats();
$memcacheStatsSingle = getMemcacheStats(false);
if (!graphics_avail()) {
exit(0);
}
function fill_box($IIIIllI1IIII,$IIIIl1ll11Il,$IIIIl1ll11I1,$IIII1l1l1I1l,$IIIIIlIIlll1,$IIII11lIIII1,$IIII11lIIIlI,$IIIIIIIlllI1='',$placeindex='') {
global $col_black;
$x1=$IIIIl1ll11Il+$IIII1l1l1I1l-1;
$y1=$IIIIl1ll11I1+$IIIIIlIIlll1-1;
imagerectangle($IIIIllI1IIII,$IIIIl1ll11Il,$y1,$x1+1,$IIIIl1ll11I1+1,$col_black);
if($y1>$IIIIl1ll11I1) imagefilledrectangle($IIIIllI1IIII,$IIIIl1ll11Il,$IIIIl1ll11I1,$x1,$y1,$IIII11lIIIlI);
else imagefilledrectangle($IIIIllI1IIII,$IIIIl1ll11Il,$y1,$x1,$IIIIl1ll11I1,$IIII11lIIIlI);
imagerectangle($IIIIllI1IIII,$IIIIl1ll11Il,$y1,$x1,$IIIIl1ll11I1,$IIII11lIIII1);
if ($IIIIIIIlllI1) {
if ($placeindex>0) {
if ($placeindex<16)
{
$px=5;
$py=$placeindex*12+6;
imagefilledrectangle($IIIIllI1IIII,$px+90,$py+3,$px+90-4,$py-3,$IIII11lIIIlI);
imageline($IIIIllI1IIII,$IIIIl1ll11Il,$IIIIl1ll11I1+$IIIIIlIIlll1/2,$px+90,$py,$IIII11lIIIlI);
imagestring($IIIIllI1IIII,2,$px,$py-6,$IIIIIIIlllI1,$IIII11lIIII1);
}else {
if ($placeindex<31) {
$px=$IIIIl1ll11Il+40*2;
$py=($placeindex-15)*12+6;
}else {
$px=$IIIIl1ll11Il+40*2+100*intval(($placeindex-15)/15);
$py=($placeindex%15)*12+6;
}
imagefilledrectangle($IIIIllI1IIII,$px,$py+3,$px-4,$py-3,$IIII11lIIIlI);
imageline($IIIIllI1IIII,$IIIIl1ll11Il+$IIII1l1l1I1l,$IIIIl1ll11I1+$IIIIIlIIlll1/2,$px,$py,$IIII11lIIIlI);
imagestring($IIIIllI1IIII,2,$px+2,$py-6,$IIIIIIIlllI1,$IIII11lIIII1);
}
}else {
imagestring($IIIIllI1IIII,4,$IIIIl1ll11Il+5,$y1-16,$IIIIIIIlllI1,$IIII11lIIII1);
}
}
}
function fill_arc($IIIIllI1IIII,$centerX,$centerY,$diameter,$IIIIIlIIll1l,$IIIIl1III1ll,$IIII11lIIII1,$IIII11lIIIlI,$IIIIIIIlllI1='',$placeindex=0) {
$IIIIIll1Ill1=$diameter/2;
$IIII1l1l1I1l=deg2rad((360+$IIIIIlIIll1l+($IIIIl1III1ll-$IIIIIlIIll1l)/2)%360);
if (function_exists("imagefilledarc")) {
imagefilledarc($IIIIllI1IIII,$centerX+1,$centerY+1,$diameter,$diameter,$IIIIIlIIll1l,$IIIIl1III1ll,$IIII11lIIII1,IMG_ARC_PIE);
imagefilledarc($IIIIllI1IIII,$centerX,$centerY,$diameter,$diameter,$IIIIIlIIll1l,$IIIIl1III1ll,$IIII11lIIIlI,IMG_ARC_PIE);
imagefilledarc($IIIIllI1IIII,$centerX,$centerY,$diameter,$diameter,$IIIIIlIIll1l,$IIIIl1III1ll,$IIII11lIIII1,IMG_ARC_NOFILL|IMG_ARC_EDGED);
}else {
imagearc($IIIIllI1IIII,$centerX,$centerY,$diameter,$diameter,$IIIIIlIIll1l,$IIIIl1III1ll,$IIII11lIIIlI);
imageline($IIIIllI1IIII,$centerX,$centerY,$centerX +cos(deg2rad($IIIIIlIIll1l)) * $IIIIIll1Ill1,$centerY +sin(deg2rad($IIIIIlIIll1l)) * $IIIIIll1Ill1,$IIII11lIIIlI);
imageline($IIIIllI1IIII,$centerX,$centerY,$centerX +cos(deg2rad($IIIIIlIIll1l+1)) * $IIIIIll1Ill1,$centerY +sin(deg2rad($IIIIIlIIll1l)) * $IIIIIll1Ill1,$IIII11lIIIlI);
imageline($IIIIllI1IIII,$centerX,$centerY,$centerX +cos(deg2rad($IIIIl1III1ll-1))   * $IIIIIll1Ill1,$centerY +sin(deg2rad($IIIIl1III1ll))   * $IIIIIll1Ill1,$IIII11lIIIlI);
imageline($IIIIllI1IIII,$centerX,$centerY,$centerX +cos(deg2rad($IIIIl1III1ll))   * $IIIIIll1Ill1,$centerY +sin(deg2rad($IIIIl1III1ll))   * $IIIIIll1Ill1,$IIII11lIIIlI);
imagefill($IIIIllI1IIII,$centerX +$IIIIIll1Ill1*cos($IIII1l1l1I1l)/2,$centerY +$IIIIIll1Ill1*sin($IIII1l1l1I1l)/2,$IIII11lIIIlI);
}
if ($IIIIIIIlllI1) {
if ($placeindex>0) {
imageline($IIIIllI1IIII,$centerX +$IIIIIll1Ill1*cos($IIII1l1l1I1l)/2,$centerY +$IIIIIll1Ill1*sin($IIII1l1l1I1l)/2,$diameter,$placeindex*12,$IIII11lIIII1);
imagestring($IIIIllI1IIII,4,$diameter,$placeindex*12,$IIIIIIIlllI1,$IIII11lIIII1);
}else {
imagestring($IIIIllI1IIII,4,$centerX +$IIIIIll1Ill1*cos($IIII1l1l1I1l)/2,$centerY +$IIIIIll1Ill1*sin($IIII1l1l1I1l)/2,$IIIIIIIlllI1,$IIII11lIIII1);
}
}
}
$IIIIl1IllI11 = GRAPH_SIZE;
$IIII1Il1llIl = imagecreate($IIIIl1IllI11+50,$IIIIl1IllI11+10);
$col_white = imagecolorallocate($IIII1Il1llIl,0xFF,0xFF,0xFF);
$col_red   = imagecolorallocate($IIII1Il1llIl,0xD0,0x60,0x30);
$col_green = imagecolorallocate($IIII1Il1llIl,0x60,0xF0,0x60);
$col_black = imagecolorallocate($IIII1Il1llIl,0,0,0);
imagecolortransparent($IIII1Il1llIl,$col_white);
switch ($_GET['IMG']){
case 1: 
$tsize=$memcacheStats['limit_maxbytes'];
$avail=$tsize-$memcacheStats['bytes'];
$IIIIl1ll11Il=$IIIIl1ll11I1=$IIIIl1IllI11/2;
$angle_from = 0;
$fuzz = 0.000001;
foreach($memcacheStatsSingle as $serv=>$mcs) {
$free = $mcs['STAT']['limit_maxbytes']-$mcs['STAT']['bytes'];
$used = $mcs['STAT']['bytes'];
if ($free>0){
$angle_to = ($free*360)/$tsize;
$perc =sprintf("%.2f%%",($free *100) / $tsize) ;
fill_arc($IIII1Il1llIl,$IIIIl1ll11Il,$IIIIl1ll11I1,$IIIIl1IllI11,$angle_from,$angle_from +$angle_to ,$col_black,$col_green,$perc);
$angle_from = $angle_from +$angle_to ;
}
if ($used>0){
$angle_to = ($used*360)/$tsize;
$perc =sprintf("%.2f%%",($used *100) / $tsize) ;
fill_arc($IIII1Il1llIl,$IIIIl1ll11Il,$IIIIl1ll11I1,$IIIIl1IllI11,$angle_from,$angle_from +$angle_to ,$col_black,$col_red,'('.$perc.')');
$angle_from = $angle_from+$angle_to ;
}
}
break;
case 2: 
$hits = ($memcacheStats['get_hits']==0) ?1:$memcacheStats['get_hits'];
$misses = ($memcacheStats['get_misses']==0) ?1:$memcacheStats['get_misses'];
$IIIIl1Ill1I1 = $hits +$misses ;
fill_box($IIII1Il1llIl,30,$IIIIl1IllI11,50,-$hits*($IIIIl1IllI11-21)/$IIIIl1Ill1I1,$col_black,$col_green,sprintf("%.1f%%",$hits*100/$IIIIl1Ill1I1));
fill_box($IIII1Il1llIl,130,$IIIIl1IllI11,50,-max(4,($IIIIl1Ill1I1-$hits)*($IIIIl1IllI11-21)/$IIIIl1Ill1I1),$col_black,$col_red,sprintf("%.1f%%",$misses*100/$IIIIl1Ill1I1));
break;
}
header("Content-type: image/png");
imagepng($IIII1Il1llIl);
exit;
}
echo getHeader();
echo getMenu();
switch ($_GET['op']) {
case 1: 
$phpversion = phpversion();
$memcacheStats = getMemcacheStats();
$memcacheStatsSingle = getMemcacheStats(false);
$mem_size = $memcacheStats['limit_maxbytes'];
$mem_used = $memcacheStats['bytes'];
$mem_avail= $mem_size-$mem_used;
$IIIIllI11l11 = time()-array_sum($memcacheStats['uptime']);
$curr_items = $memcacheStats['curr_items'];
$total_items = $memcacheStats['total_items'];
$hits = ($memcacheStats['get_hits']==0) ?1:$memcacheStats['get_hits'];
$misses = ($memcacheStats['get_misses']==0) ?1:$memcacheStats['get_misses'];
$sets = $memcacheStats['cmd_set'];
$req_rate = sprintf("%.2f",($hits+$misses)/($IIIIllIl1l1I-$IIIIllI11l11));
$hit_rate = sprintf("%.2f",($hits)/($IIIIllIl1l1I-$IIIIllI11l11));
$miss_rate = sprintf("%.2f",($misses)/($IIIIllIl1l1I-$IIIIllI11l11));
$set_rate = sprintf("%.2f",($sets)/($IIIIllIl1l1I-$IIIIllI11l11));
echo "		<div class=\"info div1\"><h2>General Cache Information</h2>
		<table cellspacing=0><tbody>
		<tr class=tr-1><td class=td-0>PHP Version</td><td>$phpversion</td></tr>";
echo "<tr class=tr-0><td class=td-0>Memcached Host".((count($IIIIl1IlI11l)>1) ?'s':'')."</td><td>";
$IIIIIlIIIll1=0;
if (!isset($_GET['singleout']) &&count($IIIIl1IlI11l)>1){
foreach($IIIIl1IlI11l as $IIIIl1IllIII){
echo ($IIIIIlIIIll1+1).'. <a href="'.$PHP_SELF.'&singleout='.$IIIIIlIIIll1++.'">'.$IIIIl1IllIII.'</a><br/>';
}
}
else{
echo '1.'.$IIIIl1IlI11l[0];
}
if (isset($_GET['singleout'])){
echo '<a href="'.$PHP_SELF.'">(all servers)</a><br/>';
}
echo "</td></tr>\n";
echo "<tr class=tr-1><td class=td-0>Total Memcache Cache</td><td>".bsize($memcacheStats['limit_maxbytes'])."</td></tr>\n";
echo "		</tbody></table>
		</div>

		<div class=\"info div1\"><h2>Memcache Server Information</h2>";
foreach($IIIIl1IlI11l as $IIIIl1IllIII){
echo '<table cellspacing=0><tbody>';
echo '<tr class=tr-1><td class=td-1>'.$IIIIl1IllIII.'</td><td><a href="'.$PHP_SELF.'&server='.array_search($IIIIl1IllIII,$IIIIl1IlI11l).'&op=6">[<b>Flush this server</b>]</a></td></tr>';
echo '<tr class=tr-0><td class=td-0>Start Time</td><td>',date(DATE_FORMAT,$memcacheStatsSingle[$IIIIl1IllIII]['STAT']['time']-$memcacheStatsSingle[$IIIIl1IllIII]['STAT']['uptime']),'</td></tr>';
echo '<tr class=tr-1><td class=td-0>Uptime</td><td>',duration($memcacheStatsSingle[$IIIIl1IllIII]['STAT']['time']-$memcacheStatsSingle[$IIIIl1IllIII]['STAT']['uptime']),'</td></tr>';
echo '<tr class=tr-0><td class=td-0>Memcached Server Version</td><td>'.$memcacheStatsSingle[$IIIIl1IllIII]['STAT']['version'].'</td></tr>';
echo '<tr class=tr-1><td class=td-0>Used Cache Size</td><td>',bsize($memcacheStatsSingle[$IIIIl1IllIII]['STAT']['bytes']),'</td></tr>';
echo '<tr class=tr-0><td class=td-0>Total Cache Size</td><td>',bsize($memcacheStatsSingle[$IIIIl1IllIII]['STAT']['limit_maxbytes']),'</td></tr>';
echo '</tbody></table>';
}
echo "
		</div>
		<div class=\"graph div3\"><h2>Host Status Diagrams</h2>
		<table cellspacing=0><tbody>";
$IIIIl1IllI11='width='.(GRAPH_SIZE+50).' height='.(GRAPH_SIZE+10);
echo "		<tr>
		<td class=td-0>Cache Usage</td>
		<td class=td-1>Hits &amp; Misses</td>
		</tr>";
echo
graphics_avail() ?
'<tr>'.
"<td class=td-0><img alt=\"\" $IIIIl1IllI11 src=\"$PHP_SELF&IMG=1&".(isset($_GET['singleout'])?'singleout='.$_GET['singleout'].'&':'')."$IIIIllIl1l1I\"></td>".
"<td class=td-1><img alt=\"\" $IIIIl1IllI11 src=\"$PHP_SELF&IMG=2&".(isset($_GET['singleout'])?'singleout='.$_GET['singleout'].'&':'')."$IIIIllIl1l1I\"></td></tr>\n"
: "",
'<tr>',
'<td class=td-0><span class="green box">&nbsp;</span>Free: ',bsize($mem_avail).sprintf(" (%.1f%%)",$mem_avail*100/$mem_size),"</td>\n",
'<td class=td-1><span class="green box">&nbsp;</span>Hits: ',$hits.sprintf(" (%.1f%%)",$hits*100/($hits+$misses)),"</td>\n",
'</tr>',
'<tr>',
'<td class=td-0><span class="red box">&nbsp;</span>Used: ',bsize($mem_used ).sprintf(" (%.1f%%)",$mem_used *100/$mem_size),"</td>\n",
'<td class=td-1><span class="red box">&nbsp;</span>Misses: ',$misses.sprintf(" (%.1f%%)",$misses*100/($hits+$misses)),"</td>\n";
echo "	</tr>
	</tbody></table>
<br/>
	<div class=\"info\"><h2>Cache Information</h2>
		<table cellspacing=0><tbody>
		<tr class=tr-0><td class=td-0>Current Items(total)</td><td>$curr_items ($total_items)</td></tr>
		<tr class=tr-1><td class=td-0>Hits</td><td>{$hits}</td></tr>
		<tr class=tr-0><td class=td-0>Misses</td><td>{$misses}</td></tr>
		<tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td>$req_rate cache requests/second</td></tr>
		<tr class=tr-0><td class=td-0>Hit Rate</td><td>$hit_rate cache requests/second</td></tr>
		<tr class=tr-1><td class=td-0>Miss Rate</td><td>$miss_rate cache requests/second</td></tr>
		<tr class=tr-0><td class=td-0>Set Rate</td><td>$set_rate cache requests/second</td></tr>
		</tbody></table>
		</div>
";
break;
case 2: 
$IIIIIlIIll1I=0;
$cacheItems= getCacheItems();
$IIIIIIIl1II1 = $cacheItems['items'];
$totals = $cacheItems['counts'];
$maxDump = MAX_ITEM_DUMP;
foreach($IIIIIIIl1II1 as $IIIIl1IllIII =>$IIIIIIIIlIll) {
echo "
			<div class=\"info\"><table cellspacing=0><tbody>
			<tr><th colspan=\"2\">$IIIIl1IllIII</th></tr>
			<tr><th>Slab Id</th><th>Info</th></tr>";
foreach($IIIIIIIIlIll as $IIIIl1IlllIl =>$slab) {
$IIIIl1Ill1lI = $PHP_SELF.'&op=2&server='.(array_search($IIIIl1IllIII,$IIIIl1IlI11l)).'&dumpslab='.$IIIIl1IlllIl;
echo
"<tr class=tr-$IIIIIlIIll1I>",
"<td class=td-0><center>",'<a href="',$IIIIl1Ill1lI,'">',$IIIIl1IlllIl,'</a>',"</center></td>",
"<td class=td-last><b>Item count:</b> ",$slab['number'],'<br/><b>Age:</b>',duration($IIIIllIl1l1I-$slab['age']),'<br/> <b>Evicted:</b>',((isset($slab['evicted']) &&$slab['evicted']==1)?'Yes':'No');
if ((isset($_GET['dumpslab']) &&$_GET['dumpslab']==$IIIIl1IlllIl) &&(isset($_GET['server']) &&$_GET['server']==array_search($IIIIl1IllIII,$IIIIl1IlI11l))){
echo "<br/><b>Items: item</b><br/>";
$IIIIIIIl1II1 = dumpCacheSlab($IIIIl1IllIII,$IIIIl1IlllIl,$slab['number']);
$IIIIIlIIIll1=1;
foreach($IIIIIIIl1II1['ITEM'] as $IIIIl1Ill1ll=>$itemInfo){
$itemInfo = trim($itemInfo,'[ ]');
echo '<a href="',$PHP_SELF,'&op=4&server=',(array_search($IIIIl1IllIII,$IIIIl1IlI11l)),'&key=',base64_encode($IIIIl1Ill1ll).'">',$IIIIl1Ill1ll,'</a>';
if ($IIIIIlIIIll1++%10 == 0) {
echo '<br/>';
}
elseif ($IIIIIlIIIll1!=$slab['number']+1){
echo ',';
}
}
}
echo "</td></tr>";
$IIIIIlIIll1I=1-$IIIIIlIIll1I;
}
echo "			</tbody></table>
			</div><hr/>";
}
break;
break;
case 4: 
if (!isset($_GET['key']) ||!isset($_GET['server'])){
echo "No key set!";
break;
}
$IIIIl1Ill1l1 = htmlentities(base64_decode($_GET['key']));
$IIIIl1Ill11I = $IIIIl1IlI11l[(int)$_GET['server']];
list($IIIIIlIIlll1,$IIIIIlIIlIIl) = get_host_port_from_server($IIIIl1Ill11I);
$IIIIIll1Ill1 = sendMemcacheCommand($IIIIIlIIlll1,$IIIIIlIIlIIl,'get '.$IIIIl1Ill1l1);
echo "        <div class=\"info\"><table cellspacing=0><tbody>
			<tr><th>Server<th>Key</th><th>Value</th><th>Delete</th></tr>";
if (!isset($IIIIIll1Ill1['VALUE'])) {
echo "<tr><td class=td-0>",$IIIIl1Ill11I,"</td><td class=td-0>",$IIIIl1Ill1l1,
"</td><td>[The requested item was not found or has expired]</td>",
"<td></td>","</tr>";
}
else {
echo "<tr><td class=td-0>",$IIIIl1Ill11I,"</td><td class=td-0>",$IIIIl1Ill1l1,
" <br/>flag:",$IIIIIll1Ill1['VALUE'][$IIIIl1Ill1l1]['stat']['flag'],
" <br/>Size:",bsize($IIIIIll1Ill1['VALUE'][$IIIIl1Ill1l1]['stat']['size']),
"</td><td>",chunk_split($IIIIIll1Ill1['VALUE'][$IIIIl1Ill1l1]['value'],40),"</td>",
'<td><a href="',$PHP_SELF,'&op=5&server=',(int)$_GET['server'],'&key=',base64_encode($IIIIl1Ill1l1),"\">Delete</a></td>","</tr>";
}
echo "			</tbody></table>
			</div><hr/>";
break;
case 5: 
if (!isset($_GET['key']) ||!isset($_GET['server'])){
echo "No key set!";
break;
}
$IIIIl1Ill1l1 = htmlentities(base64_decode($_GET['key']));
$IIIIl1Ill11I = $IIIIl1IlI11l[(int)$_GET['server']];
list($IIIIIlIIlll1,$IIIIIlIIlIIl) = get_host_port_from_server($IIIIl1Ill11I);
$IIIIIll1Ill1 = sendMemcacheCommand($IIIIIlIIlll1,$IIIIIlIIlIIl,'delete '.$IIIIl1Ill1l1);
echo 'Deleting '.$IIIIl1Ill1l1.':'.$IIIIIll1Ill1;
break;
case 6: 
$IIIIl1Ill11I = $IIIIl1IlI11l[(int)$_GET['server']];
$IIIIIll1Ill1 = flushServer($IIIIl1Ill11I);
echo 'Flush  '.$IIIIl1Ill11I.":".$IIIIIll1Ill1;
break;
}
echo getFooter();
?>
