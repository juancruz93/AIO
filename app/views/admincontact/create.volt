{% extends "templates/default.volt" %}
{% block css %}
    {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block js %}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block content %}
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Creación de un nuevo contacto administrativo
            </div>            
            <hr class="basic-line" />            
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <form action="{{url('admincontact/create')}}/{{idAllied}}" method="POST">
                <div class="block block-info">
                    <div class="body form-horizontal">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">*Nombre</label>
                            <div class="col-sm-10">
                                {{form.render('name', {'class': 'undeline-input ' })}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-2 control-label">*Apellido</label>
                            <div class="col-sm-10">
                                {{form.render('lastname', {'class': 'undeline-input ' })}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">*Correo</label>
                            <div class="col-sm-10">
                                {{form.render('email', {'class': 'undeline-input ' })}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="col-sm-2 control-label">*Teléfono</label>
                            <div class="col-sm-10">
                                {{form.render('phone', {'class': 'undeline-input ' })}}
                            </div>
                        </div>
                    </div>
                    <div class="footer text-right">
                        <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
                        <a href="{{url('admincontact/index')}}/{{(idAllied)}}/{{ user.UserType.idMasteraccount }}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
                    <p>Recuerde tener en cuentas estas recomendaciones</p>
                    <ul>
                        <li>El nombre y apellido debe tener mínimo 3 y máximo 70 caracteres</li>
                        <li>El correo debe tener una estructura válida (example@example.com)</li>
                        <li>El teléfono debe tener mínimo 7 digitos</li>
                        <li>Los campos con asterisco(*) son obligatorios.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
