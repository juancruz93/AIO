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

      $('#toggle-two').bootstrapToggle({
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
                Editar Adaptador <strong>{{adapter_value.fname}}</strong>
            </div>
        </div>
    </div>
    <hr class="basic-line" />

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <form action="{{url('adapter/edit/')}}{{adapter_value.idAdapter}}" class="form-horizontal" method="post" >
                <div class="block block-info ">
                    <div class="body">
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Nombre del adaptador</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('fname', {'class': 'undeline-input'})}}
                                    {{adapter_form.render('signal', {'class': 'undeline-input'})}}
                                    {{adapter_form.render('usedlr', {'class': 'undeline-input'})}}
                                    {{adapter_form.render('fsender', {'class': 'undeline-input'})}}
                                    {{adapter_form.render('fixedid', {'class': 'undeline-input'})}}
                                    {{adapter_form.render('coding', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        {#
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Signal</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('signal', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>
                        #}

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Prefijo</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('prefix', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Código corto</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('smscid', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        {#
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Usedlr</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('usedlr', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Sender</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('fsender', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Fixed ID</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('fixedid', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>
                        #}

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Api</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('urlIp', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*Nombre de usuario</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('uname', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">Contraseña</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    {{adapter_form.render('password', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">Estado</label>
                                <span class="input hoshi input-default  col-sm-7">
                                    <input type="checkbox" id="toggle-one" name="status" {% if adapter_value.status %} checked {% endif %}/>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">Internacional</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" id="toggle-two" name="international" {% if adapter_value.international %} checked {% endif %}/>
                                </div>
                            </div>
                        </div>

                        {#
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-5 text-right">*coding</label>
                                <span class="input hoshi input-default col-sm-7">
                                    {{adapter_form.render('coding', {'class': 'undeline-input'})}}
                                </span>
                            </div>
                        </div>
                        #}
                    </div>
                    <div class="footer text-right">
                        <button class="button  btn btn-xs-round   round-button success-inverted" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Guardar adaptador" type="submit"><span class="glyphicon glyphicon-ok"></span></button>
                        <a class="button  btn btn-xs-round   round-button danger-inverted" href="{{url('adapter/index')}}" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Cancelar">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </div>
                </div>
                <input type="hidden" name="updated" value="{{time()}}">
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
