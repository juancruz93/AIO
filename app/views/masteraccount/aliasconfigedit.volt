{% extends "templates/default.volt" %}
{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ javascript_include('js/angular/allied/controller.js') }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
{% endblock %}
{% block css %}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}    
{% endblock %}
{% block header %}
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
    {#          $(".select2").select2();#}
      });
  </script>   

{% endblock %}

{% block content %}    
  <div ng-app="aio" ng-controller="ctrlAllied" ng-init="spaceTotal = {{  masteraccount.Config.fileSpace }}; ss = {{  masteraccount.Config.fileSpace }};  
    mailTotal = {{  masteraccount.Config.mailLimit }}; mail = {{  masteraccount.Config.mailLimit }};
    contactTotal = {{  masteraccount.Config.contactLimit }}; contact = {{  masteraccount.Config.contactLimit }}; 
    smsTotal = {{  masteraccount.Config.smsLimit }}; sms = {{  masteraccount.Config.smsLimit }};
    smsVTotal = {{  masteraccount.Config.smsVelocity }}; smsV = {{  masteraccount.Config.smsVelocity }};
      mailLimit = {{ config.mailLimit }}; ml = {{ config.mailLimit }}; contactLimit = {{ config.contactLimit }}; cl = {{ config.contactLimit }};
      smsLimit = {{ config.smsLimit }}; sl =  {{ config.smsLimit }};  fileSpace = {{  config.fileSpace }}; fs = {{  config.fileSpace }} ">
      landingpageLimit = {{ config.landingpageLimit }}; lan =  {{ config.landingpageLimit }};  fileSpace = {{  config.fileSpace }}; fs = {{  config.fileSpace }} ">
      
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Edición de la configuración de la Cuenta Aliada
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>       

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form action="{{url('masteraccount/aliasconfigedit')}}/{{(alias.idAllied)}}" method="post" class="form-horizontal">
          <div class="block block-info">          
            <div class="body">

              {#<div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default">
                          {{ConfigForm.render('idMta', {'class': 'undeline-input select2'})}}
                      </span>
                  </div>
              </div>
  
              <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default">
                          {{ConfigForm.render('idAdapter', {'class': 'undeline-input select2'})}}
                      </span>
                  </div>
              </div>
  
              <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default">
                          {{ConfigForm.render('idUrldomain', {'class': 'undeline-input select2'})}}
                      </span>
                  </div>
              </div>
  
  
  
              <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default">                                    
                          {{ConfigForm.render('accountingMode', {'class': 'undeline-input'})}}
                      </span>
                  </div>
              </div>
  
              <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default">                                    
                          {{ConfigForm.render('subscriptionMode', {'class': 'undeline-input'})}}
                      </span>
                  </div>
              </div>#}

              {#            <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                              <span class="input hoshi input-default">
                                {{ConfigForm.render('idMailClass', {'class': 'undeline-input select2'})}}
                              </span>
                            </div>
                          </div>#}

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Almacenamiento :</label>
                  <span class="input hoshi input-default  col-sm-6">                                       
                    {{ConfigForm.render('fileSpace', {'class': 'undeline-input' ,  'ng-change': 'spaceEdit(fs)' , 'ng-model': 'fileSpace'})}}
                  </span>
                  <span  class="col-sm-2 " ng-class="ss > 0 ? 'success' : 'error' " >Disponible: {{"{{ ss }}"}}  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Limite de correos :</label>
                  <span class="input hoshi input-default  col-sm-6">                                       
                    {{ConfigForm.render('mailLimit', {'class': 'undeline-input' ,  'ng-model': 'mailLimit', 'ng-change': 'mailLEdit(ml)'})}}
                  </span>
                  <span  class="col-sm-2 " ng-class="mail > 0 ? 'success' : 'error' " >Disponible: {{"{{ mail }}"}}  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Limite de contactos :</label>
                  <span class="input hoshi input-default  col-sm-6">                                  
                    {{ConfigForm.render('contactLimit', {'class': 'undeline-input',  'ng-model': 'contactLimit' , 'ng-change': 'contactLEdit(cl)'})}}
                  </span>
                  <span  class="col-sm-2 " ng-class="contact > 0 ? 'success' : 'error' " >Disponible: {{"{{ contact }}"}}  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Limite de sms :</label>
                  <span class="input hoshi input-default  col-sm-6">                                        
                    {{ConfigForm.render('smsLimit', {'class': 'undeline-input' , 'ng-model': 'smsLimit' , 'ng-change': 'smsLEdit(sl)' })}}
                  </span>
                  <span  class="col-sm-2 " ng-class="sms > 0 ? 'success' : 'error' " >Disponible: {{"{{ sms }}"}}  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Mta:</label>
                  <span class="input hoshi input-default  col-sm-8">       
                    <select class="undeline-input select2"  id="mta" nam="mta">
                      {% for mt in mta %}
                        {% if(mt.idMta == config.idMta) %}
                          <option value="{{mt.idMta}}" selected>{{mt.name}}</option>
                        {% else %}
                          <option value="{{mt.idMta}}">{{mt.name}}</option>
                        {% endif %}
                      {% endfor %}
                    </select>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Adaptador:</label>
                  <span class="input hoshi input-default  col-sm-8">       
                    <select class="undeline-input select2"  ng-model="adapterSelected"  id="idAdapter">
                      {% for a in adapter %}
                        {% if(a.idAdapter == config.idAdapter) %}
                          <option value="{{a.idAdapter}}" selected>{{a.fname}}</option>
                        {% else %}
                          <option value="{{a.idAdapter}}">{{a.fname}}</option>
                        {% endif %}
                      {% endfor %}
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Mail class :</label>
                  <span class="input hoshi input-default  col-sm-8">       
                    <select class="undeline-input select2"  ng-model="mailclassSelected"  id="idMailClass">
                      {% for m in mailclass %}
                        {% if(m.idMailClass == config.idMailClass) %}
                          <option value="{{m.idMailClass}}" selected>{{m.name}}</option>
                        {% else %}
                          <option value="{{m.idMailClass}}">{{m.name}}</option>
                        {% endif %}
                      {% endfor %}
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 ">Dominio:</label>
                  <span class="input hoshi input-default  col-sm-8">       
                    <select class="undeline-input select2"  ng-model="urldomainSelected"  id="idUrldomain">
                      {% for u in urldomain %}
                        {% if(u.idUrldomain == config.idUrldomain) %}
                          <option value="{{u.idUrldomain}}" selected>{{u.name}}</option>
                        {% else %}
                          <option value="{{u.idUrldomain}}">{{u.name}}</option>
                        {% endif %}
                      {% endfor %}
                    </select>
                  </span>
                </div>
              </div>

              {#                        <div class="form-group">
                                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                              <span class="input hoshi input-default">                                    
                                                  {{ConfigForm.render('smsVelocity', {'class': 'undeline-input' , 'placeholder':'*Capacidad de envío por segundo:'})}}
                                              </span>
                                          </div>
                                      </div> 
              
                                      <div class="form-group">
                                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                              <span class="input hoshi input-default">                                    
                                                  {{ConfigForm.render('headerColor', {'class': 'undeline-input' , 'placeholder':'*Color del encabezado:'})}}
                                              </span>
                                          </div>
                                      </div> 
              
                                      <div class="form-group">
                                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                              <span class="input hoshi input-default">                                    
                                                  {{ConfigForm.render('footerColor', {'class': 'undeline-input' , 'placeholder':'*Color del pie de página:'})}}
                                              </span>
                                          </div>
                                      </div>  #} 

            </div>
            <div class="footer" align="right">
                <button class="button shining btn btn-xs-round  round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('masteraccount/aliaslist')}}/{{(alias.idMasteraccount)}}" class="button  btn btn-xs-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
              
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
              <li>El nombre de la cuenta aliada debe ser un nombre único, es decir, no pueden existir dos cuentas aliadas con el mismo nombre.</li>                                                        
              <li>El estado de la cuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul> 
            </p>
          </div>
          <div class="footer">
            Creación
          </div>
        </div>     
      </div> 

    </div>
  </div>

{% endblock %}
