<script>
  (function () {
    $.fn.datetimepicker.defaults = {
      maskInput: false,
      pickDate: true,
      pickTime: true,
      startDate: new Date()
    };
    $("#datetimepicker,#datetimepicker1").datetimepicker({
      format: 'yyyy-MM-dd hh:mm',
      language: 'es'
    });
  })();
</script>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Opciones avanzadas</em>
    </div>
    <br>
    <p class="small-text text-justify">
      Configure su propia pagina escogiendo un rango de fecha en la que quiere que este disponible la pagina y seleccione la cantidad de visualización que tendra su Landing Page.
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-primary">
      <div class="body"> 
        <div class="row">
          <div class="col-sm-10 col-lg-12">
            <span class="small-text bold">Fecha de disponibilidad</span>
            <hr class="hr-classic">
          </div>
        </div>
        <br>              
        <div class="row">
          <p class="col-sm-10">Selecciona una fecha durante la cual estará disponible la Landing Page:</p>
          <br>
          <br>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap" >            
            <div class="form-horizontal">
              <div class="form-group">                
                <label class="col-sm-3 control-label">Fecha de inicio: </label>
                <div class="col-sm-8">
                  <div id='datetimepicker' class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-10px none-padding input-append date">
                    <span class="input-append date add-on input-group none-padding">
                      <input id="startDate" data-format="yyyy-MM-dd hh:mm " type="text" ng-blur="validationDateExpiration()" class="form-control">
                      <span class="add-on input-group-addon">
                        <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                      </span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <div class="form-horizontal">
              <div class="form-group">
                <label class="col-sm-4 control-label">Fecha de expiración:</label>
                <div class="col-sm-8" >
                  <div id='datetimepicker1' class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-10px none-padding input-append date">
                    <span class="input-append date add-on input-group none-padding">
                      <input id="endDate"  data-format="yyyy-MM-dd hh:mm " type="text" class="form-control ">
                      <span class="add-on input-group-addon">
                        <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                      </span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>     
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <div class="form-horizontal">
              <div class="form-group">
                <label class="col-sm-4 control-label">Cantidad de visualizaciones disponibles:</label>
                <div class="col-sm-8" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding input-append date">
                    <span class="input-append date add-on input-group none-padding">
                      <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-click="functions.sentNow()" ng-model="data.status"></md-switch>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap" ng-show="!data.sentNow">
            <div class="form-horizontal">
              <div class="form-group">
                <label class="col-sm-4 control-label">Indique la cantidad de visualizaciones:</label>
                <div class="col-sm-8" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding input-append date">
                    <input type="number" class="input form-control" ng-model="data.countview"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12">
            <div class="form-horizontal">
              <div class="form-group">
                <div class="col-sm-12" >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer">
        <div class="col-sm- col-lg-">
          <span class="small-text bold"><font color="black" style="opacity: 0.8;">Visualizaciones</font></span>
          <hr class="hr-classic">
        </div>
        <br>
        <font color="black" style="opacity: 0.8;"><p>Actualmente usted cuenta con un total de <b><u>{{"{{data.totalview}}"}}</u></b> visualizaciones disponibles.</p></font>
      </div>
      <br>
      <br>
      <div class="footer text-right">      
        <button type="button" class="btn success-inverted" data-ng-click="resServices.savePublicationView()">Guardar y continuar</button>
      </div>
    </div>
  </div>
</div>





