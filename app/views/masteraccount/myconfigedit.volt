{% extends "templates/default.volt" %}
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

      $(".select2").select2();
    });
  </script>   

{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la configuración de mi cuenta maestra <strong>{{ masteraccount.name }}</strong>
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>       

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <form action="{{url('masteraccount/myconfigedit/' ~ masteraccount.idMasteraccount)}}" method="post" class="form-horizontal">
        <div class="block block-info">          
          <div class="body">
            {% for item in masteraccount.mxs  %}
              {% if(item.idServices == services.sms) %}
                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Límite de sms :</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      {{configForm.render('smsLimit', {'class': 'undeline-input' , 'placeholder':'Limite de sms', 'ng-model': 'smsLimit'})}}
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Velocidad de sms :</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      {{configForm.render('smsVelocity', {'class': 'undeline-input' , 'placeholder':'Velocidad de sms', 'ng-model': 'smsVelocity'})}}
                    </span>
                  </div>
                </div>
              {% endif %}
              {% if(item.idServices == services.email_marketing) %}
                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Mta:</label>
                    <span class="input hoshi input-default  col-sm-8 ">       
                      <select name="idMta[]" class="select2 select2-hidden-accessible" multiple="" placeholder="*Plataformas"  tabindex="-1" aria-hidden="true">
                        {% for item in mta %}
                          <option value="{{ item.idMta }}"  {% for a in masteraccount.maxmta %}{% if a.idMta == item.idMta %}selected{% endif %}{% endfor %}  >{{ item.name }}</option>
                        {% endfor %}
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Adaptador:</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      <select name="idAdapter[]" class="select2 select2-hidden-accessible" multiple="" placeholder="*Plataformas" tabindex="-1" aria-hidden="true">
                        {% for item in adapter %}
                          <option value="{{ item.idAdapter }}"  {% for a in masteraccount.maxadapter %}{% if a.idAdapter == item.idAdapter %}selected{% endif %}{% endfor %}  >{{ item.fname }}</option>
                        {% endfor %}
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Url :</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      <select name="idUrldomain[]" class="select2 select2-hidden-accessible" multiple="" placeholder="*Plataformas"  tabindex="-1" aria-hidden="true">
                        {% for item in urlDomain %}
                          <option value="{{ item.idUrldomain }}"  {% for a in masteraccount.maxurldomain %}{% if a.idUrldomain == item.idUrldomain %}selected{% endif %}{% endfor %}  >{{ item.name }}</option>
                        {% endfor %}
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Mail class :</label>
                    <span class="input hoshi input-default  col-sm-8">     
                      <select name="idMailClass[]" class="select2 select2-hidden-accessible" multiple="" placeholder="*Plataformas" tabindex="-1" aria-hidden="true">
                        {% for item in mailclass %}
                          <option value="{{ item.idMailClass }}"  {% for a in masteraccount.maxmailclass %}{% if a.idMailClass == item.idMailClass %}selected{% endif %}{% endfor %}  >{{ item.name }}</option>
                        {% endfor %}
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Almacenamiento (MB) :</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      {{configForm.render('fileSpace', {'class': 'undeline-input' , 'placeholder':'Almacenamiento', 'ng-model': 'fileSpace'})}}
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Límite de correos :</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      {{configForm.render('mailLimit', {'class': 'undeline-input' , 'placeholder':'Limite de correos', 'ng-model': 'mailLimit'})}}
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-right">Límite de contactos :</label>
                    <span class="input hoshi input-default  col-sm-8">       
                      {{configForm.render('contactLimit', {'class': 'undeline-input' , 'placeholder':'Limite de contactos', 'ng-model': 'contactLimit'})}}
                    </span>
                  </div>
                </div>
              {% endif %}
            {% endfor %}

            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Fecha de expiración :</label>
                <span class="input hoshi input-default  col-sm-8">       
                  {{configForm.render('expiryDate', {'class': 'undeline-input' ,  'ng-model': 'expiryDate'})}}
                </span>
              </div>
            </div>

          </div>
          <div class="footer" align="right">
              <button class="button  btn btn-xs-round   round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('masteraccount/')}}" class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
