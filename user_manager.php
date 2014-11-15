    <div class="container">
        <div class="page-header">
            <h3>Benutzerverwaltung</h3>
        </div>

        <div  class="row" id="contentPanel1222">
            <div id="usermanager" >
                <div class="col-md-3">
                    <h4>Benutzer</h4>

                    <div id="userList" class="user-list"> </div>
                </div>
                <div class="col-md-9 page" id="userDetails">

                    <div class="page-header">
                        <h4 id='user_name_in_haeder' class="page-header"></h4>

<!--                        <div class="pull-right">
                                    <a class="dismiss btn btn-light btn-danger" href="javascript:void(0);"><i class="icon-white icon-trash"></i> Nutzer L&ouml;schen</a>

                        </div>-->
                    </div>

                    <div id="content" class="tabbable"> <!-- Only required for left/right tabs -->
                      <ul class="nav nav-tabs nav-tabs-custom">
                        <li class="active"><a href="#options" data-toggle="tab">Details bearbeiten</a></li>
                        <li><a href="#permissions" data-toggle="tab">Berechtigungen einstellen</a></li>
                      </ul>
                      <div class="tab-content">
                        <div class="tab-pane active" id="options">
                            <div id="userOptions" class="form-horizontal"></div>
                        </div>
                        <div class="tab-pane" id="permissions">
                            <div id="userPermission"></div>
                        </div>
                        <hr/>
                        <div class="span btn-group" id="operButtons"></div>

                      </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

