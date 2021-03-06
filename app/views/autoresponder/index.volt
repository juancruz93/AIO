{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}
  {{stylesheet_link('css/button_help.css')}}
  {{ stylesheet_link('library/dialog-effects/css/dialog.min.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.min.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.min.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.min.css') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  {#<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">#}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  {#<link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">#}
  {{ stylesheet_link('library/tarruda/tarruda.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}
  
  {# estilo del checkbox con apariencia de switch #}
  {{ stylesheet_link('css/checkboxStyle.css') }}  
  
{% endblock %}

{% block js %}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {# {{ javascript_include('js/angular/autoresponder/app.js') }}
  {{ javascript_include('js/angular/autoresponder/controllers.js') }}
  {{ javascript_include('js/angular/autoresponder/services.js') }} #}
  {{ javascript_include('js/angular/autoresponder/dist/autoresponder.5aa8d6c767d6fa9ec0e9.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}

  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  {{ javascript_include('library/moment/src/moment.js') }}
  
  
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" >
      <div ui-view></div>
    </div>
  </div>
  <div id="container-floating">
    <div class="nd1 nds" data-toggle="tooltip" data-placement="left" title="Youtube"><img class="reminder">
      <!-- <p class="letter"></p> -->
      <?xml version="1.0" encoding="iso-8859-1"?>
      <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
      <a href="https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ/playlists" target="_blank">
      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
      <circle style="fill:#D22215;" cx="256" cy="256" r="256"/>
      <path style="fill:#A81411;" d="M384.857,170.339c-7.677,2.343-15.682,4.356-23.699,6.361
        c-56.889,12.067-132.741-20.687-165.495,32.754c-27.317,42.494-35.942,95.668-67.017,133.663L294.629,509.1
        c110.47-16.72,197.773-104.036,214.476-214.511L384.857,170.339z"/>
      <path style="fill:#FFFFFF;" d="M341.649,152.333H170.351c-33.608,0-60.852,27.245-60.852,60.852v85.632
        c0,33.608,27.245,60.852,60.852,60.852h171.298c33.608,0,60.852-27.245,60.852-60.852v-85.632
        C402.501,179.578,375.256,152.333,341.649,152.333L341.649,152.333z M300.494,260.167l-80.12,38.212
        c-2.136,1.019-4.603-0.536-4.603-2.901v-78.814c0-2.4,2.532-3.955,4.67-2.87l80.12,40.601
        C302.947,255.602,302.904,259.019,300.494,260.167L300.494,260.167z"/>
      <path style="fill:#D1D1D1;" d="M341.649,152.333h-87.373v78.605l46.287,23.455c2.384,1.208,2.341,4.624-0.069,5.773l-46.218,22.044
        v77.459h87.373c33.608,0,60.852-27.245,60.852-60.852v-85.632C402.501,179.578,375.256,152.333,341.649,152.333z"/>
      
      </svg>
    </a>
    </div>
    <div class="nd3 nds" data-toggle="tooltip" data-placement="left" title="Escr??benos">
      <a href="https://api.whatsapp.com/send?phone=573006855555&text=Hola Sigmamovil" target="_blank">
        <?xml version="1.0" encoding="iso-8859-1"?>
  <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
   viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
  <circle style="fill:#75B73B;" cx="256" cy="256" r="256"/>
  <path style="fill:#52891D;" d="M360.241,151.826c-14.843-3.712-36.671-16.532-50.8-21.671
  c-55.165-17.239-129.293-3.448-149.98,60.337c-1.724,1.724-5.172,1.724-6.896,1.724c-41.374,48.269-13.791,106.882-17.239,160.323
  c-1.177,18.839-11.083,35.497-23.831,49.588l107.282,107.17C230.931,511.067,243.355,512,256,512
  c126.436,0,231.452-91.665,252.247-212.161L360.241,151.826z"/>
  <g>
  <path style="fill:#FFFFFF;" d="M248.837,108.447c-78.029,3.712-139.604,68.696-139.369,146.811
    c0.072,23.792,5.816,46.249,15.95,66.095l-15.557,75.514c-0.841,4.086,2.843,7.663,6.901,6.701l73.995-17.53
    c19.011,9.471,40.364,14.939,62.962,15.284c79.753,1.219,146.251-62.105,148.74-141.829
    C405.121,174.035,334.591,104.362,248.837,108.447L248.837,108.447z M337.13,335.936c-21.669,21.669-50.483,33.604-81.13,33.604
    c-17.944,0-35.126-4.027-51.066-11.966l-10.302-5.134l-45.37,10.747l9.549-46.356l-5.075-9.943
    c-8.276-16.206-12.472-33.728-12.472-52.084c0-30.648,11.935-59.459,33.604-81.13c21.476-21.478,50.759-33.604,81.134-33.604
    c30.644,0,59.458,11.935,81.127,33.604c21.669,21.669,33.604,50.483,33.604,81.127C370.735,285.177,358.607,314.459,337.13,335.936
    L337.13,335.936z"/>
  <path style="fill:#FFFFFF;" d="M327.115,286.582l-28.384-8.149c-3.729-1.069-7.749-0.01-10.468,2.76l-6.942,7.07
    c-2.926,2.984-7.366,3.941-11.24,2.374c-13.427-5.434-41.672-30.548-48.881-43.106c-2.084-3.624-1.739-8.152,0.817-11.462
    l6.058-7.839c2.374-3.07,2.874-7.197,1.305-10.747l-11.941-27.008c-2.86-6.468-11.126-8.352-16.527-3.784
    c-7.921,6.701-17.32,16.88-18.461,28.16c-2.015,19.887,6.515,44.954,38.762,75.055c37.257,34.778,67.094,39.369,86.523,34.664
    c11.019-2.667,19.825-13.365,25.379-22.126C336.906,296.467,333.91,288.535,327.115,286.582L327.115,286.582z"/>
  </g>
  <g>
  <path style="fill:#D1D1D1;" d="M356.004,147.708l-22.223,22.778c1.131,1.045,2.257,2.096,3.351,3.191
    c21.67,21.669,33.604,50.483,33.604,81.127c0,30.375-12.128,59.656-33.604,81.134c-21.669,21.669-50.483,33.604-81.13,33.604
    c-17.944,0-35.125-4.027-51.066-11.966l-10.302-5.134l-45.37,10.747l0.938-4.553l-40.174,41.172
    c0.886,2.663,3.705,4.475,6.734,3.758l73.995-17.53c19.011,9.471,40.364,14.939,62.962,15.284
    c79.753,1.219,146.253-62.105,148.74-141.829C403.834,215.357,385.686,175.435,356.004,147.708z"/>
  <path style="fill:#D1D1D1;" d="M327.115,286.582l-28.384-8.149c-3.729-1.069-7.749-0.01-10.468,2.76l-6.942,7.07
    c-2.926,2.984-7.366,3.941-11.24,2.374c-7.756-3.139-20.451-12.845-31.185-22.904l-19.732,20.225
    c0.677,0.648,1.352,1.295,2.05,1.948c37.257,34.778,67.094,39.369,86.523,34.664c11.019-2.667,19.825-13.365,25.379-22.126
    C336.906,296.467,333.91,288.535,327.115,286.582z"/>
  </g>
  </svg>
  
        </a>
    </div>
  
    <div class="nd4 nds" data-toggle="tooltip" data-placement="left" title="Integraciones"><img class="reminder">
     <a href="https://drive.google.com/file/d/1rppABXW4QII3XqV5a6RGOyBADChQDN5P/view?usp=sharing" target="_blank"   rel="noopener noreferrer" data-toggle="tooltip" data-placement="top">
      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
      viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
   <path style="fill:orange;" d="M512,256.006C512,397.402,397.394,512.004,256.004,512C114.606,512.004,0,397.402,0,256.006
     C-0.007,114.61,114.606,0,256.004,0C397.394,0,512,114.614,512,256.006z"/>
   <path style="fill:orange;" d="M510.219,285.803c-0.011-0.007-0.016-0.019-0.016-0.019
     c-0.474-0.666-157.836-158.029-158.494-158.492c-1.253-1.785-3.226-3.025-5.574-3.025H210.586c-0.338-0.477-0.81-0.801-1.247-1.172
     l-28.519-28.61c-0.38-0.603-0.979-0.972-1.509-1.42l-0.158-0.25c-1.266-2.004-3.481-3.216-5.851-3.216H96.533
     c-3.833,0-6.933,3.102-6.933,6.933V325.13c0,0.569,0.067,1.131,0.21,1.679c1.582,6.334,4.884,11.731,9.285,15.997
     c0.494,0.553,167.839,167.824,168.401,168.374c0.162,0.183,0.345,0.343,0.516,0.516
     C393.852,505.887,495.903,409.243,510.219,285.803z"/>
   <g>
     <path style="fill:#F4F6F9;" d="M332.267,242.133c-49.698,0-90.133,40.435-90.133,90.133s40.435,90.133,90.133,90.133
       s90.133-40.435,90.133-90.133S381.965,242.133,332.267,242.133z M332.267,408.533c-42.053,0-76.267-34.213-76.267-76.267
       S290.213,256,332.267,256s76.267,34.213,76.267,76.267S374.32,408.533,332.267,408.533z"/>
     <path style="fill:#F4F6F9;" d="M357.617,334.921L339.2,353.347v-58.902c0-3.833-3.101-6.933-6.933-6.933s-6.933,3.101-6.933,6.933
       v58.902l-18.417-18.427c-2.708-2.708-7.095-2.708-9.804,0c-2.708,2.708-2.708,7.095,0,9.804l30.243,30.257
       c0.636,0.638,1.396,1.149,2.241,1.502c0.838,0.35,1.743,0.539,2.67,0.539c0.927,0,1.831-0.189,2.67-0.539
       c0.845-0.352,1.605-0.863,2.241-1.502l30.243-30.257c2.708-2.708,2.708-7.095,0-9.804
       C364.713,332.212,360.325,332.212,357.617,334.921z"/>
     <path style="fill:#F4F6F9;" d="M207.467,339.2h-54.803c2.755-3.602,4.896-7.73,6.06-12.39c0.142-0.548,0.21-1.11,0.21-1.679V172.8
       h208v41.6c0,3.833,3.101,6.933,6.933,6.933s6.933-3.101,6.933-6.933v-48.533c0-3.833-3.101-6.933-6.933-6.933h-20.8V131.2
       c0-3.833-3.101-6.933-6.933-6.933H199.125l-19.974-31.451c-1.266-2.004-3.481-3.216-5.85-3.216H96.533
       c-3.833,0-6.933,3.101-6.933,6.933v228.597c0,0.569,0.067,1.131,0.21,1.679c3.762,15.061,16.849,25.25,32.809,25.924
       c0.543,0.135,1.063,0.333,1.648,0.333h83.2c3.833,0,6.933-3.101,6.933-6.933S211.299,339.2,207.467,339.2z M103.467,324.223
       V103.467h66.022l19.974,31.451c1.266,2.004,3.481,3.216,5.85,3.216H339.2v20.8H152c-3.833,0-6.933,3.101-6.933,6.933v158.356
       c-3.129,10.887-13.082,14.774-20.8,14.774S106.595,335.11,103.467,324.223z"/>
   
   </svg>
     </a>
    </div>
    <div class="nd5 nds" data-toggle="tooltip" data-placement="left" title="soporte@sigmamovil.com.co"><img class="reminder">
      <a href="mailto:soporte@sigmamovil.com.co">
      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
      viewBox="0 0 299.997 299.997" style="enable-background:new 0 0 299.997 299.997;" xml:space="preserve">
   
       <path d="M149.996,0C67.157,0,0.001,67.158,0.001,149.997c0,82.837,67.156,150,149.995,150s150-67.163,150-150
         C299.996,67.158,232.835,0,149.996,0z M145.294,100.159h111.864c0.763,0,1.502,0.091,2.225,0.223l-62.648,36.017l-52.964-36.087
         C144.275,100.25,144.77,100.159,145.294,100.159z M60.572,185.31v-15.558h49.921l-0.609,5.047l-0.112,0.931v0.936
         c0,3.019,0.501,5.921,1.398,8.644H60.572z M112.293,154.842h-68.58V139.28h70.465L112.293,154.842z M117.571,111.218l-0.965,7.965
         H28.585v-15.562h90.384C118.17,106.028,117.677,108.573,117.571,111.218z M249.375,188.852H137.513
         c-3.348,0-6.378-1.351-8.58-3.538c0,0,0,0,0-0.003c-0.539-0.537-1.027-1.128-1.46-1.758c-0.01-0.016-0.029-0.031-0.039-0.047
         c-0.402-0.594-0.737-1.232-1.032-1.891c-0.029-0.065-0.073-0.122-0.104-0.189c-0.265-0.622-0.451-1.284-0.609-1.956
         c-0.029-0.117-0.083-0.223-0.106-0.34c-0.163-0.799-0.249-1.621-0.249-2.464l4.145-34.259l0.379-3.13l3.258-26.94
         c0-0.77,0.093-1.515,0.231-2.243c0.016-0.078,0.008-0.163,0.026-0.241c0.01,0.005,0.018,0.013,0.029,0.021
         c0.35-1.662,1.012-3.206,1.958-4.547v5.88l57.741,39.132c0.078,0.054,0.163,0.083,0.244,0.13c0.083,0.052,0.169,0.093,0.257,0.14
         c0.456,0.233,0.923,0.42,1.401,0.545c0.052,0.013,0.099,0.021,0.15,0.031c0.524,0.124,1.056,0.2,1.582,0.2h0.005
         c0.005,0,0.008,0,0.01,0c0.527,0,1.058-0.075,1.582-0.2c0.052-0.01,0.099-0.018,0.15-0.031c0.477-0.124,0.944-0.311,1.401-0.545
         c0.086-0.047,0.171-0.088,0.257-0.14c0.08-0.047,0.163-0.075,0.244-0.13l68.792-39.716c0.08,0.565,0.171,1.128,0.171,1.717
         l-7.781,64.329C261.559,183.4,256.105,188.852,249.375,188.852z"/>
     
   </svg>
  </a>
    </div>
    <div id="floating-button" data-toggle="tooltip" data-placement="left" data-original-title="Ayuda" onclick="newmail()">
      <!-- <p class="plus"></p> -->
      <svg  enable-background="new 0 0 58 58" height="42" viewBox="0 0 58 58" width="42" xmlns="http://www.w3.org/2000/svg"><path d="m26.64.094c-13.918 1.1-25.241 12.274-26.515 26.177-.635 6.928 1.176 13.404 4.659 18.681l-4.069 11.098c-.288.787.46 1.558 1.255 1.293l11.525-3.842c5.263 3.337 11.667 5.036 18.503 4.347 13.793-1.389 24.814-12.666 25.908-26.485 1.416-17.875-13.391-32.683-31.266-31.269z" fill="#fc3952"/><path d="m29 45c-1.104 0-2-.896-2-2v-3c0-1.104.896-2 2-2s2 .896 2 2v3c0 1.104-.896 2-2 2z" fill="#fff"/><path d="m29 34.03c-1.104 0-2-.896-2-2v-2.03c0-2.842 1.354-5.87 3.623-7.333.877-.565 1.391-1.525 1.377-2.567-.022-1.601-1.431-3.079-2.956-3.1-.013 0-.026 0-.039 0-.751 0-1.474.306-2.039.863-.614.605-.966 1.436-.966 2.281 0 1.104-.896 2-2 2s-2-.896-2-2c0-1.908.786-3.777 2.157-5.128 1.32-1.303 3.038-2.016 4.846-2.016h.096c3.687.052 6.847 3.277 6.9 7.044.034 2.429-1.166 4.666-3.208 5.984-1.105.713-1.791 2.517-1.791 3.972v2.03c0 1.104-.896 2-2 2z" fill="#fff"/><path d="m29 55c-.552 0-1-.448-1-1s.448-1 1-1c13.234 0 24-10.767 24-24s-10.766-24-24-24-24 10.767-24 24c0 .552-.448 1-1 1s-1-.448-1-1c0-14.336 11.664-26 26-26s26 11.664 26 26-11.664 26-26 26z" fill="#d5354e"/></svg>
      <!-- <img class="edit" src="http://ssl.gstatic.com/bt/C3341AA7A1A076756462EE2E5CD71C11/1x/bt_compose2_1x.png"> -->
    </div>
  
  </div>
{% endblock %}
{% block footer %}
{#  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}#}
  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "autoresponder";
  </script>
{% endblock %}
