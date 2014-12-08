
            /* globals $, tinyMCE, Handlebars, NavTools, FileTree*/


            /**
             * FileTree Object
             * @type FileTree
             */
            var FileTreeObj = null;

            /**
             * Current selected path
             * @type String
             */
            var current_path = "/";

            function getExtension(path){
                var str = path + '',
                    dotP = str.lastIndexOf('.') + 1;
                return str.substr(dotP);
            }

            function nameWOextension(path){
                var str = path + '',
                    dotP = str.lastIndexOf('.');
                return str.substr(0, dotP);
            }

            function verzeichnis(path){
                var str = path + '',
                    slash = str.lastIndexOf('/');
                return str.substr(0, slash+1);
            }


            function dateiname(path){
                var str = path + '',
                    slash = str.lastIndexOf('/');
                return str.substr(slash+1);
            }

            /**
             * Created folder (ajax)
             * @param {String} path relative! path where to create
             * @param {String} folder_name folder name
             * @param {Function} fnCallback callback function after create. Called with 1 arg: responce
             */
            function createFolder(path, folder_name, fnCallback){

                $.post("app/file_manager.php", {
                    "service": "create_subfolder",
                    "current_path": path,
                    "new_subfolder_name": folder_name
                }, function(resp) {
                    if(resp === "0") {
                        alert("Fehler bei der Erstellung des Verzeichnises; Bitte versuchen Sie es noch einmal!");
                    }

                    if(fnCallback) fnCallback(resp);
                });

            }

            /**
             * Creates file (ajax)
             * @param {String} path relative! path of folder
             * @param {String} file_name name of file to create
             * @param {String} file_ext extension of file to create, without point.
             * @param {Function} fnCallback callback function after create. Called with 1 arg: responce
             */
            function createNewFile(path, file_name, file_ext, fnCallback){
                if(file_ext != "" && file_ext != null && path != null) {
                    $.post("app/file_manager.php", {
                        "service": "create_new_file",
                        "current_path": path,
                        "new_file_name": file_name,
                        "extension": file_ext
                    }, function(resp) {
                        if(resp === "0") {
                            alert("Fehler bei der Erstellung der Datei; Bitte versuchen Sie es noch einmal!");
                        }else if(resp === "1"){
                            //tree_refresh(relFromFullPath(path));
                        } else {
                            alert(resp);
                        }

                        if(fnCallback) fnCallback(resp);
                    });
                }
            }


            /**
             * test if path is a directory
             * @param {String} path
             * @returns {Boolean} true if is directory
             */
            function isDir(path){
                return (path.substr(-1,1) === "/");
            }

//            //pruefen ob zugriff auf datei erlaubt ist, abhaengig von permissions array
//            //return 0 -> kein zugriff, 1->full zugriff, 2->unterordner/files erlaubt
//            function allowPermission(path, permissions){
//                var retVal = 0;
//
//                //pruefen ob path in permissions bzw obere element drin ist
//                $.each(permissions, function(){
//                    if(path.substr(0,this.length).toLowerCase() == this.toLowerCase()){
//                            retVal = 1;
//                            return false;
//                    }
//                });
//
//                //falls nichts gefunden
//                if (retVal == 0) {
//                   //umgekehrt, pruefen ob irgendwas unter dem path erlaubt ist, dann return 2 (dateien unter dem ordner)
//                    $.each(permissions, function(){
//                        //falls path von ordner..
//                        if(path.substr(-1,1) == "/"){
//                            if(this.substr(0,path.length).toLowerCase() == path.toLowerCase()){
//                                retVal = 2;
//                                return false;
//                            }
//                        }
//                    });
//                }
//                return retVal;
//            }

            function deleteElement(path){
                //loeschen nur falls volles zugriff
//                if(allowPermission(path, gUserPermissionsArray) !== 1){
//                    alert(unescape("Kein zugriff"));
//                    return;
//                }

                var sWarning; //warning message
                var ajaxCommand; //ajax command for php backend
                var dirToRefresh; //dir in tree to refresh

                if(isDir(path)){
                    sWarning = 'CAUTION! Sind Sie sicher, dass Sie das GANZE Verzeichnis: "'+path+'" l%F6schen wollen?';
                    ajaxCommand = 'delete_folder';
                    dirToRefresh = verzeichnis(path.slice(0,-1));
                }else{
                    sWarning = 'Sind Sie sicher, dass Sie diese Datei: "'+path+'" l%F6schen wollen?';
                    ajaxCommand = 'delete_file';
                    dirToRefresh = verzeichnis(path);
                }

                if(confirm(unescape(sWarning))) {
                    $.post('app/file_manager.php', {
                        "service": ajaxCommand,
                        "file_path": path
                    }, function() {
                        FileTreeObj.refreshPath(dirToRefresh); //parent dir.
                    });
               }
            }

            /**
             * rename file/folder (ajax)
             * @param {String} path relative path to element
             * @param {String} newName new name of directory or file without extension
             * @returns {boolean} false if not connected or some error by input data check
             */
            function renameElement(path, newName){
                if(newName === "" && newName === undefined) {
                    alert('Name darf nicht leer sein!');
                    return false;
                }

                //symbole filtern
                newName = filterSymbols(newName);

                if (!confirm('Sind Sie sicher, dass Sie : "'+path+'" in "'+newName+'" umbenennen wollen?'))
                    return false;

                if(path === "/" || path === "" || path === "\\"){
                    alert("Sie dürfen nicht Root-Ordner umbennen");
                    return false;
                }

                var result = true;
                $.post("app/file_manager.php", {
                    "service": "rename",
                    "current_path": path,
                    "new_name": newName
                }, function(resp) {
                    if(resp !== '1') {
                        alert("Fehler beim Umbenennen:\n"+resp);
                    } else {
                        var dirToRefresh = verzeichnis(path.slice(0,-1));
                        FileTreeObj.refreshPath(dirToRefresh); //parent dir.
                    }
                })
                .fail(function(){
                    alert('Server connection error');
                    result = false;
                });

                return result;

            }

            /**
             * Ensure if any path is selected
             * @returns {boolean} if selected
             */
            function ensureSelected(){
                if(current_path === "" || current_path === undefined) {
                    alert(unescape("Bitte eine Datei oder Verzeichnis w%E4hlen!"));
                    return false;
                }

                return true;
            }

            /**
             * Filter/replace symbols in string, to make accepted name
             * @param {String} string string being filtered
             * @returns {String} filtered string
             */
            function filterSymbols(string){
                if(string === undefined || string.length === 0){
                    return "";
                }
                var filteredString = string;
                var find = static_symbols_being_replaced;
                var replace = static_symbols_replacement;
                var regex;
                for (var i = 0; i < find.length; i++) {
                    regex = new RegExp(find[i], "g");
                    filteredString = filteredString.replace(regex, replace[i]);
                }

                filteredString = filteredString.replace(static_regex_removed_symbols, "");
                return filteredString;
            }

            /* ---------- Here comes jQuery: ---------- */
            $(document).ready(function() {

                var picture_exts = ["jpeg", "jpg", "png", "gif"], //todo load from config.
                    html_exts = ["shtml", "html"],
                    no_html_exts = ["htaccess", "txt", "conf"],
                    editor_exts = no_html_exts.concat(html_exts),

                    file_details_source   = $("#file-details-template").html(),
                    file_details_template = Handlebars.compile(file_details_source),

                    folder_details_source   = $("#folder-details-template").html(),
                    folder_details_template = Handlebars.compile(folder_details_source),

                    picture_preview_source   = $("#picture-preview-template").html(),
                    picture_preview_template = Handlebars.compile(picture_preview_source),

                    pictures_preview_source   = $("#pictures-preview-template").html(),
                    pictures_preview_template = Handlebars.compile(pictures_preview_source);


                tinyMCE.init({
                    forced_root_block : '',
                    selector: "#file-content-textarea",
                    language: "de",
                    theme: "modern",
                    skin: "light",
                    plugins: "image link code table preview mitarbeiter feedimport ssiInclude image_choose noneditable",
                    menubar: false,
                    toolbar1: "undo redo | cut copy paste | link image table | mitarbeiter | feedimport | code | preview",
                    toolbar2: "fontselect fontsizeselect | styleselect | alignleft aligncenter alignright alignjustify | outdent indent | bold italic underline strikethrough | bullist numlist",
                    relative_urls: false,
                    convert_urls: false,
                    height: 300
                    //plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                });



                $(".popover-container > a").click(function() {
                    var $this = $(this);

                    $this.siblings(".hover-popover").show();

                });

                $(".hover-popover .dismiss").click(function() {
                    $(this).closest(".hover-popover").hide();
                });


                // File Tree
                FileTreeObj = new FileTree($('#file-tree'), {
                    root: '/',
                    showRoot: true,
                    multiFolder: false,
                    expandCallBack: function() {
                        if(current_path !== "") {

                            var pictures = [],
                            data = { pictures : [] };

                            $.each(FileTreeObj.fileInfoArray, function(elem) {

                                if(elem.indexOf(current_path) !== -1 && picture_exts.indexOf(getExtension(elem)) !== -1) {
                                    pictures.push({ url: elem, titel: dateiname(elem) });
                                }

                            });

                            data.pictures = pictures;

                            if(pictures.length > 0) {
                                $('#file-details-container a[href="#picture-preview"]').show();
                            }

                            $("#picture-preview").html(pictures_preview_template(data));
                        }

                        //no need?
                        $("#file-tree a").click(function(evt) {
                            $("#file-tree .active").removeClass("active");
                            $(this).addClass("active");

                        });
                    },

                    selectCallBack: function(sPath, isFile) {
                        var context = {},
                        html = "";

                        $('#file-details-container .tabbable a').hide();

                        $('#file-details-container a[href="#basis"]').show().tab('show');

                        if(isFile) {
                            context = FileTreeObj.fileInfoArray[sPath];
                            html    = file_details_template(context);

                            if(context.thumb_name === "") context.thumb_name = null;



                            if(editor_exts.indexOf(getExtension(sPath)) !== -1) {


                                $('#file-details-container a[href="#file-content"]')
                                        .unbind()
                                        .show()
                                        .on('click', function(){
                                            $.post("app/file_manager.php", {
                                                "service": "load_file_content",
                                                "file_path": sPath
                                            }, function(data) {
                                                data = data.replace(/<comment_ssi>/g, "<!-" + "-#");
                                                data = data.replace(/<comment>/g, "<!-" + "-");
                                                data = data.replace(/<\/comment>/g, "-" + "->");
                                                //falls ist kein html file, nur simple area text editor
                                                if(no_html_exts.indexOf(getExtension(sPath)) !== -1){
                                                    tinyMCE.activeEditor.hide();
                                                    tinyMCE.activeEditor.getElement().value = data;
                                                }
                                                //falls html, tinyMCE einschalten
                                                else{
                                                    tinyMCE.activeEditor.show();
                                                    tinyMCE.activeEditor.setContent(data);
                                                }

                                            });
                                        });
                            }

                            if(picture_exts.indexOf(getExtension(sPath)) !== -1) {
                                context.titel = dateiname(sPath);
                                //da nu thumb file name nicht vorhanden, generieren, fuer template
                                context.thumb_file_name = dateiname(context.thumb_name);
                                $("#picture-preview").html(picture_preview_template(context));
                                $('#file-details-container a[href="#picture-preview"]').show();
                            }

                        }else {//is a folder
                            context = { verzeichnis: verzeichnis(sPath)},
                            html    = folder_details_template(context);

                        }

                        current_path = sPath;

                        $("#file-title").html(sPath);


                        $("#file-details").html(html);
                    }

                });


                //fileupload, settings and functions
                $('#fileupload').fileupload({
                    url: 'app/upload.php',
                    dataType: 'json',
                    autoUpload: true,
                    singleFileUploads: false,
                    //extra info
                    formData: function(form){
                        return [
                                {
                                    name: 'folder',
                                    value: current_path
                                }
                                ];
                    },
                   /**
                    * Wenn irgendwas von dem Server-Upload-Script erhalten.
                    * TODO bessere Fehlerbehandlung
                    * @param {Object} e jQuery event
                    * @param@param {Object} data result data
                    */
                    done: function (e, data) {
                        var result;

                        if(data.result.length === 0){
                            result = 'Vielleicht Fehlerhaft hochgeladen, bitte alle hochgeladene Dateien überprüfen!';
                        }

                        //jedes result element nach "error" property suchen, error dateien sammeln
                        $.each(data.result, function(i,element){
                            if(element.error){
                                if(!result){result = "Fehler beim Hochladen!\nNicht hochgeladene Dateien:";}
                                result += '\n"'+ element.name + "\""
                            }
                        });


                        //falls gab's fehler
                        if(!result){
                            result = "Hochgeladen!";
                        }
                        alert(result);
                        FileTreeObj.refreshPath(current_path);
                    },

                    //wenn gar kein/falsche antwort von dem Server erhalten
                    fail: function(e, data){
                        alert('Fehler beim Hochladen (Server unereichbar?)');
                    }
                });

                // File List
                $("#file-list-add").click(function() {
                    $("#fileupload").click();
                });

                //TODO one func create(path, fileOrFolder, callback)
                //$this.closest(".hover-popover").hide() as func open() and close()

                // Create New File
                $("#buttonFileCreate").click(function() {
                    var $this = $(this),
                        path = verzeichnis(current_path),
                        file_name = $("#inputFileCreateFileName").val(),
                        file_ext = $("#inputFileCreateType").val().substr(1); //substr: .ext -> ext

                    createNewFile(path, file_name, file_ext, function(){
                        FileTreeObj.refreshPath(path);
                        $this.closest(".hover-popover").hide();
                    });
                });

                // Create New Folder
                $("#buttonFolderCreate").click(function() {
                    var $this = $(this),
                        path = verzeichnis(current_path),
                        folder_name = $("#inputFolderCreateFolderName").val();

                    createFolder(path, folder_name, function(){
                        FileTreeObj.refreshPath(path);
                        $this.closest(".hover-popover").hide();
                    });
                });

                // Delete File/Folder
                $("#delete-element").click(function(){
                    if(!ensureSelected()) return;

                    deleteElement(current_path);
                });

                //Rename File/Folder
                $("#rename-element").click(function(){
                    if(!ensureSelected()) return;
                    var newName = '';
                    var oldName = NavTools.basename(current_path);
                    if(isDir(current_path)){
                        newName = prompt("Neuer Name des Verzeichnises:", oldName);
                    }else{
                        newName = prompt("Neuer Name der Datei (ohne Erweiterung!)", nameWOextension(oldName));
                    }

                    renameElement(current_path, newName);
                });

                //tyniMCE save content
                $("#btnSaveEditedFile").click(function(){
                    if(!confirm("Sind Sie sicher dass sie Inhalt speichern wollen?")) {
                        return;
                    }
                    //mini workaround fuer text editor.
                    //TODO class, mit ".getContent() .setContent() .isTiny(), .getTiny() .getArea()"
                    //falls nur txt editor
                    if($("#file-content-textarea").is(":visible")){
                        var cnt = $("#file-content-textarea").val();
                    }
                    //falls html editor
                    else{
                        var cnt = tinyMCE.get('file-content-textarea').getContent();
                    }

                    cnt = cnt.replace(new RegExp("<!-" + "-#", "g"), "<comment_ssi>");
                    cnt = cnt.replace(/<!--/g, "<comment>");
                    cnt = cnt.replace(/-->/g, "</comment>");
                    $.post("app/file_manager.php", {
                        "service": "save_file_content",
                        "file_path": current_path,
                        "new_content": cnt
                    }, function(resp) {
                        alert(resp);
                    });
                });


                //------------------DYNAMIC EVENT HANDLERS--------------------//
                //dinamisch "umbennen" button laden, wenn name geaendert wird
                $( document )
                .on('keydown', '#inputFileName',function(eventObj){
                    var input = $(eventObj.target);
                    //falls noch kein button, erstellen
                    if(input.parent().find("#buttonRename").length <= 0){
                        var dynButtonHtml = '<button name="buttonRename" id="buttonRename" class="btn btn-warning btn-light"><i class="glyphicon glyphicon-pencil"></i>Umbenennen</button>';
                        $(dynButtonHtml).insertAfter(input).hide().fadeIn('200');
                    }
                })

                //event for "rename" button click
                .on('click', '#buttonRename', function(eventObj){
                    eventObj.preventDefault();
                    if(!ensureSelected()) return;

                    var newNamePath = $("#inputFileName").val();
                    var newName = '';

                    //basename
                    if(isDir(current_path)){
                        newName = NavTools.pathinfo(newNamePath,'PATHINFO_BASENAME');
                    }else{
                        newName = NavTools.pathinfo(newNamePath,'PATHINFO_FILENAME');
                    }

                    renameElement(current_path, newName);
                });

            });