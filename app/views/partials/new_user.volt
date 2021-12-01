<script>
  function clearselect() {
    $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
  }
</script>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{tittle}}
    </div>            
    <hr class="basic-line" />            
  </div>
</div>

<div class="row" ng-app="aio" ng-controller="ctrlUser" >
  <form action="{{url(url)}}" method="post" class="">
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
                <label class="col-sm-4 text-right">*Telefono:</label>
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
                  <select class="undeline-input select2 " ng-change="selectCountryUser()" ng-model="countrySelectedUser" onchange="clearselect()" required="">
                    <option ng-repeat="c in country " value="{{"{{c.idCountry}}"}}">{{"{{c.name}}"}}</option>
                  </select>
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
                <label  class="col-sm-4 text-right">*Departamento</label>
                <span class="input hoshi input-default  col-sm-8">                 
                  <select class="undeline-input select2 clearselect" ng-change="selectStateUser()" ng-model="stateSelectedUser">
                    <option ng-repeat="s in stateUser " value="{{"{{s.idState}}"}}">{{"{{s.name}}"}}</option>
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Ciudad:</label>
                <span class="input hoshi input-default  col-sm-8">       
                  <select class="undeline-input select2 clearselect"  name="citySelectedUser" id="citySelectedUser" required ng-model="citySelectedUser">
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
          <a href="{% if user.role.idRole == -1 %} {{url('masteraccount/aliaslistuser/'~idMasteraccount)}} {% else %} {{url('user/index')}} {% endif %}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>El campo nombre  no pueden tener menos de 2 caracteres ni más de 40 caracteres</li>
          <li>El campo apellido  no pueden tener menos de 2 caracteres ni más de 40 caracteres</li>
          <li>El email debe ser unico</li>
          <li>El campo telefono  no pueden tener menos de 8 caracteres ni más de 45 caracteres</li>
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
