url="http://devprom.local/core/processjobs.php"

Set objHTTP = CreateObject( "WinHttp.WinHttpRequest.5.1" )
objHTTP.SetTimeouts 120000, 120000, 120000, 120000

On Error Resume Next 
objHTTP.Open "GET", url, False

On Error Resume Next 
objHTTP.Send