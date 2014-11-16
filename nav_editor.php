<?php require_once("auth.php")?>

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

        <div class="page-header">
            <h2 id="page-title" class="page-header">Seite und Navigation <small>Bearbeiten Sie hier Ihre Internetpräsenz</small></h2>
        </div>
        <!--=======================================================================================-->
        <!--================================== Conten ab hier =====================================-->
        <!--=======================================================================================-->

        <div class="row">

            <!--==== Menue ====-->
            <div class="col-md-3">
                <div id="menue-sidebar">

                </div>

                <hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">

                <span class="btn btn-prompt btn-light btn-success" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Nein|Abbrechen" data-prompt-icons="ok|remove|" data-callback="globaleFunktion">
                    <i class="glyphicon glyphicon-ok"></i>
                    <span class="title"> Speichern</span>
                </span>
            </div>
            <!--==== Menue Ende ====-->

            <!--==== Hauptteil ====-->
            <div class="col-md-9 page" >

                 <div class="page-header">
                    <h4 id="file-title" class="page-header"><small class="path"></small>Startseite</h4>

                    <div class="pull-right">

                        <span class="btn btn-prompt btn-light btn-success" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Nein|Abbrechen" data-prompt-icons="ok|remove|" data-callback="globaleFunktion">
                            <i class="glyphicon glyphicon-ok"></i>
                            <span class="title"> Speichern</span>
                        </span>

                        <span class="btn btn-prompt btn-light btn-danger" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Nein" data-prompt-icons="ok|remove"  data-callback="globaleFunktion">
                            <i class="glyphicon glyphicon-remove"></i>
                            <span class="title"> Seite l&ouml;schen</span>
                        </span>

                    </div>
                </div>

                <div class="tabbable"> <!-- Only required for left/right tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom">
                        <li class="active"><a href="#edit" data-toggle="tab"><i class="icon-pencil"></i> Seite bearbeiten</a></li>
                        <li><a href="#options" data-toggle="tab"><i class="icon-list-alt"></i> Optionen</a></li>
                        <li><a href="#recover" data-toggle="tab"><i class="icon-hdd"></i> Wiederherstellen</a></li>
                      </ul>
                      <div class="tab-content">
                        <div class="tab-pane active" id="edit">
                            <textarea class="padding-top" name="txtContent" cols="160" rows="25" ></textarea>
                        </div>

                        <div class="tab-pane" id="options">
                            <form class="form-horizontal">
                              <div class="form-group">
                                <label class="col-sm-2 control-label " for="inputTitel">Titel mit HTML</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputTitel" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputAlias">Alias</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputAlias" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputInfotext">Infotext</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputInfotext" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputUrl">URL</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputUrl" class="form-control" placeholder="">
                                </div>
                            </div>
                            <hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputIcon">Icon</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputIcon" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputIconAlt">Icon Alt Text</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputIconAlt" class="form-control" placeholder="">
                                        </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputIconTitle">Icon Titel</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputIconTitle" class="form-control" placeholder="">
                                </div>
                            </div>
                            </form>
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


