<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Agregar nuevo tema para personalizar el sistema
    </div>            
    <hr class="basic-line" />

  </div>
</div>

<div class="row">
  <form novalidate name="contactlistForm" class="form-horizontal" role="form" ng-submit="editTheme()" enctype="multipart/form-data" >
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12 none-padding-left">
      <div class="form-group">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <label  class="col-sm-1 text-right">*Nombre</label>
          <span class="input hoshi input-default  col-sm-11">
            <input type="text" maxlength="50" class="undeline-input" ng-model="customizing.name"  id="name" name="name" required>
          </span>
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <label  class="col-sm-1 text-right">Descripción</label>
          <span class="input hoshi input-default  col-sm-11">
            <textarea maxlength="200" class="undeline-input" ng-model="customizing.description" id="description" name="description"></textarea>
          </span>
        </div>
      </div>

    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">

      <div ng-cloak>
        <md-content>
          <md-tabs md-dynamic-height md-border-bottom>
            <md-tab label="principal">
              <md-content class="md-padding row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="block block-info">          
                    <div class="body " >
                      <div class="row">

                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Este es el título principal que contendrá la pestaña 
                                del navegador
                              </md-tooltip>
                            </span>
                            *Título de la pestaña
                          </label>
                          <span class="input hoshi input-default col-sm-9">
                            <input type="text" ng-model="customizing.title" class="form-control"   id="title" name="title" maxlength="60" >
                          </span>
                        </div>


                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Es el color más significativo que tendrá el sistema
                              </md-tooltip>
                            </span>
                            *Color principal
                          </label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.mainColor" type="text" class='form-control' style="background-color:{{"{{customizing.mainColor}}"}}; color:{{"{{getContrastYIQ(customizing.mainColor)}}"}} ">
                          </span>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Es el color del texto que contiene un hipervínculo y nos redirige a otro lugar
                              </md-tooltip>
                            </span>
                            *Color de los enlaces
                          </label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.linkColor" type="text" class='form-control' style="background-color:{{"{{customizing.linkColor}}"}};color:{{"{{getContrastYIQ(customizing.linkColor)}}"}}">
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Cuando pase por encima de un texto con hipervínculo entonces éste cambiará al color elegido
                              </md-tooltip>
                            </span>
                            *Color de los enlaces cuando pasa por encima</label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.linkHoverColor" type="text" class='form-control' style="background-color:{{"{{customizing.linkHoverColor}}"}};color:{{"{{getContrastYIQ(customizing.linkHoverColor)}}"}}">
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Es el título que aparecerá en lugar del logo si éste no ha sido seleccionado
                              </md-tooltip>
                            </span>
                            *Título del logo</label>
                          <span  class="input hoshi input-default col-sm-9">
                            <input type="text" ng-model="customizing.mainTitle" class="form-control"   id="logo" name="logo" maxlength="60" >
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Imagen del logo (preferiblemente de tipo .png)
                              </md-tooltip>
                            </span>
                            *Imagen del logo</label>
                          <span  class="input hoshi input-default col-sm-9" style="text-align: center;">
                            <img class="imgOnLoad imagenView hidden" style="width: 90px; height: 90px; margin: 10px;">
                            <input style="margin: 0 auto" type="file" id="logo" name="logo" accept="image/jpeg,image/jpg,image/png,image/gif" onchange='angular.element(this).scope().viewImg(this); angular.element(this).scope().pushFiles(this, "logo");' >
                          </span>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
        {#        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
                  <div class="fill-block fill-block-info" >
                    <div class="header">
                      Instrucciones
                    </div>
                    <div class="body">
                      <p>
                        Recuerde tener en cuenta estas recomendaciones:
                      <ul>                            
                        <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
                      </ul> 
                      </p>
                    </div>
                    <div class="footer">
                      Creación
                    </div>
                  </div>     
                </div>  #} 
              </md-content>
            </md-tab>
            <md-tab label="cabecera">
              <md-content class="md-padding row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="block block-info">          
                    <div class="body " >
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                           <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Color de fondo de la cabecera del sistema
                              </md-tooltip>
                            </span>
                            *Color de la cabecera</label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.headerColor" type="text" class='form-control' style="background-color:{{"{{customizing.headerColor}}"}};color:{{"{{getContrastYIQ(customizing.headerColor)}}"}}">
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Color de la letra y de los iconos de la cabecera del sistema
                              </md-tooltip>
                            </span>
                            *Color de la letra</label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.headerTextColor" type="text" class='form-control' style="background-color:{{"{{customizing.headerTextColor}}"}};color:{{"{{getContrastYIQ(customizing.headerTextColor)}}"}}">
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                En la parte derecha de la cabecera del sistema se encuentra el cuadro del usuario
                              </md-tooltip>
                            </span>
                            *Color del cuadro de usuario</label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.userBoxColor" type="text" class='form-control' style="background-color:{{"{{customizing.userBoxColor}}"}};color:{{"{{getContrastYIQ(customizing.userBoxColor)}}"}}">
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                 Cuando pase por encima del cuadro del usuario entonces éste cambiará al color elegido
                              </md-tooltip>
                            </span>
                            *Color del cuadro de usuario cuando pasa por encima</label><span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.userBoxHoverColor" type="text" class='form-control' style="background-color:{{"{{customizing.userBoxHoverColor}}"}};color:{{"{{getContrastYIQ(customizing.userBoxHoverColor)}}"}}">
                          </span>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

            {#    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
                  <div class="fill-block fill-block-info" >
                    <div class="header">
                      Instrucciones
                    </div>
                    <div class="body">
                      <p>
                        Recuerde tener en cuenta estas recomendaciones:
                      <ul>                            
                        <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
                      </ul> 
                      </p>
                    </div>
                    <div class="footer">
                      Creación
                    </div>
                  </div>     
                </div> #}
              </md-content>
            </md-tab>
            <md-tab label="pie de página">
              <md-content class="md-padding row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="block block-info">          
                    <div class="body " >
                      <div class="row">
                        {#<div class="form-group">
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                            <label class="col-sm-3 text-right">*Texto del <i>Powered By</i></label>
                            <span  class="input hoshi input-default col-sm-9">
                              <input type="text" ng-model="textPoweredBy" class="form-control"   id="logo" name="logo" maxlength="60" >
                            </span>
                          </div>
                        </div>#}
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Color de fondo del pie del sistema
                              </md-tooltip>
                            </span>
                            *Color de fondo</label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.footerColor" type="text" class='form-control' style="background-color:{{"{{customizing.footerColor}}"}};color:{{"{{getContrastYIQ(customizing.footerColor)}}"}}">
                          </span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                          <label class="col-sm-3 text-right">
                            <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                              <md-tooltip md-direction="top">
                                Color de los iconos del pie del sistema
                              </md-tooltip>
                            </span>
                            *Color de los iconos</label>
                          <span class="input hoshi input-default col-sm-9">
                            <input colorpicker ng-model="customizing.footerIconColor" type="text" class='form-control' style="background-color:{{"{{customizing.footerIconColor}}"}};color:{{"{{getContrastYIQ(customizing.footerIconColor)}}"}}">
                          </span>
                        </div>

                        <div class="form-group">
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                            <label class="col-sm-12 text-left" style="border-bottom: 1px solid #009fb2; ">
                              <h3 style="color: #009fb2;float: left;">Redes sociales</h3>

                            </label>
                            <span ng-show="showSocial">
                              <div class='col-lg-3' style='float: right; margin-top: 1em; margin-bottom: 1em;'>
                                <span>Posición</span>
                                <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                                  <md-tooltip md-direction="right">
                                    Posición en la que se encontrará el bloque de redes sociales
                                  </md-tooltip>
                                </span>
                                <select ng-change="alterSocialBlockPosition()" ng-model="customizing.socialBlockPosition" style="width: 100%;padding: 3%;" id="socialBlockPosition">
                                  <option value="right">Derecha</option>
                                  <option value="left">Izquierda</option>
                                </select>
                              </div>
                              <table class="table table-bordered">
                                <thead class="theader ">
                                  <tr>
                                    <th>Nombre</th>
                                    <th>Logo</th>
                                    <th>URL</th>
                                    <th>Título 
                                      <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                                        <md-tooltip md-direction="right">
                                          Texto que aparecerá cuando pasen por encima.
                                        </md-tooltip>
                                      </span>

                                    </th>
                                    <th>
                                      Posición
                                      <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                                        <md-tooltip md-direction="right">
                                          Posición de izquierda a derecha
                                        </md-tooltip>
                                      </span>
                                    </th>
                                    <th>
                                    </th>
                                  </tr>
                                </thead>
                                <tbody id="social_networks">
                                <br>
                                <br>
                                  <tr ng-repeat="key in customizing.socials">
                                    <td style='width:20%;'>
                                      <select ng-change="getInfoSocial($index,key.idSocial); orderSocial(); isEmptyPosition()" ng-init="idSocialNet=key.idSocial" ng-model="key.idSocial" style="width: 100%;padding: 5%;" id="nameSocial{{"{{$index}}"}}">
                                        <option ng-repeat="social in customizing.socialnetworks[0].items track by $index" value="{{"{{social.idSocialNetwork}}"}}">{{"{{social.name}}"}}</option>
                                      </select>
                                      {#<select  ng-model="key.nameSocial" style="width: 100%;padding: 5%;" id="nameSocial{{"{{$index}}"}}">
                                        <option ng-repeat="social in socialnetworks[0].items" value="{{"{{social.idSocialNetwork}}"}}">{{"{{social.name}}"}}</option>
                                      </select>#}
                                    </td>
                                    <td id="imgSocial{{"{{$index}}"}}" style='text-align: center; width: 10%;'>
                                      <img style="width: 60%;" src="{{"{{customizing.socialnetworks[1].url}}"}}themes/default/images/social-networks/{{"{{customizing.socialnetworks[0].items[idSocialNet - 1].img}}"}}" />
                                    </td>
                                    <td>
                                      <input type="text" maxlength="200" ng-change="orderSocial(); isEmptyPosition()" placeholder="https://www.miredsocial.com" class="undeline-input" ng-model="key.urlSocial"  id="urlSocial{{"{{$index}}"}}" >
                                    </td>
                                    <td>
                                      <input type="text" maxlength="80" class="undeline-input" ng-change="orderSocial(); isEmptyPosition()" ng-model="key.titleSocial"  id="titleSocial{{"{{$index}}"}}">
                                    </td>
                                    <td>
                                      <input type="number"  ng-change="validateSocialPosition($index); orderSocial(); isEmptyPosition(); isOrderedPosition()" class="undeline-input" ng-model="key.positionSocial"  id="positionSocial{{"{{$index}}"}}"  min='1' max='{{"{{ socialnetworks[0].items.length}}"}}'>
                                    </td>
                                    <td style='width:6%;'>

                                      <a href="" ng-click='deleteOne($index,key.idPersonalizationSocialNetwork)' class="button shining btn btn-xs-round shining-round round-button danger-inverted">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true">
                                          <md-tooltip md-direction="buttom">
                                            Eliminar red social 
                                          </md-tooltip>
                                        </span>
                                      </a>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </span>
                            <div id="errors">

                            </div>
                            <div ng-if="!showSocial" class="text-center">
                              <h3 style="margin-top:10%; color: #777777;">No hay redes sociales asociadas</h3>
                              <h3 ng-click='registerSocialNetwork()'>Haga click AQUÍ para agregar</h3>
                            </div>
                            <a ng-if="showSocial" ng-click='newOne()' style="float: right;"  data-dialog="socialNetwork" class="button btn btn-small success-inverted trigger" >
                              <span><i class="fa fa-plus-square" aria-hidden="true"></i> Nueva red social</span>
                            </a>
                          </div>
                        </div>
                       <div class="form-group">
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                            <label class="col-sm-12 text-left" style="border-bottom: 1px solid #009fb2; ">
                              <h3 style="color: #009fb2;float: left;">Información adicional</h3>

                            </label>
                            <span ng-show="showInfo">

                              <div class='col-lg-3' style='float: right; margin-top: 1em; margin-bottom: 1em;'>
                                <span>Posición</span>
                                <span class="glyphicon glyphicon-info-sign" style="margin: 1.5%;">
                                  <md-tooltip md-direction="right">
                                    Posición en la que se encontrará el bloque de información adicional
                                  </md-tooltip>
                                </span>
                                <select ng-change="alterInfoBlockPosition()" ng-model="customizing.infoBlockPosition" style="width: 100%;padding: 3%;" id="infoBlockPosition">
                                  <option value="right">Derecha</option>
                                  <option value="left">Izquierda</option>
                                </select>
                              </div>

                              <table class="table table-bordered">
                                <thead class="theader ">
                                  <tr>
                                    <th>Texto</th>
{#                                    <th>Orden de posición</th>#}
                                    <th style="width: 6%"></th>
                                  </tr>
                                </thead>
                                <tbody id="additional_info">
                                  <tr ng-repeat="key in customizing.infos">
                                    <td >
{#                                      <input type="text" maxlength="100" ng-change="orderInfo(); isEmptyInfoPosition()" class="undeline-input" ng-model="key.textInfo"  id="textInfo{{"{{$index}}"}}" name="textInfo">#}
                                      <input type="text" maxlength="100" class="undeline-input" ng-model="key.textInfo"  id="textInfo{{"{{$index}}"}}">
                                    </td>
                                   {# <td>
                                      <input type="number"  ng-change="validateInfoPosition($index); orderInfo(); isEmptyInfoPosition(); isOrderedInfoPosition()" class="undeline-input" ng-model="key.positionInfo"  id="positionInfo{{"{{$index}}"}}" name="positionInfo" min='1' max='2'>
                                    </td>#}
                                    <td>
                                      
                                      <a href="" ng-click='deleteOneInfo($index,key.idAdditionalInfo)' class="button shining btn btn-xs-round shining-round round-button danger-inverted">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true">
                                          <md-tooltip md-direction="buttom">
                                            Eliminar información adicional
                                          </md-tooltip>
                                        </span>
                                      </a>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </span>
                            <div id="errorsInfo">

                            </div>
                            <div ng-if="!showInfo" class="text-center">
                              <h3 style="margin-top:10%; color: #777777;">No hay información adicional</h3>
                              <h3 ng-click='registerAdditionalInfo()'>Haga click AQUÍ para agregar</h3>
                            </div>
                            {#<a ng-if="showInfo" style="float: right;" ng-click='newOneAdditionalInfo()'  data-dialog="blockInformation" class="button btn btn-small success-inverted trigger" data-toggle="tooltip" data-placement="top" title="">
                              <span><i class="fa fa-plus-square" aria-hidden="true"></i> Nueva información</span>
                            </a>#}

                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                {#<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">                            
                  <div class="fill-block fill-block-info" >
                    <div class="header">
                      Instrucciones
                    </div>
                    <div class="body">
                      <p>
                        Recuerde tener en cuenta estas recomendaciones:
                      <ul>                            
                        <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
                        <li>Solo se pueden añadir las redes sociales registradas en el sistema.</li>
                        <li>Solo se pueden añadir dos bloques de información adicional. Cada bloque será un solo texto, por ejemplo el PoweredBy.</li>
                      </ul> 
                      </p>
                    </div>
                    <div class="footer">
                      Creación
                    </div>
                  </div>     
                </div>#} 
              </md-content>
            </md-tab>
          </md-tabs>
        </md-content>
      </div>
    
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">

    <div id="previewHtml" style="border: 1px solid #ddd; margin: 0.5em">
      {{"{{orderBlocks()}}"}}
                  <style>

                    /*Color de los enlaces*/
                    .link-cutomized{
                      color: {{"{{customizing.linkColor}}"}};
                    }
                    .link-cutomized:hover{
                      text-decoration: none;
                      color: {{"{{customizing.linkHoverColor}}"}} !important;
                    }
                    /*Color de las líneas con el color principal*/
                    .topLine{
                      border-bottom-color: {{"{{customizing.mainColor}}"}};
                    }
                    .bottomLine{
                      border-top-color: {{"{{customizing.mainColor}}"}};
                    }

                    /*Color de la caja del usuario*/
                    .userBoxColor{
                      background: {{"{{customizing.userBoxColor}}"}};
                    }
                    /*Color de la caja del usuario en hover*/
                    .userBoxColor:hover{
                      background: {{"{{customizing.userBoxHoverColor}}"}} !important;
                    }
                    /*Color de los iconos del footer en hover*/
                    .dashed-effect-customized .hi-icon-customized:hover{
                      color: {{"{{customizing.mainColor}}"}} !important;
                    }
                    /*Color de los iconos del footer*/
                    .dashed-effect-customized .hi-icon-customized{
                      color: {{"{{customizing.footerIconColor}}"}};
                      box-shadow: 0 0 0 4px {{"{{mainColor}}"}};
                    }
                    /*Color de los iconos del footer*/
                    .icon-footer-color > li > a:after{
                      border: 2px dashed {{"{{customizing.mainColor}}"}} !important;
                    }
                    /*Color de la letra del header*/
                    .icon-footer-color .navbar-nav > li > a, .headerTextColor{
                      color: {{"{{customizing.headerTextColor}}"}};
                    }
                    /*Hover del texto del header*/
                    .headerTextColor:hover{
                      text-decoration: none;
                      color: {{"{{customizing.mainColor}}"}} !important;
                    }
                    /*Texto del header*/
                    .headerTextColor{
                      color: {{"{{customizing.headerTextColor}}"}} !important;
                    }

                    /*Color del header*/
                    .container-fluid-customized{
                      background-color:{{"{{customizing.headerColor}}"}};
                    }

                    /*Color del footer*/
                    .footerColor{
                      background: {{"{{customizing.footerColor}}"}};
                    }


                  </style>
                  <nav class="navbar navbar-default navbar-default-customized topLine" role="navigation">
                    <div class="container-fluid container-fluid-customized">
                      <div class="navbar-header" style="margin-top: 1%">
                        <a class="headerTextColor" style="display: inline;"  href="">
                          <img class="imgOnLoad imagenView hidden" style="width: 32px; height: 32px;">
                          {#                          <img id="imgOnLoad" class="imagenView hidden" style="width: 90px; height: 90px; margin: 10px;">#}


                          <spam id="mainTitle" style='width:10%;padding-left: 5px;display:inline;'>{{"{{customizing.mainTitle}}"}}</spam>
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
                    <h2 class="text-center"><a class="link-cutomized">Esto es un enlace</a></h2>
                  </div>
                  <div class="principal-menu bottomLine footerColor" style="position: inherit;">
                    <div class="left-position" style="position: absolute; margin: 2px;">
                      {#                      <div class="item-info" ng-repeat="info in infos">{{"{{info.textInfo}}"}}</div> #}

                    </div>
                    <div class="menu-footer right-position" style="bottom: initial; right: 2.5em;">
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
      <div class="footer wrap" align="right" style="margin: 2em;">          
        <button type="submit" class="button shining btn btn-xs-round round-button success-inverted">
          <span class="glyphicon glyphicon-ok"></span>
          <md-tooltip md-direction="top">
            Guardar
          </md-tooltip>
        </button>
        <a href="{{url('customizing/index')}}" class="button  btn btn-xs-round  round-button danger-inverted" >
          <span class="glyphicon glyphicon-remove"></span>
          <md-tooltip md-direction="top">
            Cancelar
          </md-tooltip>
        </a>
          <a ng-click="saveAndContinue()" class="button btn btn-small primary-inverted" data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
          Guardar y continuar
        </a>
        {#<a ng-click="openModal('preview'); orderBlocks()" data-dialog="preview"  class="button shining btn btn-xs-round shining-round round-button success-inverted" >
          <span class="fa fa-eye" aria-hidden="true"></span>
          <md-tooltip md-direction="top">
            Visualizar
          </md-tooltip>
        </a>#}
        {#      <a ng-click="openModal('preview'); orderBlocks()" data-dialog="preview" class="button btn btn-small primary-inverted trigger" data-toggle="tooltip" data-placement="top" title="">
                <span>Visualizar</span>
              </a>#}

        <div id="preview" class="dialog dialog--close">

          <div class="dialog__overlay"></div>
          <div class="dialog__content" style="max-width:1000px; width: 100%;">

            <div class="dialog-inner">
              <div>
                <div id="previewHtml">
                  <style>

                    /*Color de los enlaces*/
                    .link-cutomized{
                      color: {{"{{customizing.linkColor}}"}};
                    }
                    .link-cutomized:hover{
                      text-decoration: none;
                      color: {{"{{customizing.linkHoverColor}}"}} !important;
                    }
                    /*Color de las líneas con el color principal*/
                    .topLine{
                      border-bottom-color: {{"{{customizing.mainColor}}"}};
                    }
                    .bottomLine{
                      border-top-color: {{"{{customizing.mainColor}}"}};
                    }

                    /*Color de la caja del usuario*/
                    .userBoxColor{
                      background: {{"{{customizing.userBoxColor}}"}};
                    }
                    /*Color de la caja del usuario en hover*/
                    .userBoxColor:hover{
                      background: {{"{{customizing.userBoxHoverColor}}"}} !important;
                    }
                    /*Color de los iconos del footer en hover*/
                    .dashed-effect-customized .hi-icon-customized:hover{
                      color: {{"{{customizing.mainColor}}"}} !important;
                    }
                    /*Color de los iconos del footer*/
                    .dashed-effect-customized .hi-icon-customized{
                      color: {{"{{customizing.footerIconColor}}"}};
                      box-shadow: 0 0 0 4px {{"{{customizing.mainColor}}"}};
                    }
                    /*Color de los iconos del footer*/
                    .icon-footer-color > li > a:after{
                      border: 2px dashed {{"{{customizing.mainColor}}"}} !important;
                    }
                    /*Color de la letra del header*/
                    .icon-footer-color .navbar-nav > li > a, .headerTextColor{
                      color: {{"{{customizing.headerTextColor}}"}};
                    }
                    /*Hover del texto del header*/
                    .headerTextColor:hover{
                      text-decoration: none;
                      color: {{"{{customizing.mainColor}}"}} !important;
                    }
                    /*Texto del header*/
                    .headerTextColor{
                      color: {{"{{customizing.headerTextColor}}"}} !important;
                    }

                    /*Color del header*/
                    .container-fluid-customized{
                      background-color:{{"{{customizing.headerColor}}"}};
                    }

                    /*Color del footer*/
                    .footerColor{
                      background: {{"{{customizing.footerColor}}"}};
                    }


                  </style>
                  <nav class="navbar navbar-default navbar-default-customized topLine" role="navigation">
                    <div class="container-fluid container-fluid-customized">
                      <div class="navbar-header" style="margin-top: 1%">
                        <a class="headerTextColor" style="display: inline;"  href="">
                          <img class="imgOnLoad imagenView hidden" style="width: 32px; height: 32px;">
                          {#                          <img id="imgOnLoad" class="imagenView hidden" style="width: 90px; height: 90px; margin: 10px;">#}


                          <spam id="mainTitle" style='width:10%;padding-left: 5px;display:inline;'>{{"{{customizing.mainTitle}}"}}</spam>
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
                    <a class="link-cutomized">Esto es un enlace</a>
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
                  <a ng-click="closeModal('preview');" class="button shining btn btn-xs-round success-inverted" data-dialog-close>Ok</a>
                </div>
              </div>

            </div>
          </div>
        </div>



      </div> 
    </div>
  </form>
</div>

<script>

  $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>
