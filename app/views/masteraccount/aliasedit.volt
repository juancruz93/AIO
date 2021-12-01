{% extends "templates/default.volt" %}
{% block js %}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('js/angular/allied/controller.js') }}
  {{ partial("partials/js_notifications_partial") }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
{% endblock %}
{% block css %}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $(".select2Multiple").select2({
        theme: 'classic'
      });
    });

    var idMasteraccount = {{ alias.idMasteraccount}};

  </script>

{% endblock %}

{% block content %}    
  <div ng-app="aio" ng-controller="ctrlAllied"  ng-init="  idCountry= {{ alias.City.idCountry }}">
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Edición de la información de la Cuenta Aliada 
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>       

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form action="{{url('masteraccount/aliasedit')}}/{{(alias.idAllied)}}" method="post" class="form-horizontal">
          <div class="block block-info">          
            <div class="body">

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Nombre:</label>
                  <span class="input hoshi input-default col-sm-9">                             
                    {{aliasform.render('name', {'class': 'undeline-input' , 'placeholder':'*Nombre'})}}                                    
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Nit o C.C:</label>
                  <span class="input hoshi input-default col-sm-9">                            
                    {{aliasform.render('nit', {'class': 'undeline-input' , 'placeholder':'Nit'})}}                                    
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Direcci&oacute;n:</label>
                  <span class="input hoshi input-default col-sm-9">                             
                    {{aliasform.render('address', {'class': 'undeline-input' , 'placeholder':'Dirección'})}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Telefono:</label>
                  <span class="input hoshi input-default col-sm-9">                               
                    {{aliasform.render('phone', {'class': 'undeline-input' , 'placeholder':'Teléfono'})}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Zipcode:</label>
                  <span class="input hoshi input-default col-sm-9">                                      
                    {{aliasform.render('zipcode', {'class': 'undeline-input' , 'placeholder':'Zipcode' })}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Pais</label>
                  <span class="input hoshi input-default  col-sm-9">           
                    <select class="undeline-input select2" ng-change="selectCountry()" ng-init="selectCountry({{idCountry}}); countrySelected ='{{idCountry}}'" ng-model="countrySelected">
                      <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}"
                              ng-selected="c.idCountry ==  {{idCountry}} " >{{"{{c.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Departamento</label>
                  <span class="input hoshi input-default  col-sm-9">                 
                    <select class="undeline-input select2" ng-change="selectState()" ng-init="selectState({{idState}}); stateSelected= '{{idState}}' " ng-model="stateSelected">
                      <option ng-repeat="s in state " value="{{"{{s.idState}}"}} " 
                              ng-selected="s.idState ==  {{idState}}">{{"{{s.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Ciudad:</label>
                  <span class="input hoshi input-default  col-sm-9">       
                    <select class="undeline-input select2"  ng-model="citySelected" ng-init="citySelected = '{{(alias.city.idCity)}}' " id="citySelected" name="citySelected" >
                      <option ng-repeat="ci in cities " value="{{"{{ci.idCity}}"}}" 
                              ng-selected="ci.idCity ==   {{(alias.city.idCity)}}" >{{"{{ci.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right"> Estado:</label>
                  <span class="input hoshi input-default col-sm-9">
                    {{aliasform.render('status', {'id': 'toggle-one'})}}
                  </span>
                </div>        
              </div>
            </div>

            <div class="footer" align="right">
                <button class="button shining btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('masteraccount/aliaslist')}}/{{(alias.idMasteraccount)}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
              
            </div>
          </div>
        </form>
      </div>

      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
        <div class="fill-block fill-block-primary" >
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>                            
              <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
              <li>El nombre de la cuenta aliada debe ser un nombre único, es decir, no pueden existir dos cuentas aliadas con el mismo nombre.</li>                                                        
              <li>El estado de la cuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul> 
            </p>
          </div>
          <div class="footer">
            Creación
          </div>
        </div>     
      </div> 

    </div>
  </div>

{% endblock %}
