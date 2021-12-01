<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        {{flash.output()}}
    </div>
</div>
<style>

</style>
<div class="subtitleIndex">
    Aquí puede ver un plan de acción.
</div>
<form novalidate class="form-validate form-horizontal" id="feedback_form">

    <div class="row">
        <div class="col-lg-12 col-sm-12 panel-left">
            <section class="panel">
                <header class="panel-heading panel-title">
                    Información general del proyecto
                </header>
                <div class="panel-body">
                    <div class="form">
                        <div class="row">
                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                <div id="name" class="item-form">
                                    <div>Nombre del plan de acción:</div>
                                    <div>
                                        <b>{{"{{actionplanview.name}}"}}</b>
                                    </div>
                                </div>
                                <div id="name" class="item-form">
                                    <div>Descripcion:</div>
                                    <div>
                                        <b>{{"{{actionplanview.description}}"}}</b>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">                            
                                <div id="name" class="item-form">
                                    <div>Nombre proyecto:</div>
                                    <div>
                                        <b>{{"{{actionplanview.nameProject}}"}}</b>
                                    </div>
                                </div>
                                <div id="name" class="item-form">
                                    <div>Descripcion proyecto:</div>
                                    <div>
                                        <b>{{"{{actionplanview.descriptionProject}}"}}</b>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">                            
                                <div id="name" class="item-form">
                                    <div>impacto:</div>
                                    <div>
                                        <b>{{"{{actionplanview.impact}}"}}</b>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {# <div class="form-group " id="progress">
                           <label for="cname" class="control-label col-lg-2">Estado y progreso: </label>
                           <div class="col-lg-10">
                             <div class="progress progress-striped active progress-sm" style="height: 30px;">
                               <div class="progress-bar progress-bar-{{"{{project.status}}"}}" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                 {{'{{translate(project.status)}}'}}
                               </div>
                             </div>
                           </div>
                         </div>#}
                        {#     <div class="form-group " id="name">
                               <label for="cname" class="control-label col-lg-2">Estado:</label>
                               <div class="col-lg-10">
                                 <span class="label label-{{'{{project.status}}'}}">{{'{{translate(project.status)}}'}}</span>
                               </div>
                             </div>#}
                        {#      <div class="form-group " id="name">
                                <label for="cname" class="control-label col-lg-2">Nombre de la iniciativa</label>
                                <div class="col-lg-10">
                                  <h5><b>{{"{{project.initiativeName}}"}}</b></h5>
                                </div>
                              </div>
                              <div class="form-group " id="name">
                                <label for="cname" class="control-label col-lg-2">Nombre del proyecto</label>
                                <div class="col-lg-10">
                                  <h5><b>{{"{{project.name}}"}}</b></h5>
                                </div>
                              </div>#}
                        {#   <div class="form-group" id="idClient">
                             <label for="cname" class="control-label col-lg-2">Cliente</label>
                             <div class="col-lg-10">
                               <h5><b>{{"{{project.clientName}}"}}</b></h5>
                             </div>
                           </div>
                           <div class="form-group" id="idProjectCategory">
                             <label for="cname" class="control-label col-lg-2">Categoría</label>
                             <div class="col-lg-10">
                               <h5><b>{{"{{project.categoryName}}"}}</b></h5>
                             </div>
                           </div>#}

                        {#            <div class="form-group " id="description">
                                      <label for="cname" class="control-label col-lg-2">Descripción <span class="required">*</span></label>
                                      <div class="col-lg-10">
                                        <h5><b>{{"{{project.description}}"}}</b></h5>
                                      </div>
                                    </div>
                                    <div class="form-group " id="experts">
                                      <label for="cname" class="control-label col-lg-2">Expertos <span class="required">*</span></label>
                                      <div class="col-lg-10">
                                        <table>
                                          <tbody>
                                            <tr ng-repeat="expert in project.experts2">
                                              <td>{{"{{expert.email}}"}} </td>
                                              <td><span class="label label-estimate-{{"{{expert.status}}"}} " style='margin-left: 10px;'>{{"{{translateestimate(expert.status)}}"}} </span></td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>#}
                        {#  <div class="form-group " id="testTime"  ng-if="project.status == 'calculated' && (userRole == lider || userRole == experto)">
                            <label for="cname" class="control-label col-lg-2">Tiempo estimado <span class="required">*</span></label>
                            <div class="col-lg-10">
                              <h5><b>{{"{{project.formatedTestTime}}"}}</b></h5>
                            </div>
                          </div>
                          <div class="form-group " id="minTime"  ng-if="project.status == 'calculated' && (userRole == lider || userRole == experto)">
                            <label for="cname" class="control-label col-lg-2">Tiempo mínimo estimado <span class="required">*</span></label>
                            <div class="col-lg-10">
                              <h5><b>{{"{{project.formatedMinTime}}"}}</b></h5>
                            </div>
                          </div>
                          <div class="form-group " id="maxTime"  ng-if="project.status == 'calculated' && (userRole == lider || userRole == experto)">
                            <label for="cname" class="control-label col-lg-2">Tiempo máximo estimado <span class="required">*</span></label>
                            <div class="col-lg-10">
                              <h5><b>{{"{{project.formatedMaxTime}}"}}</b></h5>
                            </div>
                          </div>
                          <div class="form-group " id="maxTime"  ng-if="project.status == 'calculated' && (userRole == vendedor)">
                            <label for="cname" class="control-label col-lg-2">Tiempo estimado <span class="required">*</span></label>
                            <div class="col-lg-10">
                              <h5><b>{{"{{project.txp.formatedValue}}"}}</b></h5>
                            </div>
                          </div>#}

                    </div>

            </section>
        </div>
    </div>


    <div class='row'>
        <div class="col-lg-12 col-sm-12 panel-left">
            <section class="panel">
                <header class="panel-heading panel-title">
                    Actividades del plan de acción
                </header>
                <div class="panel-body">
                    <div>
                        <div class="module-item row">
                            <div class="col-lg-2 col-md-1 col-sm-1 col-xs-1"></div>
                            <div class="col-lg-8 col-md-7 col-sm-7 col-xs-7">Actividades</div>
                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4 text-right">
                            </div>
                        </div>
                        <div class="module-item row displaynone editmodule-item" id="editmodule-item">
                            <div class="col-lg-2 col-md-1 col-sm-1 col-xs-1"></div>
                            <div class="col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                <input ng-model="editmodule.name" type="text" autofocus="autofocus" class="form-control" minlength="2" maxlength="100">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4 text-right">
                            </div>
                        </div>
                        <div id="" class="requirements-items">
                            <div class="requirement-item requirement-header row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <div class="row ">
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Descripción</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Responsable</div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">fecha inicio</div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">fecha fin</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Porcentaje</div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
                                    {# Acciones#}
                                </div>
                            </div>
                            <div class="requirement-item displaynone row requirement-save" id="requirementCrud">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                            <input id="description" ng-model="activity.description" type="text" required="required"
                                                   class="form-control" minlength="2" maxlength="300"/>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                            <input ng-model="activity.responsable" type="text" required="required" autofocus="autofocus" class="form-control" minlength="2"
                                                   maxlength="45">
                                        </div>                  
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                            <input ng-model="activity.datestart" class="form-control" id='datetimepickerstart' />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                            <input ng-model="activity.dateend" class="form-control" id='datetimepickerend' />                    
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                            <input type="number" ng-model="activity.porcentage" class="form-control" value="0" id='' minlength="0" maxlength="3"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right" style="font-size: 20px;">
                                </div>
                            </div>
                            <div class="requirement-item">
                                <div class=" each-requirement" ng-if="listactivities.items.length > 0" ng-repeat="requirement in listactivities.items">
                                    <div class="row request-item" id="request-item-{{"{{requirement.idActivity}}"}}">
                                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                            <div class="row ">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text cursor-pointer" ng-click="showEditRequirement(requirement)">{{"{{requirement.description}}"}}</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text cursor-pointer" ng-click="showEditRequirement(requirement)">{{"{{requirement.responsable}}"}}</div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text cursor-pointer" ng-click="showEditRequirement(requirement)">{{"{{requirement.datestart}}"}}</div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text cursor-pointer" ng-click="showEditRequirement(requirement)">{{"{{requirement.dateend}}"}}</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text cursor-pointer" ng-click="showEditRequirement(requirement)">{{"{{requirement.porcentage}}"}}</div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row editrequest-item displaynone" id="editrequest-item-{{"{{requirement.idActivity}}"}}">
                                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                            <div class="row ">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                                    <textarea id="req-descriptionedit" id="description" ng-model="editactivity.description" rows="1" type="text" required="required"
                                                              class="form-control" minlength="2" maxlength="300"></textarea>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                                    <input ng-model="editactivity.responsable" type="text" required="required" autofocus="autofocus" class="form-control" minlength="2"
                                                           maxlength="45">
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                                    <input type='text' ng-model="editactivity.datestart" class="form-control" id='' />                        
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                                    <input type='text' ng-model="editactivity.dateend" class="form-control" id='' />                       
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                                    <input type="number" ng-model="editactivity.porcentage" class="form-control" value="0" id='' minlength="0" maxlength="3"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
                                        </div>
                                    </div>

                                </div>
                                <div class="row" ng-if="module.requirements.items.length == 0" id="noRequirements{{" {{module.idProjectModule}} "}}">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                        No hay requerimientos para este módulo
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
                {#
                <div ng-if="!showModules" class="text-center">
                  <h3>No hay módulos creados</h3>
                  <h3 ng-click='registerModules()' class="cursor-pointer">Haga click AQUÍ para agregar</h3>
                </div>#}

        </div>

    </div>      



    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10 text-right">
            <a class="btn btn-success" href="#/">
                Regresar
            </a>
            <a class="btn btn-info" data-toggle="modal" data-target="#calculateModal" ng-if="userRole == lider && (project.status == 'estimated')" ng-click="calculateProject()">
                Calcular
            </a>
            <a class="btn btn-danger" ng-if="userRole == vendedor && (project.status == 'calculated')" ng-click="declineProject()">
                Declinar proyecto
            </a>
            <a class="btn btn-info" ng-if="userRole == vendedor && (project.status == 'calculated')" ng-click="approveProject()">
                Aprobar proyecto
            </a>
            <a class="btn btn-report" ng-if="((userRole == lider || userRole == vendedor) && (project.status == 'calculated' || roject.status == 'approved'))" href="#/report/{{"{{project.idProject}}"}}">
                Ver reporte
            </a>
        </div>
    </div>
</form>



<!-- Modal -->
<div class="modal fade" id="saveandexit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">¿Está seguro de que desea publicar?</h4>
            </div>
            <div class="modal-body">
                Cuando publique este proyecto los expertos seleccionados podrán verlo. Cuando el primer experto decida empezar a estimarlo su estado cambiará a <i>Estimando</i>, en ese estado ya no se podrá editar más el proyecto.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" ng-click="validateProject('exit')" data-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#myModal').on('shown.bs.modal', function () {
        $('#myInput').focus()
    })
</script>


