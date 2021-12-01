{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block js %}
  {{ javascript_include('js/angular/supportcontact/app.js') }}
  {{ javascript_include('js/angular/supportcontact/controllers.js') }}
  {{ javascript_include('js/angular/supportcontact/services.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div ng-controller="createcontroller">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Creación de un nuevo contacto Técnico y/o Administrativo
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form ng-submit="addtechnicalcontact()" method="POST">
          <div class="block block-info">
            <div class="body form-horizontal">
              <br>
              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">*Nombre</label>
                <div class="col-sm-10">
                  {{form.render('name', {'class': 'undeline-input ' })}}
                </div>
              </div>
              <div class="form-group">
                <label for="lastname" class="col-sm-2 control-label">*Apellido</label>
                <div class="col-sm-10">
                  {{form.render('lastname', {'class': 'undeline-input ' })}}
                </div>
              </div>
              <div class="form-group">
                <label for="email" class="col-sm-2 control-label">*Correo</label>
                <div class="col-sm-10">
                  {{form.render('email', {'class': 'undeline-input ' })}}
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">*Teléfono</label>
                <div class="col-sm-10">
                  {{form.render('phone', {'class': 'undeline-input ' })}}
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">*Tipo de contactto</label>
                <div class="col-sm-10">
                  <ui-select ng-model="data.type" ng-required="true"
                             ui-select-required theme="select2" sortable="false"
                             close-on-select="true" class='min-width-100' >
                    <ui-select-match
                      placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices
                      repeat="key.id as key in types | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
              </div>
            </div>
            <div class="footer text-right">
              <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('technicalcontact/index')}}/{{(idAllied)}}/{{(idMasteraccount)}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>

            </div>
          </div>
        </form>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="fill-block fill-block-info" >
          <div class="header">
            <div class="header">
              Instrucciones
            </div>
          </div>
          <div class="body">
            <p>Recuerde tener en cuenta estas recomendaciones:</p>
            <ul>                            
              <li>El nombre y apellido debe tener mínimo 3 y máximo 70 caracteres</li>
              <li>El correo debe tener una estructura válida (example@example.com)</li>
              <li>El teléfono debe tener mínimo 7 digitos</li>
              <li>Seleccione el tipo de contacto ya sea administrativo o técnico</li>
              <li>Los campos con asterisco(*) son obligatorios.</li>
            </ul> 

          </div>
        </div>
      </div>
    </div>
    <script>
      var idAllied = "{{idAllied}}";
      var idMasteraccount = "{{idMasteraccount}}";
      var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
      var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
{#      var templateBase = "mail";#}
    </script>
  </div>
{% endblock %}

