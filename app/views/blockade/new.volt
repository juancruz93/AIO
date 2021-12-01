<script>
  $(function () {
    $(".select2").select2({
      theme: 'classic'
    });
  });
</script>   
<div class="row" data-ng-init="initComponents()">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <form method="post"
          class="form-horizontal" ng-submit="addBlockade()">
      <div class="block block-info">
        <div class="body">
          <div class="form-group">
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">Correo electrónico</label>
              <span class="input hoshi input-default  col-sm-8">                                     
                <input type="email" id="name" name="name" ng-model="block.email" class="undeline-input" maxlength="50"> 
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">Indicativo</label>
              <span class="input hoshi input-default  col-sm-8">                  
                <select class="undeline-input select2" id="indicative" name="indicative" data-ng-model="block.idCountry" data-placeholder="Seleccione">
                  <option value=""></option>
                  <option data-ng-repeat="item in listindicative track by $index" value="{{"{{item.idCountry}}"}}">(+{{"{{item.phoneCode}}"}}) {{"{{item.name}}"}}</option>
                </select>
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">Móvil</label>
              <span class="input hoshi input-default  col-sm-8">                                     
                <input type="number" id="phone" name="phone" ng-model="block.phone" class="undeline-input" maxlength="50"> 
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">*Motivo del bloqueo</label>
              <span class="input hoshi input-default  col-sm-8">                                     
                <textarea  required="" ng-model="block.motive" class="undeline-input" maxlength="160" minlength="2"> </textarea>
              </span>
            </div>
          </div>

        </div>
        <div class="footer" align="right">
          <button class="button  btn btn-xs-round   round-button success-inverted"
                  data-toggle="tooltip"
                  data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="#/"
             class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip"
             data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>

        </div>
      </div>
    </form>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-primary">
      <div class="header">
        Información
      </div>
      <div class="body text-justify">
        Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li><p>Debe ingresar el correo electrónico o el teléfono a bloquear, también puede ingresar ambos.</p></li>
          <li>
            <p>
              En esta sección podrá bloquear un contacto, debe tener en cuenta que si ingresa sólo el correo electrónico
              sólo se bloqueará este mismo, si el número de este contacto está registrado en alguna lista, de igual forma
              recibirá SMS si está dentro de una campaña de este tipo. Esta situación sucede de forma inversa.
            </p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>