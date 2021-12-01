{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}

  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/bootstrap-wizard-1.1/css/gsdk-base.css') }}
  {{ stylesheet_link('library/select2/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
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
  {#  {{  javascript_include('library/twitter-bootstrap-wizard-master/jquery.bootstrap.wizard.js') }}
    {{  javascript_include('library/twitter-bootstrap-wizard-master/prettify.js') }}#}
  {{ javascript_include('js/angular/masteraccount/controller.js') }}
{% endblock %} 
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>     

  
<div ng-app="aio">
    {#    <div class="clearfix"></div>#}
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Creación de un nuevo idioma
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>       

    <div class="row">
      <form  method="post" action="{{url('language/create')}}">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
          <div class="block block-info">          
            <div class="body " >
              <div class="row">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 text-right">*Nombre</label>
                    <span class="input hoshi input-default col-sm-9">
                      {{form.render('name', {'class': 'undeline-input ' })}}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 text-right">*Nombre corto</label>
                    <span class="input hoshi input-default col-sm-9">
                      {{form.render('shortName',{'class':'undeline-input'})}}
                    </span>
                  </div>
                </div>
                

              </div>
            </div>
            <div class="footer" align="right">          
                <button class="button shining btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('language/index')}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
              
            </div>    
          </div>
        </div>
      </form>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
        <div class="fill-block fill-block-info" >
          <div class="header">
            Instrucciones
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>                            
              <li>El campo 'nombre' no puede tener más de 60 caracteres ni menos de 2 caracteres</li>
              <li>El campo 'nombre corto' no puede tener más de 6 caracteres ni menos de 2 caracteres</li>
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