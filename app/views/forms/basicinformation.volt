<style>
  .btn-preview{
    margin-left: 5px;
    margin-top: 3px;
  }
  .width-84{
    width: 84% !important;
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Información básica del formulario</em>
    </div>
    <br>
    {#<p class="small-text">
      ---
    </p>#}
  </div>
</div>

<div class="row">
  {#  <form class="form-horizontal" ng-submit="basicInformationRegister()">#}
  <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap col-lg-offset-2 col-md-offset-2">
    <div class="block block-info">
      <div class="body row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" style="padding-left: 8%">
          <div class="row">
            <div class="margin-top">
              <label class=" text-right">*Nombre</label>
              <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                <span class="input hoshi input-default">
                  <input type="text" maxlength="50" placeholder="*Nombre" class="undeline-input "
                         ng-model="data.name" required>
                </span>
              </div>

            </div>
            {#<div style="position: absolute;" class="info_cointainer">
              <div class="cuerpo arriba-izquierda">Este nombre es para su uso personal, no le aparecerá a nadie más.</div>
            </div>#}
          </div>
          <div class="row">
            <div class="margin-top">
              <label class=" text-right">*Categoría</label>
              <span class="fa fa-info-circle color-gray drop_info" title="Categoría"></span>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 none-padding">
                  <div data-ng-show="showInputCate">
                    <input placeholder="*Categoría" data-ng-model="nameCategory" maxlength="200" class="undeline-input">
                  </div>
                  <div data-ng-show="showSelectCate">
                    <ui-select ng-model="data.idFormCategory"  ng-required="true"ui-select-required theme="select2" sortable="false"
                               close-on-select="true">
                      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                      <ui-select-choices repeat="key.idFormCategory as key in category | propsFilter: {name: $select.search}"
                                         >
                        <div ng-bind-html="key.name | highlight: $select.search"></div>
                      </ui-select-choices>
                    </ui-select>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top">
                  <div data-ng-show="showIconsCate">
                    <a class="color-primary" data-ng-click="changeStatusInputCate()" href=""><span
                        class="fa fa-plus " title="Agregar otro nombre"></span></a>
                  </div>
                  <div data-ng-show="showIconsSaveCate">
                    <a class="negative" data-ng-click="changeStatusInputCate()" href=""><span
                        class="glyphicon glyphicon-remove"
                        title="Cancelar"></span></a>
                    <a class="positive" data-ng-click="saveCategory()" href=""><span
                        class="glyphicon glyphicon-ok margin-left-10"
                        title="Guardar"></span></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="margin-top">
              <label class=" text-right">Descripción</label>
              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
              <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11 none-padding">
                <span class="input hoshi input-default">
                  <textarea  cols="50" placeholder="Descripción" class="undeline-input "
                             ng-model="data.description" required></textarea>
                </span>
              </div>
            </div>
            {#      <div style="position: absolute;" class="info_cointainer">
                    <div class="cuerpo arriba-izquierda">Este nombre es para su uso personal, no le aparecerá a nadie más.</div>
                  </div>#}
          </div>
          <div class="row">
            <div class="margin-top">
              <label class=" text-right">Página de registro exitoso</label>
              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
              <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11 none-padding">
                <span class="input hoshi input-default">
                  <input type="text" placeholder="Página de registro exitoso" class="undeline-input "
                         ng-model="data.successUrl" required>
                </span>
              </div>
            </div>
            {#      <div style="position: absolute;" class="info_cointainer">
                    <div class="cuerpo arriba-izquierda">Este nombre es para su uso personal, no le aparecerá a nadie más.</div>
                  </div>#}
          </div>
          <div class="row">
            <div class="margin-top">
              <label class=" text-right">Página de error en el registro</label>
              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
              <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11 none-padding">
                <span class="input hoshi input-default">
                  <input type="text" placeholder="Pagina de error en el registro" class="undeline-input "
                         ng-model="data.errorUrl" required>
                </span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 none-padding">
              <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  none-padding">
                <md-switch class="md-warn none-margin-left" md-no-ink aria-label="Switch No Ink"
                           ng-model="data.doubleOptin.active" data-toggle="collapse" data-target="#divdoubleOptin" 
                           aria-expanded="false" aria-controls="divdoubleOptin" >
                </md-switch>
              </div>
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                <label class="margin-top-15px cursor"  >
                  Doble optin
                </label>
                <span class="fa fa-info-circle color-gray drop_info" title="Puedes confirmar la suscripción realizada y a su vez una verificación del correo registrado, al activar esta opción, solo quedan activos los usuarios que confirmen."></span>
              </div>
            </div>
          </div>

          <div id="divdoubleOptin" class="collapse" >
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-lg-offset-1" >
              <div class="form-inline margin-top " >
                <label class=" text-right">*Asunto:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="text" class="form-control undeline-input width-84"  placeholder="Asunto" 
                         ng-model="data.doubleOptin.subject" required>
                </div>
              </div>
              <div class="form-inline margin-top ">
                <label class=" text-right">*Remitente:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="text" class="form-control undeline-input"  placeholder="Nombre" 
                         ng-model="data.doubleOptin.nameSender" required>
                  <input type="text" class="form-control undeline-input"  placeholder="example@test.com" 
                         ng-model="data.doubleOptin.emailSender" required>
                </div>
              </div>
              <div class="form-inline margin-top ">
                <label class=" text-right">Responder a:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="text" class="form-control undeline-input width-84"  placeholder="Nombre" 
                         ng-model="data.doubleOptin.replyTo" required>
                </div>
              </div>
              <div class="form-inline margin-top ">
                <label class=" text-right">*Plantilla:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <ui-select ng-model="data.doubleOptin.idMailTemplate" ng-required="true"
                             style="width: 77% !important"
                             ui-select-required theme="select2" sortable="false"
                             close-on-select="true" >
                    <ui-select-match
                      placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices
                      repeat="key.idMailTemplate as key in mailtemplate | propsFilter: {name: $select.search}"
                      refresh="getMailTemplate($select.search)"
                      refresh-delay="0">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>     
                  <button type="button" class="btn btn-small success-inverted " data-toggle="modal" data-target="#myModal"
                          data-ng-click="previewmailtempcont(data.doubleOptin.idMailTemplate)" >
                    <span class="fa fa-eye" title="Previsualizar"></span>
                  </button> 
                </div>
              </div>
              <!--<div class="form-inline margin-top ">
                <label class=" text-right">*URL de bienvenida:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="url" class="form-control undeline-input width-84"  placeholder="URL de bienvenida" 
                         ng-model="data.doubleOptin.urlSuccess" required>
                </div>
              </div>-->
            </div>
          </div>

          <div class="row">
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 none-padding">
              <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  none-padding">
                <md-switch class="md-warn none-margin-left" md-no-ink aria-label="Switch No Ink"
                           data-toggle="collapse" data-target="#divwelcome" 
                           aria-expanded="false" aria-controls="divwelcome"
                           ng-model="data.mailWelcome.active">
                </md-switch>
              </div>
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                <label class="margin-top-15px">
                  Correo de bienvenida
                </label>
                <span class="fa fa-info-circle color-gray drop_info" title="Este es un mensaje de bienvenida que recibirán las personas que se suscribieron."></span>
              </div>
            </div>
          </div>
          <div id="divwelcome" class="collapse" >
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-lg-offset-1" >
              <div class="form-inline margin-top " >
                <label class=" text-right">*Asunto:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="text" class=" undeline-input width-84"  placeholder="Asunto" 
                         ng-model="data.mailWelcome.subject" required>
                </div>
              </div>
              <div class="form-inline margin-top ">
                <label class=" text-right">*Remitente:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="text" class="form-control undeline-input"  placeholder="Nombre" 
                         ng-model="data.mailWelcome.nameSender" required>
                  <input type="text" class="form-control undeline-input"  placeholder="example@test.com" 
                         ng-model="data.mailWelcome.emailSender" required>
                </div>
              </div>
              <div class="form-inline margin-top ">
                <label class=" text-right">Responder a:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <input type="text" class="form-control undeline-input width-84"  placeholder="Nombre" 
                         ng-model="data.mailWelcome.replyTo" required>
                </div>
              </div>
              <div class="form-inline margin-top ">
                <label class=" text-right">*Plantilla:</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <ui-select ng-model="data.mailWelcome.idMailTemplate" ng-required="true"
                             ui-select-required theme="select2" sortable="false"
                             style="width: 77% !important"
                             close-on-select="true" >
                    <ui-select-match
                      placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices
                      repeat="key.idMailTemplate as key in mailtemplate | propsFilter: {name: $select.search}"
                      refresh="getMailTemplate($select.search)"
                      refresh-delay="0">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                  <button type="button" class="btn btn-small success-inverted btn-preview" data-toggle="modal" data-target="#myModal"
                          data-ng-click="previewmailtempcont(data.mailWelcome.idMailTemplate)" >
                    <span class="fa fa-eye" title="Previsualizar"></span>
                  </button>   
                </div>
              </div>
            </div>
          </div>

          <!-- <div class="row">
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 none-padding">
              <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  none-padding">
                <md-switch class="md-warn none-margin-left" md-no-ink aria-label="Switch No Ink"
                           data-toggle="collapse" data-target="#divnotification" 
                           aria-expanded="false" aria-controls="divnotification"
                           ng-model="data.notification.active">
                </md-switch>
              </div>
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                <label class="margin-top-15px">
                  Notificar a
                </label>
                <span class="fa fa-info-circle color-gray drop_info" title="Una vez exista un nuevo suscriptor, llegará a tu correo los datos de la persona que se suscribió. "></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div id="divnotification" class="collapse" >
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-lg-offset-1" >
                <div class="form-inline margin-top " >
                  <label class=" text-right">*Asunto:</label>
                  <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                  <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <input type="text" class="form-control undeline-input width-84"  placeholder="Asunto" 
                           ng-model="data.notification.subject" required>
                  </div>
                </div>
                <div class="form-inline margin-top ">
                  <label class=" text-right">*Remitente:</label>
                  <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                  <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <input type="text" class="form-control undeline-input"  placeholder="Nombre" 
                           ng-model="data.notification.nameSender" required>
                    <input type="text" class="form-control undeline-input"  placeholder="example@test.com" 
                           ng-model="data.notification.emailSender" required>
                  </div>
                </div>
                <div class="form-inline margin-top ">
                  <label class=" text-right">Responder a:</label>
                  <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                  <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <input type="text" class="form-control undeline-input width-84"  placeholder="Nombre" 
                           ng-model="data.notification.replyTo" required>
                  </div>
                </div>
                <div class="form-inline margin-top ">
                  <label class=" text-right">*Plantilla:</label>
                  <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                  <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <ui-select ng-model="data.notification.idMailTemplate" ng-required="true"
                               ui-select-required theme="select2" sortable="false"
                               style="width: 77% !important"
                               close-on-select="true" >
                      <ui-select-match
                        placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                      <ui-select-choices
                        repeat="key.idMailTemplate as key in mailtemplate | propsFilter: {name: $select.search}"
                        refresh="getMailTemplate($select.search)"
                        refresh-delay="0">
                        <div ng-bind-html="key.name | highlight: $select.search"></div>
                      </ui-select-choices>
                    </ui-select>
                    <button type="button" class="btn btn-small success-inverted btn-preview" data-toggle="modal" data-target="#myModal"
                            data-ng-click="previewmailtempcont(data.notification.idMailTemplate)" >
                      <span class="fa fa-eye" title="Previsualizar"></span>
                    </button>    
                  </div>
                </div>
                <div class="form-inline margin-top ">
                  <label class=" text-right">*Notificar a:</label>
                  <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
                  <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <textarea class="form-control undeline-input width-84"  placeholder="Correos a los que desea recibir notificaciones" 
                              ng-model="data.notification.emails" required cols="50">
                    </textarea>
                  </div>
                </div>
              </div>
            </div>
          </div> -->

          <div class="row">
            <div class="margin-top" >
              <label class=" text-right">*Lista de contactos</label>
              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                <ui-select  on-select='selectAction()'   ng-model="data.idContactlist" ng-required="true"  ui-select-required  class='min-width-100' 
                            theme="select2" title=""  sortable="false" close-on-select="true">
                  <ui-select-match>{{"{{$select.selected.name}}"}}</ui-select-match>
                  <ui-select-choices repeat="key.idContactlist as key in contactlist | propsFilter: {name: $select.search}">
                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="margin-top" >
              <label class=" text-right">Habeas data</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                <textarea class="form-control" ng-model="data.habeasData" row="4"></textarea>
              </div>
            </div>
          </div>

          {#      <div style="position: absolute;" class="info_cointainer">
                  <div class="cuerpo arriba-izquierda">Este nombre es para su uso personal, no le aparecerá a nadie más.</div>
                </div>#}
        </div>
      </div>
      <div class="footer row none-margin">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right col-lg-offset-6 col-md-offset-6">
{#          <a href="{{ url('forms') }}"#}
          <a ui-sref="list()"
             class="button btn btn-small danger-inverted"
             data-toggle="tooltip" data-placement="top" title="Cancelar">
            Cancelar
          </a>
          <button class="button btn btn-small success-inverted"
                  ng-click="saveBasicInformation()"
                  title="Guardar y continuar">
            Guardar y continuar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-prevew-width">
    <div class="modal-content modal-prevew-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h1 class="modal-title" id="myModalLabel">Previsualización</h1>
      </div>
      <div class="modal-body modal-prevew-body" id="preview-modal" style="height: 550px;"></div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="button btn btn-sm danger-inverted">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function () {
    $(".drop_info").mouseover(function () {
      $(this).parent().parent().find('.info_cointainer').show();
    });
    $(".drop_info").mouseout(function () {
      $(this).parent().parent().find('.info_cointainer').hide();
    });
  });
</script>

