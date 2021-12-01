{% block header %}
{{ stylesheet_link('library/flowchart/app.css') }}

{{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}

{{ javascript_include('library/jquery/jquery-1.11.2.min.js') }}
{{ javascript_include('library/angular-1.5/js/angular.min.js') }}
{{ javascript_include('library/flowchart/flowchart/svg_class.js') }}
{{ javascript_include('library/flowchart/flowchart/mouse_capture_service.js') }}
{{ javascript_include('library/flowchart/flowchart/dragging_service.js') }}
{{ javascript_include('library/flowchart/flowchart/flowchart_viewmodel.js?v=1.0.8') }}
{{ javascript_include('library/flowchart/flowchart/flowchart_directive.js?v=1.0.8') }}
{{ javascript_include('library/flowchart/flowchart/flowchart_services.js?v=1.0.8') }}
{{ javascript_include('library/flowchart/flowchart/flowchart_controller.js?v=1.0.8') }}
{{ javascript_include('library/flowchart/app.js?v=1.0.8') }}
<script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "automaticcampaign";
  </script>
{% endblock %}

{% block content %}
  <body ng-app="app" 
		ng-controller="AppCtrl"
		mouse-capture
		ng-keydown="keyDown($event)"
		ng-keyup="keyUp($event)">
    <flow-chart
		    		style="margin: 5px; width: 100%; height: 100%;"
			      	chart="chartViewModel"
			      	>
			    </flow-chart>
  </body>
  
{% endblock %}