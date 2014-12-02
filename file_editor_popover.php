<?php
require_once('auth.php');


// help
function has_help_file() {
	global $ne_config_info;
	$help_file = $ne_config_info['help_path'] .'file_editor'. $ne_config_info['help_filesuffix'] ;
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

        <link rel="stylesheet" type="text/css" href="css/bootstrap.css?<?php echo date('Ymdis'); ?>" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css?<?php echo date('Ymdis'); ?>" />
		<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo date('Ymdis'); ?>" />
		<script src="js/jquery-1.10.1.js" type="text/javascript"></script>
		<script src="js/bootstrap.js" type="text/javascript"></script>
		<script src="js/jquery.MultiFile.js" type="text/javascript"></script>

		<?php
		    echo NavTools::includeHtml("default",
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
	            "tinymce/plugins/compat3x/tiny_mce_popup.js",

	            "upload/jquery.fileupload.js"

		    );
		?>

		<script>
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

				if(fi.editable) {
					var admin = <?php echo(($is_admin)? 1 : 0); ?>; //todo $is_admin -> test rights >= "admin" (1000)
					var hideEditorMode = <?php echo(($ne_config_info['hide_sourceeditor'])? 1 : 0); ?>;
					var extension = getExtension(fi.file_name);
					var forb_folders = ['css/','grafiken/','img/','ssi/','js/','vkdaten/','univis/','vkapp/'];
					if (admin || hideEditorMode == -1){
						$("#btnQuickEdit").removeAttr("disabled");
						$("#btnEditorEdit").removeAttr("disabled");
					}else{
						if (extension == "shtml" || extension == "html"){
							$("#btnQuickEdit").attr("disabled", "true");
							$("#btnEditorEdit").removeAttr("disabled");
						}else if(hideEditorMode == 1){
							$("#btnQuickEdit").attr("disabled", "true");
							$("#btnEditorEdit").attr("disabled", "true");
						}else if(hideEditorMode == 0){
							$("#btnQuickEdit").removeAttr("disabled");
							$("#btnEditorEdit").attr("disabled", "true");
							for (var fold_tmp in forb_folders){
								if (fi.url.indexOf(forb_folders[fold_tmp]) != -1 && fi.url.indexOf(forb_folders[fold_tmp]) < 2){
									$("#btnQuickEdit").attr("disabled", "true");
									break;
								}
							}

						}
					}
				}else{
					$("#btnQuickEdit").attr("disabled", "true");
					$("#btnEditorEdit").attr("disabled", "true");
				}
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

			function deleteCurFileVars(){
				curFilePath = "";
				fpath = "";
				thisIsAFile = false;
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
                            console.log("success");
                            //tree_refresh(relFromFullPath(path));
                        } else {
                            alert(resp);
						}
					});
			    }
			}

			var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;

				var parentEditor = parentWin.naveditor_activeEditor;
				var parentInput = parentWin.naveditor_activeInput;


				function submit(url) {
				    var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;
				    var parentInput = parentWin.naveditor_activeInput;


				    parentWin.naveditor_chosen_file = url;
				    parentEditor.windowManager.close();
				};

			/* ---------- Here comes jQuery: ---------- */
			$(document).ready(function() {
				var current_file = "",
					tinymceReady = false,
					picture_exts = [ "jpeg", "jpg", "png", "gif"],
					text_exts = ["shtml", "html", "htaccess", "txt"],

					file_details_source   = $("#file-details-template").html(),
				 	file_details_template = Handlebars.compile(file_details_source),

				 	folder_details_source   = $("#folder-details-template").html(),
				 	folder_details_template = Handlebars.compile(folder_details_source),

					picture_preview_source   = $("#picture-preview-template").html(),
				 	picture_preview_template = Handlebars.compile(picture_preview_source),

				 	pictures_preview_source   = $("#pictures-preview-template").html(),
				 	pictures_preview_template = Handlebars.compile(pictures_preview_source);



				// var FileBrowserDialogue = {
				//     init : function () {
				//         // Here goes your code for setting your custom things onLoad.
				//     },
				//     mySubmit : function () {
				//         var URL = document.my_form.my_field.value;
				//         var win = tinyMCEPopup.getWindowArg("window");

				//         // insert information now
				//         win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

				//         // are we an image browser
				//         if (typeof(win.ImageDialog) != "undefined") {
				//             // we are, so update image dimensions...
				//             if (win.ImageDialog.getImageData)
				//                 win.ImageDialog.getImageData();

				//             // ... and preview if necessary
				//             if (win.ImageDialog.showPreviewImage)
				//                 win.ImageDialog.showPreviewImage(URL);
				//         }

				//         // close popup window
				//         tinyMCEPopup.close();
				//     }
				// }

				// tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

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
				$('#file-tree').fileTree({
					root: '/',
					multiFolder: false,
					loadCallBack: function() {
						if(current_file != "") {

							var pictures = [],
								data = { pictures : [] };

							$.each(fileInfoArray, function(elem) {

								if(elem.indexOf(current_file) != -1 && picture_exts.indexOf(getExtension(elem)) != -1) {
									pictures.push({ url: elem, titel: dateiname(elem) });
								}

							});

							data.pictures = pictures;

							console.log(pictures);

							if(pictures.length > 0) {
								console.log("show preview");
								$('#file-details-container a[href="#picture-preview"]').show();
							}

							$("#picture-preview").html(pictures_preview_template(data));
						}

						$("#file-tree a").click(function(evt) {
							$("#file-tree .active").removeClass("active");
							$(this).addClass("active");

						});
					}

				},
				function(file, folder) {

					var context = {},
						html = "";

					$('#file-details-container .tabbable a').hide();

					$('#file-details-container a[href="#basis"]').show().tab('show');

			       	if(file != null) {
			       		context = fileInfoArray[file];
						html    = file_details_template(context);

						if(context.thumb_name == "") context.thumb_name = null;



						if(text_exts.indexOf(getExtension(file)) != -1) {
							console.log("found");
							$.post("app/file_manager.php", {
								"service": "load_file_content",
								"file_path": file
							}, function(data) {
								data = data.replace(/<comment_ssi>/g, "<!-" + "-#");
								data = data.replace(/<comment>/g, "<!-" + "-");
								data = data.replace(/<\/comment>/g, "-" + "->");
								//tinymce.activeEditor.setContent(data);
							});

							$('#file-details-container a[href="#file-content"]').show();
						}

						if(picture_exts.indexOf(getExtension(file)) != -1) {
							context.titel = dateiname(file);
							$("#picture-preview").html(picture_preview_template(context));
							$('#file-details-container a[href="#picture-preview"]').show();
						}



						current_file = file;

			       	}else if(folder != null) {
			       		context = { verzeichnis: verzeichnis(folder)},
						html    = folder_details_template(context);

						current_file = folder;
			       	}



					$("#file-details").html(html);
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
			            console.log
			        }
			    });


			    // Create New File
			    $("#buttonFileCreate").click(function() {
			    	var $this = $(this),
			    		path = current_file,
			    		file_name = $("#inputFileCreateFileName").val(),
			    		file_ext = $("#inputFileCreateType").val();

			    	path = "/proj/websource/docs/RRZEWeb/www.test.rrze.uni-erlangen.de-2173/websource/" + path;

			    	createNewFile(path, file_name, file_ext);
			    });

			    // Create New Folder
			    $("#buttonFolderCreate").click(function() {
			    	var $this = $(this),
			    		path = current_file,
			    		folder_name = $("#inputFolderCreateFolderName").val();


			    	path = "/proj/websource/docs/RRZEWeb/www.test.rrze.uni-erlangen.de-2173/websource/" + path;

			    	createFolder(path, folder_name);
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
			  				<a href="javascript:submit('{{url}}');" class="picture-select btn btn-success btn-light pull-right">Ausw&auml;hlen</a>
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

	    <div class="container page" id="wrapper">

        	<div class="page-header">
                <h3 class="page-header">Bilder und Dateien verwalten</h3>
                <div class="pull-right">
					<?php
		            	// help
		            	if (has_help_file()) {
		            ?>
		            	<div class="popover-container">
							<a id="show-help" class="fetch btn btn btn-primary btn-light" href="javascript:void(0);"><i class="icon-white">?</i> Hilfe</a>
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
						<a href="javascript:void(0);" class="btn btn-success btn-light" id="btnUpdate" name="btnUpdate" ><i class="icon-white icon-plus-sign"></i> Hochladen</a>
						<div class="hover-popover">
							<div class="header clearfix">
								<h4>Hochladen</h4>
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

            		<div id="file-details-container" class="span9">
            			<h4 class="page-header">Details</h4>
            			<div class="tab-pane" id="picture-preview"></div>
            		</div>
            </div>
	    </div>
    </body>
</html>