<h1 class="bold">Iniciar sesión</h1>
<hr class="basic-line" /> 
<form method="post" data-ng-submit="login()">
  <div class="col-md-8 col-md-offset-2">
    <div class="form-group">
      <h3><b>{{"{{data.email}}"}}</b></h3>
    </div>
    <div class="form-group" data-ng-show="roles">
      {#<md-select data-ng-model="data.rol">
        <md-option><em>None</em></md-option>
        <md-option data-ng-repeat="x in roles" value="{{"{{x.idRole}}"}}">{{"{{x.name}}"}}</md-option>
      </md-select>#}
      <ui-select data-ng-model="data.rol" theme="selectize" style="text-align: left; width: 100%" title="Seleccione un rol">
        <ui-select-match placeholder="Debe seleccionar un rol">{{"{{$select.selected.name}}"}}</ui-select-match>
        <ui-select-choices repeat="item.idRole as item in roles | filter: $select.search">
          <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
        </ui-select-choices>
      </ui-select>
      {#<select class="form-control chosen" data-placeholder="Seleccione un rol" data-ng-model="data.rol">
        <option value=""></option>
        <option data-ng-repeat="x in roles" value="{{"{{x.idRole}}"}}">{{"{{x.name}}"}}</option>
      </select>#}
    </div>
    <div class="form-group">
      {{ loginForm.render('password',{'class':'undeline-input form-control input-lg', 'autofocus':'true'})}}
    </div>
  </div>

  <div class="col-md-8 col-md-offset-2"><div class="login-button">
      <div class="login-button">
        <button type="submit" class="button shining shining-round btn btn-sm-round success-inverted round-button">
          <span class="glyphicon glyphicon-menu-right"></span>
          <md-tooltip md-direction="top">
            Inciar Sesión
          </md-tooltip>
        </button>
      </div>

      <div class="login-button">
        <a  href="{{url('session#/recoverpass')}}" class="button shining shining-round btn btn-sm-round warning-inverted round-button">
          <span class="glyphicon glyphicon glyphicon-lock"></span>
          <md-tooltip md-direction="top">
            Olvidé mi contraseña
          </md-tooltip>
        </a>
      </div>

      <div class="login-button">
        <button type="button" class="button shining shining-round btn btn-sm-round danger-inverted round-button"data-ng-click="cancel()">
          <span class="glyphicon glyphicon-menu-left"></span>
          <md-tooltip md-direction="top">
            Cancelar
          </md-tooltip>
        </button>
      </div>

      <div class="login-button" data-ng-show="roles">
        <button type="button" class="button shining shining-round btn btn-sm-round info-inverted round-button"  data-toggle="popover" data-trigger="hover" title="Ayuda" data-placement="top" data-content="Nuestra plataforma, soporta multiples roles, selecciona el que necesites para iniciar sesión. Contacta a soporte para más información." data-ng-click="cancel()">
          <i class="fa fa-question-circle"></i>
        </button>
      </div></div>
</form>
<div class="row">
  <div class="col-md-12">
    <br />
{#    <a href="{{url('register#/')}}" class="text-1em">¿No tienes una cuenta?</a> <br />  #}
    <a href="{{url('session#/recoverpass')}}">Olvidé la contraseña</a>
  </div>
</div>
<script>
  $(function () {
    $(".chosen").select2();
    $('[data-toggle="popover"]').popover();
  });
</script>