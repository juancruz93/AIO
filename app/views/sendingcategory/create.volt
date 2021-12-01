{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}   
{% endblock %}
    
{% block content %}
    <div class="clearfix"></div>
    <div class="space"></div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Creación de una nueva Categoria
            </div>            
            <hr class="basic-line" />            
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <form action="{{url('sendingcategory/create')}}" method="post" class="form-horizontal">
                <div class="block block-info">               
                    <div class="body">
                        
                        <div class="form-group">
                            <div class="col-md-12 ">
                                <span class="input hoshi input-default">                                        
                                    {{sendingcategoryForm.render('name')}}
                                    <label class="input-label label-hoshi hoshi-default">
                                        <span class="input-label-content label-content-hoshi">*Nombre:</span>
                                    </label>
                                </span>
                             </div>       
                        </div>
                   
                        <div class="form-group">
                            <div class="col-md-12 ">
                                <span class="input hoshi input-default">                                        
                                    {{sendingcategoryForm.render('description')}}
                                    <label class="input-label label-hoshi hoshi-default">
                                        <span class="input-label-content label-content-hoshi">*Descripción:</span>
                                    </label>
                                </span>
                            </div>        
                        </div>
                    </div>
                        
                    <div class="footer" align="right">                        
                        <a href="{{url('sendingcategory/index')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                        <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
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
                            <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
                            <li>El nombre de la Categoria debe ser un nombre único, es decir, no pueden existir dos Categorias con el mismo nombre.</li>                            
                            <li>Recuerde que los campos con asterisco(*) son oblogatorios.</li>                        
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
