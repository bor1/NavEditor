<?php
require_once('auth.php');


// help
function has_help_file() {
	global $ne_config_info;
	$help_file = $ne_config_info['help_path'] .'file_editor'. $ne_config_info['help_filesuffix'];
	return file_exists($help_file);
}

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Bilder und Dateien</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">


		<?php
		    echo NavTools::includeHtml("default",
                "jquery.MultiFile.js",
	            "jqueryFileTree.css",
	            "jqueryFileTree.js",
	            "queryFolderImgPreview.js",
	            "handlebars.js",
	            "jquery-ui-1.8.18.custom.min.js",
	            "upload/jquery.iframe-transport.js",
	            "upload/jquery.fileupload.js",
	            "upload/jquery.fileupload-ui.js",
	            "upload/jquery.tmpl.min.js",
	            "upload/jquery.image-gallery.js",
	            "upload/jquery.xdr-transport.js",
	            "jquery.ui.accordion.min.js",
	            "tinymce/tinymce.min.js",
	            "upload/jquery.fileupload.js",
	            "nav_tools.js"

		    );
		?>

		<script>
            //globals

            /**
             * Path to root dir.
             * @type String
             */
            var root_path  = "<?php echo ($_SERVER['DOCUMENT_ROOT']); ?>";

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
//                            console.log("success");
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
             * @returns {String} filtered string
             */
            function filterSymbols(string){
                if(string === undefined || string.length === 0){
                    return "";
                }
                var filteredString = string;
                var find = $.parseJSON('<?php echo(json_encode($ne_config_info['symbols_being_replaced'])); ?>');
                var replace = $.parseJSON('<?php echo(json_encode($ne_config_info['symbols_replacement'])); ?>');
                var regex;
                for (var i = 0; i < find.length; i++) {
                    regex = new RegExp(find[i], "g");
                    filteredString = filteredString.replace(regex, replace[i]);
                }
                filteredString = filteredString.replace(<?php echo($ne_config_info['regex_removed_symbols']); ?>g, "");
                return filteredString;
            }

			/* ---------- Here comes jQuery: ---------- */
			$(document).ready(function() {

				var picture_exts = ["jpeg", "jpg", "png", "gif"], //todo load from config.
					text_exts = ["shtml", "html", "htaccess", "txt"],

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
//                        mode: "textareas",
                        selector: "#file-content-textarea",
                        language: "de",
                        theme: "modern",
                        skin: "light",
                        plugins: "image link code table preview mitarbeiter feedimport ssiInclude image_choose",
                        menubar: false,
                        toolbar1: "undo redo | cut copy paste | link image table | mitarbeiter | feedimport | code | preview",
                        toolbar2: "fontselect fontsizeselect | styleselect | alignleft aligncenter alignright alignjustify | outdent indent | bold italic underline strikethrough | bullist numlist",
                        //theme: "advanced",
                        //language: "de",
                        //skin: "o2k7",
                        relative_urls: false,
                        convert_urls: false,
                        //plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                        theme_advanced_styles: "infologo",
                        theme_advanced_toolbar_location: "top",
                        theme_advanced_toolbar_align: "left",
                        theme_advanced_statusbar_location: "bottom"
                    });

				// help
				$("#show-help").click(function() {
					var $this = $(this),
						content = $this.siblings(".hover-popover").find(".content").html(),
						showContent = function(content) {
							$this.siblings(".hover-popover").show().find(".content").html(content);
						};

					if(content === undefined || content == "") {
						$.get("app/get_help.php?r=" + Math.random(), {
							"page_name": "file_editor"
						}, showContent);
					} else {
						showContent(content);
					}
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
                        console.log("expandCallBack");
                        if(current_path !== "") {

                            var pictures = [],
                            data = { pictures : [] };

                            $.each(FileTreeObj.fileInfoArray, function(elem) {

                                if(elem.indexOf(current_path) != -1 && picture_exts.indexOf(getExtension(elem)) != -1) {
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
//                        console.log("click callback: path: "+ sPath + " isFile: " + isFile);
                        var context = {},
                        html = "";

                        $('#file-details-container .tabbable a').hide();

                        $('#file-details-container a[href="#basis"]').show().tab('show');

                        if(isFile) {
                            context = FileTreeObj.fileInfoArray[sPath];
                            html    = file_details_template(context);

                            if(context.thumb_name === "") context.thumb_name = null;



                            if(text_exts.indexOf(getExtension(sPath)) !== -1) {
                                //console.log("found");
                                $.post("app/file_manager.php", {
                                    "service": "load_file_content",
                                    "file_path": sPath
                                }, function(data) {
                                    data = data.replace(/<comment_ssi>/g, "<!-" + "-#");
                                    data = data.replace(/<comment>/g, "<!-" + "-");
                                    data = data.replace(/<\/comment>/g, "-" + "->");
                                    tinymce.activeEditor.setContent(data);
                                });

                                $('#file-details-container a[href="#file-content"]').show();
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


			    // File List
			    $("#file-list-add").click(function() {
			    	$('input[type="file"]').click();
			    });

			    $('#fileupload').fileupload({
				    url: 'app/upload.php?folder='
				})
				.bind('fileuploadstop', function (e, data) {
						alert('DONE');

				});

			    $('#fileupload').fileupload({
			        dataType: 'json',
			        autoUpload: false,
			        done: function (e, data) {

			        }
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
//                    var cnt = $("#file-content-textarea").val();
                    var cnt = tinyMCE.get('file-content-textarea').getContent();
                    console.log(cnt);
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
                        var dynButtonHtml = '<button name="buttonRename" id="buttonRename" class="btn">umbenennen</button>';
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
		</script>

        <script id="file-details-template" type="text/x-handlebars-template">
		  	<form class="form-horizontal">
			  <div class="control-group">
			    <label class="control-label" for="inputFileName">Dateiname</label>
			    <div class="controls">
			      <input type="text" id="inputFileName" value="{{file_name}}">
			    </div>
			  </div>
			  <div class="control-group">
			    <label class="control-label" for="inputFileSize">Dateigr&ouml;&szlig;e</label>
			    <div class="controls">
			      <input id="inputFileSize" type="text" placeholder="{{file_size}} Byte" disabled>
			    </div>
			  </div>
			  <div class="control-group">
			    <label class="control-label" for="inputFileChanged">Ge&auml;ndert</label>
			    <div class="controls">
			      <input id="inputFileChanged" type="text" placeholder="{{modified_time}}" disabled>
			    </div>
			  </div>
			  <div class="control-group">
			    <label class="control-label" for="inputFileUrl">URL</label>
			    <div class="controls">
			      <input class="input-xxlarge" id="inputFileUrl" type="text" placeholder="{{url}}" disabled>
			    </div>
			  </div>
			  <div class="control-group">
			    <label class="control-label" for="inputFileThumb">Thumbnail</label>
			    <div class="controls">
			      <input class="input-xxlarge" id="inputFileThumb" type="text" placeholder="{{thumb_name}}" disabled>
			    </div>
			  </div>
			</form>
		</script>


		<script id="folder-details-template" type="text/x-handlebars-template">
		  	<form class="form-horizontal">
			  <div class="control-group">
			    <label class="control-label" for="inputFileName">Verzeichnisname</label>
			    <div class="controls">
			      <input type="text" id="inputFileName" value="{{verzeichnis}}">
			    </div>
			  </div>
			</form>
		</script>

		<script id="picture-preview-template" type="text/x-handlebars-template">

		  	<div class="span3" style="height: 400px; margin-bottom: 60px;">
		  		<div class="thumbnail">
		  			<h5>{{titel}}</h5>
		  			<img src="{{url}}">
		  			<!--<div class="caption clearfix">
		  				<a href="javascript:void(0);" class="btn btn-danger btn-light pull-right">L&ouml;schen</a>
		  			</div>-->
		  		</div>
		  	</div>

		  	{{#if thumb_name}}
		  	<div class="span3" style="height: 400px; margin-bottom: 60px;">
		  		<div class="thumbnail">
		  			<h5>{{thumb_file_name}}</h5>
		  			<img src="{{thumb_name}}">
		  			<!--<div class="caption clearfix">
		  				<a href="javascript:void(0);" class="btn btn-danger btn-light pull-right">L&ouml;schen</a>
		  			</div>-->
		  		</div>
		  	</div>
		  	{{/if}}


		</script>

		<script id="pictures-preview-template" type="text/x-handlebars-template">

		  	{{#pictures}}
			  	<div class="span2" style="min-height: 200px; margin-bottom: 60px;">
			  		<div class="thumbnail">
			  			<h5>{{titel}}</h5>
			  			<img height="100px" src="{{url}}">
			  			<!--<div class="caption clearfix">
			  				<a href="javascript:void(0);" class="btn btn-danger btn-light pull-right">L&ouml;schen</a>
			  			</div>-->
			  		</div>
			  	</div>
			{{/pictures}}

		</script>

    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <?php require('common_nav_menu.php'); ?>

	    <div class="container" id="wrapper">

        	<div class="page-header">
                <h3 class="page-header">Bilder und Dateien verwalten</h3>
                <div id="edit_buttons_block" class="pull-right">
					<?php
		            	// help
		            	if (has_help_file()) {
		            ?>
		            	<div class="popover-container">
							<a id="show-help" class="fetch btn btn-primary btn-light" href="javascript:void(0);"><i class="icon-white">?</i> Hilfe</a>
							<div class="hover-popover">
								<div class="header clearfix">
									<h4>Hilfe</h4>
									<div class="pull-right">
										<a class="dismiss btn btn-black-white" href="javascript:void(0);">Ok</a>

									</div>
								</div>

								<div class="content"></div>
							</div>
						</div>
					<?php
		            	}
		            ?>

                    <div class="popover-container">
                        <a id="delete-element" class="fetch btn btn-danger btn-light" href="javascript:void(0);"><i class="icon-white icon-remove"></i>L&ouml;schen</a>
                    </div>

                    <div class="popover-container">
                        <a id="rename-element" class="btn btn-warning btn-light" href="javascript:void(0);"><i class="icon-white icon-pencil"></i>Umbenennen</a>
                    </div>



		            <div class="popover-container">
						<a href="javascript:void(0);" class="btn btn-success btn-light" id="btnUpdate" name="btnUpdate" ><i class="icon-white icon-plus-sign"></i> Hinzuf&uuml;gen</a>
						<div class="hover-popover">
							<div class="header clearfix">
								<h4>Hinzuf&uuml;gen</h4>
								<div class="pull-right">
									<a class="dismiss btn btn-black-white" href="javascript:void(0);">Abbrechen</a>
								</div>
							</div>

							<div class="content">
								<div class="tabbable"> <!-- Only required for left/right tabs -->
								  <ul class="nav nav-tabs nav-tabs-custom-dark">
								    <li class="active"><a href="#upload" data-toggle="tab">Hochladen</a></li>
								    <li><a href="#createFile" data-toggle="tab">Neue Datei</a></li>
								    <li><a href="#createFolder" data-toggle="tab">Neuer Ordner</a></li>
								    <li><a href="#archive" data-toggle="tab">Archiv</a></li>
								  </ul>
								  <div class="tab-content">
								    <div class="tab-pane active" id="upload">
										<a id="file-list-add" style="float: none;margin: 10px 30px;" class="span3 btn btn-large btn-black-white" href="javascript:void(0);">Datei ausw&auml;hlen</a>
								    </div>
								    <div class="tab-pane" id="createFile">
								    	<form class="form-horizontal">
									    	<div class="control-group">
												<label class="control-label" for="inputFileCreateType">Typ</label>
												<div class="controls">
											  		<select class="input-small" id="inputFileCreateType">
													  <option>.txt</option>
													  <option>.conf</option>
													  <option>.htaccess</option>
													</select>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="inputFileCreateFileName">Dateiname</label>
												<div class="controls">
											  		<input type="text" class="input-medium" id="inputFileCreateFileName">
												</div>
											</div>
											<div class="control-group">
												<div class="controls">
											  		<a id="buttonFileCreate" class="btn pull-left btn-black-white" href="javascript:void(0);">Erstellen</a>
												</div>
											</div>
										</form>
								    </div>
								    <div class="tab-pane" id="createFolder">
								    	<form class="form-horizontal">
									    	<div class="control-group">
												<label class="control-label" for="inputFolderCreateFolderName">Verzeichnisname</label>
												<div class="controls">
											  		<input type="text" class="input-medium" id="inputFolderCreateFolderName">
												</div>
											</div>
											<a id="buttonFolderCreate" class="btn btn-black-white" href="javascript:void(0);">Erstellen</a>
										</form>
								    </div>
								  </div>
								</div>
							</div>
						</div>
					</div>

                </div>
            </div>  <!-- Page Header End -->

            <div class="row">

            		<div id="file-tree-container" class="span3">
            			<h4 class="page-header">Ordnerstruktur</h4>
            			<div id="file-tree"></div>
            		</div>

            		<div id="file-details-container" class="span9 page">

            			 <div class="page-header">
		                    <h4 id="file-title" class="page-header"></h4>
		                </div>

            			<div class="tabbable"> <!-- Only required for left/right tabs -->
							<ul class="nav nav-tabs nav-tabs-custom">
						    	<li class="active"><a href="#basis" data-toggle="tab">Basisinformationen</a></li>
						    	<li><a href="#file-content" data-toggle="tab">Inhalt bearbeiten</a></li>
						    	<li><a href="#picture-preview" data-toggle="tab">Bilder Vorschau</a></li>
						  	</ul>
						  	<div class="tab-content">
						    	<div class="tab-pane active" id="basis">
									<div id="file-details">
			            				<form class="form-horizontal">


										</form>
			            			</div>
						    	</div>

						    	<div class="tab-pane" id="file-content">
							      <textarea id="file-content-textarea" class="padding-top" name="file-content-textarea" cols="160" rows="250" class="textBox"></textarea>
                                  <button id="btnSaveEditedFile" class="btn btn-light btn-success"><i class="icon-white icon-ok"></i>Speichern</button>
						    	</div>

						    	<div class="tab-pane" id="picture-preview">

						    	</div>
						  	</div>
						</div>

            		</div>


            </div>

	    </div>


	    <?php require('common_footer.php'); ?>



    </body>
</html>