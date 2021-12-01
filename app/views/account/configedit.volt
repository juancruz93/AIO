{% extends "templates/default.volt" %}
{% block js %}
  {{ javascript_include('js/angular/allied/controller.js') }}
{% endblock  %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}

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

      $(".select2").select2({
        theme: 'classic'
      });
    });
  </script>
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}

{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la configuración de la cuenta  <strong>{{ allied.name }}</strong>
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>       

  <div class="row"  ng-app="aio" ng-controller="ctrlAllied" ng-init="spaceTotal = {{  masteraccount.Config.fileSpace }}; ss = {{  masteraccount.Config.fileSpace }};  
    mailTotal = {{  masteraccount.Config.mailLimit }}; mail = {{  masteraccount.Config.mailLimit }};
    contactTotal = {{  masteraccount.Config.contactLimit }}; contact = {{  masteraccount.Config.contactLimit }}; 
    smsTotal = {{  masteraccount.Config.smsLimit }}; sms = {{  masteraccount.Config.smsLimit }};
    smsVTotal = {{  masteraccount.Config.smsVelocity }}; smsV = {{  masteraccount.Config.smsVelocity }};
      mailLimit = {{ config.mailLimit }}; ml = {{ config.mailLimit }}; contactLimit = {{ config.contactLimit }}; cl = {{ config.contactLimit }};
      smsLimit = {{ config.smsLimit }}; sl =  {{ config.smsLimit }};  fileSpace = {{  config.fileSpace }}; fs = {{  config.fileSpace }} ">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <form action="{{url('allied/configedit/' ~ allied.idAllied)}}" method="post" class="form-horizontal">
        <div class="block block-info">          
          <div class="body">

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Almacenamiento :</label>
                <span class="input hoshi input-default  col-sm-6">                                       
                  {{ConfigForm.render('fileSpace', {'class': 'undeline-input' ,  'ng-change': 'spaceEdit(fs)' , 'ng-model': 'fileSpace'})}}
                </span>
                <span  class="col-sm-2 " ng-class="ss > 0 ? 'success' : 'error' " >Disponible: {{"{{ ss }}"}}  </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Limite de correos :</label>
                <span class="input hoshi input-default  col-sm-6">                                       
                  {{ConfigForm.render('mailLimit', {'class': 'undeline-input' ,  'ng-model': 'mailLimit', 'ng-change': 'mailLEdit(ml)'})}}
                </span>
                <span  class="col-sm-2 " ng-class="mail > 0 ? 'success' : 'error' " >Disponible: {{"{{ mail }}"}}  </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Limite de contactos :</label>
                <span class="input hoshi input-default  col-sm-6">                                  
                  {{ConfigForm.render('contactLimit', {'class': 'undeline-input',  'ng-model': 'contactLimit' , 'ng-change': 'contactLEdit(cl)'})}}
                </span>
                <span  class="col-sm-2 " ng-class="contact > 0 ? 'success' : 'error' " >Disponible: {{"{{ contact }}"}}  </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Limite de sms :</label>
                <span class="input hoshi input-default  col-sm-6">                                        
                  {{ConfigForm.render('smsLimit', {'class': 'undeline-input' , 'ng-model': 'smsLimit' , 'ng-change': 'smsLEdit(sl)' })}}
                </span>
                <span  class="col-sm-2 " ng-class="sms > 0 ? 'success' : 'error' " >Disponible: {{"{{ sms }}"}}  </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Mta:</label>
                <span class="input hoshi input-default  col-sm-8">       
                  <select class="undeline-input select2"  id="mta" nam="mta">
                    {% for mt in mta %}
                      {% if(mt.idMta == config.idMta) %}
                        <option value="{{mt.idMta}}" selected>{{mt.name}}</option>
                      {% else %}
                        <option value="{{mt.idMta}}">{{mt.name}}</option>
                      {% endif %}
                    {% endfor %}
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Adaptador:</label>
                <span class="input hoshi input-default  col-sm-8">       
                  <select class="undeline-input select2"  ng-model="adapterSelected"  id="idAdapter">
                    {% for a in adapter %}
                      {% if(a.idAdapter == config.idAdapter) %}
                        <option value="{{a.idAdapter}}" selected>{{a.fname}}</option>
                      {% else %}
                        <option value="{{a.idAdapter}}">{{a.fname}}</option>
                      {% endif %}
                    {% endfor %}
                  </select>
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Mail class :</label>
                <span class="input hoshi input-default  col-sm-8">       
                  <select class="undeline-input select2"  ng-model="mailclassSelected"  id="idMailClass">
                    {% for m in mailclass %}
                      {% if(m.idMailClass == config.idMailClass) %}
                        <option value="{{m.idMailClass}}" selected>{{m.name}}</option>
                      {% else %}
                        <option value="{{m.idMailClass}}">{{m.name}}</option>
                      {% endif %}
                    {% endfor %}
                  </select>
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">Dominio:</label>
                <span class="input hoshi input-default  col-sm-8">       
                  <select class="undeline-input select2"  ng-model="urldomainSelected"  id="idUrldomain">
                    {% for u in urldomain %}
                      {% if(u.idUrldomain == config.idUrldomain) %}
                        <option value="{{u.idUrldomain}}" selected>{{u.name}}</option>
                      {% else %}
                        <option value="{{u.idUrldomain}}">{{u.name}}</option>
                      {% endif %}
                    {% endfor %}
                  </select>
                </span>
              </div>
            </div>


          </div>
          <div class="footer" align="right">                        
            <a href="{{url('system')}}" class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
            <button class="button  btn btn-xs-round   round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
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
            <li ng-show="email">MTA: Los Mail Transport Agent o MTA virtuales son las rutas que se utilizan para el envío de 
              email entre dos servidores de correo, estos tienen asignada una o varias direcciones IP para realizar 
              dicho proceso. Adicionalmente estos MTA cuentan con caracteristicas como: capacidad de envío/hora, 
              reputación y reglas de envío.</li>
            <li ng-show="email">Adapter: Se llama adaptador al canal que se usa para enviar un SMS, este servicio de 
              canales es prestado por lo general por los operadores de telefonía móvil, como Movistar,
              Claro, etc. .</li>
            <li ng-show="email">URL: Es posible que en algunos casos haya que tener más servidores disponibles que se 
              usen solo para cargar las imágenes de los correos para evitar la saturación. Por ello cada cuenta
              se debe configurar con la dirección URL que la plataforma usará para transformar las URL relativas 
              de las imégenes en URL absolutas. .</li>
            <li ng-show="email">Mail Class: Se llama Mail Class a una serie de reglas que se determinan en Green Arrow Engine 
              para clasificar los envíos de correo, configurar la url de retorno de respuesta de rebotados, asignar 
              MTA virtuales, etc.</li>
            <li ng-show="email">El almacenamiento es en megabytes</li>
            <li>Para la fecha de expedición no puede ser inferior a la fecha actual</li>
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

{% endblock %}
