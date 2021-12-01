<style>
  .modal {
    text-align: center;
    padding: 0!important;
  }

  .modal:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -4px;
  }

  .modal-dialog {
    display: inline-block;
    text-align: left;
    vertical-align: middle;
  }
</style>
<h1 class="bold">Iniciar sesión</h1>
<hr class="basic-line" /> 
<form method="post" data-ng-submit="login()" data-ng-init="initComponents()">
  <div class="col-md-8 col-md-offset-2">
    <div class="form-group">
      <input type="email" id="email" name="email" class="undeline-input form-control input-lg" maxlength="80" placeholder="Correo" required="required" autofocus="autofocus" data-ng-model="data.email" style="">
    </div>
  </div>

  <div class="col-md-8 col-md-offset-2">
    <div class="login-button">
      <button type="button" class="button shining shining-round btn btn-sm-round round-button" style="background-color: #3b5998;color: #f5f5f5 !important" data-ng-click="loginFacebook.loginFB()">
        <span class="fa fa-facebook-square" style="font-size: 20px"></span>
        <md-tooltip md-direction="top">
          Iniciar sesión con Facebook
        </md-tooltip>
      </button>
      <button type="submit" class="button shining shining-round btn btn-sm-round success-inverted round-button">
        <span class="glyphicon glyphicon-menu-right"></span>
        <md-tooltip md-direction="top">
          Siguiente
        </md-tooltip>
      </button>
    </div>
  </div>
</form>

<div id="modalLoginFb" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-success">
      <div class="modal-header">
        <h3 style="margin:0px;">Equipo Sigma Móvil:</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <p class="medium-text">
              Aun no estas registrado en nuestra plataforma, no pierdas la oportunidad de acceder a los beneficios de AIO. Regístrate <a href="{{url('register#/')}}">Aquí</a>
            </p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-sm-12 text-right">
            <button type="button" class="btn danger-inverted" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <br />
    <a href="{{url('register#/')}}" class="text-1em">¿No tienes una cuenta?</a> <br />
    <a href="{{url('session#/recoverpass')}}">Olvidé la contraseña</a>
  </div>
</div>