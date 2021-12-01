<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Creación de un nuevo Usuario en la cuenta maestra <strong>{{(account.name)}}</strong>
    </div>            
    <hr class="basic-line" />            
  </div>
</div>

<div class="row" ng-app="aio" ng-controller="ctrlUser" >
  <form action="{{url('user/create')}}/{{(account.idMasteraccount)}}" method="post" class="">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <div class="block block-info">          
        <div class="body " >
          <div class="row">

            <div class="form-group text-right">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 ">*Nombre</label>
                <span class="input hoshi input-default col-sm-8">                                                   
                  {{UserForm.render('name', {'class': 'undeline-input ',  'required': ''  })}}
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Apellido</label>
                <span class="input hoshi input-default col-sm-8">                                         
                  {{UserForm.render('lastname',  {'class': 'undeline-input ' ,  'required': '' })}}
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Email:</label>
                <span class="input hoshi input-default col-sm-8">                                       
                  {{UserForm.render('email',  {'class': 'undeline-input ' ,  'required': ''})}}
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Celular:</label>
                <span class="input hoshi input-default col-sm-8">                                         
                  {{UserForm.render('cellphone',  {'class': 'undeline-input ' ,  'required': '' })}}
                </span>
              </div>       
            </div> 

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Contraseña:</label>
                <span class="input hoshi input-default col-sm-8">                                          
                  {{UserForm.render('pass1',  {'class': 'undeline-input ' ,  'required': ''})}}
                </span>
              </div>       
            </div> 

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Repita la contraseña:</label>
                <span class="input hoshi input-default col-sm-8">                                    
                  {{UserForm.render('pass2',  {'class': 'undeline-input ' })}}
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Pais</label>
                <span class="input hoshi input-default  col-sm-8">           
                  <select class="undeline-input select2" ng-change="selectCountryUser()" ng-model="countrySelectedUser">
                    <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}">{{"{{c.name}}"}}</option>
                  </select>
                </span>
              </div>
            </div>

                  <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
                <label  class="col-sm-4 text-right">*Departamento</label>
                <span class="input hoshi input-default  col-sm-8">                 
                  <select class="undeline-input select2" ng-change="selectStateUser()" ng-model="stateSelectedUser">
                    <option ng-repeat="s in stateUser " value="{{"{{s.idState}}"}}">{{"{{s.name}}"}}</option>
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Ciudad:</label>
                <span class="input hoshi input-default  col-sm-8">       
                  <select class="undeline-input select2"  name="citySelectedUser" id="citySelectedUser" required ng-model="citySelectedUser">
                    <option ng-repeat="ci in citiesUser " value="{{"{{ci.idCity}}"}}">{{"{{ci.name}}"}}</option>
                  </select>
                </span>
              </div>
            </div>

          </div>
        </div>
        <div class="footer" align="right">
            <button class="button shining btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('user/index')}}/{{(account.idMasteraccount)}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
          
        </div>    
      </div>
    </div>
  </form>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
    <div class="fill-block fill-block-info" >
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>                            
          <li>Los campos nombre y apellidos no pueden tener menos de 2 caracteres</li>
          <li>Los campos nombre y apellidos no pueden tener mas de 40 caracteres</li>
          <li>El email debe ser unico</li>
          <li>La contraseña debe tener mínimo 8 caracteres y máximo 20 caracteres</li>
          <li>Para que la ciudad aparezca debes seleccionar un país y un departamento</li>
          <li>Los campos con asterisco(*) son obligatorios.</li>
        </ul> 
        </p>
      </div>
      <div class="footer">
        Creación
      </div>
    </div>     
  </div>            
</div>
