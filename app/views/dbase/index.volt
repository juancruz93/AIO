{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block content %}
  {{ partial('partials/delete_dialog') }}

  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Bases de datos
      </div>            
      <hr class="basic-line" />
      <p>
        Esta es la lista de todas las bases de datos que ha creado.
      </p>
      <div style="float:right;">
        <a href="{{url('dbase/create')}}">
          <button class="button shining btn btn-md success-inverted">
            Crear nueva base de datos
          </button>
        </a>
      </div>
    </div>
  </div>
  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered">
          <thead class="theader">
            <tr>
              <th>Nombre</th>
              <th>Información</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {% for item in page.items %}
              <tr>
                <td>
                  <div class="medium-text" style="color: {{item.color}};    text-shadow: 1px 0px 0px rgba(0, 0, 0, 1);">{{item.name}}</div>
                  <div class="small-text"><em>{{item.description}}</em></div>
                </td>
                <td>
                  <div class="row wrap">
                    <div class="inline-block text-center info">
                      <div class="medium-text">3000</div>
                      Contactos
                    </div>    

                    <div class="inline-block text-center">
                      <a href="{{url('contactlist/show')}}/{{item.idDbase}}">
                        <div class="medium-text">10</div>
                        Listas
                      </a>
                    </div>

                    <div class="inline-block text-center">
                      <div class="medium-text">8</div>
                      Segmentos
                    </div>

                    <div class="inline-block text-center">
                      <div class="medium-text">2</div>
                      Campos personalizados
                    </div>
                  </div>
                </td>


                <td>
                  <div style="float:right">
                    <a href="{{ url('dbase/edit') }}/{{item.idDbase}}" class="button  btn btn-xs-round  round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="Editar base de datos">
                      <span class="glyphicon glyphicon-pencil"</span>
                    </a>
                    <a id="delete" onClick="openModal();" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar base de datos" data-id="{{ url('dbase/delete') }}/{{item.idDbase}}">
                      <span class="glyphicon glyphicon-trash"</span>
                    </a>
                  </div>
                </td>
              </tr>
            {% endfor %}
          </tbody>
        </table>
      </div>
    </div>
  {% else %} 
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No existen bases de datos actualmente, si desea crear una haga <a href="{{url('dbase/create')}}">clic aquí</a>.
            </h2>    
          </div>
        </div>
      </div>
    </div>
  {% endif %}
  <script>
    $(document).on("click", "#delete", function () {
      $('.dialog').addClass('dialog--open');
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
{% endblock %}    
