{% extends "templates/default.volt" %}
{% block css %}
    {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}   
{% block js %}
    {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
{% endblock %}    
{% block header %}
    {# Notifications #}
    {{ partial("partials/slideontop_notification_partial") }}
    <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
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
                Editar MTA <em><strong>{{mtaData.name}}</strong></em>
            </div>            
            <hr class="basic-line" />
        </div>
    </div>                                                          

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap"> 
            <form action="{{url('mta/edit')}}/{{mtaData.idMta}}" method="post" class="form-horizontal">
                <div class="block block-info">
                    <div class="body">                    

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label class="col-sm-4 text-right">*Nombre</label>
                                <span class="input hoshi input-default  col-sm-8">                                    
                                    {{form.render('name', {'class': 'undeline-input'} )}}
                                </span>
                            </div>       
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label class="col-sm-4 text-right">*Descripción</label>
                                <span class="input hoshi input-default  col-sm-8">       
                                    {{form.render('description', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>    
                                
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-4 text-right">*Estado</label>
                                <div class="col-sm-8">                                  
                                    <input type="checkbox" id="toggle-one" name="status" {% if mtaData.status %} checked {% endif %}/>
                                </div>
                            </div>       
                        </div>
                    </div>                                            

                    <div class="footer" align="right">
                        <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
                        <a href = {{url('mta/index')}} class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                           <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </div>
                </div>    
            </form>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
            <div class="fill-block fill-block-primary" >
                <div class="header">
                    Instrucciones
                </div>
                <div class="body">
                    <p>
                        Recuerde tener en cuenta estas recomendaciones:
                    </p>
                    <ul>
                        <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
                        <li>El campo nombre debe contener mínimo 2 y máximo 90 caracteres.</li>
                        <li>El campo descripción debe contener mínimo 2 y máximo 90 caracteres.</li>
                        <li>El nombre del MTA debe ser un nombre único, es decir, no pueden existir dos MTA con el mismo nombre.</li>                            
                        <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>                  
                    </ul>
                </div>
            </div>     
        </div>
    </div>                  
{% endblock %}
