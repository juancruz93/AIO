<script type="text/javascript">
  $(function () {
    $(".select2").select2({
      theme: 'classic'
    });
  });
</script>
<div class="row" data-ng-init="initComponents()">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <form method="post" class="form-horizontal" ng-submit="addContact()">
      <div class="block block-info">
        <div class="body">
          <div class="form-group">
          </div>
          <div class="form-group">
            <label  class="col-sm-4 text-right">Nombre</label>
            <div class="col-sm-8 col-md-8">
              <input type="text" id="name" name="name" ng-model="contact.name" class="undeline-input" maxlength="80"> 
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-4 text-right">Apellido</label>
            <div class="col-sm-8 col-md-8">
              <input type="text" id="lastname" name="lastname" ng-model="contact.lastname" class="undeline-input" maxlength="80"> 
            </div>
          </div>
          <div class="form-group" >
            <label class="col-sm-4 col-md-4 text-right">Fecha de nacimiento</label>
            <div class="col-sm-8">
              <div class="dropdown dropdown-start-parent">
                <a class="dropdown-toggle" id="dropdown2" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent" href="">
                  <div class="input-group">
                    <input type="text" class="undeline-input color-default" readonly="true" data-ng-model="contact.birthdate">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </div>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                  <datetimepicker data-ng-model="contact.birthdate"
                                  data-datetimepicker-config="{ dropdownSelector: '#dropdown2', startView: 'year', minView: 'day', modelType: 'YYYY-MM-DD' }"
                                  data-before-render="functions.dateBeforeRender($dates)"/>
                </ul>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-4 text-right">Correo electrónico</label>
            <div class="col-sm-8 col-md-8">
              <input type="email" id="email" name="email" ng-model="contact.email" class="undeline-input"maxlength="80"> 
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-4 text-right">Indicativo</label>
            <div class="col-sm-8 col-md-8">
              <select class="undeline-input select2" id="indicatives" name="indicatives" data-ng-model="contact.indicative" data-placeholder="Seleccione">
                <option></option>
                <option data-ng-repeat="item in listIndicatives track by $index" value="{{"{{item.idCountry}}"}}">(+{{"{{item.phoneCode}}"}}) {{"{{item.name}}"}}</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-4 text-right">Movil</label>
            <div class="col-sm-8 col-md-8">
              <input type="tel" id="phone" name="phone" ng-model="contact.phone" class="undeline-input"> 
            </div>
          </div>
          {% for item in customfield %}
            {{ partial("partials/type_input", ['input': item ]) }}
          {% endfor %}
          <div class="form-group">
            <label  class="col-sm-4 text-right">
              Guardar contacto, aunque ya exista en la lista.
            </label>

            <div  class="col-sm-1 text-right">
              <md-checkbox md-no-ink aria-label="Checkbox No Ink" 
                           ng-model="contact.valid" class="md-primary ">
              </md-checkbox>
            </div>
            <div class="col-md-7 text-left">
              <em class="extra-small-text cursor" data-toggle="collapse" data-target="#collapseExample" >
                Si habilita esta opción, se guardará el contacto aunque ya se enceuntre en la lista de contacto. 
              </em>
              <div class="collapse" id="collapseExample">
                <div class="well">
                  Es decir que si hay varios contactos con el mismo correo, se cargarán como si fueran contactos diferentes. 
                  Nota: Esta opción es avanzada, habilite esta opción, solo si sabe lo que está haciendo. Comuníquese con soporte para obtener más información respecto a esto.
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <div ng-class="{'hidden' : progressbar}" >
            <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
          </div>
          <button ng-disabled="!progressbar" class="button  btn btn-xs-round   round-button success-inverted"
                  data-toggle="tooltip"
                  data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="#/" 
             ng-disabled="!progressbar"   
             class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip"
             data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>

        </div>
      </div>
    </form>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <div class="fill-block fill-block-primary">
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>Recuerde que el contacto debe contener al menos el correo electrónico o el número del móvil con su respectivo indicativo</li>
          <li>El nombre debe contener máximo 80 caracteres.</li>
          <li>El apellido debe contener máximo 80 caracteres.</li>
          <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
        </ul>
        </p>
      </div>
      <div class="footer">
        Edición
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
    <div class="dialog-inner">
      <h2>¿Esta seguro que desea crear el contacto?</h2>
      <div>
        {{"{{errorCreateContact.message }}"}}
        {#        Ya exite un contacto asociado con el correo ingresado y se podria encontrar en otra lista de 
                contacto, si continua la información del contacto se actualizara con la ingresada.#}
      </div>
      <br>
      <div>
        <a onClick="closeModalConfirm();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a data-ng-disabled="contact.validateConfirm" data-ng-click="saveContactConfirm()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();
  });
</script>
<script>
  function openModalConfirm() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModalConfirm() {
    $('.dialog').removeClass('dialog--open');
  }
</script>
