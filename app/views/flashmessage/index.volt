{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    $(function () {
      $('#details').tooltip();
    });

    var messages = [    {% for item in page.items %}
        {id:{{item.idFlashmessage}}, msg: '{{item.message|json_encode}}'},    {% endfor %}
          ];

          function getMessagePreview(id) {
            for (var i = 0; i < messages.length; i++) {
              if (messages[i].id === id) {
                return messages[i].msg;
              }
            }
          }
  </script>
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de Mensajes Administrativos e Informativos
      </div>            
      <hr class="basic-line" />
      <p>
        Configure mensajes administrativos e informativos para que algunos o todos los clientes puedan 
        verlos en el momento en que inician sesión.
      </p>            
    </div>
  </div>

  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right"> 
        <a href="{{ url('tools') }}" class="button shining btn btn-sm default-inverted">Regresar</a>
        <a href="{{url('flashmessage/create')}}" class="button shining btn btn-sm success-inverted">Crear un nuevo Mensaje</a>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'flashmessage/index']) }}
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered">                
          <thead class="theader">
            <tr>
              <th>Nombre</th>
              <th>Detalles</th>
              <th>Mensaje</th>
              <th></th>
            </tr>
          </thead>
          <tbody>    
            {% for item in page.items %}
              <tr class="{{item.type}}">
                <td>
                  <b>{{item.name}}</b>
                  <br>
                  {% if item.category == "info"%}
                    Informativo
                  {% elseif item.category == "admin"%}
                    Administrativo
                  {% elseif item.category == "footer"%}
                    Pie de página
                  {% endif%}
                  
                  <br>
                  <em class="extra-small-text">Creado por <b>{{item.createdBy}}</b> el día <b>{{date('d/m/Y g:i a',item.created)}}</b> <br>
                    Actualizado por <b>{{item.updatedBy}}</b> el día <b>{{date('d/m/Y g:i a',item.updated)}}</b></em>

                </td>
                <td>   
                  <b>Tipo:</b> <i>{{item.type}}</i>
                  <br>
                  <b>Fecha de inicio: </b>{{date('d/m/Y g:i a',item.start)}}
                  <br>
                  <b>Fecha de fin: </b>{{date('d/m/Y g:i a',item.end)}}

                </td>
                <td>
                  {{item.message}}
                </td>
                <td class="user-actions text-right">
                  <button id="showPreview" class="button shining btn btn-xs-round shining shining-round round-button primary-inverted" data-toggle="modal" data-target="#modal-simple-preview" data-id="{{item.idFlashmessage}}">
                    <span class="glyphicon glyphicon-eye-open"></span>
                  </button>
                  <a href="{{url('flashmessage/edit')}}/{{(item.idFlashmessage)}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar este mensaje">
                    <span class="glyphicon glyphicon-pencil"></span>
                  </a>
                  <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar este Mensaje" data-id="{{url('flashmessage/delete')}}/{{item.idFlashmessage}}">
                    <span class="glyphicon glyphicon-trash"></span>
                  </button>
                </td>
              </tr>                    
            {% endfor %}
          </tbody>
        </table>            
      </div>
    </div>


        <div class="row">
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'flashmessage/index']) }}
        </div>

    {% else %}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            No existen mensajes administrativos ni informativos creados actualmente, si desea crear una haga <a href="{{url('flashmessage/create')}}">clic aquí</a>.
                        </h2>    
                    </div>
                </div>
            </div>
        </div>
    {% endif %}

    <div class="modal fade" id="modal-simple-preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Previsualización de mensaje</h4>
                </div>
                <div class="modal-body">
                    <div id="content-preview">                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="button shining btn btn-md danger-inverted" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

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

    <script>
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

        $(document).on("click", "#showPreview", function () {
            var id = $(this).data('id');
            var message = getMessagePreview(id);
            console.log(message);
            $("#content-preview").empty();
            $('#content-preview').append(message);
        });
    </script>

{% endblock %}
