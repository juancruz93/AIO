var myApp = angular.module('colorpickerApp', ['angularSpectrumColorpicker']);

myApp.controller('ColorPickerCtrl', function($scope) {
  $scope.uschiColor = '#00ff00';
  $scope.onChange = function(color) {
    console.log('onChange event', color);
  };
});
