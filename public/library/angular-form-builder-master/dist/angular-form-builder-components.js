(function () {
  angular.module('builder.components', ['builder', 'validator.rules']).config([
    '$builderProvider', function ($builderProvider) {
      $builderProvider.registerComponent('textInput', {
        group: 'Default',
        label: 'Text Input',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        validationOptions: [
          {
            label: 'none',
            rule: '/.*/'
          }, {
            label: 'numero',
            rule: '[number]'
          }, {
            label: 'correo',
            rule: '[email]'
          }, {
            label: 'url',
            rule: '[url]'
          }
        ],
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n    <label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n    <div class=\"col-sm-8\">\n        <input type=\"text\" ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\"  id=\"{{formName+index}}\" class=\"form-control\" placeholder=\"{{placeholder}}\"/>\n        <p class='help-block'>{{description}}</p>\n    </div>\n</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        \n\
<label class='control-label'>Titulo</label>\n     \n\
   <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    \n\
</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n     \n\
   <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n     \n\
   <label class='control-label'>Marca de agua</label>\n     \n\
   <input type='text' ng-model=\"placeholder\" class='form-control'/>\n    </div>\n    \n\
<div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n     \n\
       Requerido</label>\n    </div>\n    <div class=\"form-group\" ng-hide=\"objExt.validationOptionsHide\"ng-if=\"validationOptions.length > 0\">\n      \n\
  <label class='control-label'>Validation</label>\n      \n\
  <select ng-model=\"$parent.validation\" class='form-control' ng-options=\"option.rule as option.label for option in validationOptions\"></select>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input ng-hide=\"objExt.noDeleted\"type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    \n\
</div>\n</form>"
      });
      $builderProvider.registerComponent('numberInput', {
        group: 'Default',
        label: 'Text Input',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        validationOptions: [
          {
            label: 'number',
            rule: '[numberEnteros]'
          }
        ],
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n    <label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n    <div class=\"col-sm-8\">\n        <input type=\"number\" ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\" id=\"{{formName+index}}\" class=\"form-control\" placeholder=\"{{placeholder}}\"/>\n        <p class='help-block'>{{description}}</p>\n    </div>\n</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        \n\
<label class='control-label'>Titulo</label>\n     \n\
   <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    \n\
</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n     \n\
   <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n     \n\
   <label class='control-label'>Marca de agua</label>\n     \n\
   <input type='text' ng-model=\"placeholder\" class='form-control'/>\n    </div>\n    \n\
<div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n     \n\
       Requerido</label>\n    </div>\n  <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    \n\
</div>\n</form>"
      });
      $builderProvider.registerComponent('textArea', {
        group: 'Default',
        label: 'Text Area',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n    <label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n    <div class=\"col-sm-8\">\n        <textarea type=\"text\" ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\" id=\"{{formName+index}}\" class=\"form-control\" rows='6' placeholder=\"{{placeholder}}\"/>\n        <p class='help-block'>{{description}}</p>\n    </div>\n</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Tituñp</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Marca de agua</label>\n        <input type='text' ng-model=\"placeholder\" class='form-control'/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido</label>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('dateInput', {
        group: 'Default',
        label: 'Text Area',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        validationOptions: [
          {
            label: 'fecha',
            rule: '[fecha]'
          }
        ],
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n<label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n<div class=\"col-sm-8\" >\n<p class=\"input-group\"><input type=\"text\" class=\"form-control\" validator-group=\"{{formName}}\" ng-model=\"inputText\" ng-change=\"prueba(inputText)\" uib-datepicker-popup=\"yyyy-MM-dd\"  is-open=\"objExt.openen\" datepicker-options=\"dateOptions\" ng-required=\"{{required}}\" close-text=\"Close\" placeholder=\"{{placeholder}}\"/><span class=\"input-group-btn\"><button type=\"button\" class=\"btn btn-default \" ng-click=\"openDatePicker(id)\"><i class=\"glyphicon glyphicon-calendar\"></i></button></span></p>\n<p class='help-block'>{{description}}</p>\n</div>\n</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Titulo</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Marca de agua</label>\n        <input type='text' ng-model=\"placeholder\" class='form-control'/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido</label>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('checkbox', {
        group: 'Default',
        label: 'Checkbox',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        options: ['value one', 'value two'],
        arrayToText: true,
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n    <label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n    <div class=\"col-sm-8\">\n        <input type='hidden' ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\"/>\n        <div class='checkbox' ng-repeat=\"item in options track by $index\">\n            <label><input type='checkbox' ng-model=\"$parent.inputArray[$index]\" value='item'/>\n                {{item}}\n            </label>\n        </div>\n        <p class='help-block'>{{description}}</p>\n    </div>\n</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Titulo</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\" ng-hide=\"objExt.noOptions\">\n        <label class='control-label'>Opciones(Cada linea es una nueva opción)</label>\n        <textarea class=\"form-control\" style=\"resize:none;\" validator=\"[required]\" rows=\"3\" ng-model=\"optionsText\"/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido\n        </label>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('confirmation', {
        group: 'Default',
        label: 'confirmation',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        options: ['Si, Acepto.'],
        arrayToText: true,
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n"+
                    "<label for=\"{{formName+index}}\" class=\"col-sm-12\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n"+
                    "<div class=\"col-sm-12\">\n "+
                      "<input type='hidden' ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\"/>\n"+
                      "<div class='checkbox' ng-repeat=\"item in options track by $index\">\n"+
                        "<label><input type='checkbox' ng-model=\"$parent.inputArray[$index]\" value='item'/>\n"+
                          "{{item}}\n"+
                        "</label>\n"+
                      "</div>\n"+
                      "<p class='help-block'>{{description}}</p>\n"+
                    "</div>\n\n"+
                  "</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Titulo</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\" ng-hide=\"objExt.noOptions\">\n        <label class='control-label'>Opciones(Cada linea es una nueva opción)</label>\n        <textarea class=\"form-control\" style=\"resize:none;\" validator=\"[required]\" rows=\"3\" ng-model=\"optionsText\"/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido\n        </label>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('link', {
        group: 'Default',
        label: 'link',
        description: 'description',
        placeholder: 'placeholder',
        url: '',
        required: false,
        options: ['Si, Acepto.'],
        arrayToText: true,
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n"+
                    "<label for=\"{{formName+index}}\" class=\"col-sm-12\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\"><a href=\"{{url}}\" target=\"_blank\">{{placeholder}}</a></label>\n"+
                    "<div class=\"col-sm-12\">\n "+
                      "<input type='hidden' ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\"/>\n"+
                      "<div class='checkbox' ng-repeat=\"item in options track by $index\">\n"+
                        "<label><input type='checkbox' ng-model=\"$parent.inputArray[$index]\" value='item'/>\n"+
                          "{{item}}\n"+
                        "</label>\n"+
                      "</div>\n"+
                      "<p class='help-block'>{{description}}</p>\n"+
                    "</div>\n\n"+
                  "</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Titulo</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\" ng-hide=\"objExt.noOptions\">\n        <label class='control-label'>Opciones(Cada linea es una nueva opción)</label>\n        <textarea class=\"form-control\" style=\"resize:none;\" validator=\"[required]\" rows=\"3\" ng-model=\"optionsText\"/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido\n        </label>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('encabezado', {
        group: 'Default',
        label: 'Checkbox',
        description: 'description',
        placeholder: 'placeholder',
        template: "<div ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">\n<p ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">{{label}}</p>\n</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('button', {
        group: 'Default',
        label: 'Submit',
        description: 'description',
        placeholder: 'placeholder',
        template: "<div ng-style=\"{'text-align':objExt.fontOrien}\">\n<input id=\"submitButton\"type=\"submit\" ng-disabled=\"disabledSubmit\" class=\"btn\" ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle,'background-color':objExt.colorBackground,'color':objExt.fontColor,'font-weight':(objExt.fontBold) ? 'bold':'normal' }\" ng-value=\"label\"></input>\n</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n<div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class=\"form-group\">\n <label class='control-label'>Fondo:</label>\n <spectrum-colorpicker ng-model=\"objExt.colorBackground\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n </div>\n</form>"
      });
//      $builderProvider.registerComponent('radio', {
//        group: 'Default',
//        label: 'Radio',
//        description: 'description',
//        placeholder: 'placeholder',
//        required: false,
//        options: ['value one', 'value two'],
//        template: "<div class=\"form-group\">\n    <label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\" ng-class=\"{'fb-required':required}\">{{label}}</label>\n    <div class=\"col-sm-8\">\n        <div class='radio' ng-repeat=\"item in options track by $index\">\n            <label><input name='{{formName+index}}' ng-model=\"$parent.inputText\" validator-group=\"{{formName}}\" value='{{item}}' type='radio'/>\n                {{item}}\n            </label>\n        </div>\n        <p class='help-block'>{{description}}</p>\n    </div>\n</div>",
//        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Label</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Description</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Options</label>\n        <textarea class=\"form-control\" style=\"resize:none;\" rows=\"3\" ng-model=\"optionsText\"/>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
//      });
      return $builderProvider.registerComponent('select', {
        group: 'Default',
        label: 'Select',
        description: 'description',
        placeholder: 'placeholder',
        required: false,
        options: ['value one', 'value two'],
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n    <label for=\"{{formName+index}}\" class=\"col-sm-4 control-label\"  ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</label>\n    <div class=\"col-sm-8\">\n        <select ng-options=\"value for value in options\" id=\"{{formName+index}}\" class=\"form-control\"\n            ng-model=\"inputText\" ng-init=\"inputText = options[0]\"/>\n        <p class='help-block'>{{description}}</p>\n    </div>\n</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Titulo</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\" >\n        <label class='control-label'>Optiones(Cada linea es una nueva opción)</label>\n        <textarea style=\"resize:none;\"class=\"form-control\" rows=\"3\" validator=\"[required]\" ng-model=\"optionsText\"/>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
    }
  ]);

}).call(this);
