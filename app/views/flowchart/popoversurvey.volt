<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap' ng-init="getpublicsurvey();getSender()">
    <div class="form-group" >
        <label for="mailtemplate">*Encuesta publicadas </label><a class="extra-small-text" ng-href="{{'{{selected.hrefCreateSurvey}}'}}" target="_blank">    Crear encuesta</a>
        {#    <select name="mailtemplate" id="mailTemplate" ng-change="changeSelectedMailTemplate(selected.mailtemplate)" ng-model="selected.mailtemplate" style="width: 200px"></select>  #}

        <ui-select name="senderName" ng-model="selected.publicsurvey" ng-change="changeSelectedSurveyTemplate(selected.publicsurvey)" theme="select2" sortable="false"
                   close-on-select="true" style="width: 100%" reset-search-input="false">
            <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
            <ui-select-choices
                repeat="key in publicsurvey track by $index | propsFilter: {name: $select.search}"
                refresh="refreshAddresses($select.search)"
                refresh-delay="0"
                >
                <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
        </ui-select>
    </div>
    <div class="form-group" >
        <label for="mailtemplate">*Plantilla de correo</label>
        {#    <select name="mailtemplate" id="mailTemplate" ng-change="changeSelectedMailTemplate(selected.mailtemplate)" ng-model="selected.mailtemplate" style="width: 200px"></select>  #}

        <ui-select name="senderName" ng-model="selected.mailtemplate" ng-change="changeSelectedMailTemplate(selected.mailtemplate)" theme="select2" sortable="false"
                   close-on-select="true" style="width: 100%" reset-search-input="false">
            <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
            <ui-select-choices
                repeat="key in listSMailTemplate track by $index | propsFilter: {name: $select.search}"
                refresh="refreshAddresses($select.search)"
                refresh-delay="0"
                >
                <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
        </ui-select>

    </div>
    <div class="form-group" >
        <label for="mailCategory">*Categoria de correo</label>
        {#    <select name="mailCategory" id="mailCategory"  ng-model="selected.mailcategory" style="width: 200px"></select>  #}
        <ui-select name="senderName" ng-model="selected.mailcategory" theme="select2" sortable="false"
                   close-on-select="true" style="width: 100%">
            <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
            <ui-select-choices
                repeat="key in listSMailCategory | propsFilter: {name: $select.search}">
                <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
        </ui-select>
    </div>
    <div class="form-group">
        <label for="senderName">*Nombre del remitente</label>
        <ui-select name="senderName" ng-model="selected.senderName" theme="select2" sortable="false"
                   close-on-select="true" style="width: 100%">
            <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
            <ui-select-choices
                repeat="key as key in emailname | propsFilter: {name: $select.search}">
                <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
        </ui-select>
    </div>
    <div class="form-group">
        <label for="senderEmail">*Correo del remitente</label>
        <ui-select name="senderEmail" ng-model="selected.senderEmail" theme="select2"
                   sortable="false" close-on-select="true" style="width: 100%">
            <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.email}}" }}</ui-select-match>
            <ui-select-choices
                repeat="key as key in emailsend | propsFilter: {email: $select.search}">
                <div ng-bind-html="key.email | highlight: $select.search"></div>
            </ui-select-choices>
        </ui-select> 
    </div>

    <div class="form-group" >
        <label for="subject">*Asunto</label>
        <input name="subject" type="text" placeholder="Asunto"  class="undeline-input" ng-model="selected.subject"/>
    </div>

    <div class="form-group" >
        <label for="replyto">Responder a</label>
        <input name="replyto" type="text" placeholder="Responder a"  class="undeline-input" ng-model="selected.replyto"/>
    </div>

    <div class="form-group" >
        <a ng-href="{{'{{selected.hrefSelectedSurvey}}'}}" target="_blank" ng-show="selected.flagSelected">Editar encuesta</a>
    </div>
    <div class="form-group" >
        <p class="text-danger" ng-show="selected.error">Todos los campos son obligarotios.</p>
    </div>
    <div class="clearfix"></div>

    <div class="footer" align="right">                                                
        <a  ng-click="closePopover()" class="danger-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <i class="fa fa-times "></i>
        </a>
        <a  ng-click="refreshData()" class="primary-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Actualizar">
            <i ng-class="classRefreshrotate ? 'fa fa-refresh fa-spin fa-fw': 'fa fa-refresh fa-fw'"></i>
        </a>
        <a ng-click="applyListSelectedSurvey()" class="success-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Guardar">
            <i class="fa fa-check"></i>
        </a>
    </div>
</div>





