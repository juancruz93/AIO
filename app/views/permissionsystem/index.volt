{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
    {# Bootstrap toggle #}
    {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}

{% block js %}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
    
    {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
    <script type="text/javascript">
        var apiUrl = '{{urlManager.get_api_v1_url() ~ '/security'}}';
    </script>
    
    {# Library.Ember #}
    {{ javascript_include('library/handlebars-1.1.2/handlebars-1.1.2.js') }}
    {{ javascript_include('library/ember-1.7.0/ember-1.7.0.js') }}
    {{ javascript_include('library/ember-1.7.0/ember-data.js') }}

    {# App.Ember #}
    {{ javascript_include('js/ember/mixin-save.js') }}
    {{ javascript_include('js/ember/permission-system/app-permission-system.js') }}
    {{ javascript_include('js/ember/permission-system/role.js') }}
    {{ javascript_include('js/ember/permission-system/resource.js') }}
    {{ javascript_include('js/ember/permission-system/action.js') }}
    {{ javascript_include('js/ember/permission-system/permission.js') }}
{% endblock %}
{% block content %}
<div class="clearfix"></div>
<div class="space"></div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Permisos del Sistema
        </div>
        <hr class="basic-line" />
        <p>
            En esta ventana se administran los roles, recursos, acciones y permisos que puede tener un usuario
            dentro de la aplicación.
        </p>
    </div>
</div>

<div id="app-container" class="container-fluid"> 
        
    <script type="text/x-handlebars">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="box-header">
                    <ul class="nav nav-pills">
                        <li class="active">{{'{{#link-to "roles.index"}}'}}Roles{{'{{/link-to}}'}}</li>
                        <li class="active">{{'{{#link-to "resources.index"}}'}}Recursos{{'{{/link-to}}'}}</li>
                        {# <li class="active">{{'{{#link-to "permissions.index"}}'}}Permisos{{'{{/link-to}}'}}</li> #}
                    </ul>
                </div>    
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                {{'{{outlet}}'}}
            </div>
        </div>     
    </script>
    
    {{ partial('permissionsystem/partials/roles_partial') }}
    {{ partial('permissionsystem/partials/resources_partial') }}
    {{ partial('permissionsystem/partials/actions_partial') }}
    {{ partial('permissionsystem/partials/permissions_partial') }}

</div>
{% endblock %}
