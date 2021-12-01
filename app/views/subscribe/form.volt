{% extends "templates/clean.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
 
  <style>
    .animate-show-hide.ng-hide {
      opacity: 0;
    }

    .animate-show-hide.ng-hide-add,
    .animate-show-hide.ng-hide-remove {
      transition: all linear 0.5s;
    }
    img {
      width: 100%;
      height: auto;
    }
    .contenedor{
      position: relative;
      display: inline-block;
      text-align: center;
    }
    .texto-encima{
      position: absolute;
      top: 25%;
      left: 30%;
      transform: translate(-50%, -50%);
    }
    .texto-centrado{
      position: absolute;
      top: 60%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    .centrado{
      position: absolute;
      top: 27%;
      left: 75%;
      transform: translate(-50%, -50%);
    }
    .bg-red{
      height:50px!important;
      background-color: #000;
      color: #fff;
    }

.bg-personal{
  width: 100%;
  height: 150px;
  background-color: #ff00ff; /*Tu IMAGEN*/
}
.categories-contactlist{
width:0 auto!important;
margin:0 auto!important;
}
  </style>
{% endblock %}
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
{% endblock %}
{% block js %}
  <script>
    var typeView = {{typeView}};
    var idMail = {{idMail}};
            var idContact = {{idContact}};
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "unsubscribe";
  </script>
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

{% endblock %}

{% block content %}

<div class="site-wrapper">            
  <!-- inicia sucripcion -->
  <div class="container" >
    <div class="space">
      <div class="clearfix">
        <div class="row">      
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">        
            <img class="text-center img-responsive center-block" style="width: 100px" src="{{url('')}}themes/default/images/aio.png" />
            <div>
              <h1 class="text-center" style="color:#000000;">Hola</h1><br>
              <h3 class="text-left">
                <b style="color:#E37028;">Hemos recibido tu confirmación  </b><br><br> 
                <b style="color:#000000;">A partir de este momento recibirás toda la información de las campañas que enviemos a tu correo electrónico. </b>
              </h3>
              <br><br>
              <h4 class="text-left">
                <b style="color:#000000;">Cordialmente.  </b><br><br> 
                <b style="color:#E37028;">{{ name }} </b><br><br><br> 
                <b style="color:#000000;">Gracias </b><br><br> 
              </h4>    
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
            
</div>
            
  <div id="alertMoreCaracter" class="modal" >
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h4 id="textmessage"></h4>
        
        <div>
          <button type="button" class="button shining btn btn-md danger-inverted" onClick="closeModal()">Cerrar</button>
          {#<a onClick="closeModal()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a> #}     
          {#<button type="button" class="button shining btn btn-md danger-inverted" ng-click="closeModalMoreCa();">Cerrar</button> #}  
          {#<a ng-click="alert('hola mundo');" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a> #}  
        </div>
      </div>
    </div>
  </div>
  <script>
    function openModal() {
      $('.dialog').addClass('dialog--open');
    }
    function closeModal() {
      $('#alertMoreCaracter').removeClass('dialog dialog--open');
      $('#alertMoreCaracter').addClass('modal'); 
    }
  </script>
          
  <div class="principal-menu per-footerColor per-bottomLine">
    <img class="session-logo float-right" style="width: 100px; margin-top: 10px; margin-right: 30px;" src="{{url('')}}themes/default/images/logo.png" />
  </div>
{% endblock %}  
