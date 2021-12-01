{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}

  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block css %}
{{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
    {{ stylesheet_link('css/checkboxStyle.css') }}
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">

{% endblock %}
{% block js %}
  <script>

    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "unsubscribe";
  </script>
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {# {{javascript_include('js/angular/unsubscribe/services.js') }}
  {{javascript_include('js/angular/unsubscribe/controllers.js') }} #}
{{ javascript_include('js/angular/unsubscribe/dist/unsubscribe.978d66fb89d6fd646ea0.min.js') }}
  {{ javascript_include('library/angular-dragdrop/component/jquery-ui/jquery-ui.min.js')}}
  {{ javascript_include('library/angular-dragdrop/src/angular-dragdrop.min.js')}}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
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
                    theme: 'classic',
                    placeholder : 'Seleccionar',
                    minimumResultsForSearch: -1
                });
                $('.search-select-multiple').select2({
                    dropdownAutoWidth: true,
                    multiple: true,
                    width: '100%',
                    height: '30px',
                    placeholder: "Select",
                    allowClear: true
                });
                $('.select2-search__field').css('width', 'auto');
                $('#indicative').select2().on('select2:open', function(e){
    $('.select2-search__field').attr('placeholder', 'Seleccione');
})
            });

  </script>

{% endblock %}

{% block content %}
  
 <div class="row" ng-controller="createController" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" >
        <div class="row">
            <div class="clearfix"></div>
            <div class="space"></div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="title">
                  Agregar una desuscripci&oacute;n
                </div>
                <hr class="basic-line" />
                <p>
                  En esta secci&oacute;n podr&aacute; desuscribir un contacto de las categor&iacute;as de lista de contacto a las cuales este asociado. Tenga en cuenta que un contacto puede estar en diferentes listas con diferentes categor&iacute;as
                </p>
              </div>
        </div>
    <div class="row" >
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap" style=" padding-left: 15px">
        <form method="post" class="form-horizontal" ng-submit="addUnsub()">
          <div class="block block-info">
            <div class="body">
              <div class="form-group">
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" style="padding-bottom: 0px;">
                  <label  class="col-sm-4 text-right">*Correo electr&oacute;nico</label>
                  <span class="input hoshi input-default  col-sm-8">                                     
                    <input type="email" id="name" name="name" ng-model="data.email" class="undeline-input" maxlength="50"> 
                  </span>
                </div>
              </div>
            <!--   <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" style="padding-bottom: 5px;">
                  <label  class="col-sm-4 text-right" style="padding-top: 10px;">Indicativo</label>
                  <span class="input hoshi input-default  col-sm-8">                  
                    <select class="undeline-input select2" id="indicative" name="indicative" data-ng-model="data.phoneCode" >
                      <option value=""></option>
                      <option data-ng-repeat="item in listindicative track by $index" value="{{"{{item.phoneCode}}"}}">(+{{"{{item.phoneCode}}"}}) {{"{{item.name}}"}}</option>
                    </select>
                  </span>
                </div>
              </div> 
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 text-right">M&oacute;vil</label>
                  <span class="input hoshi input-default  col-sm-8">                                     
                    <input type="number" id="phone" name="phone" ng-model="data.phone" class="undeline-input" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  maxlength = "10" > 
                  </span>
                </div>
              </div> -->
 
             <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                  <label class="col-sm-4 col-md-4 text-right" style="padding-top: 15px;">*Lista de categor&iacute;a(s):</label>
                  <span class="input hoshi input-default col-sm-8 col-md-8">
                      <select class="undeline-input select2 selectcat" multiple="multiple" name="services" id="services" required ng-model="services" style="padding-bottom: 0px;" ng-change="selectedCategories()">
                          <option ng-repeat="ci in listAllAddressee" value="{{ "{{ci.idContactlistCategory + '-' +ci.contactlist}}" }}">{{ "{{ci.name}}" }}</option>
                    </select>
                  </span>
                </div>
              </div> 
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 text-right">*Motivo del de desuscripci&oacute;n</label>
                  <span class="input hoshi input-default  col-sm-8">                                     
                    <textarea  required="" ng-model="data.motive" class="undeline-input" maxlength="160" minlength="2" style="resize: none;"> </textarea>
                  </span>
                </div>
              </div>
    
            </div>
            <div class="footer" align="right">
              <button class="button  btn btn-xs-round   round-button success-inverted"
                      data-toggle="tooltip"
                      data-placement="top" >
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{ url('unsubscribe/list')}}"
                 class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip"
                 data-placement="top" >
                <span class="glyphicon glyphicon-remove"></span>
              </a>
    
            </div>
          </div>
        </form>
      </div>
    
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="fill-block fill-block-primary">
          <div class="header">
            Informaci&oacute;n
          </div>
          <div class="body text-justify">
        <p>Recuerde tener en cuenta estas recomendaciones</p>
        <ul>
          <li>Debe ingresar el correo electr&oacute;nico.</li>
          <li>Las listas de contacto deben estar organizadas con las categor&iacute;as previamente definidas, ya que al hacer uso de esta opci&oacute;n el contacto solo se desuscribir&aacute; de las listas que tengan el mismo nombre de categor&iacute;a</li>

        </ul>
          </div>
        </div>
      </div>
    </div>
    </div>
</div>         
          

{% endblock %}  
