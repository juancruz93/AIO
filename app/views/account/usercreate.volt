{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Bootstrap Toggle #}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}
{% block js %}
  {# Angular #}
{#  {{ javascript_include('js/angular/account/controller.js') }}#}
{{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  {# Bootstrap Toggle #}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>
    function clearselect() {
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });

      $(".select2").select2({
        theme: "classic",
        placeholder: "Seleccionar"
      });
    });
  </script>
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}

{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}

{% block content %}    
  <div ng-app="aio" ng-controller="ctrlAccount" >
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Creación de un nuevo Usuario en la cuenta <strong>{{(account.name)}}</strong>
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
        <form  method="post" class="form-horizontal" ng-submit="createUser({{account.idAccount}})">
        {#<form action="{{url('account/usercreate')}}/{{(account.idAccount)}}" method="post" class="form-horizontal">#}
          <div class="block block-info">          
            <div class="body">
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Nombre:</label>
                      <span class="input hoshi input-default col-sm-8">                                     
                        {{UserForm.render('name', { 'class': 'undeline-input', 'ng-model' : 'data.name'})}}
                      </span>
                    </div>       
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Apellido:</label>
                      <span class="input hoshi input-default col-sm-8">                                  
                        {{UserForm.render('lastname', { 'class': 'undeline-input', 'ng-model' : 'data.lastname'})}}
                      </span>
                    </div>       
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Email:</label>
                      <span class="input hoshi input-default col-sm-8">                                            
                        {{UserForm.render('email', { 'class': 'undeline-input', 'ng-model' : 'data.email'})}}
                      </span>
                    </div>       
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Telefono:</label>
                      <span class="input hoshi input-default col-sm-8">                                           
                        {{UserForm.render('cellphone', { 'class': 'undeline-input', 'ng-model' : 'data.cellphone'})}}
                      </span>
                    </div>       
                  </div> 

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Contraseña:</label>
                      <span class="input hoshi input-default col-sm-8">                                         
                        {{UserForm.render('pass1', { 'class': 'undeline-input', 'ng-model' : 'data.pass1'})}}
                      </span>
                    </div>       
                  </div> 

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Repita la contraseña:</label>
                      <span class="input hoshi input-default col-sm-8">                                        
                        {{UserForm.render('pass2', { 'class': 'undeline-input', 'ng-model' : 'data.pass2'})}}
                      </span>
                    </div>       
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Pais</label>
                      <span class="input hoshi input-default  col-sm-8">           
                        <select class="undeline-input select2 " ng-change="selectCountryUser()" ng-model="countrySelectedUser" onchange="clearselect()" >
                          <option value=""></option>
                          <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}">{{"{{c.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
                      <label  class="col-sm-4 text-right">*Departamento</label>
                      <span class="input hoshi input-default  col-sm-8">                 
                        <select class="undeline-input select2 clearselect" ng-change="selectStateUser()" ng-model="stateSelectedUser">
                          <option value=""></option>
                          <option ng-repeat="s in stateUser " value="{{"{{s.idState}}"}}">{{"{{s.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Ciudad:</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2 clearselect"  name="citySelectedUser" id="citySelectedUser" required ng-model="data.citySelectedUser">
                          <option value=""></option>
                          <option ng-repeat="ci in citiesUser " value="{{"{{ci.idCity}}"}}">{{"{{ci.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>

                </div>    
              </div>    
            </div>
            <div class="footer" align="right">
              <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('account/userlist')}}/{{(account.idAccount)}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
              <li>El campo nombre  no pueden tener menos de 2 caracteres ni más de 40 caracteres</li>
              <li>El campo apellido  no pueden tener menos de 2 caracteres ni más de 40 caracteres</li>
              <li>El email debe ser unico</li>
              <li>El campo telefono  no pueden tener menos de 8 caracteres ni más de 45 caracteres</li>
              <li>La contraseña debe tener mínimo 8 caracteres y máximo 20 caracteres</li>
              <li>Para que la ciudad aparezca debes seleccionar un país y un departamento</li>
              <li>Los campos con asterisco(*) son obligatorios.</li>
            </ul> 
            </p>
          </div>
        </div>     
      </div>            
    </div>
  </div>

{% endblock %}
