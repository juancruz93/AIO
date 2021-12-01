<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Información contabilidad cuentas
    </div>            
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
    <div class="inline-block none-margin none-padding" >
      <a href="{{ url("/reports/index")}}" class="">
        <button class="button  btn btn-md default-inverted">
          Regresar al inicio
        </button>
      </a>
      <button class="button  btn btn-md info-inverted" ng-click="dowloadReport(statisallied.items)">
        Descargar reporte
      </button>
    </div>
  </div>
</div>

<div class="row">
  <div class="wrap">
    <md-progress-linear md-mode="query" data-ng-show="loader" class="md-warn"></md-progress-linear>
  </div>
  <div ng-show="statisallied.items.length > 0" class="">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{statisallied.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (statisallied.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <div ng-show="statisallied.items.length > 0" data-ng-hide="loader">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered">
          <thead class="theader ">
            <tr> 
              <th></th>           
              <th>Mes anterior({{ "{{ (statisallied.dateprev ) }}"}})</th>
              <th></th>
              <th></th>
              <th>Mes actual({{ "{{ (statisallied.datepres ) }}"}})</th>
              <th></th>
              <th></th>
            </tr>
            <tr>
              <th>Cuenta</th>
              <th>Contactos</th>
              <th>Envio mail</th>
              <th>Envio sms</th>
              <th>Contactos</th>
              <th>Envio mail</th>
              <th>Envio sms</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="statisallieds in statisallied.items" >
              <td>
                <div class="medium-text">
                  {{ '{{statisallieds.name}}' }}
                </div>
              </td>
              <td>
                <div class="small-text">
                  <em>
                    {{ '{{statisallieds.contacts}}' }}
                  </em>
                </div>
              </td>
              <td>
                <div class="small-text">
                  <em>
                    {{ '{{statisallieds.enviosmail}}' }}
                  </em>
                </div>
              </td>
              <td>
                <div class="small-text">
                  <em>
                    {{ '{{statisallieds.enviossms}}' }}
                  </em>
                </div>
              </td>
              <td>
                <div class="small-text">
                  <em>
                    {{ '{{statisallieds.contacts}}' }}
                  </em>
                </div>
              </td>
              <td>
                <div class="small-text">
                  <em>
                    {{ '{{statisallieds.enviosmaila}}' }}
                  </em>
                </div>
              </td>
              <td>
                <div class="small-text">
                  <em>
                    {{ '{{statisallieds.enviossmsb}}' }}
                  </em>
                </div>
              </td>
            </tr>          
          </tbody>
        </table>
      </div>
    </div>
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{statisallied.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (statisallied.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="page == (statisallied.total_pages)  || statisallied.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>
</div>


<br>
<div ng-show="statisallied.items.length == 0">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          No hay registros para mostrar!.
        </h2>    
        </h2>    
      </div>
    </div>
  </div>
</div>

<script>
  $('#myModal').on('shown.bs.modal', function () {
    $('#myInput').focus()
  })

</script>