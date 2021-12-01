{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Picker Color #}
  {{ stylesheet_link('library/colorpicker/colorPicker.css') }}
  {{ javascript_include('library/colorpicker/jquery.colorPicker.js') }}
  <script>
    jQuery(document).ready(function ($) {
      $('#color').colorPicker({
        pickerDefault: "ffffff",
        colors: [
          "FFECEC", "FFDFF8", "F9D9FF", "E6DBFF", "D9F3FF", "C9EAF3", "CAFFD8", "D0E6FF",
          "EAFEE2", "FFFFE3", "FFF2F2", "E3FBE9"
        ], transparency: true});
    });
  </script>
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Editar base de datos: <em><strong>{{dbase_value.name}}</strong></em>
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>
  <div class="row">
    <form action="{{url('dbase/edit/')}}{{dbase_value.idDbase}}" class="form-horizontal" method="post" >
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="block block-info">
          <div class="body">

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Nombre</label>
                <span class="input hoshi input-default  col-sm-8">   
                  <input name="name" class="undeline-input" value="{{dbase_value.name}}" required>
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Descripción</label>
                <span class="input hoshi input-default  col-sm-8">   
                  <input name="description" class="undeline-input" value="{{dbase_value.description}}">
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Color</label>
                <span class="input hoshi input-default  col-sm-8">   
                  <input id="color" name="color" type="text" value="{{dbase_value.color}}" />
                </span>
              </div>
            </div>

          </div>
          <div class="footer" align="right">                                                
            <a href = {{url('dbase/index')}} class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
               <span class="glyphicon glyphicon-remove"></span>
            </a>
            <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </div>
        </div>
      </div>
    </form>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>                            
            <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>
            <li>El nombre de la base de datos debe ser un nombre único, es decir, no pueden existir dos bases de datos con el mismo nombre.</li>
            <li>El color le permite distinguir de manera rápida cada base de datos.</li>
            <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
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
