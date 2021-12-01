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

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Confirmación de encuesta</em>
    </div>
    <br>
    <p class="small-text text-justify">
      Aquí podrás generar el link para compartir la encuesta, establecer las fechas de disponibilidad y publicar la
      encuesta si lo deseas.
    </p>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-primary">
      <div class="body">
        <div class="row">
          {#<div class="col-xs-12 col-sm-12 co-md-12 col-lg-12 wrap">
            <span class="small-text bold">Generar Link o IFrame</span>
            <hr class="hr-classic">
          </div>#}
        </div>
        <br>
        {#<div class="row">
          <div class="col-xs-11 col-sm-11 col-md-3 col-lg-3 wrap">
            <button type="button" class="btn info-inverted btn-lg btn-block" data-ng-click="linkGenerator()">
              <i class="fa fa-link" aria-hidden="true"></i> Generar link
            </button>
          </div>
          <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <md-progress-circular class="md-hue-2" md-diameter="50px" data-ng-show="loader"></md-progress-circular>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap" id="msgPublic" data-ng-if="survey.type == 'contact'">
            <div class="block-black padding-10px">
              <p>
                Este link sólo funcionará si se agrega en el cuerpo de un envío de correo o de un envío de SMS
                por lista de contactos.
              </p>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap" id="msgContact" data-ng-if="survey.type == 'public'">
            <div class="block-black padding-10px">
              <p>
                Este link se podrá distribuir de forma menos limita ya que no está restringida a contactos, es decir podrá
                compartir por cualquier medio. Además tendrá la posibilidad de generar un IFrame que podrá inserta en
                su propio sitio web.
              </p>
            </div>
          </div>
        </div>#}
        <br>
        <div class="row">
          <div class="col-xs-12 col-sm-12 co-md-12 col-lg-12 wrap">
            <span class="small-text bold">Fecha de disponibilidad</span>
            <hr class="hr-classic">
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap" data-ng-show="survey.status == 'draft'">
            <div class="form-horizontal">
              <div class="form-group">
                <label class="col-sm-3 control-label">Fecha de inicio </label>
                <div class="col-sm-8">
                  <div id='datetimepicker' class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding input-append date">
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
                <label class="col-sm-3 control-label">Fecha de expiración</label>
                <div class="col-sm-8" >
                  <div id='datetimepicker1' class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding input-append date">
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
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap" data-ng-show="survey.status == 'published'">
            <div class="block-black padding-10px">
              <p>
                En este momento la encuesta tiene como estado <b><u>Publicada</u></b>. Sólo podrá modificar la fecha de expiración, 
                en caso de que desee extender la caducidad de la encuesta.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="footer text-right">
{#        <button type="button" class="btn info-inverted" data-toggle="modal" data-target=".bs-example-modal-lg" data-ng-show="status">Guardar como borrador</button>#}
        <button type="button" class="btn success-inverted" data-ng-click="saveConfirmation(2)">Guardar y continuar</button>
      </div>
    </div>
  </div>
</div>
{# Modal para borrador #}
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <p class="small-text">
              Debe tener en cuenta que si guarda la encuesta como <b>borrador</b>, tendrá la posibilidad de modificar
              el diseño de la encuesta, podrá generar el link de compartir, pero la encuesta no permitirá enviar
              respuestas.
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center">
            <button type="button" class="btn success-inverted" data-ng-click="saveConfirmation(1)">Aceptar</button>
            <button type="button" class="btn danger-inverted" data-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{# Final modal borrador #}

{# Modal para publicado #}
<div class="modal fade published" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-success">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <p class="small-text">
              Debe tener en cuenta que si guarda la encuesta como <b>Publicada</b>, <i>NO</i> tendrá la posibilidad
              de modificar el diseño de la encuesta, esto con el fin de mantener la veracidad y seguridad de las
              respuestas, podrá modificar las fecha de expiración más no la de inicio, la fecha de inicio deberá ser
              superior a la hora en que se vaya a guardar la encuesta.
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center">
            <button type="button" class="btn success-inverted" data-ng-click="saveConfirmation(2)">Aceptar</button>
            <button type="button" class="btn danger-inverted" data-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{# Final para modal publiado #}

{# Inicio del modal para mostrar el link #}
<div class="modal fade linkgen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-success">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <p class="small-text">
              Este es el link que podrá compartir dependiendo del tipo de encuesta que haya elegido
            <div class="form-group">
              <div class="col-sm-10">
                <input type="text" id="link" class="form-control" readonly="true" data-ng-model="linksurv" />
              </div>
              <div class="col-sm-2">
                <button type="button" class="btn btn-info" id="btnCopy">
                  <i class="fa fa-copy"></i> Copiar
                </button>
              </div>
            </div>
            </p>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-sm-12 text-center">
            <button type="button" class="btn danger-inverted" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{# Final del modal link generator  #}