{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}

{% block js %}
    {{ javascript_include('library/select2/js/select2.min.js') }}
  <script>
      var idCountry = '{{ subaccount.City.State.idCountry }}';
      var idState = '{{ subaccount.City.idState}}';
      var idCity = '{{ subaccount.idCity}}';

      var idServices = {{ idServices }};

      {% if limitContact is defined %}
      var contactTotal = {{ limitContact }};
      {% else %}
      var contactTotal = 0;
      {% endif %}

      {% if  limitSms is defined %}
      var smsTotal = {{ limitSms }};
      {% else %}
      var smsTotal = 0;
      {% endif %}
        
      {#{% if  limitSmstwoway is defined %}
      var smstwowayTotal = {{ limitSmstwoway }};
      {% else %}
      var smstwowayTotal = 0;
      {% endif %}#}

      {% if  limitLandingpage is defined %}
      var landingpageTotal = {{ limitLandingpage }};
      {% else %}
      var landingpageTotal = 0;
      {% endif %}

      {% if  limitSms == 0 %}
      var totalSmsSend = {{ totalSmsSend }};
      {% else %}
      var totalSmsSend = 1;
      {% endif %}
        
      {#{% if  limitSmstwoway == 0 %}
      var totalSmstwowaySend = {{ totalSmstwowaySend }};
      {% else %}
      var totalSmstwowaySend = 1;
      {% endif %}#}
        
      {% if limitContactAccount is defined %}
      var contactTotalAccount = {{ limitContactAccount }};
      {% else %}
      var contactTotalAccount = 0;
      {% endif %}

      {% if  limitSmsAccount is defined %}
      var smsTotalAccount = {{ limitSmsAccount }};
      {% else %}
      var smsTotalAccount = 0;
      {% endif %}
      
  
      {#{% if limitSmstwowayAccount is defined %}
      var smstwowayTotalAccount = {{ limitSmstwowayAccount }};
      {% else %}
      var smstwowayTotalAccount = 0;
      {% endif %}#}

      {% if  limitLandingpageAccount is defined %}
      var landingpageTotalAccount = {{ limitLandingpageAccount }};
      {% else %}
      var landingpageTotalAccount = 0;
      {% endif %}
     
      {% if  amountQuestionAccount is defined %}
      var amountQuestionAccount = {{ amountQuestionAccount }};
      {% else %}
      var amountQuestionAccount = 0;
      {% endif %}

      {% if  amountAnswerAccount is defined %}
      var amountAnswerAccount = {{ amountAnswerAccount }};
      {% else %}
      var amountAnswerAccount = 0;
      {% endif %}

      {% if  amountQuestion is defined %}
      var questionTotal = {{ amountQuestion }};
      {% else %}
      var questionTotal = 0;
      {% endif %}

      {% if  amountAnswer is defined %}
      var answerTotal = {{ amountAnswer }};
      {% else %}
      var answerTotal = 0;
      {% endif %}
  </script>
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('js/angular/subaccount/subaccountController.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>
            $(function () {
              $('#toggle-one').bootstrapToggle({
                on: 'On',
                off: 'Off',
                onstyle: 'success',
                offstyle: 'danger',
                size: 'small'
              });
                $(".select2").select2({
                    theme: 'classic',
                    placeholder : 'Seleccionar'
                });
            });
  </script>
{% endblock %}

{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  {#{{ ser[0].idServices }}#}
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la información de la Subcuenta: <strong>{{ subaccount.name }}</strong>
      </div>
      <hr class="basic-line"/>
    </div>
  </div>

  <div ng-app="aio" ng-controller="ctrlSubaccountEdit"  ng-cloak>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        {#        <form action="{{url('subaccount/edit')}}/{{(subaccount.idSubaccount)}}"   method="post" class="form-horizontal">#}
        <form ng-submit="editSubaccount({{ subaccount.idSubaccount }})"
              method="post" class="form-horizontal" ng-cloak>
          <div class="block block-info">
            <div class="body">

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right">*Nombre:</label>
                  <span class="input hoshi input-default col-sm-9">
                    {{ subaccountForm.render('name', {'class': 'undeline-input' , 'placeholder':'*Nombre', 'ng-model':'name', 'keep-current-value':'',  'required': 'required' }) }}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right">Descripcion:</label>
                  <span class="input hoshi input-default col-sm-9">
                    {{ subaccountForm.render('description', {'class': 'undeline-input' , 'placeholder':'Descripcion', 'ng-model': 'description', 'keep-current-value':''}) }}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right">*Pais:</label>
                  <span class="input hoshi input-default  col-sm-9">
                    <select class="undeline-input select2" required ng-change="selectCountry()" ng-model="countrySelected">
                          <option ng-repeat="c in country" value="{{ "{{c.idCountry}}" }}">{{ "{{c.name}}" }}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right">*Departamento:</label>
                  <span class="input hoshi input-default  col-sm-9">
                    <select class="undeline-input select2 " required ng-change="selectState()" ng-model="stateSelected">
                          <option ng-repeat="s in state" value="{{ "{{s.idState}}" }}">{{ "{{s.name}}" }}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right">*Ciudad:</label>
                  <span class="input hoshi input-default  col-sm-9">
                    <select class="undeline-input select2" name="idCity" required ng-model="citySelected">
                          <option ng-repeat="ci in cities" value="{{ "{{ci.idCity}}" }}">{{ "{{ci.name}}" }}</option>
                    </select>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right">Estado:</label>
                  <span class="input hoshi input-default col-sm-9">
                    {{ subaccountForm.render('status', {'id': 'toggle-one'}) }}
                  </span>
                </div>
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 text-right ">* Servicios:</label>
                  <span class="input hoshi input-default col-sm-9">
                      <select class="undeline-input select2" multiple="multiple" ng-model="services" name="services[]"
                              id="services[]" ng-change="selectedServices()" required>
                        {% for detail in servicesAvailable %}
                          <option value="{{ detail.idServices }}" >{{ detail.Services.name }}</option>
                        {% endfor %}
                      </select>
                    </span>
                </div>
              </div>
                {% if "Sms" in services %}
                  <div class="form-group" ng-show="showsms">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de Mensajes de Texto:</label>
                        <div class="col-sm-6 col-md-6">
                          {#<md-slider flex min="{{ '{{ totalSmsSend }}' }}" class="md-warn" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                          </md-slider>#}
                          <md-slider flex min="0" class="md-warn" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
{#                            <input flex type="number" min="{{ '{{ totalSmsSend }}' }}" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">#}
                            <input flex type="number" min="0" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (smsTotalAccount - smsLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}
                
                {% if "Sms Doble-via" in services %}
                  <div class="form-group" ng-show="showsmstwoway">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de Mensajes de Texto:</label>
                        <div class="col-sm-6 col-md-6">
                          <md-slider flex min="{{ '{{ totalSmsSend }}' }}" class="md-warn" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                          </md-slider>
                          <md-slider flex min="0" class="md-warn" max="{{ '{{ smstwowayTotalAccount }}' }}" ng-model="smstwowayLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="{{ '{{ totalSmsSend }}' }}" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">
                            <input flex type="number" min="0" max="{{ '{{ smstwowayTotalAccount }}' }}" ng-model="smstwowayLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (smstwowayTotalAccount - smstwowayLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}
                {% if "Landing Page" in services %}
                  <div class="form-group" ng-show="showlandingpage">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de visualizaciones:</label>
                        <div class="col-sm-6 col-md-6">
                          {#<md-slider flex min="{{ '{{ totalSmsSend }}' }}" class="md-warn" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                          </md-slider>#}
                          <md-slider flex min="0" class="md-warn" max="{{ '{{ landingpageTotalAccount }}' }}" ng-model="landingpageLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
{#                            <input flex type="number" min="{{ '{{ totalSmsSend }}' }}" max="{{ '{{ smsTotalAccount }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">#}
                            <input flex type="number" min="0" max="{{ '{{ landingpageTotalAccount }}' }}" ng-model="landingpageLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (landingpageTotalAccount - landingpageLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}
                
                {% if (("Email Marketing" in services)) AND accountingMode == "sending" %}
                  <div class="form-group" ng-show="showemail ">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de Correos:</label>
                        <div class="col-sm-6 col-md-6">
                          <md-slider flex min="1" max="{{ '{{ (contactTotalAccount + contactTotal) }}' }}" ng-model="mailLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ (contactTotalAccount + contactTotal) }}' }}" ng-model="mailLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = ((contactTotalAccount + contactTotal) - mailLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}
                {% if "Survey" in services %}
                    <div class="form-group" ng-show="showsurvey">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                            <md-slider-container>
                                <label class="col-sm-3 col-md-3 text-right">*Limite de preguntas:</label>
                                <div class="col-sm-6 col-md-6">
                                    <md-slider flex min="1" class="md-warn" max="{{ '{{ amountQuestionAccount }}' }}" ng-model="questionLimit" aria-label="red" id="red-slider">
                                    </md-slider>
                                </div>
                                <div class="col-sm-2 col-md-2">
                                    <md-input-container>
                                        <input flex type="number" min="1" max="{{ '{{ amountQuestionAccount }}' }}" ng-model="questionLimit" aria-label="red" aria-controls="red-slider">
                                    </md-input-container>
                                </div>
                                <div class="col-sm-1 col-md-1 none-padding">
                                    <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                           ng-model="spaceTotal">/{{ '{{ var = (amountQuestionAccount - questionLimit) }}' }} Mensajes
                                    </label>
                                </div>
                            </md-slider-container>
                            <md-slider-container>
                                <label class="col-sm-3 col-md-3 text-right">*Limite de respuestas:</label>
                                <div class="col-sm-6 col-md-6">
                                    <md-slider flex min="1" class="md-warn" max="{{ '{{ amountAnswerAccount }}' }}" ng-model="answerLimit" aria-label="red" id="red-slider">
                                    </md-slider>
                                </div>
                                <div class="col-sm-2 col-md-2">
                                    <md-input-container>
                                        <input flex type="number" min="1" max="{{ '{{ amountAnswerAccount }}' }}" ng-model="answerLimit" aria-label="red" aria-controls="red-slider">
                                    </md-input-container>
                                </div>
                                <div class="col-sm-1 col-md-1 none-padding">
                                    <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                           ng-model="spaceTotal">/{{ '{{ var = (amountAnswerAccount - answerLimit) }}' }} Mensajes
                                    </label>
                                </div>
                            </md-slider-container>
                        </div>
                    </div>
                {% endif %}

            </div>
            <div class="footer" align="right" >
              <div ng-show="disabled" style="margin-left: 20px">
                <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
              </div>
              <button ng-disabled="disabled" class="button  btn btn-xs-round   round-button success-inverted"
                      data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{ url('subaccount/index/' ) }}/{{ (subaccount.idAccount) }}"
                 class="button  btn btn-xs-round   round-button danger-inverted"
                 data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
            </div>
          </div>
        </form>
      </div>

      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="fill-block fill-block-primary">
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>
              <li>El nombre de la subcuenta debe ser un nombre único, es decir, no pueden existir dos
                subcuenta con el mismo nombre.
              </li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul>
            </p>
          </div>
          <div class="footer">
            Edición
          </div>
        </div>
      </div>

    </div>
  </div>

{% endblock %}
