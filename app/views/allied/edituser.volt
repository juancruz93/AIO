{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('js/angular/allied/controller.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  <!-- Angular Material Dependencies -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
    });
  </script>

{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la información del Usuario
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>
  <div class="row" ng-app="aio" ng-controller="ctrlAllied" >
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <form action="{{url('allied/edituser')}}/{{(userE.idUser)}}/{{(idAllied)}}" method="post" class="form-horizontal">
        <div class="block block-info">          
          <div class="body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Nombre</label>
                    <span class="input hoshi input-default col-sm-8">         
                      {{ UserForm.render('name', {'class': 'undeline-input ' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Apellido</label>
                    <span class="input hoshi input-default col-sm-8">       
                      {{ UserForm.render('lastname', {'class': 'undeline-input ' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Celular:</label>
                    <span class="input hoshi input-default col-sm-8">        
                      {{ UserForm.render('cellphone', {'class': 'undeline-input ' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">*Pais</label>
                    <span class="input hoshi input-default  col-sm-8">           
                      <select class="undeline-input select2" ng-change="selectCountryUser()" ng-init="selectCountryUser({{idCountry}}); countrySelectedUser= '{{idCountry}}' " ng-model="countrySelectedUser">
                        <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}"
                                ng-selected="c.idCountry ==  {{idCountry}} " >{{"{{c.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">*Departamento</label>
                    <span class="input hoshi input-default  col-sm-8">                 
                      <select class="undeline-input select2" ng-change="selectStateUser()" ng-init="selectStateUser({{idState}}); stateSelectedUser = '{{idState}}' " ng-model="stateSelectedUser">
                        <option ng-repeat="s in stateUser " value="{{"{{s.idState}}"}} " 
                                ng-selected="s.idState ==  {{idState}}">{{"{{s.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">*Ciudad:</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      <select class="undeline-input select2"  ng-model="citySelectedUser" ng-init="citySelectedUser = '{{userE.idCity}}'" id="citySelectedUser" name="citySelectedUser" >
                        <option ng-repeat="ci in citiesUser " value="{{"{{ci.idCity}}"}}" 
                                ng-selected="ci.idCity ==  {{userE.idCity}}" >{{"{{ci.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>

              </div>
            </div>    
          </div>

          <div class="footer" align="right">
              <button class="button shining btn btn-xs-round   round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('allied/listuser/'~idAllied)}}" class="button shining btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
            
          </div>
        </div>
      </form>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                               
      <div class="fill-block fill-block-info" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>                            
            <li>Los campos nombre y apellidos no pueden tener menos de 2 caracteres</li>
            <li>Los campos nombre y apellidos no pueden tener mas de 40 caracteres</li>
            <li>El email debe ser unico</li>
            <li>Para que la ciudad aparezca debes seleccionar un país y un departamento</li>
            <li>Los campos con asterisco(*) son obligatorios.</li>
          </ul> 
          </p>
        </div>
        <div class="footer">
          Edición
        </div>
      </div>     
    </div>            
  </div>   

{% endblock %}
