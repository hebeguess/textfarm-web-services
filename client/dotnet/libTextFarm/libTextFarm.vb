Imports System.Text.RegularExpressions
Imports System.Text
Imports System.Net
Imports System.IO

Namespace TextFarm

' TextFarm API URL
#Const serviceURL = "http://127.0.0.1/api"

#Region "> ======== TEXTFARM API .NET LIBRARY ========"
    Public Class TextFarm
        ' NOTES -- n/a

#Region "> ======== ASSEMBLY INFO ========"
        Public Shared ReadOnly Property libraryName() As String
            Get
                Return Title & " " & Version.ToString(".00")
            End Get
        End Property

        Public Shared ReadOnly Property Title() As String
            Get
                Return CType(Reflection.AssemblyTitleAttribute.GetCustomAttribute( _
                Reflection.Assembly.GetExecutingAssembly, _
                GetType(Reflection.AssemblyTitleAttribute)), Reflection.AssemblyTitleAttribute).Title
            End Get
        End Property

        Public Shared ReadOnly Property Version() As Decimal
            Get
                With Reflection.Assembly.GetExecutingAssembly.GetName.Version
                    Return CDec(.Major & "." & .Minor)
                End With
            End Get
        End Property
#End Region

    End Class
#End Region

#Region "> ======== API INVOKE CLASS ========"
    Public Class API

#Region "> ======== DECLARATIONS ========"
        Const errMsg01 As String = "User Created"
        Const errMsg02 As String = "User Existed"
        Const errMsg03 As String = "Bad API Request"
        Const errMsg04 As String = "Invalid Private Passphrase"
        Const errMsg05 As String = "Invalid Username or Password"
        Const errMsg06 As String = "Private Passphrase Refreshed"
        Const errMsg07 As String = "Public Passphrase Refreshed"
        Const errMsg08 As String = "Content Upload Successfully"
        Const errMsg09 As String = "Authorize Keys Generated"
        Const errMsg10 As String = "Invalid Passphrase or Content ID"
        Const errMsg11 As String = "Authorize Keys State Updated"
        Const errMsg12 As String = "Invalid Passphrase or AuthKeys or Key State"
        Const errMsg13 As String = "Directory Listed"
        Const errMsg14 As String = "Content States Updated"
        Const errMsg15 As String = "Content Not Exist"
        Const errMsg16 As String = "Invalid Fields or Passphrase"
        Const errMsg17 As String = "Content Retrieved"
        Const errMsg18 As String = "Invalid Passphrase or Insufficient Permission"
        Const errMsg19 As String = "Content Not Exist or Inaccessible"
        Const errMsg20 As String = "Invalid Passphrase or AuthKeys"

        Public Enum FilePermission
            NULL
            [PRIVATE]
            [PUBLIC]
        End Enum

        Public Enum AuthKeysState
            ACTIVATE
            DISABLE
        End Enum

        Class tweakParameters
            Public Enum Attachment
                NULL
                [TRUE]
                [FALSE]
            End Enum

            Public Enum MIME
                NULL
                TEXT_CSS
                TEXT_CSV
                TEXT_HTML
                TEXT_PLAIN
                TEXT_VCARD
                TEXT_XML
                IMAGE_SVG
                APPLICATION_ATOM
                APPLICATION_EMCASCRIPT
                APPLICATION_JSON
                APPLICATION_JAVASCRIPT
                APPLICATION_OCTETSTREAM
                APPLICATION_RSS
                APPLICATION_SOAP
                APPLICATION_XHTML
                APPLICATION_DTD
                APPLICATION_ZIP
                APPLICATION_GZIP
            End Enum

            Public Enum Encoding
                NULL
                UTF32
                UTF32BE
                UTF32LE
                UTF16
                UTF16BE
                UTF16LE
                UTF7
                UTF8
                ASCII
                EUCJP
                JIS
                ISO88591
                EUCCN
                EUCTW
            End Enum

            Dim _attachment As Attachment
            Dim _mime As MIME
            Dim _encoding As Encoding
            Dim _filename As String
            Dim _fileextension As String

            Sub New()
                _attachment = Attachment.NULL
                _mime = MIME.NULL
                _encoding = Encoding.NULL
                _filename = ""
                _fileextension = ""
            End Sub

            Public WriteOnly Property ForceAttachment() As Attachment
                Set(ByVal attachment As Attachment)
                    _attachment = attachment
                End Set
            End Property

            Public WriteOnly Property MIMEType() As MIME
                Set(ByVal mime As MIME)
                    _mime = mime
                End Set
            End Property

            Public WriteOnly Property EncodingConversion() As Encoding
                Set(ByVal encoding As Encoding)
                    _encoding = encoding
                End Set
            End Property

            Public WriteOnly Property FileName() As String
                Set(ByVal fileName As String)
                    _filename = fileName.Trim
                End Set
            End Property

            Public WriteOnly Property FileExtension() As String
                Set(ByVal fileExtension As String)
                    _fileextension = fileExtension.Trim
                End Set
            End Property

            Public Function getAttachmentString() As String
                Dim value As String

                Select Case _attachment
                    Case Attachment.TRUE : value = "true"
                    Case Attachment.FALSE : value = "false"
                    Case Else : value = "" 'Attachment.NULL
                End Select

                Return value
            End Function

            Public Function getMIMEString() As String
                Dim value As String

                Select Case _mime
                    Case MIME.TEXT_CSS : value = "text/css"
                    Case MIME.TEXT_CSV : value = "text/csv"
                    Case MIME.TEXT_HTML : value = "text/html"
                    Case MIME.TEXT_PLAIN : value = "text/plain"
                    Case MIME.TEXT_VCARD : value = "text/vcard"
                    Case MIME.TEXT_XML : value = "text/xml"
                    Case MIME.IMAGE_SVG : value = "image/svg+xml"
                    Case MIME.APPLICATION_ATOM : value = "application/atom+xml"
                    Case MIME.APPLICATION_EMCASCRIPT : value = "application/ecmascript"
                    Case MIME.APPLICATION_JSON : value = "application/json"
                    Case MIME.APPLICATION_JAVASCRIPT : value = "application/javascript"
                    Case MIME.APPLICATION_OCTETSTREAM : value = "application/octet-stream"
                    Case MIME.APPLICATION_RSS : value = "application/rss+xml"
                    Case MIME.APPLICATION_SOAP : value = "application/soap+xml"
                    Case MIME.APPLICATION_XHTML : value = "application/xhtml+xml"
                    Case MIME.APPLICATION_DTD : value = "application/xml-dtd"
                    Case MIME.APPLICATION_ZIP : value = "application/zip"
                    Case MIME.APPLICATION_GZIP : value = "application/gzip"
                    Case Else : value = "" 'MIME.NULL 
                End Select

                Return value
            End Function

            Public Function getEncodingString() As String
                Dim value As String

                Select Case _encoding
                    Case Encoding.UTF32 : value = "UTF32"
                    Case Encoding.UTF32BE : value = "UTF32BE"
                    Case Encoding.UTF32LE : value = "UTF32LE"
                    Case Encoding.UTF16 : value = "UTF16"
                    Case Encoding.UTF16BE : value = "UTF16BE"
                    Case Encoding.UTF16LE : value = "UTF16LE"
                    Case Encoding.UTF7 : value = "UTF7"
                    Case Encoding.UTF8 : value = "UTF8"
                    Case Encoding.ASCII : value = "ASCII"
                    Case Encoding.EUCJP : value = "EUCJP"
                    Case Encoding.JIS : value = "JIS"
                    Case Encoding.ISO88591 : value = "ISO88591"
                    Case Encoding.EUCCN : value = "EUCCN"
                    Case Encoding.EUCTW : value = "EUCTW"
                    Case Else : value = "" 'API.Attachment.NULL
                End Select

                Return value
            End Function

            Public Function getFileName() As String
                Return _filename
            End Function

            Public Function getFileExtension() As String
                Return _fileextension
            End Function
        End Class
#End Region

#Region "> ======== CONSTRUCTOR ========"
        Const userAgent As String = "Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1"
        Const accePt As String = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
        Const acceptEncoding As String = "gzip, deflate"
        Const acceptLanguage As String = "en-us,en;q=0.5"
        Private privatePassphrase As String

        Sub New(Optional ByVal privatePassphrase As String = "")
            Me.privatePassphrase = privatePassphrase
        End Sub
#End Region

#Region "> ======== HTTP METHOD ========"
        Private Function postMethod(ByVal apiPath As String, ByVal postFields As String, Optional ByVal timeOut As Integer = 5000) As APIResponse
            Dim response As New APIResponse

            Try
                Dim responseString As String = ""
                Dim sReader As StreamReader

                Dim requestScrape As HttpWebRequest = WebRequest.Create(serviceURL & apiPath)
                Dim responseScrape As HttpWebResponse
                Dim requestStream As Stream
                Dim byteStream As Byte() = System.Text.Encoding.GetEncoding(65001).GetBytes(postFields)

                requestScrape.KeepAlive = False
                requestScrape.Timeout = timeOut
                requestScrape.ReadWriteTimeout = timeOut * 2
                requestScrape.Method = "POST"
                requestScrape.ContentType = "application/x-www-form-urlencoded"
                requestScrape.ContentLength = postFields.Length
                requestScrape.Accept = accePt
                requestScrape.Headers.Add("Accept-Encoding", acceptEncoding)
                requestScrape.Headers.Add("Accept-Language", acceptLanguage)
                requestScrape.UserAgent = userAgent
                requestScrape.AutomaticDecompression = DecompressionMethods.GZip

                requestStream = requestScrape.GetRequestStream()
                requestStream.Write(byteStream, 0, byteStream.Length)
                responseScrape = requestScrape.GetResponse

                Dim i As Integer
                While i < responseScrape.Headers.Count
                    If responseScrape.Headers.Keys(i) = "Content-Disposition" Then
                        response.ContentDisposition = responseScrape.Headers(i)
                    End If
                    i += 1
                End While

                sReader = New StreamReader(responseScrape.GetResponseStream)
                responseString = sReader.ReadToEnd()
                response.Content = responseString

                Console.WriteLine("LENGTH >> " & responseScrape.ContentLength)

                response.StatusCode = responseScrape.StatusCode
                response.ContentLength = responseScrape.ContentLength
                response.ContentType = responseScrape.ContentType

                sReader.Close()
                requestStream.Close()
                responseScrape.Close()

            Catch webEx As WebException

                If webEx.Response IsNot Nothing Then
                    response.ContentLength = webEx.Response.ContentLength
                    response.ContentType = webEx.Response.ContentType

                    If webEx.Status = WebExceptionStatus.ProtocolError Then
                        If Regex.IsMatch(webEx.Message, "403") Then
                            '403 Forbidden // Invalid Request
                            response.StatusCode = HttpStatusCode.Forbidden
                            response.APIResponseDesc = errMsg03
                        ElseIf Regex.IsMatch(webEx.Message, "404") Then
                            '404 Not Found // Invalid Content ID
                            response.StatusCode = HttpStatusCode.NotFound
                            response.APIResponseDesc = errMsg15
                        End If
                    Else
                        response.StatusCode = HttpStatusCode.Unused
                        response.APIResponseDesc = errMsg03
                    End If

                    If webEx.Response.ContentLength <> 0 Then
                        Using stream = webEx.Response.GetResponseStream()
                            Using reader = New StreamReader(stream)
                                response.Content = reader.ReadToEnd()
                            End Using
                        End Using
                    End If

                End If
            End Try

            Return response
        End Function

        Private Function getMethod(ByVal apiPath As String, Optional ByVal timeOut As Integer = 5000) As APIResponse
            Dim response As New APIResponse

            Try
                Dim requestScrape As HttpWebRequest = WebRequest.Create(serviceURL & apiPath)
                Dim responseScrape As HttpWebResponse
                Dim sReader As StreamReader

                requestScrape.KeepAlive = False
                requestScrape.Timeout = timeOut
                requestScrape.ReadWriteTimeout = timeOut * 2

                requestScrape.Accept = accePt
                requestScrape.Headers.Add("Accept-Encoding", acceptEncoding)
                requestScrape.Headers.Add("Accept-Language", acceptLanguage)
                requestScrape.UserAgent = userAgent
                requestScrape.AutomaticDecompression = DecompressionMethods.GZip
                responseScrape = requestScrape.GetResponse

                Dim i As Integer
                While i < responseScrape.Headers.Count
                    If responseScrape.Headers.Keys(i) = "Content-Disposition" Then
                        response.ContentDisposition = responseScrape.Headers(i)
                    End If
                    i += 1
                End While

                sReader = New StreamReader(responseScrape.GetResponseStream)
                response.Content = sReader.ReadToEnd()

                response.StatusCode = responseScrape.StatusCode
                response.ContentLength = responseScrape.ContentLength
                response.ContentType = responseScrape.ContentType

                sReader.Close()
                responseScrape.Close()

            Catch webEx As WebException

                If webEx.Response IsNot Nothing Then
                    response.ContentLength = webEx.Response.ContentLength
                    response.ContentType = webEx.Response.ContentType

                    If webEx.Status = WebExceptionStatus.ProtocolError Then
                        '403 Forbidden // Invalid Request 
                        response.StatusCode = HttpStatusCode.Forbidden
                        response.APIResponseDesc = errMsg03
                    End If

                    If webEx.Response.ContentLength <> 0 Then
                        Using stream = webEx.Response.GetResponseStream()
                            Using reader = New StreamReader(stream)
                                response.Content = reader.ReadToEnd()
                            End Using
                        End Using
                    End If

                End If
            End Try

            Return response
        End Function
#End Region

#Region "> ======== USER REGISTRATION ========"
        Public Function Register(ByVal userName As String, ByVal password As String) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/register/", "username=" & userName & "&password=" & password)

            '/* User Registration Suceed */
            'fffa9f30f87a619409e20b6688561ebc08e7b12f // User's Private Passphrase

            '	/* OR */

            '/* User Registration Fail */
            '204 No Content // User Existed

            '	/* OR */

            '/* User Registration Fail */
            '403 Forbidden // Invalid Request

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg01
            ElseIf response.StatusCode = HttpStatusCode.NoContent Then
                response.APIResponseDesc = errMsg02
            End If

            Return response
        End Function
#End Region

#Region "> ======== PRIVATE & PUBLIC PASSPHRASES GENERATION ========"
        Public Function RefreshPrivatePassphrase(ByVal privatePassphrase As String) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/passphrase/private/", "privatephrase=" & privatePassphrase)

            '/* User Private Passphrase Regenerated */
            'b0ef03e1d35c3185e0fa04671d23dc22a740e55a // Private Passphrase

            '	/* OR */

            '/* Private Passphrase Regeneration Fail */
            '204 No Content // Invalid Password or Private Passphrase posted

            '	/* OR */

            '/* Private Passphrase Regeneration Fail */
            '403 Forbidden // Invalid Request       

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg06
            ElseIf response.StatusCode = HttpStatusCode.NoContent Then
                response.APIResponseDesc = errMsg04
            End If

            Return response
        End Function

        Public Function RefreshPrivatePassphrase(ByVal userName As String, ByVal password As String) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/passphrase/private/", "username=" & userName & "&password=" & password)

            '/* User Private Passphrase Regenerated */
            'b0ef03e1d35c3185e0fa04671d23dc22a740e55a // Private Passphrase

            '	/* OR */

            '/* Private Passphrase Regeneration Fail */
            '204 No Content // Invalid Password or Private Passphrase posted

            '	/* OR */

            '/* Private Passphrase Regeneration Fail */
            '403 Forbidden // Invalid Request 

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg06
            ElseIf response.StatusCode = HttpStatusCode.NoContent Then
                response.APIResponseDesc = errMsg05
            End If

            Return response
        End Function

        Public Function RefreshPublicPassphrase(ByVal privatePassphrase As String) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/passphrase/public/", "privatephrase=" & privatePassphrase)

            '/* User Public Passphrase Regenerated / Created */
            'b0ef03e1d35c3185e0fa04671d23dc22a740e55a // Public Passphrase

            '	/* OR */

            '/* Public Passphrase Regeneration Fail */
            '204 No Content // Invalid Private Passphrase posted

            '	/* OR */

            '/* Public Passphrase Regeneration Fail */
            '403 Forbidden // Invalid Request

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg07
            ElseIf response.StatusCode = HttpStatusCode.NoContent Then
                response.APIResponseDesc = errMsg04
            End If

            Return response
        End Function
#End Region

#Region "> ======== BASIC & ENCHANCED CONTENT SERVING ========"
        Public Function UploadContent(ByVal privatePassphrase As String, ByVal fileName As String, ByVal permission As FilePermission, ByVal content As String) As APIResponse
            Dim response As New APIResponse
            Dim postFields As String = "privatephrase=" & privatePassphrase & "&filename=" & fileName & _
                                  "&permission=" & If(permission = FilePermission.PUBLIC, "public", "private")
            response = postMethod("/content/insert/", postFields & "&content=" & content)

            '/* Content Added into TextFarm Repository */
            '25 // Content ID

            '	/* OR */

            '/* Content Not Accept by TextFarm */
            '403 Forbidden // Zero or many invalid post fields posted

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg08
            End If

            Return response
        End Function

        Public Function RetrievePublicContent(ByVal contentID As String, ByVal publicPassphrase As String, ByVal parameters As tweakParameters) As APIResponse
            Dim response As New APIResponse
            Dim postFields As String = "publicphrase=" & publicPassphrase

            If parameters.getAttachmentString <> String.Empty Then
                postFields += "&attachment=" & parameters.getAttachmentString
            End If

            If parameters.getMIMEString <> String.Empty Then
                postFields += "&mime=" & parameters.getMIMEString
            End If

            If parameters.getEncodingString <> String.Empty Then
                postFields += "&encoding=" & parameters.getEncodingString
            End If

            If parameters.getFileName <> String.Empty Then
                postFields += "&fileName=" & parameters.getFileName
            End If

            If parameters.getFileExtension <> String.Empty Then
                postFields += "&fileExt=" & parameters.getFileExtension
            End If

            response = postMethod("/content/retrieve/" & IIf(contentID <> String.Empty, contentID & "/", ""), postFields)

            '/* Request Content Served */
            'Hello World as example file content.

            '	/* OR */

            '/* Private Content Requested */
            '403 Forbidden // Unauthorized Request

            '	/* OR */

            '/* Requested Content Not Existed */
            '404 Not Found // Invalid Content ID posted

            '	/* OR */

            '/* Public Passphrase Verification Fail */
            '403 Forbidden // Invalid Public Passphrase posted

            If response.StatusCode = HttpStatusCode.PartialContent Then
                response.APIResponseDesc = errMsg17
            ElseIf response.StatusCode = HttpStatusCode.NotFound Then
                response.APIResponseDesc = errMsg15
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg18
            End If

            Return response
        End Function

        Public Function RetrievePrivateContent(ByVal authKeys As String, ByVal publicPassphrase As String, ByVal parameters As tweakParameters) As APIResponse
            Dim response As New APIResponse
            Dim postFields As String = "publicphrase=" & publicPassphrase

            If parameters.getAttachmentString <> String.Empty Then
                postFields += "&attachment=" & parameters.getAttachmentString
            End If

            If parameters.getMIMEString <> String.Empty Then
                postFields += "&mime=" & parameters.getMIMEString
            End If

            If parameters.getEncodingString <> String.Empty Then
                postFields += "&encoding=" & parameters.getEncodingString
            End If

            If parameters.getFileName <> String.Empty Then
                postFields += "&fileName=" & parameters.getFileName
            End If

            If parameters.getFileExtension <> String.Empty Then
                postFields += "&fileExt=" & parameters.getFileExtension
            End If

            response = postMethod("/content/retrieve/" & IIf(authKeys <> String.Empty, authKeys & "/", ""), postFields)

            '/* Request Content Served */
            'Hello World as example file content.

            '	/* OR */

            '/* Requested Content Not Existed */
            '404 Not Found // Invalid Authorize Keys posted

            '	/* OR */

            '/* Requested Content Not Accessible */
            '404 Not Found // Authorize Keys is under inactive state

            '	/* OR */

            '/* Public Passphrase or Authorize Keys Verification Fail */
            '403 Forbidden // Invalid Public Passphrase or Authorize Keys posted

            If response.StatusCode = HttpStatusCode.PartialContent Then
                response.APIResponseDesc = errMsg17
            ElseIf response.StatusCode = HttpStatusCode.NotFound Then
                response.APIResponseDesc = errMsg19
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg20
            End If

            Return response
        End Function

        Public Function GetPublicContent(ByVal contentID As String, ByVal publicPassphrase As String) As APIResponse
            Dim response As New APIResponse
            response = getMethod("/content/retrieve/" & contentID & "/" & publicPassphrase & "/")

            '/* Same Responses as Using HTTP 'POST' */

            If response.StatusCode = HttpStatusCode.PartialContent Then
                response.APIResponseDesc = errMsg17
            ElseIf response.StatusCode = HttpStatusCode.NotFound Then
                response.APIResponseDesc = errMsg15
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg18
            End If

            Return response
        End Function

        Public Function GetPrivateContent(ByVal authKeys As String, ByVal publicPassphrase As String) As APIResponse
            Dim response As New APIResponse
            response = getMethod("/content/retrieve/" & authKeys & "/" & publicPassphrase & "/")

            '/* Same Responses as Using HTTP 'POST' */

            If response.StatusCode = HttpStatusCode.PartialContent Then
                response.APIResponseDesc = errMsg17
            ElseIf response.StatusCode = HttpStatusCode.NotFound Then
                response.APIResponseDesc = errMsg19
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg20
            End If

            Return response
        End Function
#End Region

#Region "> ======== AUTHORIZE KEYS ========"
        Public Function GenerateAuthKeys(ByVal privatePassphrase As String, ByVal targetContentID As String) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/authkeys/create/" & targetContentID & "/", "privatephrase=" & privatePassphrase)

            '/* Authorize Keys Passphrase Generated */
            '72a3d1dd60dd9c81f005225298b9c1453021e405 // Authorize Keys for Designated Content

            '	/* OR */

            '/* Requesting Authorize Keys Generation on Public Content */
            '403 Forbidden // Invalid Request Posted

            '	/* OR */

            '/* Private Passphrase Verification Fail */
            '403 Forbidden // Invalid Private Passphrase posted

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg09
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg10
            End If

            Return response
        End Function

        Public Function ToggleAuthKeys(ByVal privatePassphrase As String, ByVal authKeys As String, ByVal targetState As AuthKeysState) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/authkeys/" & IIf(targetState = AuthKeysState.ACTIVATE, "activate", "deactivate") & _
                                  "/" & authKeys & "/", "privatephrase=" & privatePassphrase)

            '/* Authorize Keys Status Updated */
            '200 OK // Target Authorize Keys status updated

            '	/* OR */

            '/* Authorize Keys Status Update Fail */
            '403 Forbidden // Authorize Keys already in targeted state

            '	/* OR */

            '/* Private Passphrase or Authorize Keys Verification Fail */
            '403 Forbidden // Invalid Private Passphrase or Authorize Keys posted

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg11
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg12
            End If

            Return response
        End Function
#End Region

#Region "> ======== DIRECTORY MANAGEMENT ========"
        Public Function GetDirectoryList(ByVal privatePassphrase As String) As APIResponse
            Dim response As New APIResponse
            response = postMethod("/directory/list/", "privatephrase=" & privatePassphrase)

            '/* Private Passphrase Verification Fail */
            '403 Forbidden // Invalid Private Passphrase posted

            '	/* OR */

            '/* User's Directory Listed */
            '206 Partial Content // Received user's directory list in XML format

            If response.StatusCode = HttpStatusCode.PartialContent Then
                response.APIResponseDesc = errMsg13
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg04
            End If

            Return response
        End Function

        Public Function UpdateContentMetadata(ByVal privatePassphrase As String, ByVal contentID As String, ByVal fileName As String, ByVal permission As FilePermission) As APIResponse
            Dim response As New APIResponse
            Dim postFileds As String = "privatephrase=" & privatePassphrase

            If fileName.Trim <> String.Empty Then
                postFileds &= "&fileName=" & fileName
            End If

            If permission = FilePermission.PRIVATE Then
                postFileds &= "&permission=private"
            ElseIf permission = FilePermission.PUBLIC Then
                postFileds &= "&permission=public"
            End If

            response = postMethod("/directory/move/" & contentID & "/", postFileds)

            '/* Content States Updated */
            '200 OK // Content states successfully update

            '	/* OR */

            '/* Requested Content Not Existed */
            '404 Not Found // Invalid Content ID posted

            '	/* OR */

            '/* New States Not Accept by TextFarm */
            '403 Forbidden // Invalid new Content Name or Permission posted

            '	/* OR */

            '/* Private Passphrase Verification Fail */
            '403 Forbidden // Invalid Private Passphrase posted

            If response.StatusCode = HttpStatusCode.OK Then
                response.APIResponseDesc = errMsg14
            ElseIf response.StatusCode = HttpStatusCode.NotFound Then
                response.APIResponseDesc = errMsg15
            ElseIf response.StatusCode = HttpStatusCode.Forbidden Then
                response.APIResponseDesc = errMsg16
            End If

            Return response
        End Function
#End Region

    End Class
#End Region

#Region "> ======== API RESPONSE CLASS ========"
    Public Class APIResponse
        Private _content As String
        Private _httpStatus As HttpStatusCode
        Private _contentLength As Long
        Private _contentType As String
        Private _contentDisposition As String
        Private _apiMessage As String

        Sub New()
            _content = ""
            _httpStatus = HttpStatusCode.Unused
            _contentLength = 0
            _contentType = ""
            _contentDisposition = ""
            _apiMessage = "N/A"
        End Sub

        Public Property StatusCode() As HttpStatusCode
            Get
                Return _httpStatus
            End Get
            Friend Set(ByVal responseStatus As HttpStatusCode)
                _httpStatus = responseStatus
            End Set
        End Property

        Public Property Content() As String
            Get
                Return _content
            End Get
            Friend Set(ByVal responseContent As String)
                _content = responseContent
            End Set
        End Property

        Public Property ContentLength() As Long
            Get
                Return _contentLength
            End Get
            Friend Set(ByVal length As Long)
                _contentLength = length
            End Set
        End Property

        Public Property ContentDisposition() As String
            Get
                Return _contentDisposition
            End Get
            Friend Set(ByVal disposition As String)
                _contentDisposition = disposition
            End Set
        End Property

        Public Property ContentType() As String
            Get
                Return Me._contentType
            End Get
            Friend Set(ByVal type As String)
                Me._contentType = type
            End Set
        End Property

        Public Property APIResponseDesc() As String
            Get
                Return _apiMessage
            End Get
            Friend Set(ByVal message As String)
                _apiMessage = message
            End Set
        End Property

        Public Function getHttpStatusCode() As String
            Dim status As String

            Select Case Me._httpStatus
                Case 100 : status = "Continue"
                Case 200 : status = "OK"
                Case 201 : status = "Created"
                Case 202 : status = "Accepted"
                Case 204 : status = "No Content"
                Case 206 : status = "Partial Content"
                Case 404 : status = "Not Found"
                Case Else ' 403 & Others
                    : status = "Forbidden"
            End Select

            Return Me._httpStatus & " " & status
        End Function
    End Class
#End Region

End Namespace