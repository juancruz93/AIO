
<div class="subtitleIndex">
    Aquí puede editar un plan de acción.
</div>


<div >

</div>
<div class="row">
    <div class="col-lg-6 col-sm-12 panel-left">
        <section class="panel">
            <header class="panel-heading panel-title">
                Edite el plan de acción
            </header>
            <div class="panel-body">
                <div class="form">
                    <form novalidate class="form-validate form-horizontal" id="feedback_form" method="get" ng-submit="validateActionPlan()">
                        <div class="form-group" id="idActionPlan">
                            <label for="cname" class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Nombre:
                                <span class="required">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <input ng-model="editactionplan.name" type="text" required="required" class="form-control" minlength="2" maxlength="45">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cname" class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Descripción 
                                <span class="required">*</span>
                            </label>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <textarea ng-model="editactionplan.description" rows="2" type="text" required="required" class="form-control" minlength="2"
                                          maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cname" class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Proyecto 
                                <span class="required">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <label >{{"{{editactionplan.idProject}}"}}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cname" class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Impacto:
                                <span class="required">*</span></label>                       
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <span class="input hoshi input-default">
                                    <ui-select ng-model="editactionplan.impact" ng-required="true"
                                               ui-select-required  sortable="false"
                                               close-on-select="true">
                                        <ui-select-match
                                            placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                        <ui-select-choices
                                            repeat="impact.name as impact in impacts | propsFilter: {name: $select.search}">
                                            <div ng-bind-html="impact.name| highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </span>
                            </div>
                        </div>    
                    </form>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-6 col-sm-12 ">
        <section class="panel">
            <header class="panel-heading panel-right-title">
                Instrucciones
            </header>
            <div class="panel-body">
                Antes de empezar, por favor lea estas recomendaciones:
                <ul>
                    <li>
                        El nombre del plan de acción debe tener entre 2 y 45 caracteres
                    </li>
                    <li>
                        La descripción debe contener entre 2 y 200 caracteres
                    </li>
                    <li>
                        Recuerde que los campos con (*) asterisco son obligatorios.
                    </li>
                </ul>
                Recomendaciones para la creación de actividades:
                <ul>
                    <li>
                        La descripción de la actividad debe tener entre 2 y 300 caracteres.
                    </li>
                    <li>
                        El nombre del responsable de la actividad debe tener entre 2 y 45 caracteres.
                    </li>
                    <li>
                        Recuerde ingresar las fechas de inicio y fin. la fecha fin no debe ser posterior a la fecha inicio.
                    </li>
                    <li>
                        Puede agregar un porcentaje numérico del 1 a 100 de acuerdo al cumplimiento de la actividad.
                    </li>
                </ul>
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
                            <i class="fastAdd icon_plus_alt " ng-click="showRequirementCrud()"></i>
                        </div>
                    </div>
                    <div class="module-item row displaynone editmodule-item" id="editmodule-item">
                        <div class="col-lg-2 col-md-1 col-sm-1 col-xs-1"></div>
                        <div class="col-lg-8 col-md-7 col-sm-7 col-xs-7">
                            <input ng-model="editmodule.name" type="text" autofocus="autofocus" class="form-control" minlength="2" maxlength="100">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4 text-right">
                            <i class="fastCancel icon_close_alt" ng-click="hideEditModule()"></i>
                            <i class="fastSave icon_check_alt" ng-click="validateModuleCrud('edit')"></i>
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
                                        <textarea id="req-descriptionedit" id="description" ng-model="activity.description" rows="1" type="text" required="required"
                                                  class="form-control" minlength="2" maxlength="300"></textarea>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                        <input ng-model="activity.responsable" type="text" required="required" autofocus="autofocus" class="form-control" minlength="2"
                                               maxlength="45">
                                    </div>                  
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                        <datepicker date-format="dd-MM-yyyy">
                                            <input type="text" ng-model="activity.datestart" class="form-control" onkeydown="return false" placeholder="Elige una fecha"/>
                                        </datepicker>
                                        <!--<input ng-model="activity.datestart" class="form-control" id='datetimepickerstart' />-->
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                        <datepicker date-format="dd-MM-yyyy">
                                            <input type="text" ng-model="activity.dateend" class="form-control" onkeydown="return false" placeholder="Elige una fecha"/>
                                        </datepicker>
                                        <!--<input ng-model="activity.dateend" class="form-control" id='datetimepickerend' />  -->                  
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                        <input type="number" ng-model="activity.porcentage" class="form-control" value="0" id='' minlength="0" maxlength="3"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right" style="font-size: 20px;">
                                <i class="fastCancel icon_close_alt " ng-click="hideRequirementCrud()"></i>
                                <i class="fastSave icon_check_alt " ng-click="validateActivity()"></i>
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
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
                                        <i class="fastCancel icon_close_alt" data-toggle="modal" title="Eliminar" data-target="#deleteModal" ng-click="setDeleteId(requirement.idActivity)"></i>                    {# <i class="fastEdit icon_pencil_alt " ng-click="showEditRequirement(requirement)"></i>#}
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
                                                <datepicker date-format="dd-MM-yyyy">
                                                    <input type="text" ng-model="editactivity.datestart" class="form-control" onkeydown="return false" placeholder="Elige una fecha"/>
                                                </datepicker>                      
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 small-text">
                                                <datepicker date-format="dd-MM-yyyy">
                                                    <input type="text" ng-model="editactivity.dateend" class="form-control" onkeydown="return false" placeholder="Elige una fecha"/>
                                                </datepicker>                     
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 small-text">
                                                <input type="number" ng-model="editactivity.porcentage" class="form-control" value="0" id='' minlength="0" maxlength="3"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
                                        <i class="fastCancel icon_close_alt" ng-click="hideEditRequirement()"></i>
                                        <i class="fastSave icon_check_alt" ng-click="validateActivityEdit()"></i>
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

</section>
</div>
<div class="form-group">
    <div class="col-lg-offset-2 col-lg-10 text-right">
        <a class="btn btn-danger" href="#/">
            Regresar
        </a>
        <a class="btn btn-success" ng-click="validateActionPlan()">
            Guardar
        </a>
    </div>
</div>




<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">¿Está seguro de la eliminación?</h4>
            </div>
            <div class="modal-body">
                Cuando elimine la actividad no podrá recuperar los datos
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" ng-click="deleteActivity()" data-dismiss="modal">Eliminar</button>
            </div>
        </div>
    </div>
</div>



