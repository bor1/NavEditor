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
	            "upload/jquery.fileupload.js"

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
            var FileTree = null;

            /**
             * Current selected path
             * @type String
             */
            var current_path = "/";




			function getFileInfoCallback(resp) {
				var fi = JSON.parse(resp);
				if(fi.thumb_name != "") {
			//		var thumbHtml = "<img alt='' src='" + fi.thumb_name + "' style='border:0;' />";
			//		$("#thumbImage").html(thumbHtml);
					$("#txtFileThumbUrl").val(fi.thumb_name);
				} else {
					$("#thumbImage").html("");
					$("#txtFileThumbUrl").val("");
				}
				$("#fileNameTd").html(fi.file_name);
				$("#fileSizeTd").html(fi.file_size + " Byte");
				$("#fileLastModTd").html(fi.modified_time);
				$("#txtFileUrl").val(fi.url);
//
//				if(fi.editable) {
//					var admin = <?php echo(($is_admin) ? 1 : 0); ?>; //todo $is_admin -> test rights >= "admin" (1000)
//					var hideEditorMode = <?php echo(($ne_config_info['hide_sourceeditor']) ? 1 : 0); ?>;
//					var extension = getExtension(fi.file_name);
//					var forb_folders = ['css/','grafiken/','img/','ssi/','js/','vkdaten/','univis/','vkapp/'];
//					if (admin || hideEditorMode == -1){
//						$("#btnQuickEdit").removeAttr("disabled");
//						$("#btnEditorEdit").removeAttr("disabled");
//					}else{
//						if (extension == "shtml" || extension == "html"){
//							$("#btnQuickEdit").attr("disabled", "true");
//							$("#btnEditorEdit").removeAttr("disabled");
//						}else if(hideEditorMode == 1){
//							$("#btnQuickEdit").attr("disabled", "true");
//							$("#btnEditorEdit").attr("disabled", "true");
//						}else if(hideEditorMode == 0){
//							$("#btnQuickEdit").removeAttr("disabled");
//							$("#btnEditorEdit").attr("disabled", "true");
//							for (var fold_tmp in forb_folders){
//								if (fi.url.indexOf(forb_folders[fold_tmp]) != -1 && fi.url.indexOf(forb_folders[fold_tmp]) < 2) {
//                                        $("#btnQuickEdit").attr("disabled", "true");
//                                        break;
//                                }
//                            }
//                        }
//                    }
//                } else {
//                    $("#btnQuickEdit").attr("disabled", "true");
//                    $("#btnEditorEdit").attr("disabled", "true");
//                }
            }

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

			function createFolder(path, folder_name, callback){


				$.post("app/file_manager.php", {
					"service": "create_subfolder",
					"current_path": path,
					"new_subfolder_name": folder_name
				}, function(resp) {
					if(resp == "0") {
						alert("Fehler bei der Erstellung des Verzeichnises; Bitte versuchen Sie es noch einmal!");
					} else {
						if(callback) callback();
					}
				});

			}

			function createNewFile(path, file_name, file_ext){
			    if(file_ext != "" && file_ext != null && path != null) {
			        $.post("app/file_manager.php", {
						"service": "create_new_file",
						"current_path": path,
						"new_file_name": file_name,
			                        "extension": file_ext
					}, function(resp) {
						if(resp == "0") {
							alert("Fehler bei der Erstellung der Datei; Bitte versuchen Sie es noch einmal!");
						}else if(resp == "1"){
//                            console.log("success");
                            //tree_refresh(relFromFullPath(path));
                        } else {
                            alert(resp);
						}
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
                if(path === "") {
                    alert(unescape("Bitte eine Datei oder Verzeichnis w%E4hlen!"));
                    return;
                }

                //loeschen nur falls volles zugriff
//                if(allowPermission(path, gUserPermissionsArray) !== 1){
//                    alert(unescape("Kein zugriff"));
//                    return;
//                }

                var bIsDir = isDir(path);

                if(bIsDir){
                    if(confirm(unescape("CAUTION! Sind Sie wirklich sicher, das GANZE verzeichnis zu l%F6schen?"))) {
                        $.post("app/file_manager.php", {
                            "service": "delete_folder",
                            "folder": path
                        }, function(resp) {
                            alert(resp);
//                            loadFolderTree("delete", path);
                        });
                    }
                }else{
                    if(!window.confirm(unescape("Sind Sie sicher, diese Datei zu l%F6schen?"))) {
                        return;
                    }
                    $.post("app/file_manager.php", {
                        "service": "delete_file",
                        "file_path": root_path + path
                    }, function() {
                        FileTree.refreshPath(verzeichnis(path));
//                        FileTree.openPath("/");
//                        loadFolderTree("delete", path);
                    });
                }
            }

			/* ---------- Here comes jQuery: ---------- */
			$(document).ready(function() {

				var picture_exts = ["jpeg", "jpg", "png", "gif"],
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
                        mode: "textareas",
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
						content = $this.siblings(".hover-popover").find("content").html(),
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
                FileTree = new $('#file-tree').fileTree({
                    root: '/',
                    showRoot: true,
                    multiFolder: false,
                    expandCallBack: function() {
                        console.log("expandCallBack");
                        if(current_path != "") {

                            var pictures = [],
                            data = { pictures : [] };

                            $.each(FileTree.fileInfoArray, function(elem) {

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
                        console.log("click callback: path: "+ sPath + " isFile: " + isFile);
                        var context = {},
                        html = "";

                        $('#file-details-container .tabbable a').hide();

                        $('#file-details-container a[href="#basis"]').show().tab('show');

                        if(isFile) {
                            context = FileTree.fileInfoArray[sPath];
                            html    = file_details_template(context);

                            if(context.thumb_name === "") context.thumb_name = null;



                            if(text_exts.indexOf(getExtension(sPath)) !== -1) {
                                //							console.log("found");
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
                                $("#picture-preview").html(picture_preview_template(context));
                                $('#file-details-container a[href="#picture-preview"]').show();
                            }

                        }else {//is a forder
                            context = { verzeichnis: verzeichnis(sPath)},
                            html    = folder_details_template(context);

                        }

                        current_path = sPath;

                        $("#file-title").html(sPath);


                        $("#file-details").html(html);
                    }

                });



				// Neuer Ordner
				$("#folder-add").click(function() {
					$.post("app/file_manager.php", {
						"service": "create_subfolder",
						"current_path": path,
						"new_subfolder_name": folderName
					}, function(resp) {
						if(resp == "0") {
							alert("Fehler bei der Erstellung des Verzeichnises; Bitte versuchen Sie es noch einmal!");
						} else {
							loadFolderTree('createSubFolder', relFromFullPath(path));
						}
					});
				})

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


			    // Create New File
			    $("#buttonFileCreate").click(function() {
			    	var $this = $(this),
                    path = root_path + verzeichnis(current_path),
			    		file_name = $("#inputFileCreateFileName").val(),
			    		file_ext = $("#inputFileCreateType").val().substr(1); //substr: .ext -> ext

			    	createNewFile(path, file_name, file_ext);
			    });

			    // Create New Folder
			    $("#buttonFolderCreate").click(function() {
			    	var $this = $(this),
			    		path = root_path + verzeichnis(current_path),
			    		folder_name = $("#inputFolderCreateFolderName").val();

			    	createFolder(path, folder_name);
			    });

                $("#delete-element").click(function(){
                    deleteElement(current_path);
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
			      <input id="inputFileSize" type="text" placeholder="{{file_size}} kB" disabled>
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
		  			<div class="caption clearfix">
		  				<a href="javascript:void(0);" class="btn btn-danger btn-light pull-right">L&ouml;schen</a>
		  			</div>
		  		</div>
		  	</div>

		  	{{#thumb_name}}
		  	<div class="span3" style="height: 400px; margin-bottom: 60px;">
		  		<div class="thumbnail">
		  			<h5>{{this}}</h5>
		  			<img src="{{this}}">
		  			<div class="caption clearfix">
		  				<a href="javascript:void(0);" class="btn btn-danger btn-light pull-right">L&ouml;schen</a>
		  			</div>
		  		</div>
		  	</div>
		  	{{/thumb_name}}


		</script>

		<script id="pictures-preview-template" type="text/x-handlebars-template">

		  	{{#pictures}}
			  	<div class="span2" style="min-height: 200px; margin-bottom: 60px;">
			  		<div class="thumbnail">
			  			<h5>{{titel}}</h5>
			  			<img height="100px" src="{{url}}">
			  			<div class="caption clearfix">
			  				<a href="javascript:void(0);" class="btn btn-danger btn-light pull-right">L&ouml;schen</a>
			  			</div>
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
                <div class="pull-right">
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
                        <a id="delete-element" class="fetch btn btn-danger btn-light" href="javascript:void(0);"><i class="icon-white">X</i>L&ouml;schen</a>
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