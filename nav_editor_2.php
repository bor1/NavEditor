<?php
	require_once('auth.php');
	require_once('app/config.php');

	// help
	function has_help_file() {
		global $ne2_config_info;
		$help_file = $ne2_config_info['help_path'] .'website_editor'. $ne2_config_info['help_filesuffix'] ;
		return file_exists($help_file);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Logo bearbeiten - <?php echo($ne2_config_info['app_titleplain']); ?></title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css?<?php echo date('Ymdis'); ?>" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css?<?php echo date('Ymdis'); ?>" >
	<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo date('Ymdis'); ?>" />
	<script src="js/jquery-1.10.1.js" type="text/javascript"></script>
	<script src="js/bootstrap.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/json2.js"></script>
	<script src="js/tinymce/tinymce.min.js"></script>
	<script src="js/handlebars.js"></script>
	<script src="js/naveditor2.js"></script>
	<script type="text/javascript">
		
		function globaleFunktion() {
			console.log("Hello World!");
		}

		(function () {

			$(document).ready(function() {

				var menue_source   = $("#menue-template").html(),
			 		menue_template = Handlebars.compile(menue_source);

				// help
				$("#show-help").click(function() {

					var $this = $(this),
						content = $this.siblings(".hover-popover").find("content").html(),
						showContent = function(content) {
							$this.siblings(".hover-popover").show().find(".content").html(content);
						};

					if(content === undefined || content == "") {
						$.get("app/get_help.php?r=" + Math.random(), {
							"page_name": "nav_editor"
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

				// Prompt
				$(".btn-prompt").click(function(evt) {
					var $this = $(this),
						prompt = $this.data("prompt"),
						callback = $this.data("callback"),
						$title = $this.find(".title"),
						title_temp = $title.html(),
						$controls = $('<span class="controls" />'),
						buttons = $this.data("prompt-buttons").split("|"),
						icons = $this.data("prompt-icons").split("|"),
						width_init = $this.width(),
						width_text_change = 0,
						width_final = 0,
						clean_up = function() {
							$this.removeClass("open");
							$controls.remove();
							$title.html(title_temp);
							$this.animate({
								width: width_init
							}, {
								easing: "linear",
								duration: 150,

								complete: function() {
									$this.css("width", "");
								}
							});
							$(".full-screen-popover").hide();
							$this.css("z-index", "10");
						};
						

					if(prompt && prompt != undefined && !$this.hasClass("open")) {
						// Button besitzt ein prompt.

						$.each(buttons, function(i, button){
							button = $('<a href="javascript:void(0);" data-id="'+i+'"> '+button+'</a>');
							if( icons[i] && icons[i] != "" ) {
								button.prepend( $('<i class="icon-white icon-'+icons[i]+'"></i>') );
							}

							button.click(function() {
								window[callback](i);
								clean_up();
								return false;
							});

							$controls.append( button );

						});

						$this.css("z-index", "100001");

						$title.html(prompt);
						width_text_change = $this.width();


						$this.addClass("open");
						$this.append($controls);
						
						width_final = $this.width();
						
						$title.html(title_temp);
						$this.removeClass("open");


						$this.css("width", width_init);
						$controls.hide();

						$this.animate({
							width: width_final - 22
						}, {
							easing: "linear",
							duration: 200,

							step: function(now, fx) {
								if(now > width_text_change) {
									$title.html(prompt);
								}
							},

							complete: function() {
								$controls.show();
								$this.addClass("open");
								$this.css("width", width_final);
								$(".full-screen-popover").show();
							}
						});
					}
					
				});
				

				$.get("app/load_tree_data.php?r=" + Math.random(), {},
				function(content) {
					var data = JSON.parse(content);
					console.log(data);
					
					navTreeOper.refreshNavTree(data);

					$.each(data, function(id, item) {
						var title = '';

						switch(id) {
							case 'A':
								// Hauptmenue
								title = "Hauptmenü";
								break;
							case 'Z':
								// Optionales Zielgruppenmenue
								title = "Optionales Zielgruppenmenü";
								break;
							case 'S':
								// Technisches Menue
								title = "Technisches Menü";
								break;

							default:
								title = "Unbekannt";
								break;
						}

						item = navTreeOper._cloneObject(item);
						navTreeOper.addPathInfo(item);

						$("#menue-sidebar").append(menue_template({ title: title, data: item }));
					});

					$("#menue-sidebar .menue-folder ul").hide();

					$("#menue-sidebar .menue-folder a").click(function() {
						$(this).parent().find("ul").show();
					});


					$("#menue-sidebar a").click(function() {
						var $this = $(this),
							title = $this.html(),
							key = $this.data("key"),
							data = { path: $this.data("path") };

						$.post("app/update_contents.php?r=" + Math.random(), {
							"json_oper": "get_content",
							"json_data": JSON.stringify(data)
						}, function(content) {
														
							content = JSON.parse(content);

							if(content) {

								$("#menue-sidebar .active").removeClass("active");

								$this.parent().addClass("active");

								$("#file-title").html(title);
								
								
								if(!content.is_locked) {

									console.log(content.content_html);

									content.content_html = content.content_html.replace(/<comment_ssi>/g, "<!-" + "-#");
									content.content_html = content.content_html.replace(/<comment>/g, "<!-" + "-");
									content.content_html = content.content_html.replace(/<\/comment>/g, "-" + "->");

									tinymce.activeEditor.setContent(content.content_html);
								}

							}

						});

						$.post("app/update_contents.php?r=" + Math.random(), {
							"json_oper": "get_backup_list",
							"json_data": JSON.stringify(data)
						}, function(content) {
														
							content = JSON.parse(content);

							if(content) {

								console.log(content);

							}

						});
					});
					

				});



				// $(".btn-prompt").each(function() {
				// 	var $this = $(this);

				// 	$this.css("width", $this.width());
				// 	console.log(this);
				// });


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
					theme_advanced_statusbar_location: "bottom",
				});


			});
		})();

	</script>

	<script id="menue-template" type="text/x-handlebars-template">
		  

	    <h5>{{ title }}</h5>

		<ul class="menue-list">
			{{#data}}
				{{#if child}}
					<li class="menue-folder">
						<a href="javascript: void(0);" data-path="{{path}}" data-key="{{key}}">{{title}}</a>
						<ul>
							{{#child}}
				 				{{#if child}}
									<li class="menue-folder">
										<a href="javascript: void(0);">{{title}}</a>
										<ul>
											{{#child}}
								 				<li class="menue-file"><a href="javascript: void(0);" data-path="{{path}}" data-key="{{key}}">{{title}}</a></li>
								 			{{/child}}
								 		</ul>
									</li>
								{{else}}
									<li class="menue-file"><a href="javascript: void(0);" data-path="{{path}}" data-key="{{key}}">{{title}}</a></li>
								{{/if}}
				 			{{/child}}
				 		</ul>
					</li>
				{{else}}
					<li class="menue-file"><a href="javascript: void(0);" data-path="{{path}}"  data-key="{{key}}">{{title}}</a></li>
				{{/if}}
			{{/data}}
		</ul>
	  	

	</script>

</head>

<body id="bd_Logo">

	<?php require('common_nav_menu.php'); ?>

	<div class="full-screen-popover">
		test

	</div>

	<div class="container" id="wrapper">


		<div class="page-header padding-top">
	        <h2 id="page-title" class="page-header">Seite und Navigation <small>Bearbeiten Sie hier Ihre Internetpresenz</h2>

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
	        </div>
        </div>
        <!--=======================================================================================-->
        <!--================================== Conten ab hier =====================================-->
        <!--=======================================================================================-->

        <div class="row padding-top">
        	
        	<!--==== Menue ====-->
        	<div class="span3">
        		<div id="menue-sidebar">
	        		
	        	</div>

        		<hr style="border-top: solid 1px #ccc;
border-bottom: solid 1px transparent;">

        		<span class="btn btn-prompt btn-light btn-success" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Nein|Abbrechen" data-prompt-icons="ok|remove|" data-callback="globaleFunktion">
					<i class="icon-ok icon-white"></i>
					<span class="title"> Speichern</span>
				</span>
        	</div>
        	<!--==== Menue Ende ====-->

        	<!--==== Hauptteil ====-->
        	<div class="span9 page" >

        		 <div class="page-header">
                    <h4 id="file-title" class="page-header"><small class="path"></small>Startseite</h4>

                    <div class="pull-right">
						
						<span class="btn btn-prompt btn-light btn-danger" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Nein" data-prompt-icons="ok|remove"  data-callback="globaleFunktion">
							<i class="icon-trash icon-white"></i>
							<span class="title"> Seite L&ouml;schen</span>
						</span>

						<span class="btn btn-prompt btn-light btn-success" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Nein|Abbrechen" data-prompt-icons="ok|remove|" data-callback="globaleFunktion">
							<i class="icon-ok icon-white"></i>
							<span class="title"> Speichern</span>
						</span>

			        </div>
                </div>
		        
		        <div class="tabbable"> <!-- Only required for left/right tabs -->
					<ul class="nav nav-tabs nav-tabs-custom">
				    	<li class="active"><a href="#edit" data-toggle="tab"><i class="icon-pencil"></i> Seite Bearbeiten</a></li>
				    	<li><a href="#options" data-toggle="tab"><i class="icon-list-alt"></i> Optionen</a></li>
				    	<li><a href="#recover" data-toggle="tab"><i class="icon-hdd"></i> Wiederherstellen</a></li>
				  	</ul>
			  		<div class="tab-content">
			    		<div class="tab-pane active" id="edit">
							<textarea class="padding-top" name="txtContent" cols="160" rows="25" ></textarea>
			    		</div>

			    		<div class="tab-pane" id="options">
			      			
			      				<div class="span3">
									<div class="control-group">
										<label class="control-label" for="inputTitel">Titel mit HTML</label>
										<div class="controls">
											<input type="text" id="inputTitel" placeholder="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="inputAlias">Alias</label>
										<div class="controls">
											<input type="text" id="inputAlias" placeholder="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="inputInfotext">Infotext</label>
										<div class="controls">
											<input type="text" id="inputInfotext" placeholder="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="inputUrl">URL</label>
										<div class="controls">
											<input type="text" id="inputUrl" placeholder="">
										</div>
									</div>
								</div>
								<div class="span4">
									<div class="control-group">
										<label class="control-label" for="inputIcon">Icon</label>
										<div class="controls">
											<input type="text" id="inputIcon" placeholder="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="inputIconAlt">Icon Alt Text</label>
										<div class="controls">
											<input type="text" id="inputIconAlt" placeholder="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="inputIconTitle">Icon Titel</label>
										<div class="controls">
											<input type="text" id="inputIconTitle" placeholder="">
										</div>
									</div>
								</div>
							

			    		</div>

			    		<div class="tab-pane" id="recover">
			      			<table class="table">
				              <thead>
				                <tr>
				                  <th>#</th>
				                  <th style="width:100px">Datum</th>
				                  <th>Uhrzeit</th>
				                  <th class="span5"></th>
				                </tr>
				              </thead>
				              <tbody>
				                <tr>
				                  <td>1</td>
				                  <td>28.8.2013</td>
				                  <td>16:15</td>
				                  <td>
				                  	<span class="btn btn-prompt btn-primary btn-light" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Abbrechen" data-prompt-icons="ok|remove" data-callback="globaleFunktion">
										<i class="icon-ok icon-white"></i>
										<span class="title"> Wiederherstellen</span>
									</span>
				                  </td>
				                </tr>
				                <tr>
				                  <td>2</td>
				                  <td>28.8.2013</td>
				                  <td>16:15</td>
				                  <td>
				                  	<span class="btn btn-prompt btn-primary btn-light" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Abbrechen" data-prompt-icons="ok|remove" data-callback="globaleFunktion">
										<i class="icon-ok icon-white"></i>
										<span class="title"> Wiederherstellen</span>
									</span>
				                  </td>
				                </tr>
				                <tr>
				                  <td>3</td>
				                  <td>28.8.2013</td>
				                  <td>16:15</td>
				                  <td>
				                  	<span class="btn btn-prompt btn-primary btn-light" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Abbrechen" data-prompt-icons="ok|remove" data-callback="globaleFunktion">
										<i class="icon-ok icon-white"></i>
										<span class="title"> Wiederherstellen</span>
									</span>
				                  </td>
				                </tr>
				              </tbody>
				            </table>
			    		</div>
			  		</div>
				</div>
			</div>
			<!--==== Hauptteil Ende ====-->

		</div>

		<!--=======================================================================================-->
        <!--================================== Conten endet hier ==================================-->
        <!--=======================================================================================-->


	</div>
	
<?php require('common_footer.php'); ?>	

</body>

</html>
