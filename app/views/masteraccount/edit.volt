{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
{% endblock %}

{% block js %}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {# Select 2 #}
  {{ javascript_include('js/angular/masteraccount/controller.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
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
      $(".select2").select2({
        placeholder: "Seleccionar",
        theme: 'classic'
      });
    });
    var idCountry
    = {{idCountry}};
      var idCategory = '{{ masteraccount.idAccountCategory }}';
    var idMasterAccount = '{{masteraccount.idMasteraccount}}';
  </script>

{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la Cuenta Maestra <strong>{{masteraccount.name}}</strong>
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>       

  <div class="row" ng-app="aio" ng-controller="ctrlMasteraccount" ng-cloak>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <form {#action="{{url('masteraccount/edit')}}/{{masteraccount.idMasteraccount}}"#} data-ng-submit="editmasteraccount()" method="post">
        <div class="block block-info">          
          <div class="body">
            <div class="row">

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">*Nombre</label>
                  <span class="input hoshi input-default  col-sm-9">
                    {{form.render('nameMasterAccount', {'class': 'undeline-input' , 'value': masteraccount.name, 'data-ng-model':'name'})}}                                    
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">*Nit/Identificación de la empresa</label>
                  <span class="input hoshi input-default  col-sm-9">
                    {{form.render('nit', {'class': 'undeline-input' , 'placeholder':'', 'data-ng-model': 'nit'})}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">*Dirección</label>
                  <span class="input hoshi input-default  col-sm-9">
                    {{form.render('address', {'class': 'undeline-input' , 'placeholder':'', 'data-ng-model': 'address'})}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Teléfono</label>
                  <span class="input hoshi input-default  col-sm-9">
                    {{form.render('phone', {'class': 'undeline-input' , 'placeholder':'', 'data-ng-model':'phone'})}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Pais</label>
                  <span class="input hoshi input-default  col-sm-9">
                    <select class="undeline-input select2" ng-change="selectCountry()" ng-init="selectCountry({{idCountry}}); countrySelected = '{{idCountry}}' " ng-model="countrySelected" >
                      <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}"   ng-selected="c.idCountry == {{idCountry}}"
                              >{{"{{c.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Departamento</label>
                  <span class="input hoshi input-default  col-sm-9">
                    <select class="undeline-input select2" ng-change="selectState()" ng-init="selectState({{idState}}); stateSelected = '{{idState}}' " ng-model="stateSelected" keep-current-value>
                      <option ng-repeat="s in state " value="{{"{{s.idState}}"}} " 
                              ng-selected="s.idState ==  {{idState}}">{{"{{s.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">* Ciudad:</label>
                  <span class="input hoshi input-default col-sm-9">
                    <select class="undeline-input select2"  ng-model="citySelectedUser" ng-init="citySelectedUser = '{{masteraccount.idCity}}' " id="citySelectedUser" name="citySelected" keep-current-value>
                      <option ng-repeat="ci in cities " value="{{"{{ci.idCity}}"}}" 
                              ng-selected="ci.idCity ==  {{masteraccount.idCity}}" >{{"{{ci.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">*Categoría</label>
                  <span class="input hoshi input-default col-sm-9">
                    <select class="undeline-input select2" ng-model="idAccountCategory" name="idAccountCategory" required="required">
                      <option value=""></option>
                      <option ng-repeat="cate in categories" value="{{"{{cate.idAccountCategory}}"}}" >{{"{{cate.name}}"}}</option>
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
                    {#<select class="undeline-input select2 clearselect"  ng-model="idSmsSendingRule" name="idSmsSendingRule" required="required">
                      <option value=""></option>
                      <option ng-repeat="smsSend in smsSendingRule" value="{{"{{smsSend.idSmsSendingRule}}"}}">{{"{{smsSend.name}}"}}</option>
                    </select>#}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-3 text-right">Descripción</label>
                  <span class="input hoshi input-default  col-sm-9">
                    {{form.render('description', {'class': 'undeline-input' , 'placeholder':'', 'data-ng-model':'description'})}}
                  </span>
                </div>
              </div>

              <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
                    <label  class="col-sm-3 text-right">*Estado</label>
                    <span class="input hoshi input-default  col-sm-9">
                      <md-switch class="md-primary none-margin" ng-model="status" md-no-ink aria-label="Switch 1">
                      </md-switch>
                    </span>
                  </div>        
                </div>

            </div>
          </div>
          <div class="footer" align="right"> 
            <button class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('masteraccount/index')}}" class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
            <li>El nombre de la cuenta maestra debe ser un nombre único, es decir, no pueden existir dos cuentas maestras con el mismo nombre.</li>                                                        
            <li>El nit de la cuenta maestra debe ser un nit único, es decir, no pueden existir dos cuentas maestras con el mismo nit.</li>         
            <li>El nit se debe de ingresar sin puntos, espacios, guiones ni comas</li>  
            <li>El campo nit no puede tener más de 60 caracteres</li>
            <li>El campo dirección no puede tener más de 45  caracteres</li>
            <li>El campo telefono no puede tener más de 45  caracteres</li>
            <li>Para que la ciudad aparezca debes seleccionar un país y un departamento</li>        
            <li>El estado de la cuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
            <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
          </ul> 
          </p>
        </div>
      </div>     
    </div> 

  </div>

{% endblock %}
