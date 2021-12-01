{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}

  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {#{{ javascript_include('js/angular/account/controller.js') }}#}
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
    var idServices = {{ idServices }};
    var idAccount = {{ idAccount }};
    var recharges = null;
    var idRangesPrices = null;
    $(function () {
      $('#details').tooltip();
    });
  </script>
  <style type="text/css">
  .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
  }
  .modal-header {
    padding: 15px;
    border-bottom: 1px solid #e5e5e5;
    min-height: 16.42857143px;
    background-color: rgb(63,81,181);
    color: rgba(255,255,255,0.87);
  }
</style>
{% endblock %}

{% block content %}
  <div ng-app="aio" ng-controller="ctrlRecharges" ng-cloak>
    <div class="clearfix"></div>
    <div class="space"></div>     

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Recargas de la cuenta  <strong>{{'{{account.name}}'}}</strong> del servicio  <strong>{{'{{services.name}}'}}</strong>
        </div>            
        <hr class="basic-line" />
        <p>
          En esta tabla encontrarán la recargas de la cuenta.
        </p>            
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">    
        <a ng-show="account.validateUser == true" data-ng-href="../show/{{'{{ account.idAccount }}'}}" class="button  btn btn-md default-inverted">
          <i class="fa fa-arrow-left"></i> Regresar al inicio
        </a> 
        <a ng-show="account.validateUser == false" data-ng-href="{{ url('index') }}" class="button  btn btn-md default-inverted">
          <i class="fa fa-arrow-left"></i> Regresar al inicio
        </a>        
        <a class="button btn round-button info-inverted" data-toggle="modal" data-target="#adjun">
          <i class="fa fa-cart-plus"></i> Recargar
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered table-responsive" id="resultTable">                
          <thead class="theader">
            <tr>
              <th>Fecha de Recarga</th>
              <th>Cantidad Recargada</th>
              <th>Nuevo Saldo Disponible</th>
              <th>Nuevo Limite</th>
              <th>Realizado Por</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="histories in history">
              <td>{{"{{ histories.created * 1000 | date:'yyyy-MM-dd HH:mm:ss' }}"}}</td>
              <td>{{'{{ histories.rechargeAmount }}'}}</td>
              <td>{{'{{ (histories.initialAmount*1) }}'}}</td>
              <td>{{'{{ (histories.initialTotal*1) }}'}}</td>
              <td>{{'{{ histories.createdBy }}'}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="adjun" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" style="color: rgb(250,250,250);">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">Recargar servicio {{'{{ services.name }}'}}</h4>
          </div>
          <div class="modal-body text-center">

            <div class="col-sm-12 col-md-12" ng-if="!is_services">
              <div class="card">
                <div class="card-body">
                  <h1 class="card-title">Plan – Email Marketing</h1>
                  <div class="wpb_wrapper">
                    <p>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Envíos ilimitados mensuales a base de datos.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Administración de listas de contacto y segmentación.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Estadísticas completas por campaña.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Subcuentas y usuarios ilimitados.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Programación de envíos.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Módulo de automatización.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Editor de plantillas drag and drop y  HTML.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      > Soporte por Sistema de tickets (correo electrónico)
                    </p>
                  </div>

                  <div class="range-slider my-5">
                    <h4>Personaliza tu plan</h4>
                    <h6>Tamano de tu lista</h6>
                    <div class="form-group">
                      <select 
                        class="form-control" 
                        ng-model="data" 
                        ng-options="rp as ('Cantidad: '+ rp.quantity + ' Precio: $'+ rp.totalValue) for rp in listRangesprices"
                        ng-change="ngChange(data)"
                      ></select>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            <div class="col-sm-12 col-md-12" ng-if="is_services">
              <div class="card">
                <div class="card-body">
                  <h1 class="card-title">Plan – SMS</h1>

                  <div class="wpb_wrapper">
                    <p>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      /> SMS una vía y doble vía*.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      /> SMS extendido y flash.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt=""
                        width="12" 
                        height="14"
                      /> Estadísticas completas por envío.<br>
                      <img 
                        class="alignnone wp-image-613" 
                        role="img"
                        src="https://web.sigmamovil.com/wp-content/uploads/2020/01/Coete.svg"
                        alt="" 
                        width="12" 
                        height="14"
                      /> Informe y estadísticas parametrizadas
                    </p>
                  </div>

                  <div class="range-slider my-5">
                    <h4>Personaliza tu plan</h4>
                    <h6>Tamano de tu lista</h6>
                    <div class="form-group">
                      <select 
                        class="form-control" 
                        ng-model="data" 
                        ng-options="rp as ('Cantidad: '+ rp.quantity + ' Precio: $'+ rp.totalValue) for rp in listRangesprices"
                        ng-change="ngChange(data)"
                      ></select>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>
          <div class="modal-footer" ng-show="objectRangePrices">

            <form data-dismiss="modal" class="text-center" onclick="myFunction()">
              <script
                src="https://checkout.epayco.co/checkout.js"
                class="epayco-button"
                data-epayco-key="d82cab2f67dda9bcc59182c2f627ba75"
                data-epayco-name="Sigma Móvil S.A.S"
                data-epayco-description="Vestido Mujer Primavera"
                data-epayco-currency="cop"
                data-epayco-country="co"
                data-epayco-test="false"
                data-epayco-external="false"
                data-epayco-extra1=""
                data-epayco-extra2=""
                data-epayco-response="https://ejemplo.com/respuesta.html"
                data-epayco-confirmation="https://ejemplo.com/confirmacion"
              >
              </script>
              <!-- en data-epayco-test true para modo pruebas -->
              <script type="text/javascript">
                function myFunction() {
                  var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
                  var templateBase = "account";
                  //
                  var response = fullUrlBase + templateBase + "/response";
                  var confirmation = fullUrlBase + templateBase + "/confirmation";
                  var quantity = "Recargar " + recharges.quantity + " Saldo";
                  // Si se colocan mas script anteriores de acuerdo a la cantidad de script aumentar el valor 6
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-amount", recharges.totalValue); 
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-name", "Sigma Móvil S.A.S"); 
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-description", String(quantity)); 
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-extra1", idRangesPrices); 
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-extra2", idAccount); 
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-response", String(response)); 
                  document.getElementsByTagName("script")[6].setAttribute("data-epayco-confirmation", String(confirmation)); 
                }
              </script>

            </form>

          </div>
        </div>
      </div>
    </div>

  </div>

{% endblock %}