{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  {{ javascript_include('library/moment/moment.min.js') }}
  {{ stylesheet_link('library/datepicker/css/bootstrap-datetimepicker.min.css') }}
  {{ stylesheet_link('library/bootstrap-multiselect-master/dist/css/bootstrap-multiselect.css') }}
  {{ javascript_include('library/bootstrap-multiselect-master/dist/js/bootstrap-multiselect.js') }}   
  {{ javascript_include('library/datepicker/js/bootstrap-datetimepicker.min.js') }}   
  {{ javascript_include('library/datepicker/js/bootstrap-datetimepicker.es.js') }}

  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}

  <script>
    $(function () {
      $(".select2").select2();

      $('#datetimepicker1').datetimepicker({
        locale: 'es',
        format: 'L LT'
      });
      $('#datetimepicker2').datetimepicker({
        locale: 'es',
        format: 'L LT'
      });

      if ($('#all').prop('checked')) {
        $("#selectAccount").hide();
      }

      if ($('#any').prop('checked')) {
        $("#selectAccount").show();
      }

      $("input[name=allAccounts]").on('click', function () {
        $('input[name=certainAccounts]').attr('checked', false);
        $('#accounts').val("");
        $("#selectAccount").hide();
      });

      $("input[name=certainAccounts]").on('click', function () {
        $('input[name=allAccounts]').attr('checked', false);
        $("#selectAccount").show();
      });

      if ($('#allAllied').prop('checked')) {
        $("#selectAllied").hide();
      }

      if ($('#anyAllied').prop('checked')) {
        $("#selectAllied").show();
      }

      $("input[name=allAllied]").on('click', function () {
        $('input[name=certainAllied]').attr('checked', false);
        $('#allied').val("");
        $("#selectAllied").hide();
      });

      $("input[name=certainAllied]").on('click', function () {
        $('input[name=allAllied]').attr('checked', false);
        $("#selectAllied").show();
      });

    });
  </script> 
  <script type="text/javascript">
    $(document).ready(function () {
      $('#example-dropUp').multiselect({
        enableFiltering: true,
        includeSelectAllOption: true,
        maxHeight: 400,
        dropUp: true
      });
    });
  </script>

{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Creación de un nuevo Mensaje
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>       

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <form action="{{url('flashmessage/create')}}" method="post" class="form-horizontal">
        <div class="block block-info">          
          <div class="body">

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Nombre</label>
                <span class="input hoshi input-default  col-sm-8">                                    
                  {{MessageForm.render('name', {'class': 'undeline-input'} )}}
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Mensaje</label>
                <span class="input hoshi input-default  col-sm-8">       
                  {{MessageForm.render('message', {'class': 'undeline-input'})}}
                </span>
              </div>
            </div>                                             

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Tipo de mensaje</label>
                <span class="input hoshi input-default  col-sm-8">       
                  {{MessageForm.render('type', {'class': 'undeline-input select2', 'id':'input-92', 'required': 'required'})}}
                </span>
              </div>
            </div>                                            

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Clasificación del mensaje</label>
                <span class="input hoshi input-default  col-sm-8">      
                  {{MessageForm.render('category', {'class': 'undeline-input  select2', 'id':'input-93', 'required': 'required'})}}
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Fecha y hora de inicio</label>
                <div class="input hoshi input-default  col-sm-8">      
                  <div class='input-group date' id='datetimepicker1'>
                    {{MessageForm.render('start',{'class': 'form-control'})}}
                    <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                    </span>     
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Fecha y hora de fin</label>
                <div class="input hoshi input-default  col-sm-8">      
                  <div class='input-group date' id='datetimepicker2'>                                        
                    {{MessageForm.render('end',{'class': 'form-control'})}}
                    <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                    </span>     
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Mostrar en:</label>
                <div class="input hoshi input-default  col-sm-8">
                  {{MessageForm.render('target[]', {'class': 'col-lg-12', 'id':'example-dropUp', 'multiple': 'multiple'})}}
                
                </div>
                {#<div class="col-sm-4">
                  {{MessageForm.render('allAccounts')}}                                    
                  <label for="all">Todas las cuentas</label>                                    
                </div>
                <div class="col-sm-4">
                  {{MessageForm.render('certainAccounts')}}                                  
                  <label for="any">Determinadas cuentas</label>
                  <div id="selectAccount" style="display: none;">
                    {{MessageForm.render('accounts') }}
                  </div>
                </div>#}                                
              </div>
            </div>

            {#<div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 "></label>
                <div class="col-sm-4">
                  {{MessageForm.render('allAllied')}}                                    
                  <label for="allAllied">Todas los aliados</label>                                    
                </div>
                <div class="col-sm-4">
                  {{MessageForm.render('certainAllied')}}                                  
                  <label for="anyAllied">Determinados aliados</label>
                  <div id="selectAllied" style="display: none;">
                    {{MessageForm.render('allied') }}
                  </div>
                </div>
              </div>
            </div>#}

          </div>

          <div class="footer" align="right">                        
            
            <button class="button  btn btn-xs-round   round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('flashmessage/index')}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
        </div>
      </form>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>
            <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
          </ul>
          </p>
        </div>
        <div class="footer">
          Creación
        </div>
      </div>     
    </div> 

  </div>
{% endblock %}
