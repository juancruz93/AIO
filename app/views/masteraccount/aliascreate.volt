{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}

  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/bootstrap-wizard-1.1/css/gsdk-base.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
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
  {{ javascript_include('js/angular/allied/controller.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
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
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }

    var idMasteraccount = {{ masteraccount.idMasteraccount}};
    var diskSpaceMaster = {{ masteraccount.Masterconfig.diskSpace }};
    var idCountry = '{{ masteraccount.City.State.idCountry}}';
    var idState = '{{ masteraccount.City.idState}}';
    var idCity = '{{ masteraccount.idCity}}';

    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $(".select2").select2({
        theme: 'classic',
        placeholder: 'Seleccionar'
      });
    });
  </script>   
  <div ng-app="aio" ng-controller="ctrlAllied" ng-cloak>
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Creación de un nuevo Aliado en la Cuenta <strong>{{(masteraccount.name)}}</strong>
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>

    <div class="row" >
      <form  method="post" class="" ng-submit="createAllied({{ masteraccount.idMasteraccount}})">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 wrap">
          <div class="block block-info">          
            <div class="body " >
              <div class="row">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Nombre:</label>
                    <span class="input hoshi input-default col-sm-9">                                    
                      {{AliasForm.render('name', {'class': 'undeline-input' , 'placeholder':'Nombre', 'ng-model': 'name', 'required': 'required' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Nit/Identificación de la empresa:</label>
                    <span class="input hoshi input-default col-sm-9">                                    
                      {{AliasForm.render('nit', {'class': 'undeline-input' , 'placeholder':'Nit', 'ng-model': 'nit' , 'required': 'required' })}}                                    
                    </span>
                  </div>
                </div> 

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Dirección:</label>
                    <span class="input hoshi input-default col-sm-9">                                    
                      {{AliasForm.render('address', {'class': 'undeline-input' , 'placeholder':'Dirección', 'ng-model': 'address' , 'required': 'required' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Teléfono:</label>
                    <span class="input hoshi input-default col-sm-9">                                    
                      {{AliasForm.render('phone', {'class': 'undeline-input' , 'placeholder':'Teléfono', 'ng-model': 'phone', 'required': 'required'  })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Zipcode:</label>
                    <span class="input hoshi input-default col-sm-9">                                      
                      {{AliasForm.render('zipcode', {'class': 'undeline-input' , 'placeholder':'Zipcode', 'ng-model': 'zipcode' , 'required': 'required' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Correo:</label>
                    <span class="input hoshi input-default col-sm-9">                                      
                      {{AliasForm.render('email', {'class': 'undeline-input' , 'placeholder':'Correo', 'ng-model': 'email' , 'required': 'required' })}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* País</label>
                    <span class="input hoshi input-default  col-sm-9">           
                      <select class="form-control select2" ng-change="selectCountry()" ng-model="countrySelected">
                        <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}" >{{"{{c.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right">* Departamento</label>
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
                    <label  class="col-sm-3 text-right">* Ciudad:</label>
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
                    <label  class="col-sm-3 text-right">*Planes de pago:</label>
                    <span class="input hoshi input-default  col-sm-9">
                      <select class="undeline-input select2 clearselect" data-ng-change="descriptionPlan(); calculeDiskSpace();" ng-model="paymentPlanSelected" required="required">
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
                              <th></th>
                              <th>Cantidad configurada en el plan</th>
                              <th>Cantidad disponible</th>
                              <th>Cantidad después de la operación</th>
                            </tr>
                          </thead>
                          <tbody class="color-default">
                            <tr>
                              <td>Espacio en disco</td>
                              <td>{{ '{{ plan.diskSpace }}' }} MB</td>
                              <td class="color-primary">{{ masteraccount.Masterconfig.diskSpace }} MB</td>
                              <td ng-class="totalspace >= 0 ? 'positive' : 'negative' "><b>{{ '{{ totalspace }}' }} MB </b></td>
                            </tr>
                            <tr data-ng-repeat="item in plan.planxservice">
                              <td>{{ '{{ item.service }}' }}</td>
                              <td>{{ '{{ item.amount }}' }}</td>
                              <td class="color-primary">{{ '{{ item.amountConfig }}' }}</td>
                              <td ng-class="item.totalAmount > 0 ? 'positive' : 'negative' " ><b>{{ '{{ item.totalAmount }}' }}</b></td>
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
                      <select class="undeline-input select2 clearselect"  ng-model="idAccountCategory" required="required">
                        <option value=""></option>
                        <option ng-repeat="cate in categories" value="{{"{{cate.idAccountCategory}}"}}">{{"{{cate.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-3 text-right "> *Estado:</label>
                    <span class="input hoshi input-default input-filled  col-sm-9">                                      
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
              <a href="{{url('masteraccount/aliaslist/' ~ masteraccount.idMasteraccount)}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
            </div>    
          </div>
        </div>
      </form>
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 wrap">
        <div class="fill-block fill-block-info" >
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>                            
              <li>El nombre de la cuenta aliada debe ser un nombre único, es decir, no pueden existir dos cuentas maestras con el mismo nombre.</li>                                                        
              <li>El nit de la cuenta aliada debe ser un nit único, es decir, no pueden existir dos cuentas maestras con el mismo nit.</li>             
              <li>El nit se debe de ingresar sin puntos, espacios, guiones ni comas</li>                       
              <li>Para que la ciudad aparezca debes seleccionar un país y un departamento</li>
              <li>El estado de la cuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul> 
            </p>
          </div>
        </div>     
      </div>            
    </div>

  </div>
{% endblock %}
