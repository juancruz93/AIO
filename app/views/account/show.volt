{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

{#  {{ javascript_include('js/angular/account/controller.js') }}#}
{{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  <script type="text/javascript">
    $(function () {
      $('#details').tooltip();
    });
  </script>
{% endblock %}

{% block content %}
  <div ng-app="aio">
    <div class="clearfix"></div>
    <div class="space"></div>     

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Configuracion de la cuenta  <strong>{{account.name}}</strong>
        </div>            
        <hr class="basic-line" />
        <p>
          En esta tabla encontrarán la configuracion de la cuenta.
        </p>            
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <span class="text-2em">Nombre del plan: <b>{{ account.PaymentPlan.name }}</b></span>
        <ul class="padding-left-20px">
          <li>Espacio en disco: <b>{{ space }} MB / {{ account.AccountConfig.diskSpace }} MB</b></li>
          <li>Última actualización: <b>{{ date("Y-m-d",account.AccountConfig.updated) }}</b></li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div ng-cloak>
          <md-content>
            <md-tabs md-dynamic-height md-border-bottom>
              {% set count = 0 %}
              {% for detail in account.AccountConfig.DetailConfig %}
                <md-tab label="{{ detail.Services.name }}" >
                  <md-content class="md-padding">

                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
                      <div class="block ">
                        <table class="table table-bordered sticky-enabled ">
                          <tr>
                            <th>Tipo de plan</th>
                            <td>
                              {% if detail.PlanType.name is defined %}
                                {{ detail.PlanType.name }}
                              {% else %}
                                Sin configurar
                              {% endif %}
                            </td>
                          </tr>
                          <tr>
                            <th>Lista de precio</th>
                            <td>
                              {% if detail.PriceList.name is defined %}
                                {{ detail.PriceList.name }} / {{ priceSetted[count] }}
                              {% else %}
                                Sin configurar
                              {% endif %}
                            </td>
                          </tr>
                          <tr>
                            <th>Cantidad</th>
                            <td> {% if detail.amount is defined %}
                              {{ detail.amount| numberf }} / {{ detail.totalAmount| numberf }}
                            {% else %}
                              Sin configurar
                              {% endif %}
                              </td>
                            </tr>
                            {% if detail.speed is defined %}
                              <tr>
                                <th>Velocidad de envio</th>
                                <td>
                                  {{ detail.speed }} Mensajes por minuto
                                </td>
                              </tr>
                            {% endif %}
                            {% if detail.accountingMode is defined %}
                              <tr>
                                <th>Modo de cuenta</th>
                                <td>
                                  {% if detail.accountingMode == "contact" %}
                                    Por contacto
                                  {% elseif detail.accountingMode == "sending" %}
                                    Por envio
                                  {% endif %}
                                </td>
                              </tr>
                            {% endif %}
                            {% if detail.Dcxmta|length > 0 %}
                              <tr>
                                <th>MTA</th>
                                <td>
                                  {% for item in detail.Dcxmta %}
                                    {{ item.mta.name  }}
                                    {% if  loop.last %}
                                    {% else %}
                                      ,
                                    {% endif %}
                                  {% endfor %}
                                </td>
                              </tr>
                            {% endif %}
                            {% if detail.Dcxadapter|length > 0 %}
                              <tr>
                                <th>Canal</th>
                                <td>
                                  {% for item in detail.Dcxadapter %}
                                    {{ item.Adapter.fname }}
                                    {% if  loop.last %}
                                    {% else %}
                                      ,
                                    {% endif %}
                                  {% endfor %}
                                </td>
                              </tr>
                            {% endif %}
                            {% if detail.Dcxurldomain|length > 0 %}
                              <tr>
                                <th>Url</th>
                                <td>
                                  {% for item in detail.Dcxurldomain %}
                                    {{ item.urldomain.name  }}
                                    {% if  loop.last %}
                                    {% else %}
                                      ,
                                    {% endif %}
                                  {% endfor %}
                                </td>
                              </tr>
                            {% endif %}
                            {% if detail.Dcxmailclass|length > 0 %}
                              <tr>
                                <th>Mail class</th>
                                <td>
                                  {% for item in detail.Dcxmailclass %}
                                    {{ item.MailClass.name }}
                                    {% if  loop.last %}
                                    {% else %}
                                      ,
                                    {% endif %}
                                  {% endfor %}
                                </td>
                              </tr>
                            {% endif %}
                          </table>
                          <div class="" align="right">
                            {% if(user.Role.idRole == 4) %}
                              <a href="{{url('account/')}}" class="button btn round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Regresar">
                                Regresar
                              </a>
                            {% endif %}
                            {% if(user.Role.idRole == 5) %}
                              <a href="{{url('accounts')}}" class="button btn round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Regresar">
                                Regresar
                              </a>
                            {% endif %}
                            {% if accountS == 1 and detail.idServices == 1 or detail.idServices == 2 %}
                              <a href="{{url('account/recharges/' ~ detail.idServices)}}" class="button btn round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Recargas">
                                Recargas
                              </a>
                            {% endif %}
                          </div>
                        </div>
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
                              {% if detail.Dcxmta|length > 0 %}
                                <li>MTA: Los Mail Transport Agent o MTA virtuales son las rutas que se utilizan para el envío de
                                  email entre dos servidores de correo, estos tienen asignada una o varias direcciones IP para realizar
                                  dicho proceso. Adicionalmente estos MTA cuentan con caracteristicas como: capacidad de envío/hora,
                                  reputación y reglas de envío.</li>
                                {% endif %}
                                {% if detail.Dcxadapter|length > 0 %}
                                <li>Adapter: Se llama adaptador al canal que se usa para enviar un SMS, este servicio de
                                  canales es prestado por lo general por los operadores de telefonía móvil, como Movistar,
                                  Claro, etc. .</li>
                                {% endif %}
                                {% if detail.Dcxurldomain|length > 0 %}
                                <li>URL: Es posible que en algunos casos haya que tener más servidores disponibles que se
                                  usen solo para cargar las imágenes de los correos para evitar la saturación. Por ello cada cuenta
                                  se debe configurar con la dirección URL que la plataforma usará para transformar las URL relativas
                                  de las imégenes en URL absolutas. .</li>
                                {% endif %}
                                {% if detail.Dcxmailclass|length > 0 %}
                                <li>Mail Class: Se llama Mail Class a una serie de reglas que se determinan en Green Arrow Engine
                                  para clasificar los envíos de correo, configurar la url de retorno de respuesta de rebotados, asignar
                                  MTA virtuales, etc.</li>
                                {% endif %}
                              <li>El almacenamiento es en megabytes</li>
                            </ul>
                            </p>
                          </div>
                          <div class="footer">
                            Información
                          </div>
                        </div>
                      </div>

                    </md-content>
                  </md-tab>
                  {% set count=count+1 %}   
                  {% endfor %}
                  </md-tabs>
                </md-content>
              </div>
            </div>

          </div>
        </div>
        {% endblock %}

