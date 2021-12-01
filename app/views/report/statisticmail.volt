{{ stylesheet_link('css/report_general.min.css') }}
<div class="content">
  <div class="row">
      <div class="main row">
        
      <!-- div menu -->
      
      <!-- Drag and Drop -->
{#      <div id="divOptions" class="col-lg-1 col-md-1 col-xs-1 toggled" data-drop="true" data-jqyoui-options="actionDroppableOptions" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true}" jqyoui-droppable="{multiple:true, onStart:'actionDroppableOptions.startCallback', onOver:'actionDroppableOptions.onOver', onDrop:'actionDroppableOptions.onDrop', onOut:'actionDroppableOptions.onOut'}">#}
      <div id="divOptions" class="col-lg-1 col-md-1 col-xs-1" data-drop="true" data-jqyoui-options="actionDroppableOptions" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true}" jqyoui-droppable="{multiple:true, onStart:'actionDroppableOptions.startCallback', onOver:'actionDroppableOptions.onOver', onDrop:'actionDroppableOptions.onDrop', onOut:'actionDroppableOptions.onOut'}">
          <!-- Sidebar -->
          <div id="sidebar-wrapper">
            <ul class="sidebar-nav" style="margin-left:0;">
              <li class="sidebar-brand" style="padding-right: 15px;">
                <a href="#menu-toggle" id="menu-toggle" style="margin-top:20px;float:right;" > 
                  <i class="fa fa-bars " style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i>
                </a>
              </li>
              
              <li ng-model="listFilters" ng-repeat="item in listFilters">
                <ul class="menuOptionsDisplay">
                  <li title="{{'{{item.title}}'}}" class="btn"  data-toggle="collapse" data-target="#{{'{{item.nameId}}'}}" style="cursor: pointer; font-size: 20px; width: 100%; outline: none; margin-left: 0px;">
                    <span style="color: white;">{{'{{item.title}}'}}</span>
                    <i class="{{'{{item.class}}'}}" class="iconOption" style="color:white;" aria-hidden="true"></i>
                  </li>
                  <li id="divListOptionMail" class="collapse in" ng-if="item.value == 1">
                    <a ng-model="lisFiltersOpsMail" 
                       ng-repeat="listoptionmail in lisFiltersOpsMail" 
                       ng-if="listoptionmail.category == 'm'"
                       href="javascript:void(0)" 
                       jqyoui-draggable="{index: {{"{{$index}}"}},placeholder:true}" 
                       ng-hide="!listoptionmail.title" 
                       value='{{"{{listoptionmail.value}}"}}' 
                       ng-click="subscribe(listoptionmail)"
                       class="btn list-group-item border-category boder-droppable buttonOptionCategory">
                      <span>{{'{{listoptionmail.title}}'}}</span>
                    </a>
                  </li>
                  <li id="divListOptionSms" class="collapse in" ng-if="item.value == 2">
                    <a ng-model="lisFiltersOpsMail"
                       ng-repeat="listoptionmail in lisFiltersOpsMail"
                       ng-if="listoptionmail.category == 's'"
                       data-drag="{{"{{listoptionmail.drag}}"}}" 
                       ng-hide="!listoptionmail.title" 
                       value='{{"{{listoptionmail.value}}"}}' 
                       ng-click="subscribe(listoptionmail)" 
                       href="javascript:void(0)" 
                       class="btn list-group-item border-category boder-droppable buttonOptionCategory">
                      {{'{{listoptionmail.title}}'}}
                    </a>
                  </li>
                  <!-- Drag and Drop -->
                  {#<li id="divListOptionMail" class="collapse" ng-if="item.value == 1">
                    <a ng-model="lisFiltersOpsMail" 
                       ng-repeat="listoptionmail in lisFiltersOpsMail" 
                       ng-if="listoptionmail.category == 'm'"
                       href="javascript:void(0)" 
                       data-drag="{{"{{listoptionmail.drag}}"}}" 
                       data-jqyoui-options="{revert: 'invalid'}" 
                       jqyoui-draggable="{index: {{"{{$index}}"}},placeholder:true}" 
                       ng-hide="!listoptionmail.title" 
                       value='{{"{{listoptionmail.value}}"}}' 
                       ng-click="subscribe(listoptionmail)"
                       class="btn list-group-item border-category boder-droppable buttonOptionCategory">
                      <span>{{'{{listoptionmail.title}}'}}</span>
                    </a>
                  </li>
                  <li id="divListOptionSms" class="collapse" ng-if="item.value == 2">
                    <a ng-model="lisFiltersOpsMail"
                       ng-repeat="listoptionmail in lisFiltersOpsMail"
                       ng-if="listoptionmail.category == 's'"
                       data-drag="{{"{{listoptionmail.drag}}"}}" 
                       data-jqyoui-options="{revert: 'invalid'}" 
                       jqyoui-draggable="{index: {{"{{$index}}"}},placeholder:true}" 
                       ng-hide="!listoptionmail.title" 
                       value='{{"{{listoptionmail.value}}"}}' 
                       ng-click="subscribe(listoptionmail)" 
                       href="javascript:void(0)" 
                       class="btn list-group-item border-category boder-droppable buttonOptionCategory">
                      {{'{{listoptionmail.title}}'}}
                    </a>
                  </li>#}
                </ul>
              </li>
            </ul>
          </div>
          <!-- /#sidebar-wrapper -->
        </div>

      <div id="mainDashboard" class="col-lg-9">
        <div class="row">
          <div class="panel-dash-board col-lg-3">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-5">
                    <i class="fa fa-address-card-o fa-5x"></i>
                  </div>
                  <div class="col-xs-7 text-right">
                    <div class="div-count-data">{{'{{mailOpenMonth}}'}}</div>
                    <div class="div-descript-data">Mail Mes</div>
                  </div>
                </div>
              </div>
              <div class="div-dashboard panel-footer announcement-bottom">
                <div class="row">
                  <a class="col-lg-6 col-xs-6" href="javascript:void(0)" ng-click="redirect('infomail')">
                    Ver más
                  </a>
                  <div class="col-xs-6 text-right">
                    <i class="fa fa-arrow-circle-right"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="panel-dash-board col-lg-3">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-5">
                    <i class="fa fa-barcode fa-5x"></i>
                  </div>
                  <div class="col-xs-7 text-right">
                    <div class="div-count-data">{{'{{boundedHardMonth}}'}}</div>
                    <div class="div-descript-data">Rebotes Mes</div>
                  </div>
                </div>
              </div>
              <div class="panel-footer announcement-bottom">
                <div class="row panel-dash-footer">
                  <a class="col-lg-6 col-xs-6" href="javascript:void(0)" ng-click="redirectModulo('../mail')">
                    Ver más
                  </a>
                  <div class="col-xs-6 text-right">
                    <i class="fa fa-arrow-circle-right"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="panel-dash-board col-lg-3">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-5">
                    <i class="fa fa-users fa-5x"></i>
                  </div>
                  <div class="col-xs-7 text-right">
                    <div class="div-count-data">{{'{{quantityPollMonth}}'}}</div>
                    <div class="div-descript-data">Encuestas Mes</div>
                  </div>
                </div>
              </div>
              <div class="panel-footer announcement-bottom">
                <div class="row">
                  <a class="col-lg-6 col-xs-6" href="javascript:void(0)" ng-click="redirectModulo('../survey')">
                    Ver más
                  </a>
                  <div class="col-xs-6 text-right">
                    <i class="fa fa-arrow-circle-right"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="panel-dash-board col-lg-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-5">
                    <i class="fa fa-comments fa-5x"></i>
                  </div>
                  <div class="col-xs-7 text-right">
                    <div class="div-count-data">{{'{{smsMonth}}'}}</div>
                    <div class="div-descript-data">Sms Mes</div>
                  </div>
                </div>
              </div>
              <div class="panel-footer announcement-bottom">
                <div class="row">
                  <a class="col-lg-6 col-xs-6" href="javascript:void(0)" ng-click="redirect('infosms')">
                    Ver más
                  </a>
                  <div class="col-xs-6 text-right">
                    <i class="fa fa-arrow-circle-right"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
                    
        <div class="row">
          <!-- Menu General -->
          <div id="rowDrop" class="col-lg-12">
            <div id="divChooseOptions" class="row" style="min-height: 100px;" data-drop="true" ng-model="listDropMail" data-jqyoui-options="optionsList" jqyoui-droppable="{multiple:true, onStart:'optionsList.startCallback', onOver:'optionsList.onOver(listDrop)',onDrop:'optionsList.onDrop', onOut:'optionsList.onOut'}">
              <div  ng-repeat="optionFil in listDropMail" class="col-lg-6">
                <div class="row">
                  <div class="col-md-12">
                    <i class="fa fa-close float-right cursor-pointer" style="margin-top: 0px; color: #f51818;" aria-hidden="true" title="Suscribir" ng-click="unsubscribe(optionFil)"></i>
                    <uib-tabset active="activePill" class="panel panel-default">
                      <uib-tab index="$index" ng-repeat="tab in optionFil['tabsDate']" ng-if="tab.valueoption==optionFil.value" heading="{{'{{tab.title}}'}}" select="selectTab(tab.value, optionFil.category, optionFil, tab.pos, tab.timeFindSpecific, tab.valueoption, optionFil.title)">
                        <div class="panel">
                          <div id="divCharFil{{'{{optionFil.category}}'}}{{'{{optionFil.value}}'}}{{'{{tab.pos}}'}}" class="panel-heading" style="padding: 0px;">
                              {{'{{optionFil.title}}'}}
                          </div>
                          <div class="row">
                            <div class="text-center">
                              <button class="btn btn-default btn-md" style="width: 100px;" data-toggle="tooltip" title="Columnas" value="{{'{{optionFil.category}}'}}{{'{{optionFil.value}}'}}{{'{{tab.pos}}'}}" ng-click="InvertirChart(optionFil.category+optionFil.value+tab.pos,1);">
                                <span class="glyphicon glyphicon-signal" aria-hidden="true"></span>
                              </button>
                              <button class="btn btn-default btn-md" style="width: 100px;" data-toggle="tooltip" title="Torta" value="{{'{{optionFil.category}}'}}{{'{{optionFil.value}}'}}{{'{{tab.pos}}'}}" ng-click="InvertirChart(optionFil.category+optionFil.value+tab.pos,2);">
                                <span class="glyphicon glyphicon-adjust" aria-hidden="true"></span>
                              </button>
                              <button class="btn btn-default btn-md" style="width: 100px;" data-toggle="tooltip" title="Puntos" value="{{'{{optionFil.category}}'}}{{'{{optionFil.value}}'}}{{'{{tab.pos}}'}}" ng-click="InvertirChart(optionFil.category+optionFil.value+tab.pos,3);">
                                <span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span>
                              </button>
                            </div>
                            </br>
                            {#<div class="dropdown text-center">
                              <button class="btn btn-default dropdown-toggle" style="max-width: 66%;" data-toggle="dropdown">
                                Estilos
                                <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu">
                                <li>
                                  <a href="javascript:void(0)" ng-click="themeDefault(optionFil.category+optionFil.value+tab.pos);">Default</a>
                                </li>
                                <li>
                                  <a href="javascript:void(0)" ng-click="themeChartGridLight(optionFil.category+optionFil.value+tab.pos);">Sand</a>
                                </li>
                                <li>
                                  <a href="javascript:void(0)" ng-click="themeChartSand(optionFil.category+optionFil.value+tab.pos);">Grid</a>
                                </li>
                              </ul>
                            </div>#}
                          </div>
                        </div>
                      </uib-tab>
                    </uib-tabset>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
                                
      </div>    
  </div>

  <script type="text/javascript">
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#divOptions").toggleClass("toggled");
        $("#mainDashboard").toggleClass("col-lg-11");
        $("#mainDashboard").toggleClass("col-lg-9");
    });
  </script>
  </div>
</div>
