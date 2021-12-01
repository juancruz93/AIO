{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}

  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/bootstrap-wizard-1.1/css/gsdk-base.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {#<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">#}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
{% endblock %}

{% block js %}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.bootstrap.wizard.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.validate.min.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/wizard.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('js/angular/masteraccount/controller.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
{% endblock %} 
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>     

  <script>
    function clearselect() {
      $(".clearselect").select2().val("").trigger("change");
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
        placeholder: "Seleccionar",
        theme: 'classic'
      });
    });
  </script>   
  <div ng-app="aio" ng-controller="ctrlMasteraccount" >
    {#    <div class="clearfix"></div>#}
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Creación de una nueva Cuenta Maestra
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>       

    <div class="row">
      <form  method="post" ng-submit="newmasteraccount()">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
          <div class="block block-info">          
            <div class="body " >
              <div class="row">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 text-right">*Nombre</label>
                    <span class="input hoshi input-default col-sm-9">                
                      {{masteraccountform.render('nameMasterAccount', {'class': 'undeline-input ' , 'placeholder':'Nombre', 'ng-model': 'name'})}}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Nit/Identificación de la empresa</label>
                    <span class="input hoshi input-default  col-sm-9">           
                      {{masteraccountform.render('nit', {'class': 'undeline-input' , 'placeholder':'Nit', 'ng-model': 'nit'})}}                                    
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Direccion</label>
                    <span class="input hoshi input-default  col-sm-9">           
                      {{masteraccountform.render('address', {'class': 'undeline-input' , 'placeholder':'Dirección', 'ng-model': 'address'})}}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Teléfono</label>
                    <span class="input hoshi input-default  col-sm-9">          
                      {{masteraccountform.render('phone', {'class': 'undeline-input' , 'placeholder':'Teléfono', 'ng-model': 'phone'})}}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*País</label>
                    <span class="input hoshi input-default  col-sm-9">           
                      <select class="undeline-input select2" ng-change="selectCountry()" ng-model="countrySelected" onchange="clearselect()" required>
                        <option value=""></option>
                        <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}" {{"{{selected}}"}}>{{"{{c.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Departamento</label>
                    <span class="input hoshi input-default  col-sm-9">                 
                      <select class="undeline-input select2" ng-change="selectState()" ng-model="stateSelected" required>
                        <option value=""></option>
                        <option ng-repeat="s in state " value="{{"{{s.idState}}"}}">{{"{{s.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Ciudad</label>
                    <span class="input hoshi input-default  col-sm-9">       
                      <select class="undeline-input select2"  ng-model="citySelected" required="required">
                        <option value=""></option>
                        <option ng-repeat="ci in cities " value="{{"{{ci.idCity}}"}}">{{"{{ci.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Planes de pago</label>
                    <span class="input hoshi input-default  col-sm-9">
                      <select class="undeline-input select2" data-ng-change="descriptionPlan()" ng-model="paymentPlanSelected" required="required">
                        <option value=""></option>
                        <option ng-repeat="payment in paymentPlan " value="{{"{{payment}}"}}">{{"{{payment.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group" ng-show="showDetail" ng-cloak>
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <span class="input hoshi input-default col-sm-12">
                      <div class="fill-block fill-block-warning">
                        <table class="table table-bordered table-responsive">
                          <thead class="theader">
                            <tr>
                              <th>Detalle</th>
                              <th>Cantidad configurada en el plan</th>
                            </tr>
                          </thead>
                          <tbody class="color-default">
                            <tr>
                              <td>Espacio en disco</td>
                              <td>{{ '{{ plan.diskSpace }}' }} MB</td>
                            </tr>
                            <tr data-ng-repeat="item in plan.planxservice">
                              <td>{{ '{{ item.service }}' }}</td>
                              <td>{{ '{{ item.amount }}' }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Categoría</label>
                    <span class="input hoshi input-default  col-sm-9">
                      <select class="undeline-input select2"  ng-model="idAccountCategory" required="required">
                        <option value=""></option>
                        <option ng-repeat="cate in categories" value="{{"{{cate.idAccountCategory}}"}}">{{"{{cate.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">*Regla de envío de SMS</label>
                    <span class="input hoshi input-default  col-sm-9">
                      <ui-select multiple ng-model="pp.idSmsSendingRule" theme="select2" close-on-select="true" style="text-align: left; width: 100%" title="Seleccione las reglas de envío">
                        <ui-select-match placeholder="Seleccionar reglas de envío">{{"{{$item.name}}"}}</ui-select-match>
                        <ui-select-choices repeat="item.idSmsSendingRule as item in smsSendingRule | propsFilter: {name: $select.search}">
                          <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                        </ui-select-choices>
                      </ui-select>
                      {#<ui-select  data-ng-model="idSmsSendingRule" theme="select2" style="text-align: left; width: 100%" title="Seleccione las reglas de envío">
                        <ui-select-match placeholder="Debe seleccionar las reglas de envío">{{"{{$item.name}}"}}</ui-select-match>
                        <ui-select-choices repeat="item.idSmsSendingRule as item in smsSendingRule | filter: $select.search">
                          <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                        </ui-select-choices>
                      </ui-select>#}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right ">Descripción</label>
                    <span class="input hoshi input-default  col-sm-9">               
                      {{masteraccountform.render('description', {'class': 'undeline-input' , 'placeholder':'Descripción', 'ng-model': 'description'})}}                                    
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
                    <label  class="col-sm-3 text-right">Estado</label>
                    <span class="input hoshi input-default  col-sm-9">
                      <md-switch class="md-primary none-margin" ng-model="status" md-no-ink aria-label="Switch 1">
                      </md-switch>
                    </span>
                  </div>        
                </div>

              </div>
            </div>
            <div class="footer" align="right">          
              <button class="button shining btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('masteraccount/index')}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>

            </div>    
          </div>
        </div>
      </form>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
        <div class="fill-block fill-block-info" >
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>                            
              <li>El nombre de la cuenta maestra debe ser un nombre único, es decir, no pueden existir dos cuentas maestras con el mismo nombre.</li>                                                        
              <li>El campo nombre no puede tener más de 60 caracteres ni menos de 2 caracteres.</li>
              <li>El nit de la cuenta maestra debe ser un nit único, es decir, no pueden existir dos cuentas maestras con el mismo nit.</li>         
              <li>El nit se debe de ingresar sin puntos, espacios, guiones ni comas.</li>
              <li>El campo nit no puede tener más de 60 caracteres.</li>
              <li>El campo dirección no puede tener más de 45  caracteres.</li>
              <li>El campo telefono no puede tener más de 45  caracteres.</li>
              <li>Para que las ciudades se visualicen debes seleccionar un país y un departamento.</li>
              <li>Para que los planes de pago se visualicen debes seleccionar un país.</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul> 
            </p>
          </div>
        </div>     
      </div>            
    </div>

  </div>
{% endblock %}
