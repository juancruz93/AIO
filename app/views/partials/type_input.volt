{%  if input.type == "Text" %}
  <div class="form-group" ng-init="contact.{{input.alternativename}} = '{{input.defaultvalue}}' ">
    <label  class="col-sm-4 text-right">{{input.name}}</label>
    <div class="col-sm-8 col-md-8">
      <input type="{{input.type}}" id="asd" name="as" ng-model="contact.{{input.alternativename}}" class="undeline-input" value="{{input.defaultvalue}}"> 
    </div>
  </div>
{% elseif input.type == "Date" %}
  <div class="form-group" ng-init="contact.{{input.alternativename}} = '{{input.defaultvalue}}' ">
    <label class="col-sm-4 col-md-4 text-right">{{input.name}}</label>
    <div class="col-sm-8 col-md-8">
      <input type="text" id="{{input.alternativename}}" name="{{input.alternativename}}" ng-model="contact.{{input.alternativename}}" class="undeline-input" value="{{input.defaultvalue}}"> 
      <span class="add-on input-group-addon">
        <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
  </div>
{% elseif input.type == "Numerical" %}
  <div class="form-group" ng-init="contact.{{input.alternativename}} = '{{input.defaultvalue}}' ">
    <label  class="col-sm-4 text-right">{{input.name}}</label>
    <div class="col-sm-8 col-md-8">
      <input type="number" id="{{input.alternativename}}" name="{{input.alternativename}}" ng-model="contact.{{input.alternativename}}" class="undeline-input" value="{{input.defaultvalue}}"> 
    </div>
  </div>
{% elseif input.type == "TextArea" %}
  <div class="form-group" ng-init="contact.{{input.alternativename}} = '{{input.defaultvalue}}' ">
    <label  class="col-sm-4 text-right">{{input.name}}</label>
    <div class="col-sm-8 col-md-8">
      <textarea  id="{{input.alternativename}}" name="{{input.alternativename}}" ng-model="contact.{{input.alternativename}}" class="undeline-input">{{input.defaultvalue}}</textarea>
    </div>
  </div>
{% elseif input.type == "Select" %}
  <div class="form-group" ng-init="contact.{{input.alternativename}} = '{{input.defaultvalue}}' ">
    <label class="col-sm-4 col-md-4 text-right">{{input.name}}</label>
    <div class="col-sm-8 col-md-8">
      <select class="undeline-input select2 " id="{{input.alternativename}}" name="{{input.alternativename}}" ng-model="contact.{{input.alternativename}}">
        <option  value="">Seleccione</option>
        {% for item in input.value | split %}
          {% if item == input.defaultvalue %}
            <option  value="{{item}}" selected="">{{item}}</option>
          {%else%}
            <option  value="{{item}}">{{item}}</option>
          {%endif%}
        {% endfor %}
      </select>
    </div>
  </div>
{% elseif input.type == "Multiselect" %}
  <div class="form-group" ng-init="contact.{{input.alternativename}} = '{{input.defaultvalue}}' ">
    <label class="col-sm-4 col-md-4 text-right">{{input.name}}</label>
    <div class="col-sm-8 col-md-8">
      <select class="undeline-input select2" multiple="multiple"  id="{{input.alternativename}}" name="{{input.alternativename}}" ng-model="contact.{{input.alternativename}}">
        {% for item in input.value | split %}
          {% if item == input.defaultvalue %}
            <option  value="{{item}}" selected="">{{item}}</option>
          {%endif%}
          <option  value="{{item}}">{{item}}</option>
        {% endfor %}
      </select>
    </div>
  </div>
{% endif %}