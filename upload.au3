
#include <FTPEx.au3>
#include <MsgBoxConstants.au3>

Local $filenames[4] = ["index.html", "get_buses.php", "get_stops.php", "get_route_id.php"]

Local $sServer = 'files.000webhost.com' ; UNIVERSITY OF CAMBRIDGE ANONYMOUS FTP SERVER
Local $sUsername = 'seangriffin'
Local $sPass = 'x'
Local $Err, $sFTP_Message

Local $hOpen = _FTP_Open('MyFTP Control')
Local $hConn = _FTP_Connect($hOpen, $sServer, $sUsername, $sPass)
If @error Then
	MsgBox($MB_SYSTEMMODAL, '_FTP_Connect', 'ERROR=' & @error)
Else
	_FTP_GetLastResponseInfo($Err, $sFTP_Message)
	ConsoleWrite('$Err=' & $Err & '   $sFTP_Message:' & @CRLF & $sFTP_Message)

	for $each in $filenames

		Local $result = _FTP_FilePut($hConn, @ScriptDir & "\" & $each, "/public_html/" & $each)

		if $result = 1 Then

			ConsoleWrite("200 OK. Transferred " & $each & @CRLF)
		Else

			ConsoleWrite("xxx FAIL. Transferred " & $each & @CRLF)
		EndIf
	Next

EndIf
Local $iFtpc = _FTP_Close($hConn)
Local $iFtpo = _FTP_Close($hOpen)