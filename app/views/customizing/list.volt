<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Personalización del sistema
    </div>            
    <hr class="basic-line" />
    <p>
      Lista de temas para personalizar el sistema
    </p>
  </div>
</div>
<div class="row wrap">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

  </div>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right">
    <a href="#/add" class="button shining btn btn-md success-inverted">
      Agregar un nuevo tema
    </a>

  </div>
</div>
<style>

  .selectedTheme{
    -webkit-box-shadow: 0px 0px 2px 4px rgba(0,159,178,1);
    -moz-box-shadow: 0px 0px 2px 4px rgba(0,159,178,1);
    box-shadow: 0px 0px 2px 4px rgba(0,159,178,1);
  }

  .noSelectedTheme{
    -webkit-box-shadow: 0px 0px 4px 0px rgba(0,159,178,1);
    -moz-box-shadow: 0px 0px 4px 0px rgba(0,159,178,1);
    box-shadow: 0px 0px 4px 0px rgba(0,159,178,1);

  }
  .default{
    width: 10%;
  }
  .noDefault{
    width: 32%;
  }
</style>
{#<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="box-theme col-xs-12 col-sm-12 col-md-12 col-lg-4" ng-repeat="theme in customizing[0].items">

      <div id="theme{{'{{$index}}'}}" ng-class="theme.status == 'selected' ? 'selectedTheme' : 'noSelectedTheme'" class="item-theme " ng-mouseover="mouseover($index)" ng-mouseleave="mouseleave($index)">
        <div class="theme" style="height:18em; width: 93%;z-index: 0; position: absolute;">
          <div class="header" style="border-bottom: 1px solid {{'{{theme.mainColor}}'}}; height: 3em; background: {{'{{theme.headerColor}}'}}">
            <div style="height:2.9em; background: {{'{{theme.userBoxColor}}'}}; width: 20%; float: right; text-align: center; padding-top: 3%; color:{{'{{theme.headerTextColor}}'}};">
              Usuario
            </div>
            <div style="height:40%; color: {{'{{theme.headerTextColor}}'}}; float: right; margin-top: 3%; margin-right: 1%;">325/542</div>
            <div style="height:40%; width: 4%; background: {{'{{theme.headerTextColor}}'}}; float: right; border-radius: 100%; margin-top: 3%;margin-right: 1%;"></div>
            <img ng-show="theme.logoRoute" style="width: 6%; margin: 1.8%;" src="{{"{{theme.logoRoute}}"}}">
            <div ng-show="!theme.logoRoute" style="height:40%; float: left; border-radius: 100%; margin-top: 3%;margin-left: 2%; font-size: 11px">{{'{{theme.mainTitle}}'}}</div>
          </div>
          <div class="body" style="height: 12em; z-index: 1;text-align: center;">
            <img ng-if="theme.idMasteraccount==null" class='logo' src='{{'{{customizing[1].url}}'}}/themes/default/images/aio.png' style='width:25%; margin: 5%;' alt='Sigma Móvil'/>

          </div>
          <div class="footer" style="border-top: 1px solid {{'{{theme.mainColor}}'}}; height: 3em; background: {{'{{theme.footerColor}}'}};">
            <div  style="width: 60%; margin: 0 auto;  height: 3em;">
              <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2" style=" height: 3em; padding: 0px;">
                <div style="width: 75%; height: 75%; border: 4px solid {{'{{theme.mainColor}}'}}; margin: 10%; border-radius: 100%;"> 
                  <div  style="width: 55%; height: 55%; background: {{'{{theme.footerIconColor}}'}}; margin: 24%; border-radius: 10%"></div> 
                </div> 
              </div>
              <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2" style=" height: 3em; padding: 0px;">
                <div style="width: 75%; height: 75%; border: 4px solid {{'{{theme.mainColor}}'}}; margin: 10%; border-radius: 100%;"> 
                  <div  style="width: 55%; height: 55%; background: {{'{{theme.footerIconColor}}'}}; margin: 24%; border-radius: 10%"></div> 
                </div> 
              </div>
              <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2" style=" height: 3em; padding: 0px;">
                <div style="width: 75%; height: 75%; border: 4px solid {{'{{theme.mainColor}}'}}; margin: 10%; border-radius: 100%;"> 
                  <div  style="width: 55%; height: 55%; background: {{'{{theme.footerIconColor}}'}}; margin: 24%; border-radius: 10%"></div> 
                </div> 
              </div>
              <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2" style=" height: 3em; padding: 0px;">
                <div style="width: 75%; height: 75%; border: 4px solid {{'{{theme.mainColor}}'}}; margin: 10%; border-radius: 100%;">
                  <div  style="width: 55%; height: 55%; background: {{'{{theme.footerIconColor}}'}}; margin: 24%; border-radius: 10%"></div> 
                </div> 
              </div>
              <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2" style=" height: 3em; padding: 0px;">
                <div style="width: 75%; height: 75%; border: 4px solid {{'{{theme.mainColor}}'}}; margin: 10%; border-radius: 100%;"> 
                  <div  style="width: 55%; height: 55%; background: {{'{{theme.footerIconColor}}'}}; margin: 24%; border-radius: 10%"></div> 
                </div> 
              </div>
              <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2" style=" height: 3em; padding: 0px;">
                <div style="width: 75%; height: 75%; border: 4px solid {{'{{theme.mainColor}}'}}; margin: 10%; border-radius: 100%;"> <!--theme.mainColor -->
                  <div  style="width: 55%; height: 55%; background: {{'{{theme.footerIconColor}}'}}; margin: 24%; border-radius: 10%"></div> <!--footerIconColor-->
                </div> 
              </div>


            </div>
          </div> <!--theme.mainColor, theme.footerColor-->
        </div>
        <div class="menutheme" style="height:18em; width: 93%;z-index: 1; position: absolute; background-color: #777777; opacity: 0.2; display:none;">

        </div>
        <div class="menutheme" style="height:18em;width: 93%;z-index:20;position: absolute;display:none; margin: 0 0; padding-top: 8em;">
          <div ng-class="theme.idMasteraccount==null ? 'default' : 'noDefault'" style="margin: 0 auto; text-align: center;" >
            <a ng-if="theme.idMasteraccount!=null" href="#/edit/{{ '{{theme.idPersonalizationThemes}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted">
              <span class="glyphicon glyphicon-pencil"></span>
              <md-tooltip md-direction="bottom">
                Editar
              </md-tooltip>
            </a>
            <a ng-if="theme.idMasteraccount!=null"  href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-ng-click="confirmDelete(theme.idPersonalizationThemes)">
              <span class="glyphicon glyphicon-trash"></span>
              <md-tooltip md-direction="bottom">
                Eliminar
              </md-tooltip>
            </a>
            <a href="" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-ng-click="selectTheme(theme.idPersonalizationThemes)">
              <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
              <md-tooltip md-direction="bottom">
                Seleccionar como tema del sistema
              </md-tooltip>
            </a>

          </div>
        </div>

      </div>
      <div class="title-theme"><h4><b>{{"{{theme.name}}"}}</b></h4></div>
      <br>
    </div>

  </div>
</div>#}
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <table class="table table-bordered sticky-enabled ">                
      <thead class="theader">
        <tr>
          <th style="width: 40%">Nombre</th>
          <th style="width: 40%">Detalles</th>
          <th style="width: 20%"></th>
        </tr>
      </thead>
      <tbody>
        <tr ng-repeat="theme in customizing[0].items" ng-class="theme.status == 'selected' ? 'success' : ''">
          <td>
            <font class="strong-text ng-binding medium-text" >
            </font>
            <dl>
              <dd><h4><b>{{"{{theme.name}}"}}</b></h4></dd>
              <dd><h6>{{"{{theme.description}}"}}</h6></dd>
            </dl>
          </td>
          <td>
            <dl>
              <dd> <em class="extra-small-text">Creado por <strong>{{"{{theme.createdBy}}"}}</strong> , el <strong ng-bind="theme.created | date:'dd/MM/yyyy'"></strong> </em></dd>
              <dd> <em class="extra-small-text">Actualizado por <strong>{{"{{theme.updatedBy}}"}}</strong>, el <strong ng-bind="theme.updated | date:'dd/MM/yyyy'"></strong></em></dd>
            </dl>
          </td>
          <td class="user-actions text-right">
            <a ng-if="theme.idAllied!=null" href="#/edit/{{ '{{theme.idPersonalizationThemes}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted">
              <span class="glyphicon glyphicon-pencil"></span>
              <md-tooltip md-direction="bottom">
                Editar
              </md-tooltip>
            </a>
            <a ng-if="theme.idAllied!=null"  href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-ng-click="confirmDelete(theme.idPersonalizationThemes)">
              <span class="glyphicon glyphicon-trash"></span>
              <md-tooltip md-direction="bottom">
                Eliminar
              </md-tooltip>
            </a>
            <a href="" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-ng-click="selectTheme(theme.idPersonalizationThemes)">
              <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
              <md-tooltip md-direction="bottom">
                Seleccionar como tema del sistema
              </md-tooltip>
            </a>
            <a ng-click="openModalPreview(theme)" data-dialog="preview"  class="button shining btn btn-xs-round shining-round round-button warning-inverted" >
              <span class="fa fa-eye" aria-hidden="true"></span>
              <md-tooltip md-direction="top">
                Visualizar
              </md-tooltip>
            </a>
          </td>
        </tr>
      </tbody>                    
    </table>    

  </div>
</div>
<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Esta seguro?</h2>
      <div>
        ¿Esta seguro de que desea eliminar el tema personalizado?
      </div>
      <br>
      <div>
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="#/" data-ng-click="deleteTheme()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>
               <div id="preview" class="dialog dialog--close">

          <div class="dialog__overlay"></div>
          <div class="dialog__content" style="max-width:1000px; width: 100%;">

            <div class="dialog-inner">
              <div>
                <div id="previewHtml">
                  <style>

                    /*Color de los enlaces*/
                    .link-cutomized{
                      color: {{"{{linkColor}}"}};
                    }
                    .link-cutomized:hover{
                      text-decoration: none;
                      color: {{"{{linkHoverColor}}"}} !important;
                    }
                    /*Color de las líneas con el color principal*/
                    .topLine{
                      border-bottom-color: {{"{{mainColor}}"}};
                    }
                    .bottomLine{
                      border-top-color: {{"{{mainColor}}"}};
                    }

                    /*Color de la caja del usuario*/
                    .userBoxColor{
                      background: {{"{{userBoxColor}}"}};
                    }
                    /*Color de la caja del usuario en hover*/
                    .userBoxColor:hover{
                      background: {{"{{userBoxHoverColor}}"}} !important;
                    }
                    /*Color de los iconos del footer en hover*/
                    .dashed-effect-customized .hi-icon-customized:hover{
                      color: {{"{{mainColor}}"}} !important;
                    }
                    /*Color de los iconos del footer*/
                    .dashed-effect-customized .hi-icon-customized{
                      color: {{"{{footerIconColor}}"}};
                      box-shadow: 0 0 0 4px {{"{{mainColor}}"}};
                    }
                    /*Color de los iconos del footer*/
                    .icon-footer-color > li > a:after{
                      border: 2px dashed {{"{{mainColor}}"}} !important;
                    }
                    /*Color de la letra del header*/
                    .icon-footer-color .navbar-nav > li > a, .headerTextColor{
                      color: {{"{{headerTextColor}}"}};
                    }
                    /*Hover del texto del header*/
                    .headerTextColor:hover{
                      text-decoration: none;
                      color: {{"{{mainColor}}"}} !important;
                    }
                    /*Texto del header*/
                    .headerTextColor{
                      color: {{"{{headerTextColor}}"}} !important;
                    }

                    /*Color del header*/
                    .container-fluid-customized{
                      background-color:{{"{{headerColor}}"}};
                    }

                    /*Color del footer*/
                    .footerColor{
                      background: {{"{{footerColor}}"}};
                    }


                  </style>
                  <nav class="navbar navbar-default navbar-default-customized topLine" role="navigation">
                    <div class="container-fluid container-fluid-customized">
                      <div class="navbar-header" style="margin-top: 1%">
                        <a class="headerTextColor" style="display: inline;"  href="">
                          <img class="imgOnLoad imagenView hidden" style="width: 32px; height: 32px;">
                          {#                          <img id="imgOnLoad" class="imagenView hidden" style="width: 90px; height: 90px; margin: 10px;">#}


                          <spam id="mainTitle" style='width:10%;padding-left: 5px;display:inline;'>{{"{{mainTitle}}"}}</spam>
                        </a>
                      </div>
                      <ul id="top-nav" class="nav navbar-nav navbar-right">
                        <li data-toggle="tooltip" data-placement="left" title="Queremos conocer tus comentarios">
                          <a class="headerTextColor" href=""><span class="glyphicon glyphicon-comment"></span></a>
                        <li data-toggle="tooltip" data-placement="bottom" title="Contactos">
                          <a class="headerTextColor" href="" class="default-cursor ">
                            <span class="glyphicon glyphicon-user"></span> 500/2000
                          </a>
                        </li>

                        <li data-toggle="tooltip" data-placement="bottom" title="Correos">
                          <a href="" class="default-cursor headerTextColor">
                            <div id="">
                              <span class="glyphicon glyphicon-envelope"></span> 8/2000
                            </div>
                          </a>
                        </li>

                        <li data-toggle="tooltip" data-placement="bottom" title="SMS">
                          <a href="" class="default-cursor headerTextColor">
                            <div id="">
                              <span class="glyphicon glyphicon-phone"></span> 1550/2000
                            </div>
                          </a>
                        </li>
                        <li data-toggle="tooltip" data-placement="bottom" title="Visitas">
                          <a href="" class="default-cursor headerTextColor">
                            <div id="">
                              <span class="glyphicon glyphicon-blackboard"></span> 200/2000
                            </div>
                          </a>
                        </li>

                        <li class="top-nav-hover" data-toggle="tooltip" data-placement="bottom" title="Puntuación">
                          <a href="" class="headerTextColor">
                            <div class="orange-sigma text-with-shadow" style="display: inline;">
                              <span class="glyphicon glyphicon-star"></span>
                            </div>200000

                          </a>
                        </li>


                        <li>
                          <ul class="nav nav-pills nav-pills-profile" role="tablist">
                            <li role="presentation" class="dropdown">
                              <a id="drop6" href="" class="dropdown-toggle profile-menu headerTextColor userBoxColor" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                Nombre de usuario                 <span class="caret"></span>
                              </a>
                              <ul id="menu3" class="dropdown-menu" role="menu" aria-labelledby="drop6">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href=""><b>ROOT</b></a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="">Mi perfil</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="">Another action</a></li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="">Cerrar sesión</a></li>
                              </ul>
                            </li>
                          </ul>
                        </li>    

                      </ul>	
                    </div>
                  </nav>
                  <div style="height: 20em; ">
                    <h1><a class="link-cutomized">Esto es un enlace</a></h1>
                  </div>
                  <div class="principal-menu bottomLine footerColor" style="position: inherit;">
                    <div class="left-position" style="position: absolute;left: 65px;">
                      {#                      <div class="item-info" ng-repeat="info in infos">{{"{{info.textInfo}}"}}</div> #}

                    </div>
                    <div class="menu-footer right-position" style="bottom: initial; right: 6em;">
                      {# <div class="social-network" ng-if='showBlockSocial'>
                         <a ng-repeat="social in socialsordered" href="{{"{{social.urlSocial}}"}}" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="{{"{{social.titleSocial}}"}}">
                           <img style="width: 23px;" src="{{"{{socialnetworks[1].url}}"}}themes/default/images/social-networks/{{"{{socialnetworks[0].items[social.idSocial - 1].img}}"}}" />
                         </a>
                       </div>#}

                    </div>
                    <ul class="item-menu-container dashed-effect dashed-effect-customized dashed-effect-b icon-footer-color">
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Página de inicio">
                        <a href="" target="" class="hi-icon hi-icon-customized  hi-icon-home "></a>
                      </li>
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reportes y estadísticas">
                        <a href="" target="" class="hi-icon hi-icon-customized hi-icon-statistics "></a>
                      </li>
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Herramientas">
                        <a href="" target="" class="hi-icon hi-icon-customized hi-icon-tools "></a>
                      </li>
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cuentas">
                        <a href="" target="" class="hi-icon hi-icon-customized hi-icon-accounts "></a>
                      </li>
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sistema">
                        <a href="" target="" class="hi-icon hi-icon-customized hi-icon-system "></a>
                      </li>
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ayuda">
                        <a href="" target="_blank" class="hi-icon hi-icon-customized hi-icon-help "></a>
                      </li>
                      <li class="shining" data-toggle="tooltip" data-placement="top" title="" data-original-title="Chat">
                        <a href="" target="" class="hi-icon hi-icon-customized hi-icon-chat "></a>
                      </li>

                    </ul>
                  </div>

                </div>

                <br>
                <div>
                  <a ng-click="closeModalPreview();" class="button shining btn btn-xs-round success-inverted" data-dialog-close>Ok</a>
                </div>
              </div>

            </div>
          </div>
        </div>
<script>

  {#$(".item-theme").on("mouseover", function () {
    console.log("open");
    $(this).find(".menutheme").show();
  }).on("mouseout", function () {
    console.log("close");
    $(this).find(".menutheme").hide();
  });#}
    $(document).ready(function () {
      $('[data-toggle="popover"]').popover();
    });
    $(function () {
      $('#details').tooltip();
    });

    function openModal() {
      $('#somedialog').addClass('dialog--open');
    }

    function closeModal() {
      $('#somedialog').removeClass('dialog--open');
    }

</script>

