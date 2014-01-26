Imports System.Text.RegularExpressions
Imports System.Threading
Imports System.Text
Imports System.Net
Imports System.Xml
Imports System.IO
Imports TextFarm

Public Class Client
    Delegate Sub InvokeContent(ByVal target As Integer, ByVal content As String)
    Delegate Sub InvokeMessage(ByVal message As String)
    Delegate Sub InvokeLog(ByVal log As String)
    Delegate Sub InvokeDebug(ByVal response As APIResponse, ByVal printContent As Boolean)

' TextFarm API URL
#Const serviceURL = "http://127.0.0.1/api"

#Region "Main Functions"
    Private Sub Client_Load(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles MyBase.Load

#If DEBUG Then
        txtPrivatePassphrase.Text = ""
        TabCtrl.SelectedIndex = 6
#End If

        lblMsg.Text = "Awaiting Initial Input..."
    End Sub

    Private Sub btnRegister_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRegister.Click
        'register a new user
        Dim registerThread As New Thread(AddressOf Register)
        registerThread.SetApartmentState(ApartmentState.STA)
        registerThread.IsBackground = True
        registerThread.Start()
    End Sub

    Private Sub btnRefresh_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh.Click
        'refresh private passpharase using existing private passpharase
        Dim refreshThread As New Thread(AddressOf RefreshPrivatePassphrase)
        refreshThread.SetApartmentState(ApartmentState.STA)
        refreshThread.IsBackground = True
        refreshThread.Start(True)
    End Sub

    Private Sub btnRefresh2_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh2.Click
        'refresh private passpharase using existing username & password
        Dim refreshThread As New Thread(AddressOf RefreshPrivatePassphrase)
        refreshThread.SetApartmentState(ApartmentState.STA)
        refreshThread.IsBackground = True
        refreshThread.Start(False)
    End Sub

    Private Sub btnRefresh3_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh3.Click
        'refresh public passpharase using existing private passpharase
        Dim refreshThread As New Thread(AddressOf RefreshPublicPassphrase)
        refreshThread.SetApartmentState(ApartmentState.STA)
        refreshThread.IsBackground = True
        refreshThread.Start()
    End Sub

    Private Sub btnUpload_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnUpload.Click
        'upload content
        Dim uploadThread As New Thread(AddressOf UploadContent)
        uploadThread.SetApartmentState(ApartmentState.STA)
        uploadThread.IsBackground = True
        uploadThread.Start()
    End Sub

    Private Sub btnRefresh4_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh4.Click
        'reload private content list
        btnRefresh4.Enabled = False
        btnCreate.Enabled = False
        cboPrivateContent.Enabled = False
        cboPrivateContent.Items.Clear()

        Dim loadingThread As New Thread(AddressOf LoadPrivateContentList)
        loadingThread.SetApartmentState(ApartmentState.STA)
        loadingThread.IsBackground = True
        loadingThread.Start()
    End Sub

    Private Sub btnRefresh5_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh5.Click
        'reload authorize keys list
        btnRefresh5.Enabled = False
        btnToggle.Enabled = False
        cboAuthKeys.Enabled = False
        cboAuthKeys.Items.Clear()

        Dim loadingThread As New Thread(AddressOf LoadAuthKeysList)
        loadingThread.SetApartmentState(ApartmentState.STA)
        loadingThread.IsBackground = True
        loadingThread.Start()
    End Sub

    Private Sub btnCreate_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnCreate.Click
        'create authorize keys for a private
        Dim authKeysThread As New Thread(AddressOf CreateAuthkeys)
        authKeysThread.SetApartmentState(ApartmentState.STA)
        authKeysThread.IsBackground = True
        authKeysThread.Start()
    End Sub

    Private Sub btnToggle_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnToggle.Click
        'toggle authorize keys state
        Dim authKeysThread As New Thread(AddressOf ToggleAuthkeys)
        authKeysThread.SetApartmentState(ApartmentState.STA)
        authKeysThread.IsBackground = True
        authKeysThread.Start()
    End Sub

    Private Sub rtbDirectory_DoubleClick(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles rtbDirectory.DoubleClick
        'retrieve directroy list
        Dim directoryThread As New Thread(AddressOf DirectoryListing)
        directoryThread.SetApartmentState(ApartmentState.STA)
        directoryThread.IsBackground = True
        directoryThread.Start()
    End Sub

    Private Sub btnBrowse_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnBrowse.Click
        Dim fBrowser As New OpenFileDialog
        fBrowser.Title = "Please Select a File"
        fBrowser.Filter = "css files (*.css)|*.css|csv files (*.csv)|*.csv|html files (*.html)|*.html|txt files (*.txt)|*.txt|" & _
        "vcard files (*.vcard)|*.vcard|xml files (*.xml)|*.xml|svg files (*.svg)|*.svg|atom files (*.atom)|*.atom|" & _
        "json files (*.json)|*.json|js files (*.js)|*.js|rss files (*.rss)|*.rss|xhtml files (*.xhtml)|*.xhtml|" & _
        "dtd files (*.dtd)|*.dtd|zip files (*.zip)|*.zip|gzip files (*.gzip)|*.gzip|All files (*.*)|*.*"
        fBrowser.FilterIndex = 16
        fBrowser.InitialDirectory = Application.StartupPath
        fBrowser.Multiselect = False

        If fBrowser.ShowDialog() = DialogResult.OK Then
            txtFilePath.Text = fBrowser.FileName
            txtFileName.Text = Path.GetFileName(fBrowser.FileName)
            radFile.Checked = True
        End If

        fBrowser.Dispose()
    End Sub

    Private Sub btnRefresh7_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh7.Click
        'retrieve content directory list from TextFarm
        btnRefresh7.Enabled = False
        btnRetrieve.Enabled = False
        btnGet.Enabled = False
        cboContentList.Enabled = False
        GrpTweaker.Enabled = False
        txtID.ReadOnly = True
        txtPublicPhrase.ReadOnly = True

        Dim loadingThread As New Thread(AddressOf LoadContentList)
        loadingThread.SetApartmentState(ApartmentState.STA)
        loadingThread.IsBackground = True
        loadingThread.Start()
    End Sub

    Private Sub btnRetrieve_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRetrieve.Click
        'retrieve content from TextFarm (via POST Method)
        btnRetrieve.Enabled = False
        btnGet.Enabled = False
        GrpTweaker.Enabled = False
        cboContentList.Enabled = False
        btnRefresh7.Enabled = False
        txtPublicPhrase.Enabled = False
        txtID.Enabled = False

        Dim parameters As New API.tweakParameters
        parameters.ForceAttachment = cboAttachment.SelectedIndex
        parameters.MIMEType = cboMIME.SelectedIndex
        parameters.EncodingConversion = cboEncoding.SelectedIndex
        parameters.FileName = txtFileName5.Text.Trim
        parameters.FileExtension = txtFileExt.Text.Trim

        Dim retrieveThread As New Thread(AddressOf RetrieveContent)
        retrieveThread.SetApartmentState(ApartmentState.STA)
        retrieveThread.IsBackground = True
        retrieveThread.Start(parameters)
    End Sub

    Private Sub btnGet_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnGet.Click
        'retrieve content from TextFarm (via GET Method)
        btnRetrieve.Enabled = False
        btnGet.Enabled = False
        GrpTweaker.Enabled = False
        cboContentList.Enabled = False
        btnRefresh7.Enabled = False
        txtPublicPhrase.Enabled = False
        txtID.Enabled = False

        Dim retrieveThread As New Thread(AddressOf GetContent)
        retrieveThread.SetApartmentState(ApartmentState.STA)
        retrieveThread.IsBackground = True
        retrieveThread.Start()
    End Sub

    Private Sub showContent(ByVal target As Integer, ByVal content As String)
        Select Case target
            Case 1 : txtPrivatePassphrase.Text = content
            Case 2 : txtDisplayPrivate.Text = content
            Case 3 : txtDisplayPublic.Text = content
            Case 4 : txtUploadStatus.Text = content
            Case 5 : txtAuthStatus1.Text = content
            Case 6 : txtAuthStatus2.Text = content
            Case 7 : cboPrivateContent.Items.Add(content) : cboPrivateContent.Enabled = True
            Case 8 : cboAuthKeys.Items.Add(content) : cboAuthKeys.Enabled = True
            Case 9 : btnRefresh4.Enabled = True
            Case 10 : btnRefresh5.Enabled = True
            Case 11 : btnRefresh5_Click(Me, System.EventArgs.Empty)
            Case 12 : rtbDirectory.Text = content
            Case 13 : txtUpdateStatus.Text = content
            Case 14 : cboContent.Items.Add(content) : cboContent.Enabled = True
            Case 15 : btnRefresh6.Enabled = True
            Case 16 : btnUpdate.Enabled = True
            Case 17 : txtRetrieveStatus.Text = content
            Case 18 : txtPublicPhrase.Text = content
            Case 19 : cboContentList.Items.Add(content) : cboContentList.Enabled = True
            Case 20 : btnRefresh7.Enabled = True
            Case 21 : btnRetrieve.Enabled = True
            Case Else : Exit Select
        End Select
    End Sub
#End Region

#Region "API Invokers"
    Private Sub Register()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")

        Dim registerUser As New TextFarm.API()
        Dim reply As APIResponse = registerUser.Register(txtUsername.Text.Trim(), txtPassword.Text.Trim())

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  User Registration")

        If reply.StatusCode = HttpStatusCode.OK Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
            Invoke(New InvokeContent(AddressOf showContent), 1, reply.Content)
        ElseIf reply.StatusCode = HttpStatusCode.Forbidden Or reply.StatusCode = HttpStatusCode.NoContent Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub RefreshPrivatePassphrase(ByVal usingPassphrase As Boolean)
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")

        Dim refreshPassphrase As New TextFarm.API()
        Dim reply As New APIResponse

        If usingPassphrase Then
            reply = refreshPassphrase.RefreshPrivatePassphrase(txtPrivatePassphrase.Text)
        Else
            reply = refreshPassphrase.RefreshPrivatePassphrase(txtUsername2.Text.Trim(), txtPassword2.Text.Trim())
        End If

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Private Passphrase Generation")

        If reply.StatusCode = HttpStatusCode.OK Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
            Invoke(New InvokeContent(AddressOf showContent), 1, reply.Content)
            Invoke(New InvokeContent(AddressOf showContent), 2, reply.Content)
        ElseIf reply.StatusCode = HttpStatusCode.Forbidden Or reply.StatusCode = HttpStatusCode.NoContent Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub RefreshPublicPassphrase()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")

        Dim refreshPassphrase As New TextFarm.API()
        Dim reply As APIResponse = refreshPassphrase.RefreshPublicPassphrase(txtPrivatePassphrase.Text)

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Public Passphrase Generation")

        If reply.StatusCode = HttpStatusCode.OK Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
            Invoke(New InvokeContent(AddressOf showContent), 3, reply.Content)
        ElseIf reply.StatusCode = HttpStatusCode.Forbidden Or reply.StatusCode = HttpStatusCode.NoContent Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub UploadContent()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Sending Request...")

        Try
            Dim filename As String
            Dim content As String = ""
            Dim permission As TextFarm.API.FilePermission

            If radFile.Checked = True Then
                Dim file As New IO.FileInfo(txtFilePath.Text)

                If file.Exists Then
                    If file.Length >= 16777216 Then '16MB
                        Throw New Exception("File Size Too Large.")
                    End If

                    Dim strReader As StreamReader
                    strReader = New StreamReader(file.FullName)
                    content = strReader.ReadToEnd()
                Else
                    Throw New Exception("File Not Found.")
                End If

            ElseIf txtContent.Text.TrimEnd <> String.Empty Then
                content = txtContent.Text.TrimEnd
            Else
                Throw New Exception("Nothing to Upload.")
            End If

            If radPublic.Checked = True Then
                permission = TextFarm.API.FilePermission.PUBLIC
            Else
                permission = TextFarm.API.FilePermission.PRIVATE
            End If

            If txtFileName.Text.Trim <> String.Empty Then
                filename = txtFileName.Text.Trim
            Else
                Throw New Exception("File Name is Required.")
            End If

            Invoke(New InvokeContent(AddressOf showContent), 4, "Uploading Content...")
            Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Content Serving - Upload")

            Dim contentUpload As New TextFarm.API()
            Dim reply As APIResponse = contentUpload.UploadContent(txtPrivatePassphrase.Text, filename, permission, content)

            If reply.StatusCode = HttpStatusCode.OK Then
                Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
                Invoke(New InvokeContent(AddressOf showContent), 4, "Content ID : " & reply.Content)
            Else
                Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
                Invoke(New InvokeContent(AddressOf showContent), 4, reply.Content)
            End If

        Catch ex As Exception
            Invoke(New InvokeContent(AddressOf showContent), 4, ex.Message)
        End Try

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub LoadPrivateContentList()
        Try
            Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Sending Request...")
            Invoke(New InvokeContent(AddressOf showContent), 5, "Retrieving your private content IDs.")

            Dim loadContent As New TextFarm.API()
            Dim reply As APIResponse = loadContent.GetDirectoryList(txtPrivatePassphrase.Text)

            If reply.StatusCode = HttpStatusCode.PartialContent Then
                Dim dirList As New XmlDocument
                dirList.LoadXml(Strings.Split(reply.Content, ControlChars.Lf, 2)(1))

                Dim xNodes As XmlNodeList = dirList.SelectNodes("textfarm/directory/content")

                For Each xNode As XmlNode In xNodes
                    If xNode.Attributes.GetNamedItem("permission").Value = "private" Then
                        Invoke(New InvokeContent(AddressOf showContent), 7, xNode.Attributes.GetNamedItem("fileID").Value & _
                               " :: """ & xNode.Attributes.GetNamedItem("fileName").Value & """")
                    End If
                Next

                If xNodes.Count = 0 Then
                    Invoke(New InvokeContent(AddressOf showContent), 5, "No private content found on user's repository.")
                Else
                    Invoke(New InvokeContent(AddressOf showContent), 5, "Please choose a content to create authorize keys")
                End If

            Else
                Invoke(New InvokeContent(AddressOf showContent), 5, "Error on retrieving private content IDs.")
                Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Directory Management - Listing")
                Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            End If

        Catch ex As Exception
            Invoke(New InvokeContent(AddressOf showContent), 5, "Unknown error occured.")
        End Try

        Invoke(New InvokeContent(AddressOf showContent), 9, "")
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub LoadAuthKeysList()
        Try
            Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Sending Request...")
            Invoke(New InvokeContent(AddressOf showContent), 6, "Retrieving your authorize keys list.")

            Dim loadKeys As New TextFarm.API()
            Dim reply As APIResponse = loadKeys.GetDirectoryList(txtPrivatePassphrase.Text)

            If reply.StatusCode = HttpStatusCode.PartialContent Then
                Dim dirList As New XmlDocument
                dirList.LoadXml(Strings.Split(reply.Content, ControlChars.Lf, 2)(1))

                Dim xNodes As XmlNodeList = dirList.SelectNodes("textfarm/directory/content")
                Dim found As Boolean = False

                For Each xNode As XmlNode In xNodes
                    Dim xxNodes As XmlNodeList = xNode.SelectNodes("authorize")
                    For Each xxNode As XmlNode In xxNodes

                        Invoke(New InvokeContent(AddressOf showContent), 8, xNode.Attributes.GetNamedItem("fileID").Value & _
                               " :: " & xNode.Attributes.GetNamedItem("fileName").Value & _
                               " :: " & IIf(xxNode.Attributes.GetNamedItem("status").Value = "active", "ACTIVE", "INACTIVE") & _
                               " :: " & xxNode.Attributes.GetNamedItem("authKeys").Value & "")
                        found = True
                    Next
                Next

                If found = False Then
                    Invoke(New InvokeContent(AddressOf showContent), 6, "No authorize keys found on user's repository.")
                Else
                    Invoke(New InvokeContent(AddressOf showContent), 6, "Please choose a authorize keys to update.")
                End If

            Else
                Invoke(New InvokeContent(AddressOf showContent), 6, "Error on retrieving authorize keys list.")
                Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Directory Management - Listing")
                Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            End If

        Catch ex As Exception
            Invoke(New InvokeContent(AddressOf showContent), 6, "Unknown error occured.")
        End Try

        Invoke(New InvokeContent(AddressOf showContent), 10, "")
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")

    End Sub

    Private Sub CreateAuthkeys()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")
        Invoke(New InvokeContent(AddressOf showContent), 5, "Creating Authorize Keys...")

        Dim createKeys As New TextFarm.API()
        Dim reply As New APIResponse

        reply = createKeys.GenerateAuthKeys(txtPrivatePassphrase.Text, txtTargetID.Text)

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Authorize Keys Generation")

        If reply.StatusCode = HttpStatusCode.OK Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
            Invoke(New InvokeContent(AddressOf showContent), 5, reply.Content)
            Invoke(New InvokeContent(AddressOf showContent), 11, reply.Content)
        ElseIf reply.StatusCode = HttpStatusCode.Forbidden Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub ToggleAuthkeys()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")
        Invoke(New InvokeContent(AddressOf showContent), 6, "Toggling Authorize Keys State...")

        Dim toggleKeys As New TextFarm.API()
        Dim reply As New APIResponse
        Dim keyState As TextFarm.API.AuthKeysState

        If txtAuthState.Text = "ACTIVE" Then
            keyState = API.AuthKeysState.DISABLE
        Else
            keyState = API.AuthKeysState.ACTIVATE
        End If

        reply = toggleKeys.ToggleAuthKeys(txtPrivatePassphrase.Text, txtAuthKeys.Text, keyState)

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Toggle Authorize Keys")

        If reply.StatusCode = HttpStatusCode.OK Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            Invoke(New InvokeContent(AddressOf showContent), 6, reply.APIResponseDesc)
            'Invoke(New InvokeContent(AddressOf showContent), 11, reply.Content)
        ElseIf reply.StatusCode = HttpStatusCode.Forbidden Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub DirectoryListing()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Sending Request...")

        Dim listContent As New TextFarm.API()
        Dim reply As APIResponse = listContent.GetDirectoryList(txtPrivatePassphrase.Text)

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Directory Management - Listing")

        If reply.StatusCode = HttpStatusCode.PartialContent Then
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            Invoke(New InvokeContent(AddressOf showContent), 12, reply.Content)
        Else
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            Invoke(New InvokeContent(AddressOf showContent), 12, reply.APIResponseDesc)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub LoadDirectoryList()
        Try
            Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Sending Request...")
            Invoke(New InvokeContent(AddressOf showContent), 13, "Retrieving your content list.")

            Dim loadContent As New TextFarm.API()
            Dim reply As APIResponse = loadContent.GetDirectoryList(txtPrivatePassphrase.Text)

            If reply.StatusCode = HttpStatusCode.PartialContent Then
                Dim dirList As New XmlDocument
                dirList.LoadXml(Strings.Split(reply.Content, ControlChars.Lf, 2)(1))

                Dim xNodes As XmlNodeList = dirList.SelectNodes("textfarm/directory/content")
                For Each xNode As XmlNode In xNodes

                    Invoke(New InvokeContent(AddressOf showContent), 14, xNode.Attributes.GetNamedItem("fileID").Value & _
                         ControlChars.Tab & ControlChars.Tab & " :: " & xNode.Attributes.GetNamedItem("fileName").Value & _
                           " :: " & IIf(xNode.Attributes.GetNamedItem("permission").Value = "private", "Private Content", "Public Content") & _
                           " :: " & xNode.Attributes.GetNamedItem("contentSize").Value & " Bytes")
                Next

                If xNodes.Count > 0 Then
                    Invoke(New InvokeContent(AddressOf showContent), 13, "Please choose a content metadata to update.")
                Else
                    Invoke(New InvokeContent(AddressOf showContent), 13, "No content stored on user's repository.")
                End If

            Else
                Invoke(New InvokeContent(AddressOf showContent), 13, "Error on retrieving content list.")
                Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Directory Management - Listing")
                Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            End If

        Catch ex As Exception
            MsgBox(ex.Message)
            Invoke(New InvokeContent(AddressOf showContent), 13, "Unknown error occured.")
        End Try

        Invoke(New InvokeContent(AddressOf showContent), 15, "")
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub UpdateContentMeta()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")
        Invoke(New InvokeContent(AddressOf showContent), 13, "Updating Content Metadata...")

        Dim updateMeta As New TextFarm.API()
        Dim reply As New APIResponse
        Dim permission As TextFarm.API.FilePermission

        If radPublic2.Checked = True Then
            permission = API.FilePermission.PUBLIC
        ElseIf radPrivate2.Checked = True Then
            permission = API.FilePermission.PRIVATE
        Else
            permission = API.FilePermission.NULL
        End If

        reply = updateMeta.UpdateContentMetadata(txtPrivatePassphrase.Text, txtContentID2.Text, txtNewName.Text.Trim, permission)

        Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Directory Management - Updates Content Metadata")

        Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        Invoke(New InvokeContent(AddressOf showContent), 13, reply.APIResponseDesc & ". Press refresh button for up-to-date content list.")

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
        Invoke(New InvokeContent(AddressOf showContent), 16, "")
    End Sub

    Private Sub LoadContentList()
        Try
            Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Sending Request...")
            Invoke(New InvokeContent(AddressOf showContent), 13, "Retrieving your content list.")

            Dim loadContent As New TextFarm.API()
            Dim reply As APIResponse = loadContent.GetDirectoryList(txtPrivatePassphrase.Text)

            If reply.StatusCode = HttpStatusCode.PartialContent Then
                Dim dirList As New XmlDocument
                dirList.LoadXml(Strings.Split(reply.Content, ControlChars.Lf, 2)(1))

                Dim phrase As String = dirList.SelectSingleNode("textfarm/user/publicPassphrase").InnerText
                If phrase <> String.Empty Then
                    Invoke(New InvokeContent(AddressOf showContent), 18, phrase)
                Else
                    Invoke(New InvokeContent(AddressOf showContent), 18, "You need to create public passphrase to enable content retrieval.")
                End If

                Dim xNodes As XmlNodeList = dirList.SelectNodes("textfarm/directory/content")
                For Each xNode As XmlNode In xNodes

                    If xNode.Attributes.GetNamedItem("permission").Value = "private" Then

                        For Each xxNode As XmlNode In xNode.SelectNodes("authorize")
                            Dim url As String = ""
                            If xxNode.SelectSingleNode("link") IsNot Nothing Then
                                url = " :: " & xxNode.SelectSingleNode("link").Attributes.GetNamedItem("url").Value
                            End If

                            Invoke(New InvokeContent(AddressOf showContent), 19, xNode.Attributes.GetNamedItem("fileID").Value & _
                                   ControlChars.Tab & ControlChars.Tab & " :: " & xNode.Attributes.GetNamedItem("fileName").Value & _
                                   " :: Private Content :: AuthKeys """ & xxNode.Attributes.GetNamedItem("authKeys").Value & """" & _
                                   " :: " & xxNode.Attributes.GetNamedItem("status").Value & "" & _
                                   " :: " & xNode.Attributes.GetNamedItem("contentSize").Value & " Bytes" & url)
                        Next

                    Else

                        Dim url As String = ""
                        If xNode.SelectSingleNode("link") IsNot Nothing Then
                            url = " :: " & xNode.SelectSingleNode("link").Attributes.GetNamedItem("url").Value
                        End If

                        Invoke(New InvokeContent(AddressOf showContent), 19, xNode.Attributes.GetNamedItem("fileID").Value & _
                               ControlChars.Tab & ControlChars.Tab & " :: " & xNode.Attributes.GetNamedItem("fileName").Value & _
                               " :: Public Content :: " & xNode.Attributes.GetNamedItem("contentSize").Value & " Bytes" & url)
                    End If

                Next

                If xNodes.Count > 0 Then
                    Invoke(New InvokeContent(AddressOf showContent), 17, "Please choose a content to download.")
                Else
                    Invoke(New InvokeContent(AddressOf showContent), 17, "No content stored on user's repository.")
                End If

            Else
                Invoke(New InvokeContent(AddressOf showContent), 17, "Error on retrieving content list.")
                Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Directory Management - Listing")
                Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
            End If

        Catch ex As Exception
            MsgBox(ex.Message)
            Invoke(New InvokeContent(AddressOf showContent), 17, "Unknown error occured.")
        End Try

        Invoke(New InvokeContent(AddressOf showContent), 20, "")
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
    End Sub

    Private Sub RetrieveContent(ByVal tweaks As API.tweakParameters)
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")
        Invoke(New InvokeContent(AddressOf showContent), 13, "Retrieving Content...")

        Dim retrieveContent As New TextFarm.API
        Dim reply As New APIResponse

        If txtPermission2.Text.StartsWith("Private") Then
            Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Content Serving - Retrieving Private Content (POST Method)")
            reply = retrieveContent.RetrievePrivateContent(txtID.Text.Trim, txtPublicPhrase.Text.Trim, tweaks)
        Else
            Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Content Serving - Retrieving Public Content (POST Method)")
            reply = retrieveContent.RetrievePublicContent(txtID.Text.Trim, txtPublicPhrase.Text.Trim, tweaks)
        End If

        If reply.StatusCode = HttpStatusCode.PartialContent Then
            Invoke(New InvokeContent(AddressOf showContent), 17, reply.APIResponseDesc & ". Check logs to response details.")

            If Regex.IsMatch(reply.ContentDisposition, "attachment") Then
                Dim savePath As String = Application.StartupPath & "\" & Regex.Match(reply.ContentDisposition, _
                                                                   "filename\=""(?<name>\w+\.\w+)""").Groups("name").Value
                Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Content Saved Path" & Chr(9) & ": """ & savePath & """")
                Invoke(New InvokeDebug(AddressOf printDebug), reply, False)

                Using outfile As New StreamWriter(savePath)
                    outfile.Write(reply.Content)
                    outfile.Flush()
                End Using
            Else
                Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
            End If

        Else
            Invoke(New InvokeContent(AddressOf showContent), 17, reply.APIResponseDesc)
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
        Invoke(New InvokeContent(AddressOf showContent), 21, "")
    End Sub

    Private Sub GetContent()
        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Requesting...")
        Invoke(New InvokeContent(AddressOf showContent), 13, "Retrieving Content...")

        Dim retrieveContent As New TextFarm.API
        Dim reply As New APIResponse

        If txtPermission2.Text.StartsWith("Private") Then
            Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Content Serving - Retrieving Private Content (GET Method)")
            reply = retrieveContent.GetPrivateContent(txtID.Text.Trim, txtPublicPhrase.Text.Trim)
        Else
            Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Target API Service" & Chr(9) & Chr(9) & ":  Content Serving - Retrieving Public Content (GET Method)")
            reply = retrieveContent.GetPublicContent(txtID.Text.Trim, txtPublicPhrase.Text.Trim)
        End If

        If reply.StatusCode = HttpStatusCode.PartialContent Then
            Invoke(New InvokeContent(AddressOf showContent), 17, reply.APIResponseDesc & ". Check logs to response details.")

            If Regex.IsMatch(reply.ContentDisposition, "attachment") Then
                Dim savePath As String = Application.StartupPath & "\" & Regex.Match(reply.ContentDisposition, _
                                                                   "filename\=""(?<name>\w+\.\w+)""").Groups("name").Value
                Invoke(New InvokeLog(AddressOf lstLog_AddItem), "Content Saved Path" & Chr(9) & ": """ & savePath & """")

                Using outfile As New StreamWriter(savePath)
                    outfile.Write(reply.Content)
                    outfile.Flush()
                End Using
            End If

            Invoke(New InvokeDebug(AddressOf printDebug), reply, True)
        Else
            Invoke(New InvokeContent(AddressOf showContent), 17, reply.APIResponseDesc)
            Invoke(New InvokeDebug(AddressOf printDebug), reply, False)
        End If

        Invoke(New InvokeMessage(AddressOf lblMsg_Update), "Ready...")
        Invoke(New InvokeContent(AddressOf showContent), 21, "")
    End Sub
#End Region

#Region "Logging"
    Friend Sub printDebug(ByVal response As APIResponse, Optional ByVal printContent As Boolean = False)
        lstLog_AddItem("Content-Length" & Chr(9) & Chr(9) & ":  " & response.ContentLength)
        lstLog_AddItem("Content-Type" & Chr(9) & Chr(9) & ":  " & response.ContentType)

        If response.ContentDisposition <> String.Empty Then
            lstLog_AddItem("Content-Disposition" & Chr(9) & Chr(9) & ":  " & response.ContentDisposition)
        End If

        lstLog_AddItem("HTTP Status Code" & Chr(9) & Chr(9) & ":  " & response.getHttpStatusCode())
        lstLog_AddItem("Response Descriptions" & Chr(9) & ":  " & response.APIResponseDesc)

        If printContent = True Then
            Dim content As String = response.Content

            If content.Length >= 256 Then
                lstLog_AddItem("Content (Truncated)" & Chr(9) & ":  " & content.Substring(0, 128))
            Else
                lstLog_AddItem("Content" & Chr(9) & Chr(9) & Chr(9) & ":  " & content)
            End If

        End If

        lstLog_AddSeperator()
    End Sub

    Friend Sub lstLog_AddItem(ByVal str As String)
        lstLog.Items.Add(str)
    End Sub

    Friend Sub lstLog_AddSeperator()
        lstLog.Items.Add("")
        lstLog.Items.Add("====== ======= ====== ======= ====== ======= ====== ======= ====== ======= ====== ======= ====== ======= ====== =======")
        lstLog.Items.Add("")
        lstLog.TopIndex = Math.Max(lstLog.Items.Count - lstLog.ClientSize.Height / lstLog.ItemHeight + 1, 0)
    End Sub

    Private Sub btnClear_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnClear.Click
        lstLog.Items.Clear()
    End Sub

    Friend Sub lblMsg_Update(ByVal message As String)
        lblMsg.Text = message
    End Sub
#End Region

#Region "Tab Controls Cleanup"
    Private Sub TabUserReg_Leave(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabUserReg.Leave
        txtUsername.Text = String.Empty
        txtPassword.Text = String.Empty
    End Sub

    Private Sub TabPassphrase_Leave(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabPassphrase.Leave
        txtUsername2.Text = String.Empty
        txtPassword2.Text = String.Empty
        txtDisplayPrivate.Text = String.Empty
        txtDisplayPublic.Text = String.Empty
    End Sub

    Private Sub TabUpload_Leave(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabUpload.Leave
        txtFilePath.Text = String.Empty
        txtContent.Text = String.Empty
        txtFileName.Text = String.Empty
        txtUploadStatus.Text = String.Empty
        radPublic.Checked = True
    End Sub

    Private Sub TabDirList_Enter(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabDirList.Enter
        rtbDirectory.Text = "Double Click to Retrieve / Refresh."
    End Sub

    Private Sub TabDirList_Leave(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabDirList.Leave
        rtbDirectory.Text = String.Empty
    End Sub

    Private Sub txtContent_Enter(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles txtContent.Enter
        radText.Checked = True
    End Sub

    Private Sub txtFilePath_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles txtFilePath.Click
        btnBrowse_Click(Me, System.EventArgs.Empty)
    End Sub

    Private Sub cboPrivateContent_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboPrivateContent.EnabledChanged
        If cboPrivateContent.Items.Count > 0 And cboPrivateContent.SelectedIndex = -1 Then
            cboPrivateContent.SelectedIndex = 0
            btnCreate.Enabled = True
        End If
    End Sub

    Private Sub cboAuthKeys_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboAuthKeys.EnabledChanged
        If cboAuthKeys.Items.Count > 0 And cboAuthKeys.SelectedIndex = -1 Then
            cboAuthKeys.SelectedIndex = 0
            btnToggle.Enabled = True
        End If
    End Sub

    Private Sub TabAuthKeys_Enter(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabAuthKeys.Enter
        If cboPrivateContent.Items.Count <= 0 Then
            btnRefresh4_Click(Me, System.EventArgs.Empty)
        End If

        If cboAuthKeys.Items.Count <= 0 Then
            btnRefresh5_Click(Me, System.EventArgs.Empty)
        End If
    End Sub

    Private Sub cboPrivateContent_SelectedIndexChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboPrivateContent.SelectedIndexChanged
        If cboPrivateContent.SelectedIndex = -1 Then
            txtTargetID.Text = String.Empty
        Else
            txtTargetID.Text = Strings.Split(cboPrivateContent.SelectedItem.ToString, " :: ", 2)(0)
        End If
    End Sub

    Private Sub cboAuthKeys_SelectedIndexChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboAuthKeys.SelectedIndexChanged
        If cboAuthKeys.SelectedIndex < 0 Then
            txtAuthKeys.Text = String.Empty
            txtFileName2.Text = String.Empty
            txtContentID.Text = String.Empty
            txtAuthState.Text = String.Empty
        Else
            txtAuthKeys.Text = Strings.Split(cboAuthKeys.SelectedItem.ToString, " :: ", 4)(3)
            txtFileName2.Text = Strings.Split(cboAuthKeys.SelectedItem.ToString, " :: ", 4)(1)
            txtContentID.Text = Strings.Split(cboAuthKeys.SelectedItem.ToString, " :: ", 4)(0)
            txtAuthState.Text = Strings.Split(cboAuthKeys.SelectedItem.ToString, " :: ", 4)(2)

            If txtAuthState.Text = "ACTIVE" Then
                radToggle.Text = "Disable it."
            Else
                radToggle.Text = "Activate it."
            End If

        End If
    End Sub

    Private Sub TabDirMeta_Enter(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabDirMeta.Enter
        If cboContent.Items.Count <= 0 Then
            btnRefresh6_Click(Me, System.EventArgs.Empty)
        End If
    End Sub

    Private Sub TabDirMeta_Leave(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabDirMeta.Leave
        txtNewName.Text = String.Empty
        radNoChange.Checked = True
    End Sub

    Private Sub btnRefresh6_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh6.Click
        'reload user's content list
        btnRefresh6.Enabled = False
        btnUpdate.Enabled = False
        txtNewName.Enabled = False
        cboContent.Enabled = False
        cboContent.Items.Clear()

        Dim loadingThread As New Thread(AddressOf LoadDirectoryList)
        loadingThread.SetApartmentState(ApartmentState.STA)
        loadingThread.IsBackground = True
        loadingThread.Start()
    End Sub

    Private Sub btnUpdate_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnUpdate.Click
        'update content metadata aka. manage directory
        btnRefresh6.Enabled = False
        btnUpdate.Enabled = False
        txtNewName.Enabled = False
        cboContent.Enabled = False

        Dim manageThread As New Thread(AddressOf UpdateContentMeta)
        manageThread.SetApartmentState(ApartmentState.STA)
        manageThread.IsBackground = True
        manageThread.Start()
    End Sub

    Private Sub cboDir_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboContent.EnabledChanged
        If cboContent.Items.Count > 0 And cboContent.SelectedIndex = -1 Then
            cboContent.SelectedIndex = 0
            btnUpdate.Enabled = True
        End If
    End Sub

    Private Sub cboDir_SelectedIndexChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboContent.SelectedIndexChanged
        If cboContent.SelectedIndex = -1 Then
            txtContentID2.Text = String.Empty
            txtFileName3.Text = String.Empty
        Else
            txtNewName.Enabled = True
            radNoChange.Checked = True

            txtContentID2.Text = Strings.Split(cboContent.SelectedItem.ToString, " :: ", 4)(0)
            txtFileName3.Text = Strings.Split(cboContent.SelectedItem.ToString, " :: ", 4)(1)
            txtPermission.Text = Strings.Split(cboContent.SelectedItem.ToString, " :: ", 4)(2)
            txtSize.Text = Strings.Split(cboContent.SelectedItem.ToString, " :: ", 4)(3)
        End If
    End Sub

    Private Sub btnUpdate_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnUpdate.EnabledChanged
        If btnUpdate.Enabled = True Then
            btnRefresh6.Enabled = True
            txtNewName.Enabled = True
            cboContent.Enabled = True
        End If
    End Sub

    Private Sub TabDownload_Enter(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabDownload.Enter
        If cboContentList.Items.Count <= 0 Then
            btnRefresh7_Click(Me, System.EventArgs.Empty)
        End If

        cboAttachment.SelectedIndex = 0
        cboMIME.SelectedIndex = 0
        cboEncoding.SelectedIndex = 0
    End Sub

    Private Sub TabDownload_Leave(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles TabDownload.Leave
        txtFileName5.Text = String.Empty
        txtFileExt.Text = String.Empty
    End Sub

    Private Sub btnRefresh7_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRefresh7.EnabledChanged
        If btnRefresh7.Enabled = True Then
            btnRetrieve.Enabled = True
            cboContentList.Enabled = True
            GrpTweaker.Enabled = True
        End If
    End Sub

    Private Sub cboContentList_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboContentList.EnabledChanged
        If cboContentList.Items.Count > 0 And cboContentList.SelectedIndex = -1 Then
            cboContentList.SelectedIndex = 0
            btnRetrieve.Enabled = True
        End If
    End Sub

    Private Sub btnRetrieve_EnabledChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRetrieve.EnabledChanged
        If btnRetrieve.Enabled Then
            GrpTweaker.Enabled = True
            btnGet.Enabled = True
            cboContentList.Enabled = True
            btnRefresh7.Enabled = True
            txtPublicPhrase.Enabled = True
            txtID.Enabled = True
        End If
    End Sub

    Private Sub cboContentList_SelectedIndexChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles cboContentList.SelectedIndexChanged
        If cboContentList.SelectedIndex = -1 Then
            txtPostFields.Text = String.Empty
            txtUrl2.Text = String.Empty
            GrpTweaker.Enabled = False
        Else
            txtID.ReadOnly = False
            txtPublicPhrase.ReadOnly = False
            Dim meta As String() = Strings.Split(cboContentList.SelectedItem.ToString, " :: ", 7)

            txtFileName4.Text = meta(1)

            If meta(2) = "Private Content" Then
                txtID.Text = Strings.Split(meta(3), """", 3)(1)
                lblID.Text = "Authorize Keys"
                txtSize2.Text = meta(5)

                If meta.Length >= 7 Then
                    txtPermission2.Text = meta(2) & " (with Active Authorize Keys)"
                    txtUrl.Text = meta(6)
                Else
                    txtPermission2.Text = meta(2) & " (with Inactive Authorize Keys)"
                    txtUrl.Text = "N/A (Authorize Keys Inactive)"
                End If
            Else
                lblID.Text = "Content ID"
                txtID.Text = meta(0).Trim
                txtPermission2.Text = meta(2) & ""
                txtSize2.Text = meta(3)

                txtUrl.Text = meta(4)
            End If

            updatePostFields(Me, System.EventArgs.Empty)
        End If
    End Sub

    Private Sub updatePostFields(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles _
    txtPublicPhrase.TextChanged, txtID.TextChanged, cboAttachment.SelectedIndexChanged, _
    cboMIME.SelectedIndexChanged, cboEncoding.SelectedIndexChanged, txtFileName5.TextChanged, txtFileExt.TextChanged

        Dim postFields As String = ""

        If txtPublicPhrase.Text.Trim <> String.Empty Then
            postFields = "publicphrase=" & txtPublicPhrase.Text.Trim
        End If

        Dim parameters As New API.tweakParameters
        parameters.ForceAttachment = cboAttachment.SelectedIndex
        parameters.MIMEType = cboMIME.SelectedIndex
        parameters.EncodingConversion = cboEncoding.SelectedIndex
        parameters.FileName = txtFileName5.Text.Trim
        parameters.FileExtension = txtFileExt.Text.Trim

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
            postFields += "&fileExt==" & parameters.getFileExtension
        End If

        txtPostFields.Text = postFields.Trim("&")
        If txtID.ReadOnly = False Then
            txtUrl2.Text = serviceURL & "/content/retrieve/" & IIf(txtID.Text.Trim <> String.Empty, txtID.Text.Trim & "/", "")
        End If
    End Sub
#End Region

End Class
