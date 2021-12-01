{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}

  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
<style>
.smartphone-social-network{
    display:none
}
.social-hider{
    display:none
}</style>
{% endblock %}
{% block js %}
  <script>

    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "unsubscribe";
  </script>
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {# {{javascript_include('js/angular/unsubscribe/services.js') }}
  {{javascript_include('js/angular/unsubscribe/controllers.js') }} #}
{{ javascript_include('js/angular/unsubscribe/dist/unsubscribe.978d66fb89d6fd646ea0.min.js') }}
  {{ javascript_include('library/angular-dragdrop/component/jquery-ui/jquery-ui.min.js')}}
  {{ javascript_include('library/angular-dragdrop/src/angular-dragdrop.min.js')}}

{% endblock %}

{% block content %}

  <div class="clearfix"></div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-controller="advanceController" ng-cloak>
     <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="title">
          Lista de Desuscritos 
        </div>
        <hr class="basic-line" />
      </div>
      <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div class="text-center">
          <div class="inline-block text-center none-padding">
            <strong>Cantidad de contactos desuscritos:</strong>
            <br>  
            <span class="info medium-text">{{ "{{blockade.total }}"}}</span>
          </div>    
        </div>  
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="row">

            <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3  text-right pull-left" style="padding-left: 0px">
                  <div class="input-group" >
                  <input class="form-control"  id="search" placeholder="Buscar por correo o celular"  ng-model="search" aria-invalid="false" />
        
                  <div class="input-group-btn">
        
                    <button type="button" class="btn btn-default" ng-click="searchContact(1)">
                      <i class="fa fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-default" ng-click="searchContact(2)">
                      <i class="fa fa-eraser"></i>
                    </button>
                  </div>
                </div>
            </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  text-right pull-right " style=" padding-right: 0px">
            <a href="{{ url('tools')}}">
              <button class="button  btn btn-md default-inverted">
                <i class="fa fa-arrow-left"></i> Regresar
              </button>
            </a>
            <a href="{{ url('unsubscribe/create')}}">
              <button class="button  btn btn-md info-inverted">
                Desuscribir un contacto
              </button>
            </a>
          </div> 
        </div>
      </div>
    </div>
    
    <!-- -->
    <div class="row">

    <div ng-show="blockade.total>0">
    <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{blockade.total }}"}}
          </b> registros </span><span>P&aacute;gina <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (blockade.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (blockade.total_pages) || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (blockade.total_pages)  || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
  <div class="">
    <table class="table table-bordered table-responsive sticky-enabled" >
      <thead class="theader">
        <tr>
          <th>
            Correo/M&oacute;vil
          </th>
          <th>
            Fecha
          </th>
          <th>
            Raz&oacute;n de la desuscripci&oacute;n
          </th>
          <th>
            Acciones
          </th>
        </tr>
      </thead>
      <tbody ng-repeat="key in blockade[0].items">
        <tr>
          <td class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idContact}}' }}" aria-expanded="false" >
            {{"{{key.email }}"}}
            <div ng-show="key.indicative.length != 0 && key.phone.length != 0">
              (+{{"{{key.indicative }}"}}) {{"{{key.phone }}"}}
            </div>
          </td>
          <td class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idContact}}' }}" aria-expanded="false" 
              aria-controls="allinfo{{ "{{ key.idContact }}"}}">
            Creada por <strong>{{"{{ key.createdBy }}"}}</strong> el dia <strong>{{"{{ key.created * 1000  | date : 'yyyy-MM-dd' }}"}}</strong>
          </td>
          <td class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idContact}}' }}" aria-expanded="false" >
            {{"{{key.motive }}"}}
          </td>
          <td class="text-right">
            <a ng-click="deleteUnsub(key.idContact)" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top"   title="Suscribir" >
              <span class="fa fa-unlock"></span>
            </a>
          </td>
        </tr>
        <tr id="allinfo{{ "{{ key.idContact }}"}}" class="collapse">
          <td colspan="7">
            <div class="row">
              <div class="col-lg-12">
                <div class="block block-info">
                  <div class="body row">
                    <div class="col-lg-6 col-md-6 col-sm-6 text-center col-lg-offset-3">
                      <strong>Categor&iacute;a(s) en la(s) que est&aacute; desuscrito este contacto</strong>
                      <div class="div-border">
                        <br>
                        <ul class="text-left">
                          <li ng-repeat="item in key.contactlistcategories" ng-hide="item == ''"class="small-text">
                            <strong>{{"{{item.name }}"}}</strong>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>   
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>            
  </div>
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{blockade.total }}"}}
          </b> registros </span><span>P&aacute;gina <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (blockade.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (blockade.total_pages) || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (blockade.total_pages)  || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
</div>
    <!-- --> 
<div ng-show="blockade.total<=0" class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" style="padding-left: 0px; padding-right: 0px">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          La lista de desuscritos se encuentra vac&iacute;a, para desuscribir un correo electr&oacute;nico haga <a href="{{ url('unsubscribe/create')}}">clic aqu&iacute;</a>.
        </h2>    
      </div>
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
          

{% endblock %}  
