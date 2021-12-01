{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
{% endblock %}    

{% block js %}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Bootstrap Toggle #}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {# Dialogs #}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <script type="text/javascript">
    $(function () {
      $('#details').tooltip();
    });
    $(document).on("click", "#delete", function () {
      var myURL = $(this).data('id');
      $("#btn-ok").attr('href', myURL);
    });

    function openModal() {
      $('.dialog').addClass('dialog--open');
    }

    function closeModal() {
      $('.dialog').removeClass('dialog--open');
    }
  </script>
  {{ javascript_include('js/search/search-account.js') }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de Sms del lote <strong>{{ sms.name }} </strong>
      </div>            
      <hr class="basic-line" />
      <p>
        En esta lista podra ver los SMS
      </p>            
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap" >
      <a href="{{ url("sms")}}">
        <button class="button  btn btn-sm default-inverted">
          <i class="fa fa-arrow-left"></i>
          Regresar
        </button>
      </a>
    </div>
  </div>

  {% if page.items|length != 0 %}

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'sms/showlote/' ~ sms.idSms]) }}
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered table-responsive" id="resultTable">                
          <thead class="theader">
            <tr>
              <th>Información</th>
              <th>Detalles</th>
              <th>Mensaje</th>
            </tr>
          </thead>
          <tbody> 
            {% for item in page.items %}                    
              <tr>
                <td>
                  <dl>
{#                    <dd class="small-text">Estado:  {{ StringStatus.statussms(item.status) }} </dd>   #}
                    <dd class="small-text">Estado:  {{ StringStatus.statussms((item.response == "0: Accepted for delivery" OR item.response == "PENDING_ENROUTE")?"sent":"undelivered")}} </dd>
                  </dl>
                </td>
                <td>
            <di>
              <dd>Celular: ( {{ item.indicative  }})  {{ item.phone  }} </dd>
            </di>
            </td>
            <td>
              <p> {{ item.message }}</p>
            </td>
            </tr>
          {% endfor %}
          </tbody>
        </table>            
      </div>    
    </div>

    <div class="row">
      {{ partial('partials/pagination_static_partial', ['pagination_url': 'sms/showlote/' ~ sms.idSms]) }}
    </div>

  {% else %}

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              <a href="{{url('sms/showlote/'~ sms.idSms)}}">Click aquí</a>.
            </h2>    
          </div>
        </div>
      </div>
    </div>
  {% endif %}

  <div id="somedialog" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Esta seguro?</h2>
        <div style="z-index: 999999;">           
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
