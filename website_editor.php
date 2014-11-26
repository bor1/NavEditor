<?php require_once('auth.php');?>

        <div class="container page" id="wrapper">

            <div class="page-header">
                <h2 class="page-header">Webseitendaten bearbeiten</h2>
            </div>


            <div class="tabbable"> <!-- Only required for left/right tabs -->
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="active"><a href="#logo" data-toggle="tab">Logo bearbeiten</a></li>
                    <li><a href="#osm" data-toggle="tab">OpenStreetMap-Kontaktseite erstellen</a></li>
                </ul>
                <div class="tab-content">

					<div class="tab-pane active" id="logo">
                        <form action="" method="post" name="frmEdit" id="frmEdit" class="form-horizontal">

                            <input type="hidden" id="selTempl" name="selTempl" value="seitenvorlage.html">

							<div class="form-group">
								<label class="col-sm-3 control-label" for="name-des-Webauftritts">Name des Webauftritts:</label>
								<div class="col-sm-9">
									<input type="text" id="name-des-Webauftritts" class="form-control" name="name-des-Webauftritts" size="40" />
								</div>
								<div class="col-sm-9 col-sm-offset-3">
									<label class="checkbox">
										<input type="checkbox" id="chkAllowHtml" name="chkAllowHtml" value="ja"> Eigene HTML-Anweisungen zulassen
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="titel-des-Webauftritts">Titel des Webauftritts:<br />(erscheint im Browser-Tab)</label>
								<div class="input-append col-sm-6">
									<input type="text" id="titel-des-Webauftritts" class="form-control" name="titel-des-Webauftritts" size="40" />
								</div>
								<div class="col-sm-3">
									<a class="btn btn-primary btn-light" id="btnCopySiteName">Aus Name kopieren</a>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="kurzbeschreibung-zum-Webauftritt">Kurzbeschreibung zum Webauftritt:</label>
								<div class="col-sm-9">
									<input type="text" id="kurzbeschreibung-zum-Webauftritt" class="form-control" name="kurzbeschreibung-zum-Webauftritt" size="60" />
								</div>
							</div>

							<hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">
							<div id="bildBlock">
								<div class="form-group">
									<label class="col-sm-3 control-label"for="logo-URL">Bild f&uuml;r das Logo (Optional):</label>
									<div class="input-append col-sm-6">
										<input type="text" id="logo-URL" class="form-control" name="logo-URL" size="40" />
									</div>
									<div class="col-sm-3">
										<a class="btn btn-primary btn-light" href="index.php?p=file_editor" target="_blank">Bild hochladen</a>
									</div>
									<div class="col-sm-9 col-sm-offset-3">
										<div id="imgPrev"></div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="logo-Alt">Alternativer Text f&uuml;r das Bildlogo (falls die Grafik nicht angezeigt oder angesehen werden kann):</label>
								<div class="col-sm-9">
									<input type="text" id="logo-Alt" class="form-control" name="logo-Alt" size="40" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="logo-Height">Bildh&ouml;he (optional):</label>
								<div class="col-sm-3">
									<input type="text" id="logo-Height" name="logo-Height" class="col-sm-4 form-control" size="40" />
								</div>
								<label class="col-sm-2 control-label" for="logo-Width">Bildbreite (optional):</label>
								<div class="col-sm-3">
									<input type="text" id="logo-Width" name="logo-Width" class="col-sm-4 form-control" size="40" />
								</div>
							</div>

							<div class="form-footer">
								<input type="button" id="btnUpdate" name="btnUpdate" value="Seitenvorlage aktualisieren" class="btn btn-light btn-large btn-success" />
								<input type="button" id="btnUpdateExisted" name="btnUpdateExisted" value="Existierende Seiten aktualisieren" class="btn btn-light btn-large btn-success" />
								<img id="ajaxLoader" alt="please wait..." src="ajax-loader.gif" border="0" width="16" height="16" style="display:none;" />
							</div>
						</form>
					</div>

                    <div class="tab-pane" id="osm">
                        <!-- Kontakt Block ab hier -->
                        <script type="text/javascript">
                            <!--
                            var helpText = "";

                            $(document).ready(function() {
                                // $("#btnLoadConf").click(function() {
                                // $.get("app/load_osm.php", function(data) {
                                // var arrValues = data.split('\\:\\');
                                // var n = 1;
                                // $.each(
                                // arrValues, function( intIndex, objValue){
                                // $("#" + n).attr("value", objValue);
                                // n++;
                                // }
                                // );
                                // var lat = arrValues[9];
                                // var lon = arrValues[10];
                                // setCenter(lat, lon);
                                // });
                                // });

                                $("#save_osm").click(function() {
                                    loading(true);
                                    var inst = $("#name").attr("value");
                                    var street = $("#strasse").attr("value");
                                    var plz = $("#plz").attr("value");
                                    var city = $("#ort").attr("value");
                                    var personname = $("#kontakt1-name").attr("value");
                                    var personvorname = $("#kontakt1-vorname").attr("value");
                                    var telefon = $("#telefon").attr("value");
                                    var fax = $("#fax").attr("value");
                                    var email = $("#email").attr("value");

                                    $.post("app/save_osm.php", { inst: inst, street: street, plz: plz, city: city, personname: personname, personvorname: personvorname, telefon: telefon, fax: fax, email: email}, function(resp) {
                                        saveVars("kontakt.shtml wurde erstellt");
                                    });
                                });
                            });
                            -->
                        </script>
<!--                        <form action="" method="post" name="frmEdit" id="frmEdit">
                            <fieldset>
                                <legend>OpenStreetMap-Kontaktseite erstellen</legend>
                            </fieldset>
                        </form>-->
                        <form action="" method="" id="suche" class="form-horizontal">
                            <div class="form-group">
								<label for="name" class="col-sm-2 control-label">Institut</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="name" name="inst" />
								</div>
							</div>
                            <div class="form-group">
								<label for="strasse" class="col-sm-2 control-label">Stra&szlig;e</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="strasse" name="street" />
								</div>
                            </div>
                            <div class="form-group">
								<label class="col-sm-2 control-label">PLZ, Ort</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="plz" name="plz" />
								</div>
                                <div class="col-sm-4">
									<input type="text" class="form-control" id="ort" name="city" />
								</div>
                            </div>
                            <div class="form-group">
								<label class="col-sm-2 control-label">Kontaktperson<br />(Name, Vorname)</label>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="kontakt1-name" name="person-name" />
								</div>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="kontakt1-vorname" name="person-vorname" />
								</div>
							</div>
                            <div class="form-group">
								<label for="telefon" class="col-sm-2 control-label">Telefon</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="telefon" name="telefon" />
								</div>
                            </div>
                            <div class="form-group">
								<label for="fax" class="col-sm-2 control-label">Fax</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="fax" name="fax" />
								</div>
                            </div>
                            <div class="form-group">
								<label for="email" class="col-sm-2 control-label">E-Mail</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="email" name="email" />
								</div>
                            </div>
                            <div class="form-footer">
                                <input id="save_osm" type="button" class="submit btn btn-light btn-large btn-success" name="submit" value="Kontaktseite erstellen" />
                                <input id="btnSaveall" type="button" class="submit btn btn-light btn-large btn-success" name="submit" value="Nur Variablen speichern" />
                            </div>
						</form>
					</div>
				</div>
			</div>
		</div>
