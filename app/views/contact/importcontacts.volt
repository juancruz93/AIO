{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/angular-xeditable-0.2.0/css/xeditable.css') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  {{ stylesheet_link('library/angular-bootstrap-datetimepicker-master/src/css/datetimepicker.css') }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {# {{ javascript_include('js/angular/contact/app.js') }}
  {{ javascript_include('js/angular/contact/controllers.js') }}
  {{ javascript_include('js/angular/contact/services.js') }}
  {{ javascript_include('js/angular/contact/directives.js') }} #}
  {{ javascript_include('js/angular/contact/dist/contact.d311b2c7b96f67c60f22.min.js') }}
  {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}
  {{ javascript_include('js/checklist-model.js') }}

  <!-- Angular Material Dependencies -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>

  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.min.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.templates.js') }}
  <script>
    $(function () {
      $(".select2").select2({
        theme: 'classic'
      });

    });

    function clearselect() {
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }


  </script>
{% endblock %}
{% block content %}
  <div data-ng-app="contact" data-ng-controller="ContactImportController" ng-cloak>
    <div class="clearfix"></div>
    <div class="space"></div>

    {% for item in contactlist.Subaccount.Saxs %}
      {% if item.idServices == services.email_marketing AND item.accountingMode == "contact" %}
        {% for value in contactlist.Subaccount.Account.AccountConfig.DetailConfig %}
          {% if value.idServices == services.email_marketing %}
            {% if value.amount < arrayCsvRowsEmail AND contactlist.Subaccount.idAccount != '572' %}
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    <b>Cuenta con {{ value.amount }} contactos disponibles a importar y el archivo cargado contiene {{ arrayCsvRowsEmail }} contactos válidos, algunos contactos no podrán ser importados.</b>
                  </div>
                </div>
              </div>
            {% endif %}
          {% endif %}
        {% endfor %}
      {% endif %}
    {% endfor %}

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Importar contactos a: <strong>{{ contactlist.name }}</strong>
        </div>
        <hr class="basic-line"/>
      </div>
    </div>
    <div class="row wrap">
      <div class="div-border-thin " >
        <div class="col-lg-6 inline-block">
          <p>
            Creada por <strong>{{ contactlist.createdBy }}</strong> el dia <strong>{{contactlist.created}}</strong>
            <br>
            Actualizada por <strong>{{ contactlist.createdBy }}</strong> el dia <strong>{{ contactlist.updated }}</strong>
          </p>
        </div>
        <div class="row wrap text-right">

          <div class="inline-block text-center none-padding">
            Totales
            <br>  
            <span class="info medium-text">{{contactlist.ctotal}}</span>
          </div>    
          <div class="inline-block text-center none-padding">
            Activos       <br>  
            <span class="medium-text primary">{{contactlist.cactive}}</span>
          </div>    
          <div class="inline-block text-center none-padding">
            Desuscritos       <br>  
            <span class="medium-text ">{{contactlist.cunsubscribed}}</span>
          </div>    
          <div class="inline-block text-center none-padding">
            Rebotados       <br>  
            <span class="medium-text danger">{{contactlist.cbounced}}</span>
          </div>    
          <div class="inline-block text-center none-padding">
            Spam       <br>  
            <span class="medium-text warning">{{ contactlist.cspam}}</span>
          </div>
        </div>  
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="fill-block fill-block-default">
          <div class="header">
            <strong>Esta es la información del archivo cargado, se muestran las 5 primeras filas, el archivo CSV
              contiene {{ arrayCsvRows }} registros.</strong>
          </div>
          <div class="body">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th data-ng-repeat="data in dataCsv[0] track by $index">
                      Campo
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr data-ng-repeat="obj in dataCsv ">
                    <td data-ng-repeat="data in obj track by $index">
                      {{ '{{ data }}' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <form action="{{ url('contact/processfile/') }}{{ contactlist.idContactlist }}/{{ idImportfile }}/{{ arrayCsvRows }}" method="post">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-info">
            <div class="header">
              <span class="fa fa-cog"></span> <a class="a-link" data-toggle="collapse" data-target="#detail">Opciones de importación (Haga clic aquí)</a>
            </div>
            <div class="body collapse" id="detail">
              <table class="table table-bordered">
                <tbody >
                  <tr>
                    <td class="strong-text" colspan="5">Encabezado:</td>
                    <td colspan="5">
                      <div class="checkbox td-content">
                        <label>
                          <input type="checkbox" name="header" data-ng-model="header" ng-change="adjustDelimiter()">
                          Tratar primera fila de archivo como el encabezado de las columnas.
                          <br>
                          <i>Si habilita esta opción no se importará la primera línea del archivo.</i>
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="strong-text" colspan="5">Delimitador:</td>
                    <td colspan="5">
                      <div>
                        <select name="delimiter" data-ng-model="delimiter" ng-change="adjustDelimiter()" onchange="clearselect()" class="form-control select2">
                          <option value="," selected>,</option>
                          <option value=";">;</option>
                          <option value="/">/</option>
                        </select>
                      </div>
                      <span class="format-text"><i>Carácter que separa los campos en cada fila.</i></span>
                    </td>
                  </tr>
                  <tr>
                    <td class="strong-text" colspan="5">Formato de fecha:</td>
                    <td colspan="5">
                      <div>
                        <select name="dateformat" class="form-control select2">
                          <option disabled selected></option>
                          <option value="Y-m-d">Y-m-d (1969-12-31)</option>
                          <option value="Y/m/d">Y/m/d (1969/12/31)</option>
                          {#<option value="d-m-Y">d-m-Y (31-12-1969)</option>
                          <option value="d/m/Y">d/m/Y (31/12/1969)</option>
                          <option value="m-d-Y">m-d-Y (12-31-1969)</option>
                          <option value="m/d/Y">m/d/Y (12/31/1969)</option>#}
                        </select>
                      </div>
                      <span class="format-text"><i>Indica cómo se deben procesar los campos de tipo fecha.</i></span>
                    </td>
                  </tr>
                  <tr>
                    <td class="strong-text" colspan="5">Modo de importación:</td>
                    <td colspan="5" >
                      <div>
                        <select name="importmode" class="form-control select2">
                          <option value="active" selected>Contactos Suscritos - Opción recomendada</option>
                          <option value="unsubscribed">Desuscritos - Los contactos se marcaran como desuscritos</option>
                          <option value="bounced">Rebotados - Las direcciones de correo se marcaran como rebotadas</option>
                          <option value="spam">Spam - Los contactos se marcaran como spam</option>
                          <option value="blocked">Bloqueados - Los contactos se marcaran como bloqueados</option>
                          <option value="respectiveState">Cargar contactos con su respectivo estado</option>
                        </select>
                      </div>
                      <span class="format-text">
                        <i>En qué <strong>estado</strong> deben quedar los contactos después de importados:</i>
                        <br>
                        <span class="color-warning format-text"><strong>Nota:</strong> Esta opción es avanzada, si no sabe cuál debe elegir utilice el valor recomendado!</span>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="strong-text" colspan="5">Actualización de datos:</td>
                    <td colspan="5" width="50px">
                      <div class="checkbox td-content">
                        <label>
                          <input  onClick="chekout(this.name)" id="update" type="checkbox" name="update" checked>
                          Actualizar los datos de los contactos que ya se encuentren en la base de datos.
                          <br>
                          {#<i>Si habilita esta opción se reemplazara la información de los contactos que ya se encuentran en la base de datos por la información que esté en el archivo, este proceso podría tardar más de lo habitual.</i>#}
                          <i>Esta opción reemplazará la información de los contactos que ya se encuentran en la lista de contactos por la información del archivo CSV. Tenga en cuenta lo siguiente:</i></br>
                          <i>1. Si existe un correo + teléfono y el archivo contiene el mismo correo sin teléfono, se creará un nuevo contacto. </i></br>
                          <i>2. Si existe un correo y/o teléfono más de una vez el sistema actualizará el primer registro que encuentra.</i></br>
                          {#<i>Si habilita esta opción se reemplazará la información de los contactos que ya se encuentran en la lista por la información del archivo, este proceso podría tardar más de lo habitual.</i>#}
                           <i class="color-warning format-text">Este proceso podría tardar más de lo habitual.</i>
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr>  
                    <td class="strong-text" colspan="5">Importar si están repetidos:</td>
                    <td colspan="5" width="50px">
                      <div class="checkbox td-content">
                        <label>
                          <input  onClick="chekout(this.name)" id="importrepeated" type="checkbox" name="importrepeated">
                          Importar contactos, aunque ya exista en la lista.
                          <br>
                          <i>Al habilitar esta opción, se importan los contactos que se encuentren repetidos en esta lista de contactos.</i>
                          <br>
                          <span class="error"><strong>Nota:</strong> Esta opción es avanzada, habilite esta opción, solo si desea guardar los contactos con correos o celulares duplicados en el archivo CSV.</span>
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="strong-text" colspan="5">Importar si están repetidos en archivo csv:</td>
                    <td colspan="5" width="50px">
                      <div class="checkbox td-content">
                        <label>
                          <input  onClick="chekout(this.name)" id="importrepeatedCsv" type="checkbox" name="importrepeatedCsv">
                          Importar contactos repetidos en el archivo csv.
                          <br>
                          <i>Al habilitar esta opción, se importan los contactos con correos repetidos en el archivo csv.</i>
                          <br>
                          <span class="error"><strong>Nota:</strong> Esta opción es avanzada, habilite esta opción, solo si desea guardar los contactos con correos duplicados en el archivo csv.</span>
                        </label>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="fill-block fill-block-info">
            <div class="header">
              Emparejamiento de datos
            </div>
            <div class="body color-default">
              <div class="row">
                <div class="col-md-6">
                  <table class="table table-bordered">
                    <tbody>
                      <tr>
                        <td class="strong-text">
                          Dirección de correo electrónico:
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="email" data-ng-model="email" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Nombre:
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="name" data-ng-model="name" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Apellido:
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="lastname" data-ng-model="lastname" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Fecha de nacimiento:
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="birthdate" data-ng-model="birthdate" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Indicativo:
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="indicative" data-ng-model="indicative" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Móvil:
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="phone" data-ng-model="phone" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      {#{% for field in custom %}#}
                      <tr data-ng-repeat="field in customfield">
                        <td class="strong-text">
                          {{  '{{ field.name }}' }}
                        </td>
                        <td width="50%">
                          <select class="form-control select2 clearselect" name="campo{{  '{{ field.idCustomfield }}' }}" data-ng-model="typevalues[field.name]" id="">
                            <option value="" selected disabled></option>
                            <option value="">No importar</option>
                            <option data-ng-repeat="data in dataCsv[0] track by $index " value="{{ '{{ $index }}' }}" >{{ '{{ data }}' }}</option>
                          </select>
                        </td>
                      </tr>
                      {#{% endfor %}#}
                    </tbody>
                  </table>
                </div>
                <div class="col-md-6 adjust-float-right">
                  <table class="table ">
                    <tbody>
                      <tr>
                        <td class="strong-text">
                          Dirección de correo electrónico:
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][email] }}' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Nombre:
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][name] }}' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Apellido:
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][lastname] }}' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Fecha de nacimiento:
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][birthdate] }}' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Indicativo:
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][indicative] }}' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="strong-text">
                          Móvil:
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][phone] }}' }}
                        </td>
                      </tr>
                      <tr data-ng-repeat="field in customfield">
                        <td class="strong-text">
                          {{ '{{ field.name }}' }}
                        </td>
                        <td width="50%" height="55px">
                          {{ '{{ dataCsv[1][typevalues[field.name]] }}' }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="wrap text-center">
          <a href="{{url('contact/index/' )}}{{ contactlist.idContactlist }}#/import" class="button  btn btn-small danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span >Cancelar</span>
          </a>
          <button class="button shining btn btn-small success-inverted" data-toggle="tooltip" data-placement="top" title="Importar">
            <span >Importar</span>
          </button>
        </div>
      </div>
    </form>
  </div>
          
   <div id="somedialog" class="dialog">
      <div class="dialog__overlay"></div>
      <div class="dialog__content">
        <div class="morph-shape">
          <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
          <rect x="3" y="3" fill="none" width="556" height="276"/>
          </svg>
        </div>
        <div class="dialog-inner">
          <h2>¿Esta seguro que desea des-habilitar esta opción?</h2>
          <h3>Recuerda que si deshabilitas esta opcion se creara un nuevo registro por cada correo existente en la base de datos</h3>
          <div>                    
            <a onClick="cancelarbuttonModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
            <a onClick="confirmarbuttonModal();{#addDisabled('btn-ok')#}"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
          </div>
        </div>
      </div>
    </div>
          
{% endblock %}
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <script>
      
   
      
    var chekout = function (event) {
      var checkUpdate = document.getElementById("update");
      var checkExist = document.getElementById("importrepeated");
      var checkExistCsv = document.getElementById("importrepeatedCsv");
   
      if (event == "update") {
        if (checkExist.checked == true) {
          if(checkUpdate.checked == true){
            checkExist.click();
          } else {
            $('.dialog').addClass('dialog--open');
          }
        } else if (checkExistCsv.checked == true) {
          if(checkUpdate.checked == true){
            checkExistCsv.click();
          } else {
            $('.dialog').addClass('dialog--open');
          }
        }
      } 
      else if (event == "importrepeated") {
       if (checkUpdate.checked == true) {
          checkUpdate.click();
        }
        if (checkExistCsv.checked == true){
          checkExistCsv.click();
        }
      }
      else if(event == "importrepeatedCsv"){
        if (checkUpdate.checked == true) {
            checkUpdate.click();
        }
        if (checkExist.checked == true) {
          checkExist.click();  
        }
      }
    }
  </script>
    <script>
      {{hoursms.startHour}};
        function confirmarbuttonModal() {
          document.getElementById("update").checked = false;
          $('.dialog').removeClass('dialog--open');
        }

        function cancelarbuttonModal() {
          //document.getElementById("update").checked = true;
          //document.getElementById("importrepeated").checked = false;
          //document.getElementById("importrepeatedCsv").checked = false;
          $('.dialog').removeClass('dialog--open');
        }
        var startHour = {{hoursms.startHour}};
          var endHour = {{hoursms.endHour}};
          var typeSms = 'lote';
    </script>

  <script type="text/javascript">
    var dataCsv = [];
    {% if arrayCsv[0] is defined %}
      dataCsv.push("{{ arrayCsv[0] }}");
    {% endif %}
    {% if arrayCsv[1] is defined %}
      dataCsv.push("{{ arrayCsv[1] }}");
    {% endif %}
    {% if arrayCsv[2] is defined %}
      dataCsv.push("{{ arrayCsv[2] }}");
    {% endif %}
    {% if arrayCsv[3] is defined %}
      dataCsv.push("{{ arrayCsv[3] }}");
    {% endif %}
    {% if arrayCsv[4] is defined %}
      dataCsv.push("{{ arrayCsv[4] }}");
    {% endif %}
      var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
      var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
      var templateBase = "contact";
              var idContactlist = {{ contactlist.idContactlist }};
      var customfield = [];
              customfield = {{ customfield }};
  </script>

  {#{{ javascript_include('library/angular-1.5/js/angular.min.js') }}#}

{% endblock %}
