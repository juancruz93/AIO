{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('js/angular/subaccount/subaccountController.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}

  <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });

    {#      $(".select2").select2();#}
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
  <div ng-app="aio" ng-controller="ctrlSubaccountCreateUser" class="row">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <form action="{{url('subaccount/edituser')}}/{{(userE.idUser)}}" method="post" class="form-horizontal">
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
                    <label class="col-sm-4 text-right ">*Celular:</label>
                    <span class="input hoshi input-default col-sm-8">        
                      {{ UserForm.render('cellphone', {'class': 'undeline-input ' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Pais</label>
                    <span class="input hoshi input-default  col-sm-8">           
                      <select class="undeline-input select2" ng-change="selectCountryUser()" ng-init="selectCountryUser({{idCountry}}); countrySelectedUser='{{idCountry}}'"
                              ng-model="countrySelectedUser" keep-current-value>
                        <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}"
                                ng-selected="c.idCountry ==  {{idCountry}} " >{{"{{c.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Departamento</label>
                    <span class="input hoshi input-default  col-sm-8">                 
                      <select class="undeline-input select2" ng-change="selectStateUser()" ng-init="selectStateUser({{idState}}); stateSelectedUser='{{idState}}'" ng-model="stateSelectedUser">
                        <option ng-repeat="s in stateUser " value="{{"{{s.idState}}"}} " 
                                ng-selected="s.idState ==  {{idState}}">{{"{{s.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Ciudad:</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      <select class="undeline-input select2" ng-init="citySelectedUser='{{userE.idCity}}'" ng-model="citySelectedUser" id="citySelectedUser" name="citySelectedUser" >
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
            <button class="button btn btn-xs-round  round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('subaccount/userlist/' ~ userE.userType.idSubaccount)}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
