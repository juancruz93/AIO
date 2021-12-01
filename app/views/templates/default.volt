<!DOCTYPE html>
<html {% if app_name is defined %} ng-app="{{app_name}}" {% endif%}>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=1">
    {{getTitle()}}
    <link href='https://fonts.googleapis.com/css?family=Questrial' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" type="image/x-icon" href="{{url('')}}themes/{{theme.name}}/images/favicons/favicon48x48.ico">
    <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    {{ partial("partials/css_notifications_partial") }}
    {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    {# Jquery#}
    <script src="{{url("")}}library/jquery/jquery-1.11.2.min.js"  type="text/javascript"></script>
{#    {{ javascript_include('library/jquery/jquery-1.11.2.min.js') }}#}
    {# base de bootstrap #}
    {{ stylesheet_link('library/bootstrap-3.3.4/css/bootstrap.css') }}
    {# Para cambiar el tema modificar la ruta en el siguiente enlace#}
    {{ stylesheet_link('themes/' ~ theme.name ~ '/css/styles.css') }}
    {{ stylesheet_link('css/adjustments.min.css') }}
    {# sticky tables #}
    {{ stylesheet_link('library/sticky-table-headers/css/component.min.css') }}
    {# base de fontawesome #}
    {{ stylesheet_link('library/font-awesome-4.7.0/css/font-awesome.min.css') }}
    <script type="text/javascript">
          var myBaseURL = '{{url('')}}';
          $(".nav li.disabled a").click(function () {
            return false;
          });
    </script>
    {% block css %}<!-- custom header code -->{% endblock %}
    {% block header %}<!-- custom header code -->{% endblock %}
  </head>
  <body style="overflow-x: hidden;">
    {{personalizedCss.getPersonalizedCss()}}

    <!-- nav bar -->        


    <nav class="navbar navbar-default alert-success per-topLine" role="navigation">
      <div class="container-fluid per-container-fluid-customized">
        <div class="navbar-header">
          <a class="navbar-brand" style="display: inline;"  href="{{url('')}}">
            {{personalizedCss.getLogo()}}
{#            <img class='logo' src='{{personalizedCss.getLogoRoute()}}' style='width:45px;height:44px;padding-left: 5px;display:inline;' alt='Sigma Móvil'/>#}
          </a>
        </div>
        <ul id="top-nav" class="nav navbar-nav navbar-right">
          {% if chat.enabled %}
            <!-- BEGIN OLARK CHAT LINK -->
            <li class="top-nav-hover">
              <a href="javascript:void(0);" onclick="olark('api.box.expand');">
                Necesita ayuda? <i class="fa fa-comments"></i>
              </a>
            </li>
            <!-- END OLARK CHAT LINK -->
          {% endif %}
          {# <li data-toggle="tooltip_default" data-placement="bottom" title="Queremos conocer tus comentarios">
               {{link_to ('suggestion', '<span class="glyphicon glyphicon-comment per-headerTextColor"></span>')}}
#}
          {{globalCountersManager.getGlobalAccountants()}}


          {#<li data-toggle="tooltip" data-placement="bottom" title="Visitas">
            <a href="javascript: void(0);" class="default-cursor unlink">
              <div id="">
                <span class="glyphicon glyphicon-blackboard"></span> 55/2000
              </div>
            </a>
          </li>

          <li class="top-nav-hover" data-toggle="tooltip" data-placement="bottom" title="Puntuación">
            <a href="{{url('smartmanagment/behavior')}}">
              <div class="orange-sigma text-with-shadow" style="display: inline;">
                <span class="glyphicon glyphicon-star"></span>
              </div>200000
              {#
              <div class="score-in-bar" id="score"></div>
              #}
          {#</a>
        </li>#}

          {% if userEfective.enable %}
            <li class="top-nav-hover" data-toggle="tooltip" data-placement="bottom" title="Regresar a la sesión anterior">
              <a class="per-headerTextColor" href="{{url('session/logoutsuperuser')}}"><span class="glyphicon glyphicon-log-out"></span></a>
            </li>
          {% endif %}

          <li>
            <ul class="nav nav-pills nav-pills-profile" role="tablist">
              <li role="presentation" class="dropdown">
                <a id="drop6" href="" class="dropdown-toggle profile-menu per-userBoxColor" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                  {{user.name}} {{user.lastname}} 
                  <span class="caret"></span>
                </a>
                <ul id="menu3" class="dropdown-menu" role="menu" aria-labelledby="drop6">
                  <li role="presentation">
                    <a role="menuitem" tabindex="-1" href="#">
                      <b>
                        {% if(user.UserType.idSubaccount) %}
                          {{user.usertype.subaccount.name|upper}}
                        {% elseif(user.UserType.idAccount) %}
                          {{user.usertype.account.name|upper}}
                        {% elseif(user.UserType.idAllied) %}
                          {{user.usertype.allied.name|upper}}
                        {% elseif(user.UserType.idMasteraccount) %}
                          {{user.usertype.masteraccount.name|upper}}
                        {% endif %}
                      </b>
                    </a>
                  </li>
                  {#
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url('user/profile')}}/{{user.idUser}}">Mi perfil</a></li>
                  #}
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><b>Tipo:</b> {{user.Role.nameForView}}</a></li>
                    {% if not userEfective.enable %}
                      {% if session.get('mode') == 'advanced' %}
                      <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url('session/changemodeadvanced')}}"><i><b>Modo avanzado</b></i></a></li>
                            {% elseif session.get('mode') == 'basic' %}
                      <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url('session/changemodebasic')}}"><i><b>Modo Básico</b></i></a></li>
                            {% endif %}
                          {% endif %}
                  <li role="presentation" class="divider"></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url('session/logout')}}">Cerrar sesión</a></li>
                </ul>
              </li>
            </ul>
          </li>    
          {#
          <li><a href="{{url('session/logout')}}" title="Cerrar sesión"><span class="glyphicon glyphicon-log-out"></span></a></li>
          #}
        </ul>	
      </div>
    </nav>

    <!-- Contenedor principal -->
    {% if flashMessage.getLengthAdmin() > 0 %}
      <div class="row">
        <div class="col-sm-12">
          {% for msg in flashMessage.getMessagesAdmin()%}      
            <div class="alert alert-completed alert-aoi-{{msg.type}}">
              <button type="button" class="close" data-dismiss="alert">×</button>
              {{msg.message}}                    
            </div>
          {% endfor %}
        </div>
      </div>
    {% endif %}
    <div class="container-fluid" style="margin-bottom: 10%;">

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

          {# Zona de mensajes #}
          {% if flashMessage.getLengthInfo() > 0 %}
            <div class="space"></div>
            <div class="row">
              <div class="col-sm-12">
                {% for msg in flashMessage.getMessagesInfo()%}
                  <div class="alert alert-aoi-{{msg.type}}">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{msg.message}}
                  </div>
                {% endfor %}
              </div>
            </div>
          {% endif %}                                            
          {# Fin de zona de mensajes #}

          <!-- Inicio de contenido -->
          {% block content %}
            <!-- Aqui va el contenido -->
          {% endblock %}
          <!-- Fin de contenido -->
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="space"></div>         
      <div class="clearfix"></div>
      <div class="space"></div> 
      <div class="clearfix"></div>
      <div class="space"></div> 
      <div class="clearfix"></div>
      <div class="space-responsive"></div> 
      <div class="clearfix"></div>



    </div>
    {% if flashMessage.getLengthFooter() > 0 %}
      <div class="">
        <div class="alert-footer container-footermessages">
          {% for msg in flashMessage.getMessagesFooter()%}      
            <div class="alert alert-completed alert-aoi-{{msg.type}}">
              <button type="button" class="close" data-dismiss="alert">×</button>
              {{msg.message}}                    
            </div>
          {% endfor %}
        </div>
      </div>
    {% endif %}

    <!-- Menú inteligente -->                    
    <div class="principal-menu per-footerColor per-bottomLine">
      {{ partial("partials/menu_partial") }}
    </div>
    <!-- Fin del menú inteligente-->    

    <!-- Ocultador del menú -->
    <div class="hider-container">
      <button class="button fill btn btn-sm warning menu-hider" onClick="hideElement('principal-menu');" data-toggle="tooltip" data-placement="top" title="Ocultar menú">
        <span class="glyphicon glyphicon-th-large"></span>
      </button>
    </div>  

    <!-- Ocultador del menú -->
    <div class="hider-container">
      <button class="button fill btn btn-sm info social-hider" onClick="hideElement('smartphone-social-network');" data-toggle="tooltip" data-placement="top" title="Síguenos en nuestras redes sociales">
        <span class="glyphicon glyphicon-sunglasses"></span>
      </button>
    </div>

    <!-- Ocultador del menú -->
    <div class="hider-container">
      <div class="smartphone-social-network">
        <a href="https://es-es.facebook.com/SigmaMovil" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en facebook">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/facebook-icon-lg.png" width="70" height="70" />
        </a>
        <a href="https://twitter.com/SigmaMovil" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en twitter">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/twitter-icon-lg.png" width="70" height="70" />
        </a>
        <a href="https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en youtube">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/youtube-icon-lg.png" width="70" height="70" />
        </a>
        <a href="https://plus.google.com/+Sigmamovil/posts" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en google plus">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/google-plus-icon-lg.png" width="70" height="70"/>
        </a>
        <a href="https://www.linkedin.com/company/sigma-m-vil-s.a." class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en linkedin">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/linkedin-icon-lg.png" width="70" height="70"/>
        </a>
      </div>
    </div>    

    {# OLARK #}
    {% if chat.enabled %}
      <!-- begin olark code -->
      <script data-cfasync="false" type='text/javascript'>/*<![CDATA[*/window.olark || (function (c) {
          var f = window, d = document, l = f.location.protocol == "https:" ? "https:" : "http:", z = c.name, r = "load";
          var nt = function () {
            f[z] = function () {
              (a.s = a.s || []).push(arguments)
            };
            var a = f[z]._ = {
            }, q = c.methods.length;
            while (q--) {
              (function (n) {
                f[z][n] = function () {
                  f[z]("call", n, arguments)
                }
              })(c.methods[q])
            }
            a.l = c.loader;
            a.i = nt;
            a.p = {
              0: +new Date};
            a.P = function (u) {
              a.p[u] = new Date - a.p[0]
            };
            function s() {
              a.P(r);
              f[z](r)
            }
            f.addEventListener ? f.addEventListener(r, s, false) : f.attachEvent("on" + r, s);
            var ld = function () {
              function p(hd) {
                hd = "head";
                return["<", hd, "></", hd, "><", i, ' onl' + 'oad="var d=', g, ";d.getElementsByTagName('head')[0].", j, "(d.", h, "('script')).", k, "='", l, "//", a.l, "'", '"', "></", i, ">"].join("")
              }
              var i = "body", m = d[i];
              if (!m) {
                return setTimeout(ld, 100)
              }
              a.P(1);
              var j = "appendChild", h = "createElement", k = "src", n = d[h]("div"), v = n[j](d[h](z)), b = d[h]("iframe"), g = "document", e = "domain", o;
              n.style.display = "none";
              m.insertBefore(n, m.firstChild).id = z;
              b.frameBorder = "0";
              b.id = z + "-loader";
              if (/MSIE[ ]+6/.test(navigator.userAgent)) {
                b.src = "javascript:false"
              }
              b.allowTransparency = "true";
              v[j](b);
              try {
                b.contentWindow[g].open()
              } catch (w) {
                c[e] = d[e];
                o = "javascript:var d=" + g + ".open();d.domain='" + d.domain + "';";
                b[k] = o + "void(0);"
              }
              try {
                var t = b.contentWindow[g];
                t.write(p());
                t.close()
              } catch (x) {
                b[k] = o + 'd.write("' + p().replace(/"/g, String.fromCharCode(92) + '"') + '");d.close();'
              }
              a.P(2)
            };
            ld()
          };
          nt()
        })({
          loader: "static.olark.com/jsclient/loader0.js", name: "olark", methods: ["configure", "extend", "declare", "identify"]});
        /* custom configuration goes here (www.olark.com/documentation) */
        olark.identify('1459-326-10-6576');/*]]>*/</script><noscript><a href="https://www.olark.com/site/1459-326-10-6576/contact" title="Contact us" target="_blank">Questions? Feedback?</a> powered by <a href="http://www.olark.com?welcome" title="Olark live chat software">Olark live chat software</a></noscript>
      <!-- end olark code -->
      <script type="text/javascript">
        // Set user's email address, fullname and nickname
        olark('api.visitor.updateEmailAddress', {
          emailAddress: '{{ userObject.email }}'
        });
        olark('api.visitor.updateFullName', {
          fullName: '{{ userObject.firstName }} {{ userObject.lastname }}'
        });
        olark('api.chat.updateVisitorNickname', {
          snippet: '{{ userObject.firstName }}'
        });
      </script>
    {% else %}
      <!-- No chat available -->
    {% endif %}
    {# /OLARK #}

    <script type="text/javascript">
      $(function () {
      {#        $('[data-toggle="tooltip"]').tooltip();#}
          });

          (function () {
            if (!String.prototype.trim) {
              $(function () {
                // Make sure we trim BOM and NBSP
                var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                String.prototype.trim = function () {
                  return this.replace(rtrim, '');
                };
              })();
            }

            [].slice.call(document.querySelectorAll('input.input-field')).forEach(function (inputEl) {
              // in case the input is already filled..
              if (inputEl.value.trim() !== '') {
                classie.add(inputEl.parentNode, 'input-filled');
              }

              // events:
              inputEl.addEventListener('focus', onInputFocus);
              inputEl.addEventListener('blur', onInputBlur);
            });

            function onInputFocus(ev) {
              classie.add(ev.target.parentNode, 'input-filled');
            }

            function onInputBlur(ev) {
              if (ev.target.value.trim() === '') {
                classie.remove(ev.target.parentNode, 'input-filled');
              }
            }
          })();


          function hideElement(element) {
            if ($('.' + element).is(":visible")) {
              $('.' + element).hide('slow');
            } else {
              $('.' + element).show('slow');
            }
          }
    </script>
    <script>
      $(document).ready(function () {
        $('[data-toggle="tooltip_default"]').tooltip();
        $('[data-toggle="tooltip"]').tooltip();
      });
    </script>
    {# Modernizr #}
    {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
    {% block footer %} {% endblock %}   
    {# Base JS de bootstrap #}
    {{ javascript_include('library/bootstrap-3.3.4/js/bootstrap.min.js') }}
    {# Sticky Tables #}
{#    {{ javascript_include('library/sticky-table-headers/js/jquery.ba-throttle-debounce.min.js') }}
    {{ javascript_include('library/sticky-table-headers/js/jquery.stickyheader.min.js') }}#}
    {# Classie #}
    {{ javascript_include('library/text-input-effects/js/classie.min.js') }}
    {#    {{ javascript_include('library/angular-1.5/js/angular.min.js') }}#}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    {#        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.js"></script>#}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
    {% block js %}<!-- custom js code -->{% endblock %}
  </body>
</html>
