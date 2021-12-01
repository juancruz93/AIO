<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Editar plantilla prediseñada <i>{{"{{data.nametempsms}}"}}</i>
    </div>            
    <hr class="basic-line">
    <p class="text-justify">
      Las plantillas predefinidas le serán útiles en el momento de crear contenido de un SMS,
      ya que tendrás con que partir y solo necesitaras hacer algunos retoques si lo consideras necesario.
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <form>
      <div class="block block-info">
        <div class="body row">
          <div class="col-md-12">
            <div class="body form-horizontal">
              <div class="form-group">
                <label for="nametempsms" class="col-sm-2 control-label">*Nombre</label>
                <div class="col-sm-10">
                  <input type="text" class="undeline-input form-control" id="nametempsms" name="nametempsms" maxlength="45" minlength="2" autofocus data-ng-model="data.nametempsms">
                  <div class="text-right" data-ng-class="nametempsms.length > 45 ? 'negative':''">{{"{{data.nametempsms.length > 0 ?  data.nametempsms.length+'/45':''}}"}}</div>
                </div>
              </div>
              <div class="form-group" data-ng-show="!newcategorytemplatesms">
                <label for="smstempcateg" class="col-sm-2 control-label">*Categoría</label>
                <div class="col-sm-9">
                  <select class="chosen form-control input-lg" style="width: 100%" data-ng-model="data.smstempcateg">
                    <option ng-repeat="x in listcateg" value="{{"{{x.idSmsTemplateCategory}}"}}" ng-selected="x.id == data.smstempcateg">{{"{{x.name}}"}}</option>
                  </select>
                </div>
                <div class="col-sm-1 text-left">
                  <a class="positive tooltip-de" data-placement="top" title="Nueva categoría" href="" data-ng-click="newCateg()"><i class="fa fa-plus fa-2x"></i></a>
                </div>
              </div>
             <div class="form-group" ng-cloak>
                  <label class="col-sm-2 control-label text-justify">Usar más de&nbsp 160 caracteres:</label>
                  <div class="col-sm-1">
                    <div class="onoffswitch">
                      <input type="checkbox" name="morecaracter" data-ng-model="data.morecaracter" class="onoffswitch-checkbox" id="morecaracter" ng-click="opeModalMoreCa()">
                      <label class="onoffswitch-label" for="morecaracter">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </div>               
              </div>
              <div class="form-group" data-ng-show="newcategorytemplatesms">
                <label for="newsmstempcateg" class="col-sm-2 control-label">Nueva categoría</label>
                <div class="col-sm-8">
                  <input type="text" class="undeline-input form-control" maxlength="80" data-ng-model="newsmstempcateg">
                  <div class="text-right" data-ng-class="newsmstempcateg.length > 80 ? 'negative':''">{{"{{data.newsmstempcateg.length > 0 ?  data.newsmstempcateg.length+'/80':''}}"}}</div>
                </div>
                <div class="col-sm-2 text-right">
                  <a class="negative tooltip-de" data-toggle="tooltip" data-placement="top" title="Cancelar" href="" data-ng-click="cancelCateg()"><i class="fa fa-times fa-2x"></i></a>
                  <a class="positive tooltip-de" data-toggle="tooltip" data-placement="top" title="Guardar" href="" data-ng-click="saveCateg()"><i class="fa fa-check fa-2x"></i></a>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 col-md-2 text-right">Etiquetas:</label>
                <span class="input hoshi input-default col-sm-10 col-md-10">
                  <spam ng-click="toggleAllTags()" class=" allTags color-info" style="width: 100%; margin: 0;"> Mostrar las etiquetas</spam>
                  <table id="customers" class="allTags" style="display: none;">
                    <tbody>
                      <tr  ng-show="tags.length>0">
                        <th>Campo</th>
                        <th>Etiqueta</th>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: center;" ng-show="!tags.length>0">
                          No hay etiquetas disponibles                          </td>
                      </tr>

                      <tr  class="alt" ng-repeat="(key, value) in tags track by $index"  ng-show="tags.length>0">
                        <td>{{"{{value.name}}"}}</td>
                        <td ng-click="addTag(value.tag)" style="cursor: pointer;">{{"{{value.tag}}"}}</td>
                      </tr>

                    </tbody></table>
                  <spam ng-click="toggleAllTags()" class=" allTags color-warning" style="width: 100%; margin: 15px 0px 0px;display: none;"> Ocultar las etiquetas</spam>

                </span>
              </div>
              <div class="form-group">
                <label for="contenttempsms" class="col-sm-2 control-label">*Contenido</label>
                <div class="col-sm-10">
                  <textarea class="undeline-input form-control" id="contenttempsms" style="resize: none;" name="contenttempsms" maxlength="{{'{{data.morecaracter == true ? 300:160 }}'}}" minlength="1" rows="3" data-ng-model="data.contenttempsms" ng-change="validateContent();validateInLine()"></textarea>
                    <div class="text-right" ng-hide='data.morecaracter' data-ng-class="data.contenttempsms.length > 160 ? 'negative':''">{{"{{data.contenttempsms.length > 0 ?  data.contenttempsms.length+'/160 aproximadamente':''}}"}} </div>                
                    <div class="text-right" ng-show='data.morecaracter' data-ng-class="data.contenttempsms.length > 300 ? 'negative':''">{{"{{data.contenttempsms.length > 0 ?  data.contenttempsms.length+'/300 aproximadamente':''}}"}} </div>                  

                  {#                  <div class="text-left"  ng-show="contenttempsms.length > 0"><p class="negative" ng-show="errorChart">Hay caracteres no permitidos en el contenido</p></div>#}
                  <h6 class="color-danger text-justify" ng-show='existTags'>Tenga en cuenta que si personaliza el contenido con etiquetas se enviará un espacio en blanco en vez de la etiqueta a los contactos que no tienen el campo.</h6>
                  <h6 class="color-warning" ng-show='existTags == true && data.morecaracter == false'>Si personaliza el mensaje SMS y éste excede los 160 caracteres permitidos será cortado en el momento del envío</h6>
                  <h6 class="color-warning" ng-show='existTags == true && data.morecaracter == true'>Si personaliza el mensaje SMS y éste excede los 300 caracteres permitidos será cortado en el momento del envío</h6>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer text-right">
          <button type="button" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar" data-ng-click="editSmsTemplate()">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('smstemplate#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
          <a ng-show="data.contenttempsms" ng-click="openPreview()" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Visualizar">
            <span class="fa fa-eye" aria-hidden="true"></span>
          </a>
        </div>
      </div>
    </form>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-primary">
      <div class="header">
        Instrucciones
      </div>
      <div class="body">
        <p>Recuerde tener en cuenta estas recomendaciones</p>
        <ul>
          <li>El nombre debe tener mínimo 2 y máximo 45 caracteres</li>
          <li>Debe seleccionar un item de la lista de categorías, 
            también puede crear una nueva categoría haciendo click en el icono inmediatamente después del listado</li>
           <li>El contenido debe tener máximo 160 caracteres, si <b> no </b>usa la opción de más de 160 caracteres</li>
          <li>Al usar la opción de más de 160 caracteres, su mensaje no podrá superar los <b>300</b> caracteres</li>
           <li>La cantidad de caracteres puede variar si se utilizan campos personalizados. 
            Por ejemplo, al usar la etiqueta <i>%%NOMBRE%%</i>, en un SMS puede aparecer <i>Juan</i> con 4 caracteres y en otro <i>Fernando</i> con 8 caracteres</li>
          <li>Los campos con asterisco(*) son obligatorios.</li>
        </ul>
      </div>
    </div>
  </div>

</div>
<div id="preview" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">

    <div class="dialog-inner" style="height:auto" >
      <div class='smsContainer'>
        Tu mensaje tendrá el siguiente aspecto

        <div class="smsContent" style="height:auto" ng-bind-html="taggedMessage">
        </div>
      </div>
      <br>
      <div class='smsFooter'>                    
        <a ng-click="closePreview()"  id="btn-ok" class="button shining btn btn-md success-inverted float-right">Ok</a>
      </div>
    </div>
  </div>
</div>
<div id="alertMoreCaracter" class="modal" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner" >
      <div class="form-group row">
        <label for="name" class="col-xs-12" style="padding-top: 10px; font-size: 18px">Tenga en cuenta que cada mensaje que contenga entre <b>160 </b> y <b>300</b> caracteres, será cobrado por 2 sms</label>      
      </div>
      <div>
        <a onClick="closeModalForm()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>      
      </div>
    </div>
  </div>
</div>
<script>
  $(".chosen").select2({
    placeholder: 'Seleccione una categoría'
  });
    function closeModalForm() {
        $('#alertMoreCaracter').removeClass('dialog dialog--open');
        $('#alertMoreCaracter').addClass('modal'); 
    } 
</script>