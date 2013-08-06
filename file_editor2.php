<?php
require_once('auth.php');
require_once('app/config.php');


// help
function has_help_file() {
	global $ne2_config_info;
	$help_file = $ne2_config_info['help_path'] .'file_editor'. $ne2_config_info['help_filesuffix'] ;
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
		            "upload/jquery.tmpl.min.js"
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
					var hideEditorMode = <?php echo(($ne2_config_info['hide_sourceeditor'])? 1 : 0); ?>;
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
				var str = path + '';
			        var dotP = str.lastIndexOf('.') + 1;
				return str.substr(dotP);
			}

			function nameWOextension(path){
				var str = path + '';
			        var dotP = str.lastIndexOf('.');
				return str.substr(0, dotP);
			}

			function verzeichnis(path){
				var str = path + '';
			        var slash = str.lastIndexOf('/');
				return str.substr(0, slash+1);
			}


			function dateiname(path){
				var str = path + '';
			        var slash = str.lastIndexOf('/');
				return str.substr(slash+1);
			}

			function deleteCurFileVars(){
				curFilePath = "";
				fpath = "";
				thisIsAFile = false;
			}

			/* ---------- Here comes jQuery: ---------- */
			$(document).ready(function() {
				var file_details_source   = $("#file-details-template").html(),
				 	file_details_template = Handlebars.compile(file_details_source);

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
				$('#file-tree').fileTree({ 
						root: '/',
						loadCallBack: function(data) {
							console.log(data);
						}
					},
					function(file_path) {
				       
						var context = { filename : dateiname(file_path) },
							html    = file_details_template(context);

						$("#file-details").html(html);
			    });


			    // File List
			    $("#file-list-add").click(function() {
			    	$('input[type="file"]').click();
			    });

			    $('#fileupload').fileupload({
			        dataType: 'json',
			        done: function (e, data) {
			            $.each(data.result.files, function (index, file) {
			                $('<p/>').text(file.name).appendTo("file-list");
			            });
			        }
			    });

			});
		</script>
        
        <script id="file-details-template" type="text/x-handlebars-template">
		  	<form class="form-horizontal">
			  <div class="control-group">
			    <label class="control-label" for="inputFileName">Dateiname</label>
			    <div class="controls">
			      <input type="text" id="inputFileName" value="{{filename}}">
			    </div>
			  </div>
			</form>
		</script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <?php require('common_nav_menu.php'); ?>

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
								  </ul>
								  <div class="tab-content">
								    <div class="tab-pane active" id="upload">
								    	<div class="file-list-container">
											<h5 class="file-list-titel">Dateien:</h5>
											<ul class="file-list">
												<li class="">Noch keine Dateien hinzugef&uuml;gt.</li>
											</ul>
										</div>
										<a id="file-list-add" class="btn btn-large btn-black-white" href="javascript:void(0);">Hinzuf&uuml;gen</a>
								    </div>
								    <div class="tab-pane" id="createFile">
								    	<form class="form-horizontal">
									    	<div class="control-group">
												<label class="control-label" for="inputTyp">Typ</label>
												<div class="controls">
											  		<select class="input-small" id="inputTyp">
													  <option>.txt</option>
													  <option>.conf</option>
													  <option>.htaccess</option>
													</select>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="inputFileName">Dateiname</label>
												<div class="controls">
											  		<input type="text" class="input-medium" id="inputFileName">
												</div>
											</div>
											<div class="control-group">
												<div class="controls">
											  		<a id="file-list-add" class="btn pull-left btn-black-white" href="javascript:void(0);">Erstellen</a>
												</div>
											</div>
										</form>
								    </div>
								    <div class="tab-pane" id="createFolder">
								    	<form class="form-horizontal">
									    	<div class="control-group">
												<label class="control-label" for="inputFileName">Verzeichnisname</label>
												<div class="controls">
											  		<input type="text" class="input-medium" id="inputFileName">
												</div>
											</div>
											<a id="file-list-add" class="btn btn-black-white" href="javascript:void(0);">Erstellen</a>
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
            		
            		<div id="file-tree-container" class="span4">
            			<h4 class="page-header">Ordnerstruktur</h4>
            			<div id="file-tree">

            			</div>
            		</div>

            		<div id="file-details-container" class="span8">
            			<h4 class="page-header">Details</h4>
            			<div id="file-details">
            				<form class="form-horizontal">
							  
							  
							</form>
            			</div>
            		</div>

           
            </div>

	    </div>


	    <?php require('common_footer.php'); ?>

       
        
    </body>
</html>