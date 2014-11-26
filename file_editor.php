<?php require_once('auth.php'); ?>

            <script id="file-details-template" type="text/x-handlebars-template">
                  <form class="form-horizontal">
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="inputFileName">Dateiname</label>
                    <div class="controls col-sm-10">
                      <input type="text" class="form-control" id="inputFileName" value="{{file_name}}">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="inputFileSize">Dateigr&ouml;&szlig;e</label>
                    <div class="controls col-sm-10">
                      <input id="inputFileSize" class="form-control" type="text" placeholder="{{file_size}} Byte" disabled>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="inputFileChanged">Ge&auml;ndert</label>
                    <div class="controls col-sm-10">
                      <input id="inputFileChanged" class="form-control" type="text" placeholder="{{modified_time}}" disabled>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="inputFileUrl">URL</label>
                    <div class="controls col-sm-10">
                      <input class="form-control" class="form-control" id="inputFileUrl" type="text" placeholder="{{url}}" disabled>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="inputFileThumb">Thumbnail</label>
                    <div class="controls col-sm-10">
                      <input class="form-control" class="form-control" id="inputFileThumb" type="text" placeholder="{{thumb_name}}" disabled>
                    </div>
                  </div>
                </form>
            </script>


            <script id="folder-details-template" type="text/x-handlebars-template">
                  <form class="form-horizontal">
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="inputFileName">Verzeichnisname</label>
                    <div class="controls col-sm-10">
                      <input type="text" id="inputFileName" class="form-control" value="{{verzeichnis}}">
                    </div>
                  </div>
                </form>
            </script>

            <script id="picture-preview-template" type="text/x-handlebars-template">

                  <div class="span3">
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

            <div class="page-header">
                <h2 class="page-header">Bilder und Dateien verwalten</h2>
            </div>  <!-- Page Header End -->

			<div id="edit_buttons_block" class="pull-right">

				<div class="popover-container">
					<a href="javascript:void(0);" class="btn btn-success btn-light" id="btnUpdate" name="btnUpdate" ><i class="glyphicon glyphicon-plus"></i> Hinzuf&uuml;gen</a>
					<div class="hover-popover"><div class="arrow"></div>
						<div class="header clearfix">
							<div class="pull-right">
								<a class="dismiss btn btn-primary btn-light" href="javascript:void(0);">Abbrechen</a>
							</div>
							<h4>Hinzuf&uuml;gen</h4>
						</div>
						<div class="content">
							<div class="tabbable"> <!-- Only required for left/right tabs -->
								<ul class="nav nav-tabs nav-tabs-custom">
                                    <li class="active"><a href="#upload" data-toggle="tab">Hochladen</a></li>
                                    <li><a href="#createFile" data-toggle="tab">Neue Datei</a></li>
                                    <li><a href="#createFolder" data-toggle="tab">Neuer Ordner</a></li>
									<li><a href="#archive" data-toggle="tab">Archiv</a></li>
								</ul>
								<div class="tab-content">
                                    <div class="tab-pane active" id="upload">
                                        <a id="file-list-add" class="span3 btn btn-large btn-success btn-light" href="javascript:void(0);">Datei ausw&auml;hlen</a>
                                        <!--hidden input field for file upload -->
                                        <input id="fileupload" type="file" multiple="" name="files[]" style="display: none">
                                    </div>
                                    <div class="tab-pane" id="createFile">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="inputFileCreateType">Typ</label>
                                                <div class="controls col-sm-8">
													<select class="form-control" id="inputFileCreateType">
														<option>.txt</option>
														<option>.conf</option>
														<option>.htaccess</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="inputFileCreateFileName">Dateiname</label>
                                                <div class="controls col-sm-8">
													<input type="text" class="form-control" id="inputFileCreateFileName">
                                                </div>
                                            </div>
                                            <a id="buttonFileCreate" class="btn btn-success btn-light" href="javascript:void(0);"><i class="glyphicon glyphicon-ok"></i>Erstellen</a>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="createFolder">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="inputFolderCreateFolderName">Ordnername</label>
                                                <div class="controls col-sm-8">
													<input type="text" class="form-control" id="inputFolderCreateFolderName">
                                                </div>
                                            </div>
                                            <a id="buttonFolderCreate" class="btn btn-success btn-light" href="javascript:void(0);"><i class="glyphicon glyphicon-ok"></i>Erstellen</a>
                                        </form>
                                    </div>
									<div class="tab-pane" id="archive">Vor&uuml;bergehend deaktiviert</div>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- End Popover Container "Hinzufügen" -->

				<div class="popover-container">
					<a id="rename-element" class="btn btn-warning btn-light" role="button" href="javascript:void(0);"><i class="glyphicon glyphicon-pencil"></i>Umbenennen</a>
				</div>

				<div class="popover-container">
					<a id="delete-element" class="fetch btn btn-danger btn-light" href="javascript:void(0);"><i class="glyphicon glyphicon-remove"></i>L&ouml;schen</a>
				</div>

			</div> <!-- End Edit Buttons Block -->

			<div class="row">

				<div id="file-tree-container" class="col-md-3">
					<h4>Ordnerstruktur</h4>
					<div id="file-tree"></div>
				</div>

                <div id="file-details-container" class="col-md-9 page">

					<h4 id="file-title"></h4>

					<div class="tabbable"> <!-- Only required for left/right tabs -->
						<ul class="nav nav-tabs nav-tabs-custom">
							<li class="active"><a href="#basis" data-toggle="tab">Informationen</a></li>
							<li><a href="#file-content" data-toggle="tab">Inhalt bearbeiten</a></li>
							<li><a href="#picture-preview" data-toggle="tab">Bildervorschau</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="basis">
								<div id="file-details">
									<form class="form-horizontal">
									</form>
								</div>
							</div>

							<div class="tab-pane" id="file-content">
								<div class="form-group">
									<textarea id="file-content-textarea" class="form-control textBox padding-top input-block-level" name="file-content-textarea" cols="160" rows="20"></textarea>
								</div>
								<button id="btnSaveEditedFile" class="btn btn-light btn-primary"><i class="glyphicon glyphicon-ok"></i>Speichern</button>
							</div>

							<div class="tab-pane" id="picture-preview">
							</div>
						</div>
					</div>
				</div>


            </div>
