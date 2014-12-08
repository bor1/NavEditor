<?php
require_once('auth.php');

$custom_css_classes = '';
if($ne_config_info['custom_content_css_classes'] != '') {
	$custom_css_classes = array();
	$arr_cls = explode('|', $ne_config_info['custom_content_css_classes']);
	foreach($arr_cls as $ac) {
		array_push($custom_css_classes, $ac . '=' . $ac);
	}
	$custom_css_classes = implode(';', $custom_css_classes);
}

?>

    <div class="container">
		<div class="page-header">
			<h2 id="page-title" class="page-header">UnivIS-Integration <small>Personendaten für UnivIS-Plugin bearbeiten</small></h2>
        </div>

		<div class="row">
			<div id="confList" class="col-md-3">

				<div class="form-group">
					<h4>UnivIS-ID</h4>
					<input type="text" id="txtUnivISId" class="textBox form-control" style="margin-bottom: 5px;" placeholder="UnivIS-Org-Nr eintragen"/>

					<input type="button" id="btnSetUnivISId" class="btn btn-light btn-primary" value="setzen" />
				</div>
				<hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">
				<div class="form-group">
						<h4 class="help-block">Personen:</h4>
						<input type="button" id="btnAddNew" class="btn btn-light btn-success" style="margin-bottom: 10px;" value="Mitarbeiter hinzuf&uuml;gen" />
				</div>
				<ul></ul>

			</div>

			<div id="contentPanel2" class="col-md-9 page">
				<div class="page-header">
					<h4 class="page-header">Mitarbeiter bearbeiten</h4>
				</div>

				<form id="frmEdit" class="form-horizontal">
					<div class="row">
					<div class="col-md-9">
						<div id="fld_feedimport" class="form-group">
							<label class="col-md-2 control-label" for="txtFirstName">Vorname:</label>
							<div class="col-md-4">
								<input type="text" id="txtFirstName" name="txtFirstName" class="textBox form-control" />
							</div>
							<label class="col-md-2 control-label" for="txtLastName">Nachname:</label>
							<div class="col-md-4">
								<input type="text" id="txtLastName" name="txtLastName" class="textBox form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label" for="filAttachment">Foto:</label>
							<div class="col-md-7">
								<input type="file" id="filAttachment" name="filAttachment" class="textBox" />
								<img id="ajaxWaiting" src="ajax-loader.gif" border="0" style="width:16px;height:16px;border:0;display:none;" />
							</div>
							<div class="col-md-2 controls span">
								<input type="button" id="btnUpload" name="btnUpload" class="btn btn-light btn-primary" value="hochladen" />
							</div>
						</div>

						<p><a href="index.php?p=file_editor" target="_blank">Dateien manuell &uuml;ber den Dateimanager hochladen</a></p>
					</div>

					<div class="col-md-3">
						<img id="userPhoto" border="0" style="display:none;margin-bottom:10px;" />
					</div>
				</div>

					<textarea id="txtContent" name="txtContent" cols="120" rows="15" class="textBox"></textarea>
					<hr size="1" noshade="noshade" />
					<input type="button" id="btnUpdate" name="btnUpdate" value="Speichern" class="btn btn-success btn-light" style="margin: 5px" />
				</form>
			</div>
		</div>
	</div>
