{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-wizard-1.1/css/gsdk-base.css') }}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {#-----con este css la barra se sale de su pocision, por esta razon se deja el otro-----
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">#}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.bootstrap.wizard.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.validate.min.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/wizard.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('js/angular/subaccount/subaccountController.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>
    function clearselect() {
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }
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
        placeholder: 'Seleccionar'
      });
    });
    var idCountry = '{{ account.City.State.idCountry }}';
    var idState = '{{ account.City.idState}}';
    var idCity = '{{ account.idCity}}';

    {% if  fileSpace is defined %}
      var spaceTotal = {{ fileSpace }};
    {% else %}
      var spaceTotal = 0;
    {% endif %}

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
      
    {% if  limitSmstwoway is defined %}
      var smstwowayTotal = {{ limitSmstwoway }};
    {% else %}
      var smstwowayTotal = 0;
    {% endif %}

    {% if  limitLandingpage is defined %}
      var landingpageTotal = {{ limitLandingpage }};
    {% else %}
      var landingpageTotal = 0;
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

{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Creación de una nueva Subcuenta
      </div>
      <hr class="basic-line"/>
    </div>
  </div>
  <div ng-app="aio" ng-controller="ctrlSubaccount">

    <div class="row" ng-cloak>
      {#<form method="post" action="{{ url("subaccount/create/" ~account.idAccount) }}" class="form-horizontal">#}
      <form method="post" ng-submit="createSubaccount({{ (account.idAccount) }})" class="form-horizontal">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 wrap">
          <div class="block block-info">
            <div class="body ">
              <div class="row">
                <br>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 col-md-3 text-right">*Nombre:</label>
                    <span class="input hoshi input-default col-sm-9 col-md-9">
                      {{ subaccountForm.render('name', {'class': 'undeline-input' , 'placeholder':'Nombre', 'ng-model':'name', 'required' : 'required'  }) }}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 col-md-3 text-right">Descripciòn:</label>
                    <span class="input hoshi input-default col-sm-9 col-md-9">
                      {{ subaccountForm.render('description', {'class': 'undeline-input' , 'ng-model':'description', 'placeholder':'Descripciòn'}) }}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 col-md-3 text-right">*Pais:</label>
                    <span class="input hoshi input-default  col-sm-9 col-md-9">
                      <select class="undeline-input select2" ng-change="selectCountry()"
                              ng-model="countrySelected" name="countrySelected" required="">
                        <option value=""></option>
                        <option ng-repeat="c in country "
                                value="{{ "{{c.idCountry}}" }}">{{ "{{c.name}}" }}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 col-md-3 text-right">*Departamento:</label>
                    <span class="input hoshi input-default  col-sm-9 col-md-9">
                      <select class="undeline-input select2" ng-change="selectState()"
                              ng-model="stateSelected" name="stateSelected" required="">
                        <option value=""></option>
                        <option ng-repeat="s in state "
                                value="{{ "{{s.idState}}" }}">{{ "{{s.name}}" }}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 col-md-3 text-right">*Ciudad:</label>
                    <span class="input hoshi input-default  col-sm-9 col-md-9">
                      <select class="undeline-input select2 clearselect" ng-model="citySelected" name="citySelected"
                              required="">
                        <option value=""></option>
                        <option ng-repeat="ci in cities "
                                value="{{ "{{ci.idCity}}" }}">{{ "{{ci.name}}" }}</option>
                      </select>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 col-md-3 text-right">Estado:</label>
                    <span class="input hoshi input-default col-sm-9 col-md-9">
                      <md-switch class="md-primary none-margin" ng-model="status" md-no-ink aria-label="Switch 1">
                      </md-switch>
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
                          <option value="{{ detail.idServices }}">{{ detail.Services.name }}</option>
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
                          <md-slider flex min="1" class="md-warn" max="{{ '{{ smsTotal }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ smsTotal }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (smsTotal - smsLimit) }}' }} Mensajes
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
                          <md-slider flex min="1" class="md-warn" max="{{ '{{ smstwowayTotal }}' }}" ng-model="smstwowayLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ smstwowayTotal }}' }}" ng-model="smstwowayLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (smstwowayTotal - smstwowayLimit) }}' }} Mensajes
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
                          <md-slider flex min="1" class="md-warn" max="{{ '{{ landingpageTotal }}' }}" ng-model="landingpageLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ landingpageTotal }}' }}" ng-model="landingpageLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (landingpageTotal - landingpageLimit) }}' }} Mensajes
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
                          <md-slider flex min="1" class="md-warn" max="{{ '{{ questionTotal }}' }}" ng-model="questionLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ questionTotal }}' }}" ng-model="questionLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (questionTotal - questionLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de respuestas:</label>
                        <div class="col-sm-6 col-md-6">
                          <md-slider flex min="1" class="md-warn" max="{{ '{{ answerTotal }}' }}" ng-model="answerLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ answerTotal }}' }}" ng-model="answerLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (answerTotal - answerLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}
                {#{% if (("Email Marketing" in services)) AND accountingMode == "contact" %}

                  <div class="form-group" ng-show="showemail">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de Contactos:</label>
                        <div class="col-sm-6 col-md-6">
                          <md-slider flex min="1" max="{{ '{{ contactTotal }}' }}" ng-model="contactLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ contactTotal }}' }}" ng-model="contactLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (contactTotal - contactLimit) }}' }} Contactos
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}#}
                {% if (("Email Marketing" in services)) AND accountingMode == "sending" %}
                  <div class="form-group" ng-show="showemail ">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <md-slider-container>
                        <label class="col-sm-3 col-md-3 text-right">*Limite de Correos:</label>
                        <div class="col-sm-6 col-md-6">
                          <md-slider flex min="1" max="{{ '{{ contactTotal }}' }}" ng-model="mailLimit" aria-label="red" id="red-slider">
                          </md-slider>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <md-input-container>
                            <input flex type="number" min="1" max="{{ '{{ contactTotal }}' }}" ng-model="mailLimit" aria-label="red" aria-controls="red-slider">
                          </md-input-container>
                        </div>
                        <div class="col-sm-1 col-md-1 none-padding">
                          <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                                 ng-model="spaceTotal">/{{ '{{ var = (contactTotal - mailLimit) }}' }} Mensajes
                          </label>
                        </div>
                      </md-slider-container>
                    </div>
                  </div>
                {% endif %}

              </div>
            </div>
            <div class="footer" align="right">
              <button class="button shining btn btn-xs-round round-button success-inverted"
                      data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{ url('subaccount/index/' ~ account.idAccount) }}"
                 class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip"
                 data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
            </div>

          </div>
        </div>
      </form>
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 wrap">
        <div class="fill-block fill-block-info">
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
              <li>Para que la ciudad aparezca debes seleccionar un país y un departamento</li>
              <li>El estado de la subcuenta por defecto esta desactivada (off) si desea activarla haga
                clic en el switch para que cambie a activada (on).
              </li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

{% endblock %}
