<div class="row" data-ng-init="initComponents()">
  <form data-ng-submit="createAccount()" name="form">
    <div class="col-xs-1 col-sm-2 col-md-1"></div>
    <div class="col-xs-10 col-sm-8 col-md-10">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-7">
          <h1>Suscríbete a una cuenta GRATUITA</h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 text-right" style="padding-top: 30px;">
          <p class="line-height-1px">¿Ya tienes cuenta?</p>
          <p><a href="{{url('session#/')}}">Iniciar sesión</a></p>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="panel panel-default box-shadow-light-gray">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-12 col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.name.$valid}">
                    <label for="name">Nombre</label>
                    {{form.render('name', {'class' : 'form-control', 'data-ng-model':'data.account.name', 'aria-describedby':'inputSuccess2Status'})}}
                    <div class="validation" data-ng-show="form.$submitted || form.name.$touched">
                      <span class="help-block" data-ng-show="form.name.$error.required">Nombre es necesario</span>
                      <span class="help-block" data-ng-show="form.name.$error.minlength">Al menos 2 caracteres</span>
                      <span class="help-block" data-ng-show="form.name.$error.maxlength">Debe tener máximo 100 caracteres</span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.lastname.$valid}">
                    <label for="lastnameus">Apellido</label>
                    <input type="text" id="lastnameus" class="form-control" name="lastname" required maxlength="100" minlength="2" data-ng-model="data.account.lastname">
                    <div class="validation" data-ng-show="form.$submitted || form.lastname.$touched">
                      <span class="help-block" data-ng-show="form.lastname.$error.required">Apellido es necesario</span>
                      <span class="help-block" data-ng-show="form.lastname.$error.minlength">Al menos 2 caracteres</span>
                      <span class="help-block" data-ng-show="form.lastname.$error.maxlength">Debe tener máximo 100 caracteres</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.phone.$valid}">
                    <label for="phone">Número telefónico completo</label>
                    {{form.render('phone', {'class':'form-control', 'data-ng-model':'data.account.phone'})}}
                    <div class="validation" data-ng-show="form.$submitted || form.phone.$touched">
                      <span class="help-block" data-ng-show="form.phone.$error.required">Teléfono es necesario</span>
                      <span class="help-block" data-ng-show="form.phone.$error.minlength">Al menos 7 caracteres</span>
                      <span class="help-block" data-ng-show="form.phone.$error.maxlength">Debe tener máximo 45 caracteres</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.email.$valid}">
                    <label for="email">Correo electrónico</label>
                    {{form.render('email', {'class' : 'form-control', 'data-ng-model':'data.account.email'})}}
                    <div class="validation" data-ng-show="form.$submitted || form.email.$touched">
                      <span class="help-block" data-ng-show="form.email.$error.required">Correo es necesario</span>
                      <span class="help-block" data-ng-show="form.email.$error.email">Dirección de correo inválida</span>
                      <span class="help-block" data-ng-show="form.email.$error.minlength">Al menos 2 caracteres</span>
                      <span class="help-block" data-ng-show="form.email.$error.maxlength">Debe tener máximo 40 caracteres</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 col-md-2">
                    <div class="form-group">
                        <label for="checkEmp">Empresa: </label>
                    </div>
                </div>
                <div class="col-md-6 col-md-2">
                  <div class="form-group">
                    <label for="ycheckEmp">Si: </label>
                    <input  type="checkbox" id="ycheckEmp" data-ng-model="data.account.ycheckEmp" ng-checked="validateEmpShow" ng-click="validateEmpY(this)"/>
                  </div>
                </div>
                <div class="col-md-6 col-md-2">
                  <div class="form-group">
                    <label for="ncheckEmp">No: </label>
                    <input  type="checkbox" id="ncheckEmp" data-ng-model="data.account.ncheckEmp" ng-click="validateEmpN(this)"/>
                  </div>
                </div>
              </div>
              <div class="row" ng-show="validateEmpShow">
                <div class="col-sm-12 col-md-6">
                  <div class="form-group">
                     <label for="Nit">Nit de empresa</label>
                     <input type="text" id="nit" class="form-control" name="nit" data-ng-model="data.account.nit">
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                       <label for="nomemp">Nombre de empresa</label>
                       <input type="text" id="nomemp" class="form-control" name="nomemp" maxlength="100" data-ng-model="data.account.nomemp">
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.pass1.$valid}">
                    <label for="pass1">Contraseña</label>
                    <input type="password" id="pass1" class="form-control" name="pass1" required maxlength="30" minlength="5" data-ng-model="data.account.pass1">
                    <div class="validation" data-ng-show="form.$submitted || form.pass1.$touched">
                      <span class="help-block" data-ng-show="form.pass1.$error.required">Es necesario una contraseña</span>
                      <span class="help-block" data-ng-show="form.pass1.$error.minlength">Al menos 5 caracteres</span>
                      <span class="help-block" data-ng-show="form.pass1.$error.maxlength">Debe tener máximo 30 caracteres</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.pass2.$valid}">
                    <label for="pass2">Repita Contraseña</label>
                    <input type="password" id="pass2" class="form-control" name="pass2" required maxlength="30" minlength="5" data-ng-model="data.account.pass2">
                    <div class="validation" data-ng-show="form.$submitted || form.pass2.$touched">
                      <span class="help-block" data-ng-show="form.pass2.$error.required">Es debe repetir la contraseña</span>
                      <span class="help-block" data-ng-show="form.pass2.$error.minlength">Al menos 5 caracteres</span>
                      <span class="help-block" data-ng-show="form.pass2.$error.maxlength">Debe tener máximo 30 caracteres</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.country.$valid}">
                    <label for="country">País</label>
                    <select class="form-control select2" id="country" name="country" ng-required="true" style="width: 100%" data-ng-model="data.account.idCountry" data-placeholder="Seleccionar un país" data-ng-change="states(data.account.idCountry)">
                      <option value=""></option>
                      <option data-ng-repeat="country in listcountry track by $index" value="{{"{{country.idCountry}}"}}">{{"{{country.name}}"}}</option>
                    </select>
                    <div class="validation" data-ng-show="form.$submitted || form.country.$touched">
                      <span class="help-block" data-ng-show="form.country.$error.required">Debe seleccionar un país</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.state.$valid}">
                    <label for="state">Departamento/Estado</label>
                    <select class="form-control select2" id="state" name="state" ng-required="true" style="width: 100%" data-ng-model="data.account.idState" data-placeholder="Seleccione un Departamento / Estado / Provincia" data-ng-change="cities(data.account.idState)" {#ng-disabled="true"#}>
                      <option value=""></option>
                      <option data-ng-repeat="state in liststates track by $index" value="{{"{{state.idState}}"}}">{{"{{state.name}}"}}</option>
                    </select>
                    <div class="validation" data-ng-show="form.$submitted || form.state.$touched">
                      <span class="help-block" data-ng-show="form.state.$error.required">Debe seleccionar un país</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" data-ng-class="{'has-success':form.city.$valid}">
                    <label for="city">Ciudad</label>
                    <select class="form-control select2" id="city" name="city" ng-required="true" style="width: 100%" data-ng-model="data.account.idCity" data-placeholder="Seleccione una ciudad">
                      <option value=""></option>
                      <option data-ng-repeat="city in listcities track by $index" value="{{"{{city.idCity}}"}}">{{"{{city.name}}"}}</option>
                    </select>
                    <div class="validation" data-ng-show="form.$submitted || form.city.$touched">
                      <span class="help-block" data-ng-show="form.city.$error.required">Debe seleccionar un país</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  {# Espacio en blanco #}
                </div>
              </div>
              <div class="row">
                <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1">
                  <div class="checkboxFive" ng-required="true">
                    <input  type="checkbox" id="acceptTerms" data-ng-model="data.account.acceptTermsConditions"/>
                    <label for="acceptTerms"></label>
                  </div>
                </div>
                <div class="col-md-11 col-lg-11 col-sm-11 col-xs-11">
                  <label class="radio-inline" for="acceptTerms" ng-class="{'text-danger':misc.termsConfitionsAccount}">Acepto términos y condiciones</label>
                </div>
              </div>  
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <button type="submit" class="btn success-inverted btn-block btn-lg"  style="margin-top:8px">
                    Suscribirse <i class="fa fa-spinner fa-spin"  data-ng-if="loader" style="font-size: 1.2em"></i>
                  </button>
                  <br/>
                  <p class="smaill-text">Al hacer clic en “Suscríbete”, estás aceptando nuestros <a href="http://www.sigmamovil.com/terminos-y-condiciones/ 
                                                                       " target="_blank" ><ins>Términos de uso y la Política</ins><a/> de privacidad, recepción de noticias y sugerencias por correo electrónico.</p>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-default box-shadow-light-gray">
              <div class="panel-body">
                <div classs="row"> 
                  <h3>O bien, inicia sesión con tu cuenta  de Facebook</h3>
                  <p class="text-justify">
                    Ahora puedes vincular tus cuentas e iniciar sesión en AIO con tu cuenta de Facebook.
                    Es rápido, fácil y seguro; tus datos de AIO serán completamente privados.
                  </p>
                </div>
                {#<p>
                  <button type="button" class="btn btn-default">
                    <img src="{{url('images/register-auth/icon-google.ico')}}" class="icons-auth" alt="Google">
                    <span>Suscríbete con Google</span>
                  </button>
                </p>#}
                <div class="row">
                  <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1">
                    <div class="checkboxFive" required="required">
                      <input  type="checkbox" id="acceptTermsFacebook" ng-model="acceptTermsConditionsFacebook"/>
                      <label for="acceptTermsFacebook"></label>
                    </div>
                  </div>
                  <div class="col-md-11 col-lg-11 col-sm-11 col-xs-11">
                    <label class="radio-inline" for="acceptTermsFacebook" ng-class="{'text-danger':misc.termsConfitionsFacebook}">Acepto términos y condiciones</label>
                  </div>
                </div>
                &nbsp;
                <div class="row">
                  <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                    <p>
                      <button type="button" class="btn btn-default"  data-ng-click="loginnetworkingsocials.loginFB()">
                        <img src="{{url('images/register-auth/icon-facebook.png')}}" class="icons-auth" alt="Facebook">
                        <span>Suscríbete con Facebook</span>
                      </button>
                    </p>
                    <md-progress-linear class="md-warn" md-mode="query" data-ng-show="loaderBar"></md-progress-linear>
                  </div>
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
  (function () {
    $(".select2").select2();
  })();
  
  $('#nit').keyup(function () { 
        this.value = this.value.replace(/[^0-9\-]/g,'');
    });
</script>