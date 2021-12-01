<form>
  <div class="form-group">
    <label class='control-label'>Label</label>
    <input type='text' ng-model="label" validator="[required]" class='form-control'/>
  </div>
  <div class="form-group">
    <label class='control-label'>Description</label>
    <input type='text' ng-model="description" class='form-control'/>
  </div>
  <div class="form-group">
    <label class='control-label'>Placeholder</label>
    <input type='text' ng-model="placeholder" class='form-control'/>
  </div>
  <div class="checkbox">
    <label>
      <input type='checkbox' ng-model="required" />
      Required</label>
  </div>
  <div class="form-group" ng-if="validationOptions.length > 0">
    <label class='control-label'>Validation</label>
    <select ng-model="$parent.validation" class='form-control' ng-options="option.rule as option.label for option in validationOptions"></select>
  </div>

  <hr/>
  <div class='form-group'>
    <input type='submit' ng-click="popover.save($event)" class='btn btn-primary' value='Save'/>
    <input type='button' ng-click="popover.cancel($event)" class='btn btn-default' value='Cancel'/>
    <input type='button' ng-click="popover.remove($event)" class='btn btn-danger' value='Delete'/>
  </div>
</form>
