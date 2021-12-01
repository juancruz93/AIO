<div class="row">
  <div class="col-xs-12 col-sm-2 col-md-3 col-lg-4"></div>
  <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4">
    <h2>Por favor selecciona tu ciudad</h2>
  </div>
  <div class="col-xs-12 col-sm-2 col-md-3 col-lg-4"></div>
</div>
<div class="form-complete-profile" data-ng-init="initComponents()">
  <div class="row">
    <div class="col-xs-12 col-sm-2 col-md-3 col-lg-4"></div>
    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4">
      <div class="panel panel-default box-shadow-light-gray">
        <div class="panel-body">
          <form data-ng-submit="completeProfile()">
            <div class="form-group">
              <label for="idCountry">País</label>
              <select class="form-control select2" id="country" name="country" ng-required="true" style="width: 100%" data-ng-model="data.account.idCountry" data-placeholder="Seleccionar un país" data-ng-change="states(data.account.idCountry)">
                <option value=""></option>
                <option data-ng-repeat="country in listcountry track by $index" value="{{"{{country.idCountry}}"}}">{{"{{country.name}}"}}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="state">Departamento/Estado</label>
              <select class="form-control select2" id="state" name="state" ng-required="true" style="width: 100%" data-ng-model="data.account.idState" data-placeholder="Seleccione un Departamento / Estado / Provincia" data-ng-change="cities(data.account.idState)" {#ng-disabled="true"#}>
                <option value=""></option>
                <option data-ng-repeat="state in liststates track by $index" value="{{"{{state.idState}}"}}">{{"{{state.name}}"}}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="city">Ciudad</label>
              <select class="form-control select2" id="city" name="city" ng-required="true" style="width: 100%" data-ng-model="data.account.idCity" data-placeholder="Seleccione una ciudad">
                <option value=""></option>
                <option data-ng-repeat="city in listcities track by $index" value="{{"{{city.idCity}}"}}">{{"{{city.name}}"}}</option>
              </select>
            </div>
            <div class="text-right">
              <button type="submit" class="btn success-inverted">Guardar</button>    
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-2 col-md-3 col-lg-4"></div>
  </div>
</div>
<script>
  (function () {
    $(".select2").select2();
  })();
</script>