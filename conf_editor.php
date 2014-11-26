<?php require_once('auth.php');?>

	<div class="container" id="wrapper">

		<div class="page-header">
            <h2 class="page-header">Konfigurationsdateien bearbeiten</h2>
        </div>

		<div class="row">

			<div class="col-md-3" id="confList">
				<h4>Konfigurationsdateien</h4>
					<ul></ul>
			</div>

			<div class="col-md-9 page" id="contentPanel2">
				<form id="frmEdit" class="form-horizontal">
					<fieldset id="fld_feedimport">
						<legend>feedimport.conf</legend>
						<div id="feedimport">Loading...<div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<div id="feedimportOpts"></div>
						<div id="feedimportGenItems"><div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnAddFeedBox" value="Feed einf&uuml;gen" class="btn btn-primary btn-light" style="margin:5px;" />
						<input type="button" id="btnAddItemFeedImport" value="Konfig-Eintrag einf&uuml;gen" class="btn btn-primary btn-light" style="margin:5px;" />
						<input type="button" id="btnUpdFeedImport" value="Speichern" class="btn btn-success btn-light" style="margin:5px;" />
					</fieldset>

					<fieldset id="fld_website">
						<legend>website.conf</legend>
						<div id="websiteContainer">Loading...</div>
						<div id="genWebItemsContainer"><div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnWebAddConf" value="Eintrag einf&uuml;gen" class="btn btn-primary btn-light" />
						<input type="button" id="btnWebUpdConf" value="Nur Variablen speichern" class="btn btn-warning btn-light" />
						<br><br>
						<input type="button" id="btnUpdate" name="btnUpdate" value="Vorlagen aktualisieren" class="btn btn-success btn-light" />
						<input type="button" id="btnUpdateExisted" name="btnUpdateExisted" value="Existierende Seiten aktualisieren" class="btn btn-success btn-light" />
						<input type="hidden" id="selTempl" name="selTempl" value="seitenvorlage.html">
						<input id="save_osm" type="button" class="btn btn-primary btn-light" name="submit" value="Kontaktseite erstellen" />
					</fieldset>

					<fieldset id="fld_common">
						<legend></legend>
						<div id="container">Loading...</div>
						<div id="genItemsContainer"><div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnAddConf" value="Eintrag einf&uuml;gen" class="btn btn-primary btn-light" />
						<input type="button" id="btnUpdConf" value="Speichern" class="btn btn-success btn-light" />
					</fieldset>

					<fieldset id="fld_vorlagen">
						<legend>vorlagen.conf</legend>
						<div id="vorlagen">Loading...</div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnUpdVorlagen" value="Speichern" class="btn btn-success btn-light" />
					</fieldset>

					<fieldset id="fld_htusers">
						<legend>.htusers</legend>
						<div id="htusers"></div>
						<hr size="1" noshade="noshade" />
						<div class="form-group">
							<label for="txtNewHtUserName" class="col-md-2 control-label">Username</label>
							<div class="col-sm-3">
								<input type="text" id="txtNewHtUserName" class="textBox form-control" />
							</div>
							<label for="txtNewHtUserPass" class="col-md-2 control-label">Password</label>
							<div class="col-sm-3">
								<input type="password" id="txtNewHtUserPass" class="txtBox form-control" />
							</div>
							<input type="button" id="btnAddNewHtUser" value="Hinzufügen" class="btn btn-success btn-light" />
						</div>
					</fieldset>

					<fieldset id="fld_hthosts">
						<legend>hthosts</legend>
						<div id="hthosts"></div>
						<hr size="1" noshade="noshade" />
						<div class="form-group">
							<label for="txtNewHtHost" class="col-md-2 control-label">Domain/IP</label>
							<div class="col-sm-5">
								<input type="text" id="txtNewHtHost" class="textBox form-control" />
							</div>
							<input type="button" id="btnAddNewHtHost" value="Hinzufügen" class="btn btn-success btn-light" />
						</div>
					</fieldset>
				</form>
			</div>
		</div>

	</div>
