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
  {# {{javascript_include('js/angular/unsubscribe/services.js') }} #} 
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
   {# {{javascript_include('js/angular/unsubscribe/controllers.js') }} #}
  {{ javascript_include('js/angular/unsubscribe/dist/unsubscribe.978d66fb89d6fd646ea0.min.js') }}
  {{ javascript_include('library/angular-dragdrop/component/jquery-ui/jquery-ui.min.js')}}
  {{ javascript_include('library/angular-dragdrop/src/angular-dragdrop.min.js')}}

{% endblock %}

{% block content %}

<div class="clearfix"></div>
<div class="space"></div>
<div class="site-wrapper" ng-controller="contactController" ng-cloak>            
    <!-- inicia desucripcion por lista-->
    <div class="container" ng-show="validateView == false">
        <div class="space">
            <div class="clearfix">
                <div class="row">      
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">        
                        <img class="text-center img-responsive center-block" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
                        <div>
                            <h1 class="text-center" style="color:#000000;">Hola <b>{{'{{name}}'}},</b></h1>
                            <h3 class="small-text text-center" style="color:#000000;">
                                ¿Estás seguro que quieres desuscribirte?
                            </h3>
                            <br>
                            <div ng-repeat="value in data.selectOption">
                                <button type="button" class="button btn primary-inverted  btn-lg" ng-click="selectButton(value.id)"  ng-disabled="disabled"><span class="glyphicon glyphicon-ok" ng-show="data.click == value.id"></span> <b style="color:#FFFFFF" >{{'{{ value.name }}'}}</b></button><br>
                            </div>
                        </div>              
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <h3 class="text-left">
                            <b style="color:#000000;">Queremos saber </b> 
                            <b style="color:#E37028;">¿Cuál es el motivo de la Desuscripción?</b>
                        </h3>
                        <br>
                        <div class="small-text text-left">
                            <div class="form-group" ng-repeat="value in data.options">
                                <label>
                                <input type="radio" ng-model="data.option" ng-value="value.name" ng-click="selectOption(value.name)" ng-disabled="disabled" > {{'{{ value.name }}'}}
                                </label>
                                
                            </div>
                            <div class="form-group" ng-show="data.option === 'Otro' ">
                                <textarea class="form-control" ng-model="data.other" style="resize:none;" ng-disabled="disabled"></textarea>
                            </div>
                        </div>          
                    </div>      
                </div>
              <br>
              <div class="row">        
                <div class="col-md-12 text-center">
                  <button type="button" class="button btn btn-lg" ng-click="sendUnsubscribe()" style="color: #FFFFFF; background-color: #E37028; border-color: #E37028;" ng-disabled="disabled">Confirmar</button>
                </div>
              </div>
            </div>
        </div>
    </div>
    <!-- fin desucripcion por lista-->
    
    
<!-- inicia desucripcion por categories-->
    <div class="container" ng-show="validateView">
        <div class="space">
            <div class="clearfix">
              <div class="row">      
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="padding-right: 40px">                
                    <img class="text-center img-responsive center-block" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
                    <div>
                      <h1 class="text-center" style="color:#000000;">Hola <b>{{'{{name}}'}}</b></h1>
                      <h3 class="small-text text-center" style="color:#000000;">
                        ¿Estás seguro que quieres desuscribirte?
                      </h3>
                      <br>
                        <div class="animate-show-hide">
                          <p class="small-text categories-contactlist text-left" style="padding-left: 20px; padding-right: 10px">
                            Puede arrastrar las categorías de un lado a otro para suscribirlas o desuscribirlas, ¡no olvides <b>confirmar</b> la desuscripción!</p>
                          <br>
                          <div class="categories-contactlist row" >
                            <div class="col-md-6  panel-sigma">
                              <div class="panel-heading " style="border-color: #00bede; background-color: #00bede;">Categorías en las que está suscrito</div>
                              <div id="subs" style="background-color: #B5B5B8;" class="panel-body border-category  boder-droppable " data-drop="true" ng-model='arrSubs' data-jqyoui-options="actionDroppableSubs"  jqyoui-droppable="{multiple:true,onOver:'actionDroppableSubs.onOver',onDrop:'actionDroppableSubs.onDrop',onOut:'actionDroppableSubs.onDrop'}">
                                <ol class="" style="padding: 0px;" >
                                  <li data-draggable="item" class="item-collap cursor-move " ng-repeat="category in arrSubs track by $index" data-drag="true" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrSubs" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}"  ng-hide="!category.name" ng-disabled="disabled">
                                    {{"{{category.name}}"}}
                                    <i class="fa fa-thumbs-up float-right cursor-pointer" style="margin-top: 5px; color: #5cbd56;" aria-hidden="true" title="Desuscribir" ng-click="unsubscribe(category)"></i>
                                  </li>
                                </ol>
                              </div>
                            </div>
                            <div class="col-md-6  panel-sigma">
                              <div class="panel-heading" style="border-color: #00bede; background-color: #00bede;">Categorías en las que está desuscrito</div>
                              <div id="unsubs" style="background-color: #B5B5B8;" class="panel-body border-category boder-droppable " data-drop="true" ng-model="arrUnsubs" data-jqyoui-options="actionDroppableUnSubs" jqyoui-droppable="{multiple:true,onOver:'actionDroppableUnSubs.onOver',onDrop:'actionDroppableUnSubs.onDrop',onOut:'actionDroppableUnSubs.onDrop'}">
                                <ol style="padding: 0px;" >
                                  <li data-draggable="item" class="item-collap cursor-move" ng-repeat="category in arrUnsubs track by $index" data-drag="true" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrUnsubs" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}" ng-hide="!category.name" ng-disabled="disabled">
                                    {{"{{category.name}}"}}
                                    <i class="fa fa-thumbs-down float-right cursor-pointer" style="margin-top: 5px; color: #f51818;" aria-hidden="true" title="Suscribir" ng-click="subscribe(category)"></i>
                                  </li>
                                </ol>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>          
                </div>
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="padding-left: 40px">
                        
                                <h3 class="text-left">
                                    <b style="color:#000000;">Queremos saber </b> 
                                    <b style="color:#E37028;">¿cuál es el motivo de la Desuscripción?</b>
                                </h3>
                                <br>
                                <div class="small-text text-left">
                                    <div class="form-group" ng-repeat="value in data.options">
                                    <label><input type="radio"  ng-model="data.option" ng-value="value.name" ng-click="selectOption(value.name)" checked="checked" ng-disabled="disabled"> {{'{{ value.name }}'}}</label>
                                        
                                    </div>
                                    <div class="form-group" ng-show="data.option === 'Otro' ">
                                        <textarea class="form-control" ng-model="data.other" style="resize:none;" ng-disabled="disabled"></textarea>
                                    </div>
                                </div>
                        
                    </div>  
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                        <div class="space">
                            <div class="clearfix">
                                <button type="button" class="button btn btn-lg" ng-click="sendUnsubscribe()" style="color: #FFFFFF; background-color: #E37028; border-color: #E37028;" ng-disabled="disabled">Confirmar</button>
                            </div>
                        </div>
                   </div>    
                </div>
            </div>
        </div>
    </div>   
</div>       
<!--fin desucripcion por categories -->
<!-- INICIO DESUSCRIPCION POR CATEGORIA DE ANTES-->            
     {# <div class="center-container">
        <div class="session-container">
          <img class="session-logo" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
          <div class="space">
            <div class="clearfix">
              <div>
                <p class="small-text contactlist-categories text-left">Hola <strong>{{'{{name}}'}}</strong>,
                  <br>¿Estas seguro que quieres desuscribirte?
                </p>
                <br>
                <button id="simpleDesuscribeBtn" class="button btn primary-inverted" ng-click="restService.sendUnsubscribeSimple()" ng-hide="misc.advanceUnsuscribe">Confirmar</button>
                <div>
                  <a ng-click="functions.changeAdvanceUnsuscribe()" class="pointer-cursor">Desuscripción avanzada</a>  
                </div>
                <div ng-show="misc.advanceUnsuscribe" class="animate-show-hide">
                  <p class="small-text contactlist-categories text-left">
                    Puede arrastrar las categorías de un lado a otro para inscribirlas o desuscribirlas</p>
                  <br>
                  <div class="contactlist-categories row">
                    <div class="col-lg-6  panel-sigma">
                      <div class="panel-heading">Categorías en las que está inscrito</div>
                      <div id="subs" style="background-color: #B5B5B8;" class="panel-body border-category  boder-droppable " data-drop="true" ng-model='arrSubs' data-jqyoui-options="actionDroppableSubs"  jqyoui-droppable="{multiple:true,onOver:'actionDroppableSubs.onOver',onDrop:'actionDroppableSubs.onDrop',onOut:'actionDroppableSubs.onDrop'}">
                        <ol class="" style="padding: 0px;" >
                          <li data-draggable="item" class="item-collap cursor-move " ng-repeat="category in arrSubs track by $index" data-drag="true" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrSubs" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}"  ng-hide="!category.name">
                            {{"{{category.name}}"}}
                            <i class="fa fa-thumbs-down float-right cursor-pointer" style="margin-top: 5px; color: #f51818;" aria-hidden="true" title="Desuscribir" ng-click="unsubscribe(category)"></i>
                          </li>
                        </ol>
                      </div>
                    </div>
                    <div class="col-lg-6  panel-sigma">
                      <div class="panel-heading">Categorías en las que está desuscrito</div>
                      <div id="unsubs" style="background-color: #B5B5B8;" class="panel-body border-category boder-droppable " data-drop="true" ng-model="arrUnsubs" data-jqyoui-options="actionDroppableUnSubs" jqyoui-droppable="{multiple:true,onOver:'actionDroppableUnSubs.onOver',onDrop:'actionDroppableUnSubs.onDrop',onOut:'actionDroppableUnSubs.onDrop'}">
                        <ol style="padding: 0px;" >
                          <li data-draggable="item" class="item-collap cursor-move" ng-repeat="category in arrUnsubs track by $index" data-drag="true" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrUnsubs" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}" ng-hide="!category.name">
                            {{"{{category.name}}"}}
                            <i class="fa fa-thumbs-up float-right cursor-pointer" style="margin-top: 5px; color: #5cbd56;" aria-hidden="true" title="Suscribir" ng-click="subscribe(category)"></i>
                          </li>
                        </ol>
                      </div>
                    </div>
                  </div>
                  <div class="text-right">
                    <button id="sendUnsub" class="button btn primary-inverted text-center btn-lg" ng-click="sendUnsubscribe()">Confirmar assdsadsad</button>
                    <button class="button btn primary-inverted text-center btn-lg" ng-click="selectButton(value.id)"><span class="glyphicon glyphicon-ok" ng-show="data.click == value.id"></span> <b style="color:#FFFFFF">1dsdssdfsdfsdfsdfsdfsd</b></button>
                  </div>
                </div>
              </div>
              <br>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              </div>
            </div>
          </div>
        </div>
      </div>   #} 
<!-- BEFORE DESUSCRIPCION POR CATEGORIA DE ANTES-->                               



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
