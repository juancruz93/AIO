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

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
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
        Configuracion de la cuenta  <strong>{{subaccount.name}}</strong>
      </div>            
      <hr class="basic-line" />
      <p>
        En esta tabla encontrarán la configuracion de la cuenta.
      </p>            
    </div>
  </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <ul class="padding-left-20px">
          <li>Espacio en disco: <b>{{ space }} MB / {{ subaccount.Account.AccountConfig.diskSpace }} MB</b></li>
          <li>Última actualización: <b>{{ date("Y-m-d",subaccount.Account.AccountConfig.updated) }}</b></li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div ng-cloak>
          <md-content>
            <md-tabs md-dynamic-height md-border-bottom>
              {% for detail in subaccount.Saxs %}
                <md-tab label="{{ detail.Services.name }}" >
                  <md-content class="md-padding">

                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
                      <div class="block ">
                        <table class="table table-bordered sticky-enabled ">
                          <tr>
                            <th>Cantidad</th>
                            <td> {% if detail.amount is defined %}
                                {{ detail.amount| numberf }} / {{ detail.totalAmount | numberf}}
                              {% else %}
                                Sin configurar
                              {% endif %}
                            </td>
                          </tr>
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
                        </table>
                        <div class="" align="right">
                          <a href="{{url('subaccount/index/'~subaccount.idAccount)}}" class="button btn round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Regresar">
                            Regresar
                          </a>
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
              {% endfor %}
            </md-tabs>
          </md-content>
        </div>
      </div>

  </div>
</div>
{% endblock %}

