{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
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
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de Cuentas Maestras
      </div>            
      <hr class="basic-line" />
      <p>
        En esta lista encontrarán las cuentas maestras que existen en nuestra plataforma.
      </p>            
    </div>
  </div>

  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
        <a href="{{url('masteraccount/create')}}" class="button shining btn btn-sm success-inverted">Crear una nueva cuenta maestra</a>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'masteraccount/index']) }}
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
        <table class="table table-bordered sticky-enabled ">                
          <thead class="theader">
            <tr>
              <th>ID</th>            
              <th>Detalles</th>
              <th>Ubicacion</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {% for item in page.items %}
              <tr {% if item.status == 0 %} class="danger letter-no-hover" {% endif %}>
                <td>{{item.idMasteraccount}}</td>      
                <td>
                  <font class="strong-text ng-binding medium-text" >
                  {{item.name}}
                  </font>
                  <dl>
                    <dd>Nit: {{item.nit}}</dd>
                    <dd class="extra-small-text"> Telefono: {{item.phone}} </dd>
                    <dd> <em class="extra-small-text">Creado por <strong>{{(item.createdBy)}}</strong> , a las <strong>{{date('d/m/Y g:i a', item.created)}}</strong> </em></dd>
                    <dd> <em class="extra-small-text">Actualizado por <strong> {{(item.updatedBy)}}</strong>, a las  <strong>{{date('d/m/Y g:i a', item.updated)}}</strong></em></dd>
                  </dl>
                </td>
                <td>
                  Direccion:   {{item.address}}
            <di>
              <dd><em>{{item.city.state.country.name}}, {{item.city.state.name}}, {{item.city.name}} </em></dd>
            </di>
            </td>
            <td class="user-actions text-right">
              {% if(user.Role.idRole == -1) %}
                <a href="{{url('masteraccount/aliaslistuser')}}/{{item.idMasteraccount}}" class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Usuarios maestros de esta cuenta">
                  <span class="glyphicon glyphicon-user"></span>
                </a>
              {% endif %}
              <a href="{{url('masteraccount/aliaslist')}}/{{item.idMasteraccount}}" class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Aliados de esta cuenta maestra">
                <span class="glyphicon glyphicon-list-alt"></span>
              </a>
              <a href="{{url('masteraccount/show')}}/{{item.idMasteraccount}}" class="button btn btn-xs-round default-inverted" data-toggle="tooltip" data-placement="top" title="Informacion completa de la cuenta maestra">
                <span class="glyphicon glyphicon-eye-open"></span>
              </a>
              <a href="{{url('masteraccount/edit')}}/{{item.idMasteraccount}}" class="button btn btn-xs-round info-inverted" data-toggle="tooltip" data-placement="top" title="Editar esta cuenta maestra">
                <span class="glyphicon glyphicon-pencil"></span>
              </a>                 
              {#<button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar esta cuenta maestra" data-id="{{url('masteraccount/delete')}}/{{item.idMasteraccount}}">
                  <span class="glyphicon glyphicon-trash">
              </button>#}
            </td>
            </tr>
            </tbody>
          {% endfor %}                
        </table>            
      </div>    
    </div>
    <div class="row">
      {{ partial('partials/pagination_static_partial', ['pagination_url': 'masteraccount/index']) }}
    </div>
  {% else %}    
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No existen cuentas maestras creadas actualmente, si desea crear una haga <a href="{{url('masteraccount/create')}}">clic aquí</a>.
            </h2>    
          </div>
        </div>
      </div>
    </div>
  {% endif %}

  {# <div id="somedialog" class="dialog">
     <div class="dialog__overlay"></div>
     <div class="dialog__content">
       <div class="morph-shape">
         <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
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
   </div>#}

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
  </script>


{% endblock %}    
