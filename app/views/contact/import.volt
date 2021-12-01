<script>
    function ButtonDisable(obj) {
        obj.setAttribute("disabled", true);
    }
</script>
<div class="clearfix"></div>
<div class="space"></div>
<div class="row">

  <form method="post" action="{{url("contact/importcontacts/")}}{{ idContactlist }}" onSubmit="document.getElementById('submit').disabled=true;" data-ng-model="idContactlist" class="form-horizontal" enctype="multipart/form-data">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="block block-info">
        <div class="header">
          <span class="small-text">Importar contactos desde archivo .csv</span>
        </div>
        <div class="body">
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label class="col-xs-1 col-sm-1 col-md-1 small-text adjust-asterisk">*</label>
              <span class="input hoshi input-default col-xs-11 col-sm-11 col-md-11">
                <input name="filecsv" type="file" class="undeline-input" accept=".csv" required>
              </span>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <button id="submit" type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Cargar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="#/" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
        </div>
      </div>
    </div>
  </form>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <div class="fill-block fill-block-info" >
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>Los programas de hojas de cálculo como Microsoft Excel u OpenOffice.org Calc permiten crear y editar archivos CSV fácilmente.</li>
          <li>El archivo debe ser una tabla con un encabezado que defina los campos que contiene, por ejemplo: email, nombre, apellido, etc</li>
          <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
          <li><b>Los contactos contados son los que contienen correos electrónicos, los contactos con solo celular NO son sumados en el contador global.</b></li>
        </ul>
        </p>
      </div>
    </div>
  </div>
</div>
