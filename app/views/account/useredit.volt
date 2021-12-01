{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Bootstrap Toggle #}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
{% endblock %}

{% block js %}
  {# Angular #}
{#  {{ javascript_include('js/angular/account/controller.js') }}#}
{{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {# Bootstrap Toggle #}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  {#<script>
    < script >
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
  </script>#}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la información del Usuario
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <form action="{{url('account/useredit')}}/{{(userEdit.idUser)}}" method="post" class="form-horizontal">
        <div class="block block-info">          
          <div class="body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Nombre:</label>
                    <span class="input hoshi input-default col-sm-8">                                   
                      {{UserForm.render('name', { 'class': 'undeline-input'})}}
                    </span>
                  </div>       
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Apellido:</label>
                    <span class="input hoshi input-default col-sm-8">                              
                      {{UserForm.render('lastname', { 'class': 'undeline-input'})}}
                    </span>
                  </div>       
                </div>

{#                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Email:</label>
                    <span class="input hoshi input-default col-sm-8">                                    
                      {{UserForm.render('email', { 'class': 'undeline-input'})}}
                    </span>
                  </div>       
                </div>#}

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 text-right">*Telefono:</label>
                    <span class="input hoshi input-default col-sm-8">                                    
                      {{UserForm.render('cellphone', { 'class': 'undeline-input'})}}
                    </span>
                  </div>       
                </div>                                                                    

{#                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 ">*Rol:</label>
                    <span class="input hoshi input-default col-sm-8"> 
                      {{UserForm.render('idRole', { 'class': 'undeline-input'})}}
                    </span>
                  </div>
                </div>    #}

              </div>    
            </div>    
          </div>

          <div class="footer" align="right">
            <button class="button  btn btn-xs-round shining  round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('account/userlist')}}/{{(userEdit.UserType.idAccount)}}" class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
        </div>
      </form>
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
              <li>El nombre de usuario debe tener mínimo 4 caracteres</li>
              <li>La contraseña debe tener mínimo 8 caracteres</li>
              <li>Los campos con asterisco(*) son obligatorios.</li>
            </ul> 
          </p>
        </div>
        <div class="footer">
          Edición
        </div>
      </div>     
    </div>            
  </div>            

{% endblock %}
