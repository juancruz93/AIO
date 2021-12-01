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
        Lista de Sms del lote <strong>TAL </strong>
      </div>            
      <hr class="basic-line" />
      <p>
        En esta lista podra ver los SMS
      </p>            
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap" >
      <a href="{{ url("smstwoway")}}">
        <button class="button  btn btn-sm default-inverted">
          <i class="fa fa-arrow-left"></i>
          Regresar
        </button>
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
      {#      {{ partial('partials/pagination_static_partial', ['pagination_url': 'sms/showlote/' ~ sms.idSms]) }}
      #}    </div>
  </div>

    <div id="pagination" class="text-center">
                <ul class="pagination">
                    <li ng-class="page == 1 ? 'disabled'  : ''">
                        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="page == 1 ? 'disabled'  : ''">
                        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{smstwoway.total }}"}}
                            </b> registros </span><span>Página <b>{{"{{ page }}"}}
                            </b> de <b>
                                {{ "{{ (smstwoway.total_pages ) }}"}}
                            </b></span>
                    </li>
                    <li   ng-class="page == (smstwoway.total_pages) || smstwoway.total_pages == 0 ? 'disabled'  : ''">
                        <a href="#/" ng-click="page == (smstwoway.total_pages)  || smstwoway.total_pages == 0  ? true  : false || page == (smstwoway.total_pages)  || smstwoway.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="page == (smstwoway.total_pages)  || smstwoway.total_pages == 0 ? 'disabled'  : ''">
                        <a ng-click="page == (smstwoway.total_pages)  || smstwoway.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                </ul>
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
          {#{% for item in page.items %}   #}                 
            <tr>
              <td>
                <dl>
                  <dd class="small-text">Estado:  {#{{ StringStatus.statussms(item.status) }}#} </dd>
                </dl>
              </td>
              <td>
          <di>
            <dd>Celular: {#( {{ item.indicative  }})  {{ item.phone  }}#} </dd>
          </di>
          </td>
          <td>
            <p> {#{{ item.message }}#}</p>
          </td>
          </tr>
{#        {% endfor %}
#}        </tbody>
      </table>            
    </div>    
  </div>


{% endblock %}