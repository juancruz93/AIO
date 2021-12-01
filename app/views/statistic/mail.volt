<style>
  .progress-bar-success{
    background-color: #00c1a5 !important;
  }
  .progress-bar-warning{
    background-color: #ff6e00 !important;
  }
  .progress-bar-info{
    background-color: #b700c1 !important;
  }
  .progress-bar-danger{
    background-color: #ff2400 !important;
  }
  .progress-bar-primary{
    background-color: #00bede !important;
  }
  .progress-bar-default{
    background-color: #777 !important;
  }
  .ch-item-resize{
    width: 45px !important;
    height: 45px !important;
    padding-top:  9px !important;
  }

  .htitle{
    font-weight: bold;
    text-align: left;
  }
</style>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ title }}"}}      
    </div>            
    <hr class="basic-line" />
  </div>
</div>

<div class="clearfix"></div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <em class="text-2em"><strong>{{'{{stactics.mail.name}}'}}</strong></em><br>
    <em>enviado el <strong>{{'{{stactics.mail.confirmationDate}}'}}</strong></em>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <table class="border-table-block-not-padding" style="width: 100%">
      <tr>
        <td class="text-right">
          <em ><strong>Asunto</strong></em>
        </td>
        <td class="text-left">
          {{'{{stactics.mail.subject}}'}}
        </td>
        <td class="text-right">
          <em ><strong>Remitente</strong></em>
        </td>
        <td class="text-left">
          {{"{{ stactics.mail.namesender  }}"}} 
          <{{"{{ stactics.mail.emailsender  }}"}}>
        </td>
      </tr>
      <tr>
        <td class="text-right">
          <em ><strong>Destinatarios</strong></em>
        </td>
        <td class="text-left">
          {{"{{ stactics.mail.target  }}"}}
        </td>
        <td class="text-right">
          <em ><strong>Responder a</strong></em>
        </td>
        <td class="text-left" ng-if="stactics.mail.replyto != 'No asignado'">
          {{"{{ stactics.mail.replyto  }}"}}
        </td>
        <td class="text-left" ng-if="stactics.mail.replyto == 'No asignado'">
          <i>{{"{{ stactics.mail.replyto  }}"}}</i>
        </td>
      </tr>
      <tr>
        <td class="text-right">
          <em ><strong>Correos enviados</strong></em>
        </td>
        <td class="text-left">
          <em class="small-text"><strong>{{"{{ stactics.messageSent }}"}}</strong></em>
        </td>
        <td class="text-center" colspan="2">
          <a href="#/" data-ng-click="previewmailtempcont(stactics.mail.idMail);" data-toggle="modal" data-target="#myModal"><strong>Ver contenido del correo</strong></a><br>
      <spam ng-show="stactics.mail.test==1"><b>Correo marcado como prueba</b></spam>
      </td>
      </tr>
    </table>
  </div>
  <div class="clearfix"></div>
  {#  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="pull-right">
        <button class="btn btn-md primary-inverted">Enviar estadistica por correo</button>
        <button class="btn btn-md default-inverted">Descargar estadisticas como un archivo PDF</button>
      </div>
    </div>#}

  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
    {#    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 text-center ">
          <div class="inline-block none-padding">
            <em class="text-3em"><strong>{{"{{ stactics.messageSent }}"}}</strong></em>
            <br>  
            <span >Correos enviados</span>
          </div>  
        </div>#}
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="success-no-hover small-text" ng-click="goOpen(); activeJustified = 0 ">Aperturas únicas</span>
        <span class="medium-text success-no-hover">
          <uib-progressbar style="height: 10px" class="success-no-hover" value="stactics.open" 
                           max="stactics.messageSent" type="success"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.open }}"}}
        <span class="medium-text success-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.open)}}"}}%
        </span>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="warning-no-hover small-text" ng-click="goBounced(); activeJustified = 3" >Rebotes</span>
        <span class="medium-text success-no-hover">
          <uib-progressbar style="height: 10px" class="success-no-hover" value="stactics.bounced" 
                           max="stactics.messageSent" type="warning"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.bounced }}"}}
        <span class="medium-text warning-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.bounced)}}"}}%
        </span>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="primary-no-hover small-text" ng-click="goClic(); activeJustified = 1">Clicks únicos</span>
        <span class="medium-text primary-no-hover">
          <uib-progressbar style="height: 10px" class="" value="stactics.uniqueClicks" 
                           max="stactics.messageSent" type="primary"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.uniqueClicks }}"}} 
        <span class="medium-text success-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.uniqueClicks)}}"}}%
        </span>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="danger-no-hover small-text" ng-click="goSpam(); activeJustified = 4">Marcados como Spam</span>
        <span class="medium-text danger-no-hover">
          <uib-progressbar style="height: 10px" class="danger-no-hover" value="stactics.spam" 
                           max="stactics.messageSent" type="danger"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.spam }}"}} 
        <span class="medium-text danger-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.spam)}}"}}%
        </span>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="default-no-hover small-text" ng-click="goUnsuscribe(); activeJustified = 2">Desuscritos</span>
        <span class="medium-text default-no-hover">
          <uib-progressbar style="height: 10px" class="default-no-hover" value="stactics.unsubscribed" 
                           max="stactics.messageSent" type="default"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.unsubscribed }}"}} 
        <span class="medium-text default-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.unsubscribed)}}"}}%
        </span>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="info-no-hover small-text" ng-click="goBuzon(); activeJustified = 5">Buzón</span>
        <span class="medium-text info-no-hover">
          <uib-progressbar style="height: 10px" class="info-no-hover" value="stactics.buzon" 
                           max="stactics.messageSent" type="info"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.buzon }}"}} 
        <span class="medium-text info-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.buzon)}}"}}%
        </span>
      </div>
    </div>
{#    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
      <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
        <span class="default-no-hover small-text" ng-click="goBuzon(); activeJustified = 5">Buzón</span>
        <span class="medium-text default-no-hover">
          <uib-progressbar style="height: 10px" class="default-no-hover" value="stactics.buzon" 
                           max="stactics.messageSent" type="default"></uib-progressbar>
        </span>
      </div>
      <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
        {{"{{ stactics.buzon }}"}} 
        <span class="medium-text default-no-hover" style="font-size: 1.8em">
          {{"{{calculatePercentage(stactics.messageSent, stactics.buzon)}}"}}% 
        </span>
      </div>
    </div>#}
  </div>
</div>
<div class="clearfix"></div>
<div class="space"></div>
{#<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
  <hr class="basic-line" />
  <ul class="ch-grids ">
    <li>
      <div class="" style="display: inline-block">
        <div class="ch-items ch-item-resize social-network-Facebook text-center" ><i class="fa fa-facebook fa-2x" style="font-size: 1.5em"></i></div>        
      </div>
      <div class="text-left" style="display: inline-block; margin-left: 5%">
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Apertura</strong>          
          </div>
        </div>
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Veces compartido</strong> 
          </div>
        </div>
      </div>
    </li>
    <li>
      <div class="" style="display: inline-block">
        <div class="ch-items ch-item-resize social-network-Twitter text-center" ><i class="fa fa-twitter fa-2x" style="font-size: 1.5em"></i></div>        
      </div>
      <div class="text-left" style="display: inline-block; margin-left: 5%">
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Apertura</strong>          
          </div>
        </div>
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Veces compartido</strong> 
          </div>
        </div>
      </div>
    </li>
    <li>
      <div class="" style="display: inline-block">
        <div class="ch-items ch-item-resize social-network-Google text-center" ><i class="fa fa-google-plus fa-2x" style="font-size: 1.5em"></i></div>        
      </div>
      <div class="text-left" style="display: inline-block; margin-left: 5%">
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Apertura</strong>          
          </div>
        </div>
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Veces compartido</strong> 
          </div>
        </div>
      </div>
    </li>
    <li>
      <div class="" style="display: inline-block">
        <div class="ch-items ch-item-resize social-network-Linkedin text-center" ><i class="fa fa-linkedin fa-2x" style="font-size: 1.5em"></i></div>        
      </div>
      <div class="text-left" style="display: inline-block; margin-left: 5%">
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Apertura</strong>          
          </div>
        </div>
        <div class="" style="vertical-align: top">
          <div class="" style="display: inline-block">
            <label class="small-text">0</label>
          </div>
          <div class="" style="display: inline-block">
            <strong class="extra-small-text">Veces compartido</strong> 
          </div>
        </div>
      </div>
    </li>
  </ul>    
</div>#}
<div class="clearfix"></div>
<div class="space"></div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <hr class="basic-line" />
  <label class="medium-text"> Detalle de estadística</label>
  <uib-tabset active="activeJustified" >
    <uib-tab index="0" heading="Aperturas" ng-click="opening()" id="open">
      <div class="clearfix"></div>
      <div class="space"></div>
      <div ng-show="countTotal >= 1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Aperturas únicas</span>
            <br>
            <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%</label>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div id="highchartOpen"></div>
        {#        <div class="highchart"></div>#}
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">Detalle de aperturas</label>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()" >Descargar Detalle</button>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <table class="table table-bordered">
            <thead class="theader ">
              <tr>
                <th>Fecha</th>
                <th>Direccion de correo electronico</th>
                <th>Total de aperturas</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Indicativo</th>
                <th>Número de móvil</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="detail in graph.info[0]" >
                <td>
                  <div class="small-text">
                    {{ "{{detail.dateOpen * 1000 | date:'yyyy-MM-dd HH:mm:ss Z'}}" }}
                  </div>    
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.email}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      1 {#{{ '{{detail.totalOpening}}' }}#}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.name}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.lastName}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.indicative}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.phone}}' }}
                    </em>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{graph.info[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (graph.info[1].total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>  
        </div>
      </div>
      <div ng-show="countTotal == 0">
        <img class='logo' src='/images/general/open.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
      </div>
    </uib-tab>
    <uib-tab index="1" heading="Clics" ng-click="clic()" id="clic">
      <div class="clearfix"></div>
      <div class="space"></div>
      <div ng-show="countTotal >= 1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 medium-text">
            <label>{{"{{countTotal}}"}} </label> <span> Clics únicos.</span>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="">{{"{{countTotal}}"}} </label> <span class="small-text"> Contactos de </span><label class="small-text"> {{"{{stactics.messageSent}}"}} </label>
            <span class="small-text"> que recibieron correo hicieron click en un enlace.</span>
            <br>
            <label class="small-text">({{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%)</label><span class="small-tex"> Tasa de clics</span>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Contactos de </span> <label class="small-text">{{"{{stactics.open}}"}} </label>
            <span class="small-text"> que abrieron el correo, hicieron clic en un enlace.</span>
            <br>
            <label class="small-text">({{"{{calculatePercentage(stactics.open, countTotal)}}"}}%) </label> <span class="small-text"> Click To Open Rate</span>
          </div>

        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div id="highchartClic"></div>
        {#        <div class="highchart"></div>#}
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">Cantidad de clicks por cada enlace</label>
          </div>
          {#<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()">Descargar Detalle</button>
          </div>#}
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <table class="table table-bordered">
            <thead class="theader ">
              <tr>
                <th>
                  Vinculos
                </th>
                <th>
                  Total clics
                </th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="detail in graph.info[0]" >
                <td>
                  {{ '{{detail.link}}' }}
                </td>
                <td>
                  {{ '{{detail.totalClicks}}' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="pagination" class="text-center">
          <ul class="pagination">
            <li ng-class="page == 1 ? 'disabled'  : ''">
              <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
            </li>
            <li  ng-class="page == 1 ? 'disabled'  : ''">
              <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
            </li>
            <li>
              <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{graph.info[1].total }}"}}
                </b> registros </span><span>Página <b>{{"{{ page }}"}}
                </b> de <b>
                  {{ "{{ (graph.info[1].total_pages ) }}"}}
                </b></span>
            </li>
            <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
              <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
            </li>
            <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
              <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
            </li>
          </ul>
        </div>  
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 medium-text">
            Detalle de clicks únicos por contacto
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <ui-select ng-model="filter.link" theme="selectize"  title="----" ng-change="infoDataClic()">
              <ui-select-match placeholder="">{{"{{$select.selected.link}}"}}</ui-select-match>
              <ui-select-choices repeat="item in graph.info[2] | filter: $select.search">
                <small ng-bind-html="item.link | highlight: $select.search"></small>
              </ui-select-choices>
            </ui-select>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()">Descargar Detalle</button>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <table class="table table-bordered">
              <thead class="theader ">
                <tr>
                  <th>
                    Dirección de correo
                  </th>
                  <th>
                    Teléfono
                  </th>
                  <th>
                    Nombre
                  </th>
                  <th>
                    Apellido
                  </th>
                  <th>
                    Enlace
                  </th>
                  <th>
                    Fecha y hora
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="detail in infolink.info[0]" ng-show="infolink.info[0][0]">
                  <td>
                    {{ '{{detail.email}}' }}
                  </td>
                  <td>
                    (+{{ '{{detail.inidicative}}' }}) {{ '{{detail.phone}}' }}
                  </td>
                  <td>
                    {{ '{{detail.name}}' }}
                  </td>
                  <td>
                    {{ '{{detail.lastname}}' }}
                  </td>
                  <td>
                    {{ '{{detail.link}}' }}
                  </td>
                  <td>
                    {{ "{{detail.date * 1000 | date:'yyyy-MM-dd HH:mm:ss Z'}}" }}
                  </td>
                </tr>
                <tr ng-show="!infolink.info[0][0]">
                  <td colspan="3">
                    No se encuentra coincidencias
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="pageClic == 1 ? 'disabled'  : ''">
                <a  ng-click="pageClic == 1 ? true  : false || fastbackwardClic()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="pageClic == 1 ? 'disabled'  : ''">
                <a  ng-click="pageClic == 1 ? true  : false || backwardClic()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{infolink.info[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ pageClic }}"}}
                  </b> de <b>
                    {{ "{{ (infolink.info[1].total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="pageClic == (infolink.info[1].total_pages)  || infolink.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="pageClic == (infolink.info[1].total_pages)  || infolink.info[1].total_pages == 0  ? true  : false || forwardClic()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="pageClic == (infolink.info[1].total_pages)  || infolink.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="pageClic == (infolink.info[1].total_pages)  || infolink.info[1].total_pages == 0  ? true  : false || fastforwardClic()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>  
        </div>
      </div>
      <div ng-show="countTotal == 0">
        <img class='logo' src='/images/general/clics.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
      </div>
    </uib-tab>
    <uib-tab index="2" heading="Desuscritos" ng-click="unsuscribe()" id="unsuscribe">
      <div class="clearfix"></div>
      <div class="space"></div>
      <div ng-show="countTotal >= 1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Total de desuscritos</span>
            <br>
            <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%</label>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div id="highchartUnsuscribe"></div>
        {#<div class="highchart"></div>#}
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">Detalle de desuscritos</label>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()" >Descargar Detalle</button>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <table class="table table-bordered">
            <thead class="theader ">
              <tr>
                <th>Fecha</th>
                <th>Direccion de correo electronico</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Indicativo</th>
                <th>Número de móvil</th>
                <th>Motivo de desuscripción</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="detail in graph.info[0]" >
                <td>
                  <div class="small-text">
                    {{ "{{detail.dateOpen * 1000 | date:'yyyy-MM-dd HH:mm:ss Z'}}" }}
                  </div>    
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.email}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.name}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.lastname}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.indicative}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.phone}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.motive}}' }}
                    </em>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{graph.info[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (graph.info[1].total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>  
        </div>
      </div>
      <div ng-show="countTotal == 0">
        <img class='logo' src='/images/general/unsuscribed.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
      </div>
    </uib-tab>
    <uib-tab index="3" heading="Rebotados" ng-click="bounced()" id="bounced">

      <div class="clearfix"></div>
      <div class="space"></div>
      {#      <div >#}
      <div ng-show="countTotal >= 1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">{{"{{graphPie[0].hard}}"}} </label> 
            <br>
            <label class="small-text">{{"{{calculatePercentage(countTotal, graphPie[0].hard)}}"}}% </label><span class="small-text"> Duro</span>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">{{"{{graphPie[0].soft}}"}} </label> 
            <br>
            <label class="small-text">{{"{{calculatePercentage(countTotal, graphPie[0].soft)}}"}}% </label><span class="small-text"> Suave</span>
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="space"></div>
        <div id="highchartBounced"></div>
        {#        <div class="highchart"></div>#}
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">Detalle de rebotados</label>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()" >Descargar Detalle</button>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="row">
          <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 text-left">
            <span class="small-text">Filtrar por:</span>       
            <md-radio-group ng-model="filter.setvalue" ng-change="filterSelected()">
              <md-radio-button value="type" class="md-primary" ng-style="{'display':'inline'}"> Tipo</md-radio-button>
              <md-radio-button value="category" class="md-primary" ng-style="{'display':'inline'}"> Categoria</md-radio-button>
              <!--<md-radio-button value="domain" class="md-primary" ng-style="{'display':'inline'}"> Dominio </md-radio-button>-->
            </md-radio-group>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <ui-select ng-model="filter.selected" theme="selectize"  title="----" ng-change="getData()">
              <ui-select-match placeholder="">{{"{{$select.selected.name}}"}}</ui-select-match>
              <ui-select-choices repeat="item in filter.filters | filter: $select.search">
                <small ng-bind-html="item.name | highlight: $select.search"></small>
              </ui-select-choices>
            </ui-select>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <table class="table table-bordered">
              <thead class="theader ">
                <tr>
                  <th>Fecha</th>
                  <th>Direccion de correo electronico</th>
                  <th>Tipo</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="detail in graph.info[0]" ng-show="graph.info[0][0]">
                  <td>
                    <div class="small-text">
                      {{ "{{detail.date * 1000 | date:'yyyy-MM-dd HH:mm:ss Z'}}" }}
                    </div>    
                  </td>
                  <td>
                    <div class="small-text">
                      <em>
                        {{ '{{detail.email}}' }}
                      </em>
                    </div>
                  </td>
                  <td>
                    <div class="small-text">
                      <em>
                        {{ '{{detail.type}}' }}
                      </em>
                    </div>
                  </td>
                  <td>
                    <div class="small-text">
                      <em>
                        {{ '{{detail.description}}' }}
                      </em>
                    </div>
                  </td>
                </tr>
                <tr ng-show="!graph.info[0][0]">
                  <td colspan="4">
                    No se encuentra coincidencias
                  </td>
                </tr>
              </tbody>
            </table>

            <div id="pagination" class="text-center">
              <ul class="pagination">
                <li ng-class="page == 1 ? 'disabled'  : ''">
                  <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                </li>
                <li  ng-class="page == 1 ? 'disabled'  : ''">
                  <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                </li>
                <li>
                  <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{graph.info[1].total }}"}}
                    </b> registros </span><span>Página <b>{{"{{ page }}"}}
                    </b> de <b>
                      {{ "{{ (graph.info[1].total_pages ) }}"}}
                    </b></span>
                </li>
                <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                  <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                  <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
              </ul>
            </div>    
          </div>
        </div>
      </div>
      <div ng-show="countTotal == 0">
        <img class='logo' src='/images/general/rebound.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
      </div>
    </uib-tab>
    <uib-tab index="4" heading="Spam Marcados" ng-click="spam()" id="spam">
      <div class="clearfix"></div>
      <div class="space"></div>
      <div ng-show="countTotal >= 1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Total de spam</span>
            <br>
            <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%</label>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div id="highchartSpam"></div>
        {#<div class="highchart"></div>#}
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">Detalle de spam</label>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()" >Descargar Detalle</button>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <table class="table table-bordered">
            <thead class="theader ">
              <tr>
                <th>Fecha</th>
                <th>Direccion de correo electronico</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Indicativo</th>
                <th>Número de móvil</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="detail in graph.info[0]" >
                <td>
                  <div class="small-text">
                    {{ "{{detail.dateOpen * 1000 | date:'yyyy-MM-dd HH:mm:ss Z'}}" }}
                  </div>    
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.email}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.name}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.lastname}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.indicative}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.phone}}' }}
                    </em>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{graph.info[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (graph.info[1].total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>  
        </div>
      </div>
      <div ng-show="countTotal == 0">
        <img class='logo' src='/images/general/spam.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
      </div>
    </uib-tab>
    <uib-tab index="5" heading="Buzón" ng-click="buzon()" id="buzon">
      <div class="clearfix"></div>
      <div class="space"></div>
      <div ng-show="stactics.buzon >= 1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">{{"{{stactics.buzon}}"}} </label> <span class="small-text"> Total de Email en Buzón.</span>
            <br>
            <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, stactics.buzon)}}"}}%</label>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="small-text">Detalle de Email en buzón</label>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
            <button class="btn btn-md default-inverted pull-right" ng-click="reportClic()" >Descargar Detalle</button>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <table class="table table-bordered">
            <thead class="theader ">
              <tr>
                <th>Fecha</th>
                <th>Dirección de correo electrónico</th>
                <th>Total en Buzón</th>
                <th>Nombre</th>
                <th>Apellido</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="detail in graph.info[0]" >
                <td>
                  <div class="small-text">
                    {{ "{{detail.dateOpen}}" }}
                  </div>    
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.email}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.buzon}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.name}}' }}
                    </em>
                  </div>
                </td>
                <td>
                  <div class="small-text">
                    <em>
                      {{ '{{detail.lastname}}' }}
                    </em>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{graph.info[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (graph.info[1].total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (graph.info[1].total_pages)  || graph.info[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>  
        </div>
      </div>
      <div ng-show="!stactics.buzon">
        <img class='logo' src='/images/general/correos_buzon.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
      </div>
    </uib-tab>
  </uib-tabset>
  <br>
</div>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    {#<label class="small-text">Comparar estadística con otro envio de correo</label>
    <div class="form-inline">
      <select class="form-control"><option selected>Otro envio del pais</option></select> 
      <button class="btn btn-md primary-inverted">Comparar</button>
    </div>#}

  </div>
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
    <a href="{{ url("mail")}}" class="btn btn-md default-inverted " style="margin-top: 20px; margin-right: 3px;">
      <i class="fa fa-arrow-left"></i>
      Regresar
    </a>
    <a class="btn btn-md success-inverted pull-right" style="margin-top: 20px;" onclick="openModal();">Compartir estadística</a>
  </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-prevew-width">
    <div class="modal-content modal-prevew-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h1 class="modal-title" id="myModalLabel">Contenido del correo</h1>
      </div>
      <div class="modal-body modal-prevew-body" id="content-preview" style="height: 550px;">

      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
      </div>
    </div>
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
    <div class="dialog-inner" style="padding: 2em;">
      <div class="modal-header">
        <a type="button" class="close"aria-hidden="true" onClick="closeModal();">×</a>
        <h4 class="modal-title htitle">Compartir estadísticas</h4>
      </div>
      <div class="modal-body">
        <p style="text-align: left;">
          Copie estos enlaces y compartalos con quien quiera, y así las personas que lo abran
          en el navegador podrán ver las estadisticas del correo.
        </p>

        <h4 class="htitle">Compartir estadisticas completas del correo</h4>
        <p id="complete"><input type="text" class="col-sm-12" readonly="readonly" id="inputcomplete" value="{{"{{ stactics.fullStaticsUrl }}"}}" onclick='highlight(this);'></p>
        <h4 class="htitle">Compartir estadisticas parciales del correo</h4>
        <p id="summary"><input type="text" class="col-sm-12" readonly="readonly" id="inputsummary" value="{{"{{ stactics.shortStaticsUrl }}"}}" onclick='highlight(this);'></p>

      </div>
      <div class="modal-footer">
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
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
    $('.dialog').removeClass('dialog--open');
  }
</script>
<script type='text/javascript'>
  function highlight(field)
  {
    field.focus();
    field.select();
  }

</script>
{#<a id="bottom"></a> You're at the bottom!#}


{#</div>#}
