
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Paises
    </div>            
    <hr class="basic-line" />
    <p>
      A continuación la lista de paises del sistema
    </p>
  </div>
</div>

<div  ng-if="countries.items.length>0">

  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{countries.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (countries.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (countries.total_pages) || countries.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (countries.total_pages)  || countries.total_pages == 0  ? true  : false || page == (countries.total_pages)  || countries.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (countries.total_pages)  || countries.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (countries.total_pages)  || countries.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
  <div class="row" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <table class="table table-bordered sticky-enabled" >
        <thead class="theader">
          <tr>
            <th>Nombre</th>
            <th>Detalles</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="item in countries.items">
            <td>
              {{'{{item.name}}'}}
            </td>
            <td>
              <dl>
                <dd ng-if='item.minDigits > 0'> <em class="extra-small-text">Dígitos mínimos: <strong>{{'{{item.minDigits}}'}}</strong> </em></dd>
                <dd ng-if='item.maxDigits > 0'> <em class="extra-small-text">Dígitos máximos: <strong> {{'{{item.maxDigits}}'}}</strong></em></dd>
              </dl>
            </td>
            <td>
              <a href="#/edit/{{ '{{item.idCountry}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="left" title="Editar">
                <md-tooltip>
                  Editar
                </md-tooltip>
                <span class="glyphicon glyphicon-pencil"></span>
              </a>
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
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{countries.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (countries.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (countries.total_pages) || countries.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (countries.total_pages)  || countries.total_pages == 0  ? true  : false || page == (countries.total_pages)  || countries.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (countries.total_pages)  || countries.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (countries.total_pages)  || countries.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
<div ng-if="countries.items.length<=0">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay paises
          </h2>
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

</div>
</div>

