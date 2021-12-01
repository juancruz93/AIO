{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ stylesheet_link('library/angular-toastr-master/dist/angular-toastr.min.css') }}
  {{ stylesheet_link('css/checkboxStyle.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.min.css') }}
  {{ stylesheet_link('css/ngrepeat.css') }}
{% endblock %}

{% block js %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {# cargas angular   #}
  {{ javascript_include('library/moment/src/prueba.js') }}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.js') }}
  {{ javascript_include('library/angular-strap-master/test/~1.4.0/components/angular-animate.js') }}
  {{ javascript_include('library/angular-toastr-master/dist/angular-toastr.tpls.min.js') }}{# documentacion https://github.com/Foxandxss/angular-toastr#}
  {{ javascript_include('js/angular/landingpagecategory/services.js') }}
  {{ javascript_include('js/angular/landingpagecategory/controllers.js') }}
  {{ javascript_include('js/angular/landingpagecategory/app.js') }}
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <ui-view></ui-view>

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
  </script>
{% endblock %}