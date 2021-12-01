(function () {
  angular.module('builder.components', ['builder', 'validator.rules']).config([
    '$builderProvider', function ($builderProvider) {
      $builderProvider.registerComponent('radio', {
        objExt: {configItem: {nameComponent: 'radio', viewAdd: false, id: "radio", title: "Selección única", icon: "radio-button.png"}, index: 0, anotherAnswer: false},
        group: 'Default',
        label: 'Edite su pregunta de selección única',
        description: 'Descripción o especificación de la pregunta',
        placeholder: 'Marca de agua',
        required: false,
        options: ['Amarillo', 'Azul'],
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n" +
                "<label for=\"{{formName+index}}\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{objExt.index}}. {{label}}</label>\n" +
                "<div class='radio' ng-repeat=\"item in options track by $index\">\n" +
                "<label ng-style=\"{'color':objExt.fontColor, 'font-family':objExt.fontStyle}\" > " +
                "<input name='{{formName+index}}' ng-model=\"$parent.inputText\" validator-group=\"{{formName}}\" value='{{item}}' type='radio'/>\n{{item}}\n" +
                "</label>\n" +
                "</div>\n" +
                "<div class='radio' ng-if=\"objExt.anotherAnswer\">\n" +
                "<label ng-style=\"{'color':objExt.fontColor, 'font-family':objExt.fontStyle}\" > " +
                "<input name='{{formName+index}}' ng-model=\"$parent.inputText\" validator-group=\"{{formName}}\" value='other' type='radio'/>\nOtro (Especifique)\n" +
                "</label>\n" +
                "<div  id=\"{{formName+id}}anotherAnswer\">" +
                "<input name='{{formName+index}}' class=\"form-control\" ng-model=\"objExt.inputTextOther\" validator-group=\"{{formName}}\" type='text'/>\n" +
                "</div>" +
                "</div>\n" +
                "<span class='help-block'>{{description}}</span>\n" +
                "</div>\n",
        popoverTemplate: "<form>\n" +
                "<div class=\"form-group\">\n" +
                "<label class='control-label'>Pregunta</label>\n" +
                "<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n" +
                "</div>\n" +
                "<div class=\"form-group\">\n" +
                "<label class='control-label'>Descripción</label>\n" +
                "<input type='text' ng-model=\"description\" class='form-control'/>\n" +
                "</div>\n" +
                "<div class=\"form-group\">\n" +
                "<label class='control-label'>Opciones <br>(Cada linea es una nueva opción)</label>\n" +
                "<textarea class=\"form-control\" style=\"resize:none;\" rows=\"3\" ng-model=\"optionsText\"/>\n" +
                "</div>\n" +
                "<div class=\"checkbox\">\n" +
                "<label>\n" +
                "<input type='checkbox' ng-model=\"required\" />\n Requerido" +
                "</label>\n" +
                "</div>\n\n" +
                "<div class=\"form-group\">\n" +
                "<button class=\"btn btn-warning\" ng-click=\"conditionalModal.show($event)\" >Lógica (condicional)</button>\n" +
                "</div>\n\n" +
                "<div class=\"checkbox\">\n" +
                "<label>\n" +
                "<input type='checkbox' ng-model=\"objExt.anotherAnswer\" />\n Otra respuesta" +
                "</label>\n" +
                "</div>" +
                "<hr/>\n" +
                "<div class='form-group'>\n" +
                "<input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n" +
                "<input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n" +
                "<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n" +
                "</div>\n" +
                "</form>"
      });
      $builderProvider.registerComponent('checkbox', {
        objExt: {configItem: {nameComponent: 'checkbox', viewAdd: false, id: "checkbox", title: "Opción multiple", icon: "checked.png"}, index: 0, anotherAnswer: false, inputTextOther: ""},
        group: 'Default',
        label: 'Edite su pregunta de opción multiple',
        description: 'Descripción o especificación de la pregunta',
        placeholder: 'Marca de agua',
        required: false,
        options: ['Valor 1', 'Valor 2'],
        arrayToText: true,
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n" +
                "<label for=\"{{formName+index}}\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{objExt.index}}. {{label}}</label>\n" +
                "<input type='hidden' ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\"/>\n" +
                "<div class='checkbox' ng-repeat=\"item in options track by $index\">\n" +
                "<label ng-style=\"{'color':objExt.fontColor,'font-family':objExt.fontStyle}\">" +
                "<input type='checkbox' ng-model=\"$parent.inputArray[$index]\" value='item'/>\n{{item}}\n" +
                "</label>\n" +
                "</div>\n" +
                "<div class=\"form-group\" ng-if=\"objExt.anotherAnswer\">" +
                "<label>Otro (especifique)</label>" +
                "<input name='{{formName+index}}' class=\"form-control\" ng-model=\"objExt.inputTextOther\" validator-group=\"{{formName}}\" type='text'/>\n" +
                "</div>" +
                "<span class='help-block'>{{description}}</span>\n" +
                "</div>\n",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Pregunta</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\" ng-hide=\"objExt.noOptions\">\n        <label class='control-label'>Opciones <br>(Cada linea es una nueva opción)</label>\n        <textarea class=\"form-control\" style=\"resize:none;\" validator=\"[required]\" rows=\"3\" ng-model=\"optionsText\"/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido\n        </label>\n    </div>\n\n<div class=\"checkbox\">\n\<label>\n\<input type='checkbox' ng-model=\"objExt.anotherAnswer\" />\n Otra respuesta</label>\n</div>   <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('select', {
        objExt: {configItem: {nameComponent: 'select', viewAdd: false, id: "select", title: "Menú desplegable", icon: "select.png"}, index: 0, anotherAnswer: false},
        group: 'Default',
        label: 'Edite su pregunta estilo menú desplegable',
        description: 'Descripción o especificación de la pregunta',
        placeholder: 'Marca de agua',
        required: false,
        options: ['Valor 1', 'Valor 2'],
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n" +
                "<label for=\"{{formName+index}}\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{objExt.index}}. {{label}}</label>\n" +
                "<select  id=\"{{formName+index}}\" class=\"form-control\" ng-model=\"inputText\" ng-init=\"inputText = options[0]\">\n" +
                "<option ng-repeat=\"value in options\" value=\"{{value}}\">{{value}}</option>" +
                "<option ng-if=\"objExt.anotherAnswer\" value=\"other\">Otro (especifique)</option>" +
                "</select>" +
                "<div ng-if=\"objExt.anotherAnswer\">"+
                "<div class=\"form-group\" id=\"{{formName+id}}anotherAnswer\">" +
                "<label>Especifique</label>"+
                "<input name='{{formName+index}}' class=\"form-control\" ng-model=\"objExt.inputTextOther\" validator-group=\"{{formName}}\" type='text'/>\n" +
                "</div>" +
                "</div>" +
                "<span class='help-block'>{{description}}</span>\n" +
                "</div>",
        popoverTemplate: "<form>\n" +
                "<div class=\"form-group\">\n" +
                "<label class='control-label'>Pregunta</label>\n" +
                "<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n" +
                "</div>\n" +
                "<div class=\"form-group\">\n" +
                "<label class='control-label'>Descripción</label>\n" +
                "<input type='text' ng-model=\"description\" class='form-control'/>\n" +
                "</div>\n" +
                "<div class=\"form-group\" >\n" +
                "<label class='control-label'>Opciones <br>(Cada linea es una nueva opción)</label>\n" +
                "<textarea style=\"resize:none;\"class=\"form-control\" rows=\"3\" validator=\"[required]\" ng-model=\"optionsText\"/>\n" +
                "</div>\n\n" +
                "<div class=\"checkbox\">\n" +
                "<label>\n" +
                "<input type='checkbox' ng-model=\"objExt.anotherAnswer\" />\n Otra respuesta" +
                "</label>\n" +
                "</div>" +
                "<hr/>\n" +
                "<div class='form-group'>\n" +
                "<input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n" +
                "<input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n" +
                "<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n" +
                "</div>\n" +
                "</form>"
      });
      $builderProvider.registerComponent('textArea', {
        objExt: {configItem: {nameComponent: 'textArea', viewAdd: false, id: "textarea", title: "Cuadro de texto", icon: "text-box.png"}, index: 0},
        group: 'Default',
        label: 'Edite su pregunta de respuesta abierta',
        description: 'Descripción',
        placeholder: 'Escriba su respuesta aquí',
        required: false,
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">\n" +
                "<label for=\"{{formName+index}}\" ng-class=\"{'fb-required':required}\" ng-style=\"{'color':objExt.fontColor,'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{objExt.index}}. {{label}}</label>\n" +
                "<textarea type=\"text\" ng-model=\"inputText\" validator-required=\"{{required}}\" validator-group=\"{{formName}}\" id=\"{{formName+index}}\" class=\"form-control no-resize-textarea\" rows='3' placeholder=\"{{placeholder}}\"/>\n" +
                "<span class='help-block'>{{description}}</span>\n" +
                "</div>",
        popoverTemplate: "<form>\n    <div class=\"form-group\">\n        <label class='control-label'>Pregunta</label>\n        <input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Descripción</label>\n        <input type='text' ng-model=\"description\" class='form-control'/>\n    </div>\n    <div class=\"form-group\">\n        <label class='control-label'>Marca de agua</label>\n        <input type='text' ng-model=\"placeholder\" class='form-control'/>\n    </div>\n    <div class=\"checkbox\">\n        <label>\n            <input type='checkbox' ng-model=\"required\" />\n            Requerido</label>\n    </div>\n\n    <hr/>\n    <div class='form-group'>\n        <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n        <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n    </div>\n</form>"
      });
      $builderProvider.registerComponent('encabezado', {
        objExt: {configItem: {viewAdd: false, id: "encabezado", title: "Encabezado", icon: "text-box.png", hide: true},notDb:true},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template: "<div id=\"{{formName+id}}\" ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">\n<p ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">{{label}}</p>\n</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('paragraph', {
        objExt: {configItem: {nameComponent: 'paragraph', viewAdd: false, id: "parrafo", title: "Parrafo", icon: "justify.png"},notDb:true},
        group: 'Default',
        label: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.',
        description: 'Head',
        placeholder: 'Playman',
        template: "<div id=\"{{formName+id}}\" ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">\n<p ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">{{label}}</p>\n</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('button', {
        objExt: {configItem: {viewAdd: false, id: "button", title: "Encabezado", icon: "text-box.png"},notDb:true},
        group: 'Default',
        label: 'Submit',
        description: 'description',
        placeholder: 'placeholder',
        template: "<div id=\"{{formName+id}}\" ng-class=\"form-group\"ng-style=\"{'text-align':objExt.fontOrien}\">\n<input id=\"submitButton\"type=\"submit\" ng-disabled=\"disabledSubmit\" class=\"btn\" ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle,'background-color':objExt.colorBackground,'color':objExt.fontColor,'font-weight':(objExt.fontBold) ? 'bold':'normal' }\" ng-value=\"label\"></input>\n</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n<div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class=\"form-group\">\n <label class='control-label'>Fondo:</label>\n <spectrum-colorpicker ng-model=\"objExt.colorBackground\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('encabezado-logo', {
        objExt: {configItem: {viewAdd: false, id: "encabezado", title: "Encabezado", icon: "text-box.png", hide: true},notDb:true,location:"vertical-align"},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template:
                "<div class=\"form-group\" id=\"{{formName+id}}\">" +
                "<div class=\"row {{objExt.location}} \">" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">" +
                "<p ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</p>" +
                "</div>" +
                "</div>" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'float':objExt.fontOrienImg}\">"+
                "<img class='img-responsive' ng-style=\"{'width':objExt.widthImg,'heigth':objExt.widthImg}\" ng-src='{{objExt.srcImage}}'></img>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n\n\
<div class=\"form-group\">\n\
                                <label class='control-label'>Seleccionar imagen</label>\n\
                                <button class=\"btn btn-warning\" ng-click=\"showModalImage.show($event)\" >Cambiar Imagen</button>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Tamaño de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.widthImg\">\n\
                                  <option value=''>Tamaño de la imagen</option>\n\
                                  <option value='100px'>Pequeño (100px)</option>\n\
                                  <option value='200px'>Mediano (200px)</option>\n\
                                  <option value='300px'>Grande (300px)</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Ubicación de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.location\">\n\
                                  <option value='vertical-align'>Horizontal</option>\n\
                                  <option value=''>Vertical</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Posición de la imagen</label>\n\
                                <label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n\n\
                                  <input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='left' style=\"display:none;\">\n\n\
                                  <span class=\"glyphicon glyphicon-align-left\">\n</span>\n\n\
                                </label>\n\n\
                                <label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n\n\
                                  <input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='right' style=\"display:none;\">\n\n\
                                  <span class=\"glyphicon glyphicon-align-right\"></span>\n\n\
                                </label>\n\
                              </div>\n\
                              <hr/>\n\n\
                              <div class='form-group'>\n \n\
                                <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        \n\
                                <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n\n\
                                <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n \n\
                              </div>\n\n\
                            </form>"
      });
      $builderProvider.registerComponent('logo-encabezado', {
        objExt: {configItem: {viewAdd: false, id: "encabezado", title: "Encabezado", icon: "text-box.png", hide: true},notDb:true,location:"vertical-align"},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template:
                "<div class=\"form-group\" id=\"{{formName+id}}\">" +
                "<div class=\"row {{objExt.location}}\">" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'float':objExt.fontOrienImg}\">"+
                "<img class='img-responsive' ng-style=\"{'width':objExt.widthImg,'heigth':objExt.widthImg}\" ng-src='{{objExt.srcImage}}'></img>" +
                "</div>" +
                "</div>" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">" +
                "<p ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</p>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n <div class=\"form-group\">\n\
                                <label class='control-label'>Seleccionar imagen</label>\n\
                                <button class=\"btn btn-warning\" ng-click=\"showModalImage.show($event)\" >Cambiar Imagen</button>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Tamaño de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.widthImg\">\n\
                                  <option value=''>Tamaño de la imagen</option>\n\
                                  <option value='100px'>Pequeño (100px)</option>\n\
                                  <option value='200px'>Mediano (200px)</option>\n\
                                  <option value='300px'>Grande (300px)</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Ubicación de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.location\">\n\
                                  <option value='vertical-align'>Horizontal</option>\n\
                                  <option value=''>Vertical</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Posición de la imagen</label>\n\
                                <label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n\n\
                                  <input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='left' style=\"display:none;\">\n\n\
                                  <span class=\"glyphicon glyphicon-align-left\">\n</span>\n\n\
                                </label>\n\n\
                                <label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n\n\
                                  <input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='right' style=\"display:none;\">\n\n\
                                  <span class=\"glyphicon glyphicon-align-right\"></span>\n\n\
                                </label>\n\
                              </div>\n\
                              \n\<hr/>\n\n\
                              <div class='form-group'>\n \n\
                                <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        \n\
                                <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n\n\
                                <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n \n\
                              </div>\n\n\
                            </form>"
      });
      $builderProvider.registerComponent('logo', {
        objExt: {configItem: {viewAdd: false, id: "encabezado", title: "Encabezado", icon: "text-box.png", hide: true},notDb:true},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">" +
                "<div class=\"row\">" +
                "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
                "<div ng-style=\"{'float':objExt.fontOrienImg}\">"+
                "<img class='img-responsive' ng-style=\"{'width':objExt.widthImg,'heigth':objExt.widthImg}\" ng-src='{{objExt.srcImage}}'></img>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
        popoverTemplate: "<form>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Seleccionar imagen</label>\n\
                                <button class=\"btn btn-warning\" ng-click=\"showModalImage.show($event)\" >Cambiar Imagen</button>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Tamaño de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.widthImg\">\n\
                                  <option value=''>Tamaño de la imagen</option>\n\
                                  <option value='100px'>Pequeño (100px)</option>\n\
                                  <option value='200px'>Mediano (200px)</option>\n\
                                  <option value='300px'>Grande (300px)</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Posición de la imagen</label>\n\
                                <label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n\n\
                                  <input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='left' style=\"display:none;\">\n\n\
                                  <span class=\"glyphicon glyphicon-align-left\">\n</span>\n\n\
                                </label>\n\n\
                                <label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n\n\
                                  <input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='right' style=\"display:none;\">\n\n\
                                  <span class=\"glyphicon glyphicon-align-right\"></span>\n\n\
                                </label>\n\
                              </div>\n\
                              <hr/>\n\
                              <div class='form-group'>\n\
                                <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n\
                                <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n\
                                <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n\
                              </div>\n\
                          </form>"
      });
      $builderProvider.registerComponent('footer', {
        objExt: {configItem: {viewAdd: false, id: "footer", title: "Encabezado", icon: "text-box.png", hide: true},notDb:true},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template: "<div id=\"{{formName+id}}\" ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">\n<p ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">{{label}}</p>\n</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('footer-logo', {
        objExt: {configItem: {viewAdd: false, id: "footer", title: "Pie Pagina / Logo", icon: "text-box.png", hide: true},notDb:true,location:"vertical-align"},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template:
                "<div class=\"form-group\" id=\"{{formName+id}}\">" +
                "<div class=\"row {{objExt.location}}\">" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">" +
                "<p ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</p>" +
                "</div>" +
                "</div>" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'float':objExt.fontOrienImg}\">"+
                "<img class='img-responsive' ng-style=\"{'width':objExt.widthImg,'heigth':objExt.widthImg}\" ng-src='{{objExt.srcImage}}'></img>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n\n\
<div class=\"form-group\">\n\
                                <label class='control-label'>Seleccionar imagen</label>\n\
                                <button class=\"btn btn-warning\" ng-click=\"showModalImage.show($event)\" >Cambiar Imagen</button>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Tamaño de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.widthImg\">\n\
                                  <option value=''>Tamaño de la imagen</option>\n\
                                  <option value='100px'>Pequeño (100px)</option>\n\
                                  <option value='200px'>Mediano (200px)</option>\n\
                                  <option value='300px'>Grande (300px)</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Ubicación de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.location\">\n\
                                  <option value='vertical-align'>Horizontal</option>\n\
                                  <option value=''>Vertical</option>\n\
                                </select>\n\
                              </div>\n\
\n\<div class=\"form-group\">\n\
                               <label class='control-label'>Posición de la imagen</label>\n\
\n\<label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n\
</div>\n\
\n\<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('logo-footer', {
        objExt: {configItem: {viewAdd: false, id: "footer", title: "Logo/Pie Pagina", icon: "text-box.png", hide: true},notDb:true,location:"vertical-align"},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template:
                "<div class=\"form-group\" id=\"{{formName+id}}\">" +
                "<div class=\"row {{objExt.location}}\">" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'float':objExt.fontOrienImg}\">"+
                "<img class='img-responsive' ng-style=\"{'width':objExt.widthImg,'heigth':objExt.widthImg}\" ng-src='{{objExt.srcImage}}'></img>" +
                "</div>" +
                "</div>" +
                "<div class=\"col-xs-12 col-sm-12 {{ objExt.location != '' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }}\">" +
                "<div ng-style=\"{'text-align':objExt.fontOrien,'font-weight':(objExt.fontBold) ? 'bold':'normal','color':objExt.fontColor}\">" +
                "<p ng-style=\"{'font-size':objExt.sizeStyle,'font-family':objExt.fontStyle}\">{{label}}</p>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
        popoverTemplate: "<form>\n<div class=\"form-group\">\n<label class='control-label'>Titulo</label>\n<input type='text' ng-model=\"label\" validator=\"[required]\" class='form-control'/>\n</div>\n    <div class=\"form-group\">\n        <label class='control-label'>Tamaño</label>\n <select class='form-control' ng-model=\"objExt.sizeStyle\">\n<option value='8px'>8</option>\n<option value='10px'>10</option>\n<option value='12px'>12</option>\n<option value='14px'>14</option>\n<option value='18px'>18</option>\n<option value='24px'>24</option>\n<option value='36px'>36</option>\n</select>\n    </div>\n  <div class=\"form-group\">\n <label class='control-label'>Tipografia</label>\n <select class='form-control' ng-model='objExt.fontStyle'><option value='Arial'>Arial</option><option value='Courier New'>Courier New</option><option value='Verdana'>Verdana</option><option value='Comic Sans MS'>Comic Sans MS</option><option value='Georgia'>Georgia</option><option value='Times New Roman' >Times New Roman</option></select></div>\n<div class='form-group'>\n<label class='btn' ng-class=\"objExt.fontBold ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"checkbox\" ng-model=\"objExt.fontBold\"  style=\"display:none;\">\n<span class=\"glyphicon glyphicon-bold\"></span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'center') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='center' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-center\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrien == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrien\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n<spectrum-colorpicker ng-model=\"objExt.fontColor\" format=\"'rgb'\" options=\"{showInput: true,showAlpha: true,allowEmpty:true,showPalette: true,palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">\n</spectrum-colorpicker></div>\n <div class=\"form-group\">\n\
                                <label class='control-label'>Seleccionar imagen</label>\n\
                                <button class=\"btn btn-warning\" ng-click=\"showModalImage.show($event)\" >Cambiar Imagen</button>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Tamaño de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.widthImg\">\n\
                                  <option value=''>Tamaño de la imagen</option>\n\
                                  <option value='100px'>Pequeño (100px)</option>\n\
                                  <option value='200px'>Mediano (200px)</option>\n\
                                  <option value='300px'>Grande (300px)</option>\n\
                                </select>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Ubicación de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.location\">\n\
                                  <option value='vertical-align'>Horizontal</option>\n\
                                  <option value=''>Vertical</option>\n\
                                </select>\n\
                              </div>\n\
\n\<div class=\"form-group\">\n\
                               <label class='control-label'>Posición de la imagen</label>\n\
\n\<label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n\
</div>\n\
\n\<hr/>\n<div class='form-group'>\n <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n        <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n<input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n </div>\n</form>"
      });
      $builderProvider.registerComponent('logofooter', {
        objExt: {configItem: {viewAdd: false, id: "footer", title: "Encabezado", icon: "text-box.png", hide: true},notDb:true},
        group: 'Default',
        label: 'Header',
        description: 'Head',
        placeholder: 'Playman',
        template: "<div class=\"form-group\" id=\"{{formName+id}}\">" +
                "<div class=\"row\">" +
                "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
                "<div ng-style=\"{'float':objExt.fontOrienImg}\">"+
                "<img class='img-responsive' ng-style=\"{'width':objExt.widthImg,'heigth':objExt.widthImg}\" ng-src='{{objExt.srcImage}}'></img>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
        popoverTemplate: "<form>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Seleccionar imagen</label>\n\
                                <button class=\"btn btn-warning\" ng-click=\"showModalImage.show($event)\" >Cambiar Imagen</button>\n\
                              </div>\n\
                              <div class=\"form-group\">\n\
                                <label class='control-label'>Tamaño de la imagen</label>\n\
                                <select class='form-control' ng-model=\"objExt.widthImg\">\n\
                                  <option value=''>Tamaño de la imagen</option>\n\
                                  <option value='100px'>Pequeño (100px)</option>\n\
                                  <option value='200px'>Mediano (200px)</option>\n\
                                  <option value='300px'>Grande (300px)</option>\n\
                                </select>\n\
                              </div>\n\
\n\<div class=\"form-group\">\n\
                               <label class='control-label'>Posición de la imagen</label>\n\
\n\<label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'left') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='left' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-left\">\n</span>\n</label>\n<label class=\"btn\" ng-class=\"(objExt.fontOrienImg == 'right') ? 'info-inverted':'default-inverted'\" style=\"padding: 6px 6px 1px 6px;border-radius: 30px;\">\n<input type=\"radio\" ng-model=\"objExt.fontOrienImg\" value='right' style=\"display:none;\">\n<span class=\"glyphicon glyphicon-align-right\"></span>\n</label>\n\
</div>\n\
                              <hr/>\n\
                              <div class='form-group'>\n\
                                <input type='submit' ng-click=\"popover.save($event)\" class='btn btn-primary btn-sm' value='Guardar'/>\n\
                                <input type='button' ng-click=\"popover.cancel($event)\" class='btn btn-default btn-sm' value='Cancelar'/>\n\
                                <input type='button' ng-click=\"popover.remove($event)\" class='btn btn-danger btn-sm' value='Remover'/>\n\
                              </div>\n\
                          </form>"
      });
    }
  ]);

}).call(this);
