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
	            
	            "handlebars.js",
	            "jquery-ui-1.8.18.custom.min.js",
	            "upload/jquery.iframe-transport.js",
	            
	            "tinymce/plugins/compat3x/tiny_mce_popup.js"

	   
	           
		    );
		?>

		<script>
			
			var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;

			var parentEditor = parentWin.naveditor_activeEditor;
			var parentInput = parentWin.naveditor_activeInput;


			function submit(url) {
			    var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;
			    var parentInput = parentWin.naveditor_activeInput;
			    
			    
			    parentWin.naveditor_chosen_file = url;
			    parentEditor.windowManager.close();
			};

			function globaleFunktion(index) {
				if(index == 0) return;

				var content = parentEditor.getContent();

				content += $("#osm-container").html();
				parentEditor.execCommand('mceInsertContent', 0, $("#osm-container").html());

				parentEditor.windowManager.close();
			}

			

			/* ---------- Here comes jQuery: ---------- */
			$(document).ready(function() {


				$(".vorschau").click(function() {
					var active_tab = $(".tab-pane.active"),
						active_mode = active_tab.attr("id"),
						active_input = active_tab.find("input"),
						iframe_url = "http://karte.fau.de/api/v1/iframe/" + active_mode + "/" + active_input.val(),
						iframe = $('<iframe src="' + iframe_url + '" width="724px" height="400px" seamless style="border: 0; padding: 0; margin: 0; overflow: hidden;"></iframe>');

					console.log(iframe_url);

					$("#osm-container").html(iframe);
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

			});
		</script>
        
        


    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

	    <div class="container padding-top" id="wrapper">

	    	<div class="span12 page">

	    		<div class="tabbable"> <!-- Only required for left/right tabs -->
					<ul class="nav nav-tabs nav-tabs-custom">
				    	<li class="active"><a href="#term" data-toggle="tab"><i class="icon-pencil"></i> Suchbegriff</a></li>
				    	<li><a href="#address" data-toggle="tab"><i class="icon-list-alt"></i> Adressen</a></li>
				    	<li><a href="#org" data-toggle="tab"><i class="icon-hdd"></i> Organisationsnummer</a></li>
				    	<li><a href="#famos" data-toggle="tab"><i class="icon-hdd"></i> FAMOS</a></li>
				  	</ul>
			  		<div class="tab-content">
			    		<div class="tab-pane active" id="term">
							<div class="control-group span6">
								<div class="controls">
									<input type="text" id="inputTerm" placeholder="">
								</div>
							</div>
							<div class="span4">
								<a class="vorschau btn btn-light btn-primary pull-right" href="javascript:void(0);">Vorschau anzeigen</a>
							</div>
							
			    		</div>

			    		<div class="tab-pane" id="address">
			    			<div class="control-group span6">
								<div class="controls">
									<input type="text" id="inputAddress" placeholder="">
								</div>
							</div>
							<div class="span4">
								<a class="vorschau btn btn-light btn-primary pull-right" href="javascript:void(0);">Vorschau anzeigen</a>
							</div>
			    		</div>

			    		<div class="tab-pane" id="org">
			    			<div class="control-group span6">
								<div class="controls">
									<input type="text" id="inputOrg" placeholder="">
								</div>
							</div>
							<div class="span4">
								<a class="vorschau btn btn-light btn-primary pull-right" href="javascript:void(0);">Vorschau anzeigen</a>
							</div>
			    		</div>

			    		<div class="tab-pane" id="famos">
			    			<div class="control-group span6">
								<div class="controls">
									<input type="text" id="inputFamos" placeholder="">
								</div>
							</div>
							<div class="span4">
								<a class="vorschau btn btn-light btn-primary pull-right" href="javascript:void(0);">Vorschau anzeigen</a>
							</div>
			    		</div>

			  		</div>
			  	</div>

			  	<div id="osm-container">
		            <iframe src="http://karte.fau.de/api/v1/iframe/term/lft/zoomcontrol/off/layercontrol/off" width="724px" height="400px" seamless style="border: 0; padding: 0; margin: 0; overflow: hidden;"></iframe>
	            </div>
	    		
	    		<div class="page-header">
	                <h3 class="page-header"></h3>
	                <div class="pull-right">
						<span class="btn btn-prompt btn-light btn-success" data-prompt="Sind Sie sicher?" data-prompt-buttons="Nein|Ja" data-prompt-icons="remove|remove" data-callback="globaleFunktion">
							<i class="icon-ok icon-white"></i>
							<span class="title"> Einf&uuml;gen</span>
						</span>
			        </div>
	            </div>

			 </div>
	    </div>
    </body>
</html>