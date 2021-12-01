<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ title }}"}}      
    </div>    
    <hr class="basic-line" />
  </div>
</div>

<div class="clearfix"></div>
<div ng-class="{'hidden' : misc.progressbar}" style="margin-left: 20px">
  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
</div>
<div class="row" ng-show="validateData">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <em class="text-2em"><strong>{{'{{sms.sms.name}}'}}</strong></em><br>
    <em>enviado el <strong>{{'{{sms.sms.startdate}}'}}</strong></em>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <table class="border-table-block-not-padding" style="width: 100%">
      <tr>
        <td class="text-right">
          <em ><strong>Categoria</strong></em>
        </td>
        <td class="text-left">
          {{'{{sms.sms.namecategory}}'}}
        </td>
        <td class="text-right">
          <em ><strong>Destinatarios</strong></em>
        </td>
        <td class="text-left" ng-if="sms.sms.birthdatetype == false">
          {{"{{ sms.sms.target  }}"}} 
        </td>
        <td class="text-left" ng-if="sms.sms.birthdatetype == true">
          {{"{{ sms.sent + sms.undelivered  }}"}} 
        </td>
      </tr>
    </table>
  </div>

</div>
<div class="clearfix"></div>
<div class="space"></div>
<div class="row" ng-show="validateData">
  <div class="row" ng-show='sms.sent > 0 || sms.undelivered > 0'>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div id="highchart"></div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="space"></div>
  {#<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
      <a class="btn btn-md success-inverted pull-right" style="margin-top: 20px;" onclick="openModal();">Compartir estadística</a>
    </div>
  </div>#}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <label class="small-text">Detalle del envio</label>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
      <a href="{{ url("sms#/")}}" class="btn btn-md default-inverted ">
        <i class="fa fa-arrow-left"></i>
        Regresar
      </a>
{#      <button class="btn btn-md info-inverted " ng-click="reportSms()" >
        <i class="fa fa-download"></i>
        Descargar Detalle
      </button>#}
      <a href="{{url('downloadsms/download/')}}{{'{{sms.detail[0].idSms}}'}}" class="btn btn-md info-inverted ">
        <i class="fa fa-download"></i>
        Descargar Detalle
      </a>
    </div>
  </div>

  <div class="clearfix"></div>
  <div class="space">  
  </div>
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
            <div class="input-group">
          <input class="form-control"  id="phone" placeholder="Buscar por celular"  ng-model="phone" oncopy="return false" onkeypress='return validaNumericos(event)' ng-change="searchNumber(1)"  maxlength="10" aria-invalid="false" />

          <div class="input-group-btn">

            <button type="button" class="btn btn-default" ng-click="searchNumber(1)">
              <i class="fa fa-search"></i>
            </button>
            <button type="button" class="btn btn-default" ng-click="searchNumber(2)">
              <i class="fa fa-eraser"></i>
            </button>
          </div>
        </div>
    </div>
  </div>

  <div ng-show="validatePhone" ng-hide="validatePhone == false" class="row">
    <div ng-if="misc.progressbar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div ng-disabled class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            El número ingresado no se encuentra en la campaña <br /> 
          </h2>
        </div>
      </div>
    </div>
  </div>  

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-hide="validatePhone">
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{listsms.detail[1].total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (listsms.detail[1].total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
        <a ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
        <a ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div> 
  </div> 
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-hide="validatePhone">
    <table class="table table-bordered">
      <thead class="theader ">
        <tr>
          <th>
            Codigo del pais
          </th>
          <th>
            Movil
          </th>
          <th>
            Mensaje
          </th>
          <th>
            Estado
          </th>
          <th>
            Cantidad para cobro
          </th>
        </tr>
      </thead>
      <tbody>
        <tr ng-repeat="detail in listsms.detail[0]" >
          <td>
            <div class="small-text">
              <em>
                +{{ '{{detail.indicative}}' }}
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
                {{ '{{detail.message}}' }}
              </em>
            </div>
          </td>
          <td>
            <div class="small-text">
              <em>
                {{ '{{traslateStatus(detail.status)}}' }}
              </em>
            </div>
          </td>
          <td>
            <div class="small-text text-center">
              <em>
                {{ '{{detail.messageCount }}' }}
              </em>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="pagination" class="text-center" ng-hide="validatePhone">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{listsms.detail[1].total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (listsms.detail[1].total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
        <a ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
        <a ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
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
            Copie este enlace y compartalo con quien quiera, y así las personas que lo abran
            en el navegador podrán ver las estadisticas del envío de SMS.
          </p>

          <h4 class="htitle">Compartir estadisticas del envío de SMS</h4>
          <p id="summary"><input type="text" class="col-sm-12" readonly="readonly" id="inputsummary" value="{{'{{sms.sms.shortStaticsUrl}}'}}" onclick='highlight(this);'></p>
        </div>
        <div class="modal-footer">
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
        </div>
      </div>
    </div>
  </div>
</div>

  <div ng-show="validateData == false" class="row">
    <div ng-if="misc.progressbar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div ng-disabled class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            La campaña no tiene estadísticas <br /> 
            
          </h2>
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
    function validaNumericos(event) {
    if(event.charCode >= 48 && event.charCode <= 57){
      return true;
     }
     return false;        
    }
    
  </script>
