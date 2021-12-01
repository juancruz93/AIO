<header>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Esquema de la campaña {{"{{campaign.nameCampaign}}"}}
      </div>            
      <hr class="basic-line" />
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
      <a ui-sref="index" class="btn default-inverted"><i class="fa fa-arrow-left"></i> Regresar</a>
    </div>
  </div>
</header>
<br>
<section>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 block-basic none-padding no-click">
      <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1 none-padding no-click">
        <div class="list-group " >
          <a class="list-group-item " ng-repeat="item in items" ng-class="item.class" ng-click="addNewNode(item,item.image,item.template)" ng-show="item.name != 'Tiempo'">
            <i ng-if="item.icon" ng-class="item.iconClass"></i>
            <div ng-if="!item.icon">{{'{{item.name}}'}}</div> 
            <md-tooltip ng-if="item.icon" md-direction="right">
              {{'{{item.name}}'}}
            </md-tooltip>
          </a>
        </div>
      </div>
      <md-progress-linear md-mode="query" ng-if="!complet" class="md-warn"></md-progress-linear>
      <div class="col-xs-12 col-sm-11 col-md-11 col-lg-11 none-padding-left no-click" mouse-capture
           ng-keydown="keyDown($event)"
           ng-keyup="keyUp($event)" ng-if="complet">
        <flow-chart  style="margin: 5px; width: 100%; height: 4000px;" chart="chartViewModel"></flow-chart>
      </div>
    </div>
  </div>
</section>

<script>

  function closeModal() {
    $('#dialogDeleteNode').removeClass('dialog--open');
  }

  function closeModalForm() {
    $('#updateAutomaticCampaign').removeClass('dialog--open');
  }
</script>    
