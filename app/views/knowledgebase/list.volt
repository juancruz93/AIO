<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Base del conocimiento
    </div>            
    <hr class="basic-line" />
    <p>
      Desde este apartado se podrá alimentar una base de datos con los correos electrónicos bloqueados para que no se envíen correos a ninguno de ellos
    </p>
  </div>
</div>


<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    <a href="{{url('knowledgebase#/validate')}}" class="button shining btn btn-sm warning-inverted">Validar correos desde CSV</a>
    <a href="{{url('knowledgebase#/import')}}" class="button shining btn btn-sm info-inverted">Importar nuevos correos desde CSV</a>

  </div>
</div>

<div  ng-if="imports.items.length>0">
  <div >

    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{imports.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (imports.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (imports.total_pages) || imports.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (imports.total_pages)  || imports.total_pages == 0  ? true  : false || page == (imports.total_pages)  || imports.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (imports.total_pages)  || imports.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (imports.total_pages)  || imports.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <div class="row" >
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered">
          <thead class="theader ">
            <tr>
              <th >Información</th>
              <th >Detalles</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr ng-repeat="import in imports.items">
              <td>
                <div class="small-text">
                  <em>
                    Archivo: <b>{{"{{import.fileName}}"}}</b>
                  </em>
                </div>
                <div class="extra-small-text">
                  Creada el {{"{{import.created}}"}} por {{"{{import.createdBy}}"}}<br>
                  Actualizada {{"{{import.updated}}"}} por {{"{{import.updatedBy}}"}}<br>
                </div>
              </td>
              <td>
                <div class="row wrap text-center" id="contentList">

                  <div class="inline-block text-center success">
                    <div class="medium-text">{{"{{import.totalImported}}"}}</div>
                    Importados
                  </div>

                  <div class="inline-block text-center danger">
                    <div class="medium-text">{{"{{import.totalNotImported}}"}}</div>
                    No importados
                  </div>
                    
                  <div class="inline-block text-center info">
                    <div class="medium-text">{{"{{import.total}}"}}</div>
                    Totales
                  </div>

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
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{imports.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (imports.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (imports.total_pages) || imports.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (imports.total_pages)  || imports.total_pages == 0  ? true  : false || page == (imports.total_pages)  || imports.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (imports.total_pages)  || imports.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (imports.total_pages)  || imports.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>
</div>
<div ng-if="imports.items.length==0">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No existen importaciones actualmente, si desea realizar una haga <a href="{{url('knowledgebase#/import')}}">clic aquí</a>.
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


