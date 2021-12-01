<style type="text/css">
  .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
  }
  .modal-header {
    padding: 15px;
    border-bottom: 1px solid #e5e5e5;
    min-height: 16.42857143px;
    background-color: rgb(63,81,181);
    color: rgba(255,255,255,0.87);
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Opciones avanzadas</em>
    </div>
    <br>
    <p class="small-text">
      Agregue archivos adjuntos, configure si el contenido del correo debe postearse en las principales redes
      sociales una vez haya sido enviado, configure Google Analytics, etc. No es necesario configurar nada para
      realizar el envío del correo.
    </p>
  </div>
</div>
<div class="row" ng-cloak>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-info">
      <div class="body row">
        {% if attachment == true or customizedpdf == true %}
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <label class="small-text margin-top-15px">Adjuntar archivos pdf</label>
            <hr class="hr-classic">
            <div class="col-sm-12">
              <ul id="nav-tabs-wrapper" class="nav nav-tabs nav-tabs-horizontal">
                {% if attachment == true and customizedpdf == true%}
                  <li ng-class="fileadjunt.length > 0 ? 'active' : (fileadjuntpdf.files.length > 0 ? '' : 'active')"><a href="/#htab1" data-toggle="tab" ng-click="statusAttached('attachment')">Adjuntar archivos pdf</a></li>
                  <li ng-class="fileadjuntpdf.files.length > 0 ? 'active' : '' "><a href="/#htab2" data-toggle="tab" ng-click="statusAttached('customizedpdf')">Pdf Personalizado</a></li>
                {% elseif attachment == true and customizedpdf == false %}
                  <li class="active"><a href="/#htab1" data-toggle="tab">Adjuntar archivos pdf</a></li>
                {% elseif attachment == false and customizedpdf == true %}
                  <li class="active"><a href="/#htab2" data-toggle="tab">Pdf Personalizado</a></li>
                {% endif %}
              </ul>
              <div class="tab-content">
                {% if attachment == true and customizedpdf == true%}
                  <div role="tabpanel" ng-class="fileadjunt.length > 0 ? 'tab-pane fade in active' : (fileadjuntpdf.files.length > 0 ? 'tab-pane fade' : 'tab-pane fade in active')" id="htab1">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" >
                      {#<md-button class="btn btn-primary btn-xs " ng-click="showTabDialog($event)" >
                        Clic aqui
                      </md-button>#}
                      <button style='margin: 1em;' type="button" class="btn btn-primary" data-toggle="modal" data-target="#adjun">Adjuntar archivos </button>
                      {# <button style='margin: 1em;' type="button" class="btn btn-primary" ng-click="showTabDialog($event)">Adjuntar archivos pdf </button> #}
                      <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <h5>Maximo tamaño permitido: <strong>1.4 MB</strong></h5>
                        <h5>Tamaño de archivos: <strong>{{"{{sizeFiles/1024/1024|number:3}}"}} MB</strong></h5>
                      </div>

                      {#<button style='margin: 1em;' type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">Clic aqui</button>#}
                    </div>
                  </div>
                  <div role="tabpanel" ng-class="fileadjuntpdf.files.length > 0 ? 'tab-pane fade in active' : 'tab-pane fade' " id="htab2">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" >
                      <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" id="htab3">
                        <h1 class="sectiontitle">Cargar los archivos <strong>PDF</strong></h1>
                        <div class="bs-callout bs-callout-info" style="font-size: 1.1em;">
                          <p>
                          Aqui se cargan los archivos <strong>PDF:</strong>
                          </p>
                          <ol>
                            <li>Se deben comprimir todos los <strong>PDF's</strong> en un archivo <strong>ZIP.</strong></li>
                            <li>Hacer clic en el botón <strong>Seleccionar</strong> archivo y seleccionar el archivo <strong>ZIP</strong> con los <strong>PDF's.</strong></li>
                            <li>Hacer clic en el botón <strong>Cargar</strong> y esperar a que finalice el proceso.</li>
                          </ol>
                          si todo esta bien aparecerá un botón que dice <strong>Continuar</strong>, haga clic en él para seguir con el 
                          proceso.
                        </div>
                      </div>

                      <div class="row" id="next" style="display: none;">

                          <br>
                          <div ng-show="result.files.length > 0 ">
                            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Archivos encontrados en el servidor: <strong>{{'{{result.total}}'}}</strong>, clic aqui para ampliar la información
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="panel-body">
                                                <ol>
                                                    <div ng-repeat="file in result.files">
                                                        <li>{{'{{file.name}}'}}</li>
                                                    </div>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 text-right" id="buttons">
                                <a href="{{url('mail/compose')}}/{{'{{idMail}}'}}" class="btn btn-sm btn-default">Atrás</a>
                                <a href="{{url('mail/structurename')}}/{{'{{idMail}}'}}" class="btn btn-sm btn-success">Siguiente</a>
                            </div>
                          </div>   
                          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                            <div class="header-background"><br>
                              <p style="font-size: 1.4em;color: #5cb85c;font-weight: 600;">
                                {{'{{result.enunciadoFinal}}'}}
                              </p>
                            </div>
                          </div>
                      </div>

                      <div class="row" id="resume" style="display: none;">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="header-background">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Archivos encontrados en el servidor</th>
                                            <th>Archivos que coinciden con la estructura seleccionada</th>
                                            <th>Contactos totales en la lista</th>
                                            <th>Contactos totales que coinciden con al menos un pdf</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{'{{ result.totalfiles }}'}}</td>
                                            <td>{{'{{ result.totalfilematch }}'}}</td>
                                            <td>{{'{{ result.totalcontacts }}'}}</td>
                                            <td>{{'{{ result.totalcontactsmatch }}'}}</td>
                                            <td class="text-center">
                                              <button id="delete" ng-click="deleteAll(idMail)"  class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" >
                                                <md-tooltip md-direction="bottom">
                                                  Borrar este archivo
                                                </md-tooltip>
                                                <span class="glyphicon glyphicon-trash"></span>
                                              </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                      </div>

                      <div class="small-space"></div>
                      <ul id="filelist"></ul>
                      <div id="container">
                          <a id="pickfiles" href="javascript:;" class="btn btn-sm btn-primary">Selecciona archivo</a>
                          <a id="uploadfiles" href="javascript:;" class="btn btn-sm btn-success" >Cargar</a>
                      </div>
                    </div>

                  </dir> <!-- ================================ -->
                </div> 
                {% elseif attachment == true and customizedpdf == false %}
                  <div role="tabpanel" ng-class="tab-pane fade in active" id="htab1">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" >
                      {#<md-button class="btn btn-primary btn-xs " ng-click="showTabDialog($event)" >
                        Clic aqui
                      </md-button>#}
                      <button style='margin: 1em;' type="button" class="btn btn-primary" data-toggle="modal" data-target="#adjun">Adjuntar archivos  </button>
                      {# <button style='margin: 1em;' type="button" class="btn btn-primary" ng-click="showTabDialog($event)">Adjuntar archivos pdf </button> #}
                      <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <h5>Maximo tamaño permitido: <strong>1.4 MB</strong></h5>
                        <h5>Tamaño de archivos: <strong>{{"{{sizeFiles/1024/1024|number:3}}"}} MB</strong></h5>
                      </div>

                      {#<button style='margin: 1em;' type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">Clic aqui</button>#}
                    </div>
                  </div>
                {% elseif attachment == false and customizedpdf == true %}
                  <div role="tabpanel" ng-class="tab-pane fade in active" id="htab2">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" >
                      <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" id="htab3">
                        <h1 class="sectiontitle">Cargar los archivos <strong>PDF</strong></h1>
                        <div class="bs-callout bs-callout-info" style="font-size: 1.1em;">
                          <p>
                          Aqui se cargan los archivos <strong>PDF:</strong>
                          </p>
                          <ol>
                            <li>Se deben comprimir todos los <strong>PDF's</strong> en un archivo <strong>ZIP.</strong></li>
                            <li>Hacer clic en el botón <strong>Seleccionar</strong> archivo y seleccionar el archivo <strong>ZIP</strong> con los <strong>PDF's.</strong></li>
                            <li>Hacer clic en el botón <strong>Cargar</strong> y esperar a que finalice el proceso.</li>
                          </ol>
                          si todo esta bien aparecerá un botón que dice <strong>Continuar</strong>, haga clic en él para seguir con el 
                          proceso.
                        </div>
                      </div>

                      <div class="row" id="next" style="display: none;">

                          <br>
                          <div ng-show="result.files.length > 0 ">
                            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Archivos encontrados en el servidor: <strong>{{'{{result.total}}'}}</strong>, clic aqui para ampliar la información
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="panel-body">
                                                <ol>
                                                    <div ng-repeat="file in result.files">
                                                        <li>{{'{{file.name}}'}}</li>
                                                    </div>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 text-right" id="buttons">
                                <a href="{{url('mail/compose')}}/{{'{{idMail}}'}}" class="btn btn-sm btn-default">Atrás</a>
                                <a href="{{url('mail/structurename')}}/{{'{{idMail}}'}}" class="btn btn-sm btn-success">Siguiente</a>
                            </div>
                          </div>   
                          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                            <div class="header-background"><br>
                              <p style="font-size: 1.4em;color: #5cb85c;font-weight: 600;">
                                {{'{{result.enunciadoFinal}}'}}
                              </p>
                            </div>
                          </div>
                      </div>

                      <div class="row" id="resume" style="display: none;">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="header-background">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Archivos encontrados en el servidor</th>
                                            <th>Archivos que coinciden con la estructura seleccionada</th>
                                            <th>Contactos totales en la lista</th>
                                            <th>Contactos totales que coinciden con al menos un pdf</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{'{{ result.totalfiles }}'}}</td>
                                            <td>{{'{{ result.totalfilematch }}'}}</td>
                                            <td>{{'{{ result.totalcontacts }}'}}</td>
                                            <td>{{'{{ result.totalcontactsmatch }}'}}</td>
                                            <td class="text-center">
                                              <button id="delete" ng-click="deleteAll(idMail)"  class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" >
                                                <md-tooltip md-direction="bottom">
                                                  Borrar este archivo
                                                </md-tooltip>
                                                <span class="glyphicon glyphicon-trash"></span>
                                              </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                      </div>

                      <div class="small-space"></div>
                      <ul id="filelist"></ul>
                      <div id="container">
                          <a id="pickfiles" href="javascript:;" class="btn btn-sm btn-primary">Selecciona archivo</a>
                          <a id="uploadfiles" href="javascript:;" class="btn btn-sm btn-success" >Cargar</a>
                      </div>
                    </div>

                  </dir> <!-- ================================ -->
                </div> 
                {% endif %}
            </div>
            <!-- Trigger the modal with a button -->
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding lista-horizontal" >
              <md-progress-linear md-mode="query" ng-show="queryProgressAtachment" class="md-warn"></md-progress-linear>
              <ul ng-hide="!hidefile" style='margin: 1em;'>
                <li ng-repeat="key in fileadjunt">
                  {{"{{key.name}}"}} <span class="fa fa-times danger-no-hover" ng-click="deleteAttached(key.idMailattachment, $index)"></span>
                </li>
              </ul>
            </div>
            </div>
          </div>
        {% endif %}
        {# Aqui comienza lo de posteo #}
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <label class="small-text margin-top-15px">Redes sociales</label>
          <hr class="hr-classic">
  {#        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
              <md-switch class="md-warn" md-no-ink aria-label="Switch No Ink" ng-model="data.twitter">
              </md-switch>
            </div>
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 margin-top-15px">
              <span class="fa fa-twitter-square color-twitter"></span>
              <label for="">Postear en twitter</label>
              <p class="color-gray">Envía un tweet, una vez finalice el envío de la campaña.</p>
              <button class="btn button-twitter">Conectarse a twitter</button>
            </div>
          </div>#}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
              <md-switch class="md-warn"  aria-label="Switch No Ink" ng-model="appFacebook.facebook" ng-change="appFacebook.changeSwitch()">
              </md-switch>
            </div>
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 margin-top-15px">
              <span class="fa fa-facebook-official color-facebook"></span>
              <label for="">Postear en facebook</label>
              <p class="color-gray">Comparte el contenido del correo, una vez finalice el envío de la campaña.</p>
              <div ng-show="appFacebook.fanPageSelected && appFacebook.facebook">
                <img src="{{"{{appFacebook.fanPageSelected.picture}}"}}" class="img-circle margin-top padding-right-10px"/><a ng-href="https://www.facebook.com/{{"{{appFacebook.fanPageSelected.id}}"}}" >{{"{{appFacebook.fanPageSelected.name}}"}}</a>
                <div class="margin-top">
                  <label>Contenido de la publicación (Opcional)</label>
                  <textarea ng-model="appFacebook.descriptionPublish" rows="3" maxlength="2000" class="form-control"></textarea>
                  <div class="text-right" ng-class="appFacebook.descriptionPublish.length > 2000 ? 'negative':''">{{"{{appFacebook.descriptionPublish.length > 0 ?  appFacebook.descriptionPublish.length+'/2000':''}}"}}</div>
                </div>
                <button class="btn button-facebook margin-top"  ng-click="appFacebook.login()">Cambiar Fan Page</button>
              </div>
            </div>
          </div>
        </div>
        {# Aquí finaliza lo de posteo #}
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <label class="small-text margin-top-15px">Google Analytics</label>
          <hr class="hr-classic">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 adjust-icon none-padding">
              <md-switch class="md-warn" md-no-ink aria-label="Switch No Ink" ng-model="showLinksGoogleAnalytics" data-ng-change="clearDataGoogleAnalytics()">
              </md-switch>
            </div>
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 margin-top-15px">
              <span class="icon-google-analytics organize-icon"></span>
              <label for="">Añadir seguimiento de Google Analytics</label>
              <div ng-show="showLinksGoogleAnalytics" class="block block-info container-fluid " >
                <div class="space"></div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Nombre de la campaña:</label>
                    <span class="input hoshi input-default  col-sm-8">
                      <input type="text" ng-disabled="disabledSendDataGoogleAnalytics" class="undeline-input" ng-model="googleAnalytics.campaignName" minLength="2" maxLength="80"/>
                      {#<textarea class="form-control" data-ng-model="dataGoogleAnalytics.campaignDescription"></textarea>#}
                    </span>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-11 col-sm-offset-11 col-md-offset-11 col-lg-offset-11"  ng-class="dataGoogleAnalytics.campaignName.length > 80 ? 'negative':''">{{"{{dataGoogleAnalytics.campaignName.length > 0 ?  dataGoogleAnalytics.campaignName.length+'/80':''}}"}}</div>
                  </div>

                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Agregar seguimiento de Google Analitycs a los siguientes enlaces:</label>
                    <span class="input hoshi input-default  col-sm-8">
                      {#<select class="chosen" ng-model="dataGoogleAnalytics.link" multiple >
                        <option ng-repeat="links in googleAnalyticsLink" >{{"{{links.link}}"}}</option>
                      </select>#}
                      <ui-select multiple ng-model="googleAnalytics.links" ng-required="true" ui-select-required class='min-width-100'
                                 theme="select2" title="" sortable="false" close-on-select="true">
                        <ui-select-match >{{"{{$item}}"}}</ui-select-match>
                        <ui-select-choices repeat="key in googleAnalyticsLinks | propsFilter: {link: $select.search} track by $index">
                          <div ng-bind-html="key | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                    </span>
                  </div>
                </div>

                <div class="form-group text-right col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" style="width: 102%;">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <button type="button" ng-disabled="disabledSendDataGoogleAnalytics" class="button btn btn-small default-inverted" ng-click="clearDataGoogleAnalytics()">Limpiar Cambios</button>
                    <button type="button" ng-disabled="disabledSendDataGoogleAnalytics" class="button btn btn-small default-inverted" ng-click="discardChangesGoogleAnalytics()">Descartar Cambios</button>
                    <button type="button" ng-disabled="disabledSendDataGoogleAnalytics" class="button btn btn-small primary-inverted" ng-click="sendDataGoogleAnalytics()">Aplicar Cambios</button>
                  </div>
                </div> 

                <p class="color-gray col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">Haga seguimiento de los enlaces en el correo con Google analytics,
                  de esta manera podrá saber cuantas veces hicieron clic, cuanto tiempo estuvieron en
                  el sitio, desde donde llegaron, etc.</p>
              </div> 
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <label class="small-text margin-top-15px">Otras opciones</label>
          <hr class="hr-classic">
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 none-padding">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 adjust-icon none-padding">
              <md-switch class="md-warn" md-no-ink aria-label="Switch No Ink" ng-change="changeStatistics()" ng-model="dataadv.statistics">
              </md-switch>
            </div>
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 margin-top-15px">
              <label for="">Enviar estadísticas automáticamente a los siguientes correos:</label>
              <br><b id="advert-error-statistics">Máximo 8 correos electrónicos. </b><i class="input hoshi input-default">Ej: <b>ej1@aio.com, ej3@aio.com, ej3@aio.com</b></i>
              <textarea id="statisticsArea" class="form-control" rows="3" ng-model="dataadv.statisticsEmails" ng-change="validateEmailStatistics() " ng-readonly="!dataadv.statistics"></textarea>
              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-right-30px margin-top none-padding">

                <select class="form-control"  ng-model="dataadv.quantity" ng-disabled="!dataadv.statistics">  
                  <option ng-repeat="(key,value) in numbers" value="{{"{{key}}"}}">{{"{{value}}"}}</option>
                </select>
              </div>

              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-right-30px margin-top none-padding">
                <select class="form-control" ng-model="dataadv.typeTime" ng-disabled="!dataadv.statistics">
                  <option ng-repeat="(key,value) in typesTimes" value="{{"{{key}}"}}">{{"{{value}}"}}</option>

                </select>
              </div>
              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 margin-top-15px none-padding">
                <label><small>después de haberse enviado el correo</small></label>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 none-padding">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 adjust-icon none-padding">
              <md-switch class="md-warn" md-no-ink aria-label="Switch No Ink" ng-change="changeNotifications()" ng-model="dataadv.notifications">
              </md-switch>
            </div>
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 margin-top-15px">
              <label for="">Enviar notificaciones de envío a los siguientes correos electrónicos :</label>
              <br><b id="advert-error">Máximo 8 correos electrónicos. </b><i class="input hoshi input-default">Ej: <b>ej1@aio.com, ej3@aio.com, ej3@aio.com</b></i>
              <textarea id="notificationArea" class="form-control" rows="3" ng-model="dataadv.notificationEmails" ng-change="validateEmail() " ng-readonly="!dataadv.notifications"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="footer" align="right">
        <a href="{{ url('mail') }}"
           class="button btn danger-inverted"
           data-toggle="tooltip" data-placement="top" title="Salir">
          Salir
        </a>
        <a ui-sref="content({id:idMailGet})"
           class="button btn btn-small info-inverted"
           data-toggle="tooltip" data-placement="top" title="Atrás">
          Atrás
        </a>
        <button
          ng-click="saveAdvanceOptions()"
          class="button btn btn-small primary-inverted"
          data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
          Guardar y continuar
        </button>
      </div>
    </div>
  </div>
  <div id="cancelDialog" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>Al cambiar de tipo de adjunto se eliminará los archivos cargados.</h2>
        {#        <p></p>#}
        <div style="z-index: 999999;">
          <a ng-click="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a ng-click="saveAdjunt();" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="adjun" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Adjuntar archivos pdf</h4>
      </div>
      <div class="modal-body">
        <div role="tabpanel">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="/#adjun1" data-toggle="tab">GALERIA</a></li>
            <li><a href="/#adjun2" data-toggle="tab">CARGAR</a></li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="adjun1">
              <div id="pagination" class="text-center">
                <ul class="pagination">
                  <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b>{{ "{{gallery.total }}"}}</b> registros </span><span>Página <b>{{"{{ page }}"}}</b> de <b>{{ "{{ (gallery.total_pages ) }}"}}</b></span>
                  </li>
                  <li ng-class="page == (gallery.total_pages) || gallery.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li ng-class="page == (gallery.total_pages)  || gallery.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>
              <div class="main text-center" ng-init="url = '{{ url.get() }}' ">
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" ng-repeat="key in gallery[0].items" >
                    <div ng-if="key.type == 'File'">
                      <img ng-click="selectedAsset(key,$index)" style="width: 150px;" ng-style="key.class" id="{{'{{key.idAsset}}'}}" alt="{{'{{key.name}}'}}" ng-src="{{"{{ url + 'images/gallery/' + key.extension + '.png'}}"}}" alt="{{'{{key.name}}'}}"/>
                      <p>{{'{{key.name}}'}}</p>
                    </div>
                    <div ng-if="key.type == 'Image'"  >
                      <div>
                        <img ng-click="selectedAsset(key,$index)" style="width: 150px;" ng-style="key.class" id="{{'{{key.idAsset}}'}}" alt="{{'{{key.name}}'}}" ng-src="{{"{{ url + 'gallery/thumbnail/' + key.idAsset }}"}}" alt="{{'{{key.name}}'}}"/>
                      </div>
                      <p>{{'{{key.name}}'}}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div id="pagination" class="text-center">
                <ul class="pagination">
                  <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b>{{ "{{gallery.total }}"}}</b> registros </span><span>Página <b>{{"{{ page }}"}}</b> de <b>{{ "{{ (gallery.total_pages ) }}"}}</b></span>
                  </li>
                  <li ng-class="page == (gallery.total_pages) || gallery.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li ng-class="page == (gallery.total_pages)  || gallery.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="adjun2">
              <br>
              <div>
                <input type="file" nv-file-select="" accept=".pdf" uploader="uploader" multiple  /><br/>
              </div>
              <div  style="margin-bottom: 40px">
                <p>Cantidad de archivos seleccionados: {{'{{ uploader.queue.length }}'}}</p>
                <table class="table table-bordered"  >
                  <thead>
                    <tr>
                      <th width="50%">Nombre</th>
                      <th ng-show="uploader.isHTML5">Tamaño</th>
                      <th ng-show="uploader.isHTML5">Progreso</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="item in uploader.queue">
                      <td><strong>{{"{{ item.file.name }}"}}</strong></td>
                      <td ng-show="uploader.isHTML5" nowrap>{{"{{ item.file.size/1024/1024|number:2 }}"}} MB</td>
                      <td ng-show="uploader.isHTML5">
                        <div class="progress" style="margin-bottom: 0;">
                          <div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
                        </div>
                      </td>
                      <td class="text-center">
                        <span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
                        <span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
                        <span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>
                      </td>
                      {#<td nowrap>
                      <button type="button" class="btn success-inverted btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess">
                      <span class="glyphicon glyphicon-upload"></span> Adjuntar
                      </button>
                      <button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
                      <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
                      </button>
                      <button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()">
                      <span class="glyphicon glyphicon-trash"></span> Remover
                      </button>
                      </td>#}
                    </tr>
                  </tbody>
                </table>

                <div>
                  <div>

                    {#<div class="progress" style="">
                    <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
                    </div>#}
                  </div>
                  {#<button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length">
                   <span class="glyphicon glyphicon-upload"></span> Adjuntar todos
                   </button>
                   <button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
                   <span class="glyphicon glyphicon-ban-circle"></span> Cancelar todos
                   </button>
                   <button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
                   <span class="glyphicon glyphicon-trash"></span> Remover todos
                   </button>#}
                </div>

              </div>

            </div>
            <div class=row>
              <div class="col-lg-12 text-right">
                <button type="button" class="btn btn-default"  ng-click="answer()">Cerrar</button>
                <button type="button" class="btn btn-primary" ng-click="addFile()">Adjuntar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/ng-template" id="tabDialog.tmpl.html">
  <md-dialog aria-label="">
  <md-toolbar>
  <div class="md-toolbar-tools">
  <h4 class="modal-title" id="exampleModalLabel">Adjuntar archivos pdf</h4>
  <span flex></span>
  <md-button class="md-icon-button" ng-click="cancel()">
  <md-icon  aria-label="Close dialog"></md-icon>
  </md-button>
  </div>
  </md-toolbar>
  <md-dialog-content >
  {#  <md-dialog-content style="max-width:800px;max-height:810px; ">#}
  <md-tabs md-dynamic-height md-border-bottom>
  <md-tab label="Galeria">
  <md-content  style="min-width:1000px;overflow-x:hidden">
  <div id="pagination" class="text-center">
  <ul class="pagination">
  <li ng-class="page == 1 ? 'disabled'  : ''">
  <a   ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
  </li>
  <li  ng-class="page == 1 ? 'disabled'  : ''">
  <a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
  </li>
  <li>
  <span><b>{{ "{{gallery.total }}"}}
  </b> registros </span><span>Página <b>{{"{{ page }}"}}
  </b> de <b>
  {{ "{{ (gallery.total_pages ) }}"}}
  </b></span>
  </li>
  <li   ng-class="page == (gallery.total_pages) || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
  </li>
  <li   ng-class="page == (gallery.total_pages)  || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
  </li>
  </ul>
  </div>

  <div class="main text-center" ng-init="url = '{{ url.get() }}' ">
  <div class="row">
  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" ng-repeat="key in gallery[0].items" >
  <div ng-if="key.type == 'File'">
  <img   ng-click="selectedAsset(key,$index)" style="width: 150px;" ng-style="key.class" id="{{"{{key.idAsset}}"}}" alt="{{"{{key.name}}"}}" src="{{"{{ url + 'images/gallery/' + key.extension + '.png'}}"}}" alt="{{"{{key.name}}"}}"/>
  <p>{{"{{key.name}}"}}</p>
  </div>
  <div ng-if="key.type == 'Image'"  >
  {#  <img   ng-click="closeModal(key)" class="{{"{{key.idAsset}}"}}" alt="{{"{{key.name}}"}}" src="{{"{{ url + 'gallery/thumbnail/' + key.idAsset }}"}}" alt="{{"{{key.name}}"}}"/>#}
  <div>
  <img   ng-click="selectedAsset(key,$index)" style="width: 150px;" ng-style="key.class" id="{{"{{key.idAsset}}"}}" alt="{{"{{key.name}}"}}" src="{{"{{ url + 'gallery/thumbnail/' + key.idAsset }}"}}" alt="{{"{{key.name}}"}}"/>
  </div>
  <p>{{"{{key.name}}"}}</p>
  </div>
  </div>
  </div>
  </div>

  <div id="pagination" class="text-center">
  <ul class="pagination">
  <li ng-class="page == 1 ? 'disabled'  : ''">
  <a   ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
  </li>
  <li  ng-class="page == 1 ? 'disabled'  : ''">
  <a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
  </li>
  <li>
  <span><b>{{ "{{gallery.total }}"}}
  </b> registros </span><span>Página <b>{{"{{ page }}"}}
  </b> de <b>
  {{ "{{ (gallery.total_pages ) }}"}}
  </b></span>
  </li>
  <li   ng-class="page == (gallery.total_pages) || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
  </li>
  <li   ng-class="page == (gallery.total_pages)  || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
  </li>
  </ul>
  </div>

  </md-content>
  </md-tab>
  <md-tab label="Cargar">
  <md-content style="min-width:1000px">
  <br>
  <div>
  <input type="file" nv-file-select="" uploader="uploader" multiple  /><br/>
  </div>
  <div  style="margin-bottom: 40px">
  <p>Cantidad de archivos seleccionados: {{"{{ uploader.queue.length }}"}}</p>
  <table class="table table-bordered"  >
  <thead>
  <tr>
  <th width="50%">Nombre</th>
  <th ng-show="uploader.isHTML5">Tamaño</th>
  <th ng-show="uploader.isHTML5">Progreso</th>
  <th>Estado</th>
  {#<th>Acciones</th>#}
  </tr>
  </thead>
  <tbody>
  <tr ng-repeat="item in uploader.queue">
  <td><strong>{{"{{ item.file.name }}"}}</strong></td>
  <td ng-show="uploader.isHTML5" nowrap>{{"{{ item.file.size/1024/1024|number:2 }}"}} MB</td>
  <td ng-show="uploader.isHTML5">
  <div class="progress" style="margin-bottom: 0;">
  <div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
  </div>
  </td>
  <td class="text-center">
  <span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
  <span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
  <span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>
  </td>
  {#<td nowrap>
  <button type="button" class="btn success-inverted btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess">
  <span class="glyphicon glyphicon-upload"></span> Adjuntar
  </button>
  <button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
  <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
  </button>
  <button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()">
  <span class="glyphicon glyphicon-trash"></span> Remover
  </button>
  </td>#}
  </tr>
  </tbody>
  </table>

  <div>
  <div>

  {#<div class="progress" style="">
  <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
  </div>#}
  </div>
  {#<button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length">
   <span class="glyphicon glyphicon-upload"></span> Adjuntar todos
   </button>
   <button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
   <span class="glyphicon glyphicon-ban-circle"></span> Cancelar todos
   </button>
   <button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
   <span class="glyphicon glyphicon-trash"></span> Remover todos
   </button>#}
  </div>

  </div>
  </md-content>
  </md-tab>
  </md-tabs>

  <div class=row>
  <md-dialog-actions class="col-lg-12 text-right">
  <button type="button" class="btn btn-default"  ng-click="answer('not useful')">Cerrar</button>
  <button type="button" class="btn btn-primary" ng-click="addFile()">Adjuntar</button>

  </md-dialog-actions>
  </div>
  </md-dialog-content>



  </md-dialog>
</script>

<script type="text/ng-template" id="tabDialog.tmpl.html">
  <md-dialog aria-label="">
  <md-toolbar>
  <div class="md-toolbar-tools">
  <h4 class="modal-title" id="exampleModalLabel">Adjuntar archivos pdf</h4>
  <span flex></span>
  <md-button class="md-icon-button" ng-click="cancel()">
  <md-icon  aria-label="Close dialog"></md-icon>
  </md-button>
  </div>
  </md-toolbar>
  <md-dialog-content >
  {#810px; ">#}
  <md-tabs md-dynamic-height md-border-bottom>
  <md-tab label="Galeria">
  <md-content  style="min-width:1000px;overflow-x:hidden">
  <div id="pagination" class="text-center">
  <ul class="pagination">
  <li ng-class="page == 1 ? 'disabled'  : ''">
  <a   ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
  </li>
  <li  ng-class="page == 1 ? 'disabled'  : ''">
  <a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
  </li>
  <li>
  <span><b>{{ "{{gallery.total }}"}}
  </b> registros </span><span>Página <b>{{"{{ page }}"}}
  </b> de <b>
  {{ "{{ (gallery.total_pages ) }}"}}
  </b></span>
  </li>
  <li   ng-class="page == (gallery.total_pages) || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
  </li>
  <li   ng-class="page == (gallery.total_pages)  || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
  </li>
  </ul>
  </div>

  <div class="main text-center" ng-init="url = '{{ url.get() }}' ">
  <div class="row">
  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" ng-repeat="key in gallery[0].items" >
  <div ng-if="key.type == 'File'">
  <img   ng-click="selectedAsset(key,$index)" style="width: 150px;" ng-style="key.class" id="{{"{{key.idAsset}}"}}" alt="{{"{{key.name}}"}}" src="{{"{{ url + 'images/gallery/' + key.extension + '.png'}}"}}" alt="{{"{{key.name}}"}}"/>
  <p>{{"{{key.name}}"}}</p>
  </div>
  <div ng-if="key.type == 'Image'"  >
  {#  <img   ng-click="closeModal(key)" class="{{"{{key.idAsset}}"}}" alt="{{"{{key.name}}"}}" src="{{"{{ url + 'gallery/thumbnail/' + key.idAsset }}"}}" alt="{{"{{key.name}}"}}"/>#}
  <div>
  <img   ng-click="selectedAsset(key,$index)" style="width: 150px;" ng-style="key.class" id="{{"{{key.idAsset}}"}}" alt="{{"{{key.name}}"}}" src="{{"{{ url + 'gallery/thumbnail/' + key.idAsset }}"}}" alt="{{"{{key.name}}"}}"/>
  </div>
  <p>{{"{{key.name}}"}}</p>
  </div>
  </div>
  </div>
  </div>

  <div id="pagination" class="text-center">
  <ul class="pagination">
  <li ng-class="page == 1 ? 'disabled'  : ''">
  <a   ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
  </li>
  <li  ng-class="page == 1 ? 'disabled'  : ''">
  <a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
  </li>
  <li>
  <span><b>{{ "{{gallery.total }}"}}
  </b> registros </span><span>Página <b>{{"{{ page }}"}}
  </b> de <b>
  {{ "{{ (gallery.total_pages ) }}"}}
  </b></span>
  </li>
  <li   ng-class="page == (gallery.total_pages) || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
  </li>
  <li   ng-class="page == (gallery.total_pages)  || gallery.total_pages == 0 ? 'disabled'  : ''">
  <a ng-click="page == (gallery.total_pages)  || gallery.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
  </li>
  </ul>
  </div>

  </md-content>
  </md-tab>
  <md-tab label="Cargar">
  <md-content style="min-width:1000px">
  <br>
  <div>
  <input type="file" nv-file-select="" uploader="uploader" multiple  /><br/>
  </div>
  <div  style="margin-bottom: 40px">
  <p>Cantidad de archivos seleccionados: {{"{{ uploader.queue.length }}"}}</p>
  <table class="table table-bordered"  >
  <thead>
  <tr>
  <th width="50%">Nombre</th>
  <th ng-show="uploader.isHTML5">Tamaño</th>
  <th ng-show="uploader.isHTML5">Progreso</th>
  <th>Estado</th>
  {#<th>Acciones</th>#}
  </tr>
  </thead>
  <tbody>
  <tr ng-repeat="item in uploader.queue">
  <td><strong>{{"{{ item.file.name }}"}}</strong></td>
  <td ng-show="uploader.isHTML5" nowrap>{{"{{ item.file.size/1024/1024|number:2 }}"}} MB</td>
  <td ng-show="uploader.isHTML5">
  <div class="progress" style="margin-bottom: 0;">
  <div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
  </div>
  </td>
  <td class="text-center">
  <span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
  <span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
  <span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>
  </td>
  {#<td nowrap>
  <button type="button" class="btn success-inverted btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess">
  <span class="glyphicon glyphicon-upload"></span> Adjuntar
  </button>
  <button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
  <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
  </button>
  <button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()">
  <span class="glyphicon glyphicon-trash"></span> Remover
  </button>
  </td>#}
  </tr>
  </tbody>
  </table>

  <div>
  <div>

  {#<div class="progress" style="">
  <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
  </div>#}
  </div>
  {#<button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length">
   <span class="glyphicon glyphicon-upload"></span> Adjuntar todos
   </button>
   <button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
   <span class="glyphicon glyphicon-ban-circle"></span> Cancelar todos
   </button>
   <button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
   <span class="glyphicon glyphicon-trash"></span> Remover todos
   </button>#}
  </div>

  </div>
  </md-content>
  </md-tab>
  </md-tabs>

  <div class=row>
  <md-dialog-actions class="col-lg-12 text-right">
  <button type="button" class="btn btn-default"  ng-click="answer('not useful')">Cerrar</button>
  <button type="button" class="btn btn-primary" ng-click="addFile()">Adjuntar</button>

  </md-dialog-actions>
  </div>
  </md-dialog-content>



  </md-dialog>
</script>
