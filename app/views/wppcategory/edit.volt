{% extends "templates/default.volt" %}
{% block css %}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}
{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}  
  <script>
    $(function () {
      $('#toggle-one, #toggle-two, #toggle-three, #toggle-four').bootstrapToggle({
        on: 'Activo',
        off: 'Inactivo',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small',
      });
      
    });

    var n = -1;
  </script>
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de una categoría de WhatsApp
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <form action="{{url('wppcategory/edit')}}/{{wppcategory.idWppCategory}}" method="POST">
        <div class="block block-info">
          <div class="body form-horizontal">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">*Nombre</label>
              <div class="col-sm-10">
                {{form.render('name', {'class': 'undeline-input ' })}}
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">Descripción</label>
              <div class="col-sm-10">
                {{form.render('description',{'class':'undeline-input'})}}
              </div>
            </div>
            <div class="form-group">
              <label for="status" class="col-sm-2 control-label">{{form.label("status")}}</label>
              <div class="col-sm-10">
                <span class="input hoshi input-default" ng-click="sendnow()">
                  {{ form.render('status', {'id': 'toggle-two' , 'ng-model': 'notification', 'ng-click' :  'sendnow()'}) }}
                </span>
              </div>
            </div>
          </div>
          <div class="footer text-right">
            <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('wppcategory#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>

          </div>
        </div>
      </form>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <div class="fill-block fill-block-primary">
        <div class="header">
          Instrucciones
        </div>
        <div class="body">
          <p>Recuerde tener en cuenta estas recomendaciones</p>
          <ul>
            <li>El nombre debe tener mínimo 2 y máximo 80 caracteres</li>
            <li>La descripción debe tener máximo 400 caracteres</li>
            <li>Los campos con asterisco(*) son obligatorios.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
