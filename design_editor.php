<?php require_once('auth.php');?>

	<div class="container page" id="contentPanel1">

        <div class="page-header">
            <h3 class="page-header">Design</h3>
        </div>

        <div class="row">
        	<div class="col-md-6">
        		<form action="" method="post" name="frmEdit" id="frmEdit">
					<fieldset>
						<legend>Design ausw&auml;hlen</legend>
						<select id="selDesigns" class="form-control form-group"></select>

						<div id="previewImage" class="form-group"></div>
						<input type="button" id="btnUpdate" name="btnUpdate" value="Dieses Design aktivieren" class="btn btn-primary btn-light" />

					</fieldset>
				</form>
        	</div>
        	<div class="col-md-6">
        		<form id="frmKopf">
					<fieldset>
						<legend id="confDesignLegend">Design Konfigurieren</legend>
						<div id="settingsBlock" class="form-group">
						</div>
						<input type="button" id="btnUpdKopf" name="btnUpdKopf" class="btn btn-success btn-light" value="Einstellungen speichern" />
					</fieldset>
				</form>
        	</div>
        </div>


	</div>
