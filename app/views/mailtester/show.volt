{% extends "templates/clean.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block css %}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  <style>
    details {
      border-radius: 3px;
      background: #EEE;
      margin: 1em 0;
    }

    summary {
      background: #333;
      color: #FFF;
      border-radius: 3px;
      padding: 5px 10px;
      outline: none;
    }

    /* Style the summary when details box is open */
    details[open] summary {
      background: #4cae4c;
      color: #333;
    }
    body{
      text-align: left !important;
    }
    progress-bar{
      margin-top:8%;
    }
  </style>
{% endblock %}

{% block js %}
  <script>
    var mailTester = "{{mailTester}}";
    var idAllied
    = {{idAllied}};
            var relativeUrlBase = "{{ urlManager.get_base_uri()}}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true)}}";
  </script>
  {{ javascript_include('library/angular-1.5/js/angular.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap-tpls-2.5.0.min.js') }}

  {{javascript_include('js/angular/unsubscribe/services.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{javascript_include('js/angular/mailtester/app.js') }}


{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="site-wrapper" ng-controller="appMailTesterCtrl" ng-cloak ng-init="objGlobal.initFun()">
    <div class="site-wrapper-inner container-fluid">
      <div class="session-container">
        <div class="container">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
              <img class="session-logo" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
            </div>
            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
              <div class="row padding-top-15px">
                <div class="text-primary text-3em text-center">
                  {{'{{objGlobal.MailTester.displayedMark}}'}}
                  <div class="pull-right">
                    CARA
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top:1%;">
                <div class="border-blue ">
{#                  <pre>{{'{{objGlobal}}'}}</pre>#}
                  <div ng-style="{'height':'5px','width':objGlobal.porcenProgressBar,'background-color':objGlobal.colorProgressBar}"></div>
                </div>  
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <uib-accordion close-others="objGlobal.oneAtATime">
              <div uib-accordion-group class="panel-default" is-open="objGlobal.openFirst" >
                <uib-accordion-heading>
                  Haz clic aquí para ver tu mensaje <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': objGlobal.openFirst, 'glyphicon-chevron-right': !objGlobal.openFirst}"></i>
                </uib-accordion-heading>
                <div class="small-tex">{{"{{objGlobal.MailTester.messageInfo.fromAddress}}"}}</div>
                <div class="small-tex" ng-bind-html="objGlobal.MailTester.messageInfo.fromAddressDisplayed"></div>

                <details>
                  <summary>{{"{{objGlobal.MailTester.body.html.title}}"}}</summary>
                  <div class="small-tex" ng-bind-html="objGlobal.MailTester.body.html.content"></div>
                </details>

                <details>
                  <summary>{{"{{objGlobal.MailTester.body.imageLess.title}}"}}</summary>
                  <div class="small-tex">{{"{{objGlobal.MailTester.body.imageLess.content}}"}}</div>
                </details>

                <details>
                  <summary>{{"{{objGlobal.MailTester.body.text.title}}"}}</summary>
                  <div class="small-tex">{{"{{objGlobal.MailTester.body.text.content}}"}}</div>
                </details>

                <details>
                  <summary>{{"{{objGlobal.MailTester.body.raw.title}}"}}</summary>
                  <pre class="small-tex">{{"{{objGlobal.MailTester.body.raw.content}}"}}</pre>
                </details>
              </div>
              <div uib-accordion-group class="panel-default" is-open="objGlobal.openSecond" >
                <uib-accordion-heading>
                  {{'{{objGlobal.MailTester.spamAssassin.title}}'}}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': objGlobal.openSecond, 'glyphicon-chevron-right': !objGlobal.openSecond}"></i>
                </uib-accordion-heading>
                <table>
                  <tr ng-repeat="(key, value) in objGlobal.MailTester.spamAssassin.rules">
                    <td>{{"{{value.score}}"}}</td>
                    <td>{{"{{key}}"}}</td>
                    <td>{{"{{value.description}}"}}<br><strong>{{"{{value.solution}}"}}</strong></td>
                  </tr>
                </table>
              </div>
              <div uib-accordion-group class="panel-default" is-open="objGlobal.openThird" >
                <uib-accordion-heading>
                  {{'{{objGlobal.MailTester.signature.title}}'}}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': objGlobal.openThird, 'glyphicon-chevron-right': !objGlobal.openThird}"></i>
                </uib-accordion-heading>
                <div ng-repeat="(key, value) in objGlobal.MailTester.signature.subtests">
                  <details>
                    <summary ng-bind-html="value.title"></summary>
                    <div class="small-tex" ng-bind-html="value.description"></div>
                    <div class="small-tex" ng-bind-html="value.messages"></div>
                  </details>
                </div>
              </div>   

              <div uib-accordion-group class="panel-default"  is-open="objGlobal.openFourth" >
                <uib-accordion-heading>
                  {{'{{objGlobal.MailTester.body.title}}'}}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': objGlobal.openFourth, 'glyphicon-chevron-right': !objGlobal.openFourth}"></i>
                </uib-accordion-heading>
                <div ng-repeat="(key, value) in objGlobal.MailTester.body.subtests">
                  <details>
                    <summary ng-bind-html="value.title"></summary>
                    <div class="small-tex" ng-bind-html="value.description"></div>
                    <div class="small-tex" ng-bind-html="value.messages"></div>
                  </details>
                </div>
              </div>  

              <div uib-accordion-group class="panel-default" is-open="objGlobal.openFifth" >
                <uib-accordion-heading>
                  {{'{{objGlobal.MailTester.blacklists.title}}'}}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': objGlobal.openFifth, 'glyphicon-chevron-right': !objGlobal.openFifth}"></i>
                </uib-accordion-heading>
                <div class="small-tex" >{{"{{objGlobal.MailTester.blacklists.description}}"}}</div>
                <div class="small-tex" ng-bind-html="objGlobal.MailTester.blacklists.messages"></div>
              </div>  

              <div uib-accordion-group class="panel-default" is-open="objGlobal.openSixth" >
                <uib-accordion-heading>
                  {{'{{objGlobal.MailTester.links.title}}'}}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': objGlobal.openSixth, 'glyphicon-chevron-right': !objGlobal.openSixth}"></i>
                </uib-accordion-heading>
                <div class="small-tex" >{{"{{objGlobal.MailTester.links.description}}"}}</div>
                <div class="small-tex" ng-bind-html="objGlobal.MailTester.links.messages"></div>
              </div>  

            </uib-accordion>
          </div>
        </div>
      </div>
    </div>
  </div>
{% endblock %}  

