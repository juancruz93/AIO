(function () {
  angular.module('scheduled.controllers', [])
          .controller('ScheduledController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              
              $scope.initialSMS = 0;
              $scope.pageSMS = 1;
              $scope.initialMail = 0;
              $scope.pageMail = 1;
              $scope.filter = "";

              $scope.getAll = function () {
                if (!$scope.filter) {
                  $scope.filter.name = "1";
                }
                restService.getAll($scope.initialSMS,$scope.initialMail, $scope.filter).then(function (data) {

                  $scope.scheduledSms = data['sms'];
                  $scope.scheduledMail = data['mail'];
                });
              };

              $scope.confirmCancel = function (idMail) {
                  $scope.idMail = idMail;
                  openModalCancel();
              };

              $scope.search = function () {
                $scope.getAll();
              };
              $scope.pauseSmsAn = function (idSms) {
                pauseSms(idSms);
              };

              $scope.cancelSmsAn = function (idSms) {
                cancelSms(idSms);
              };

              $scope.resumeSmsAn = function (idSms) {
                resumeSms(idSms);
              };

              $scope.pauseMailAn = function (idMail) {
                  //console.log(idMail);
                  pauseMail(idMail);
              };

              $scope.cancelMailAn = function (idMail) {
                  cancelMail(idMail);
              };

              $scope.resumeMailAn = function (idMail) {
                  //console.log(idMail);
                  resumeMail(idMail);
              };

              $scope.cancelMail = function () {
                  restService.cancelMail($scope.idMail).then(function (data) {
                      notificationService.warning(data.message);
                      closeModalCancel();
                      $scope.getAll();
                  });
              };

              $scope.forwardSMS = function () {
                $scope.initialSMS += 1;
                $scope.pageSMS += 1;
                $scope.getAll();
              };
              $scope.fastforwardSMS = function () {
                $scope.initialSMS = ($scope.scheduledSms.total_pages - 1);
                $scope.pageSMS = $scope.scheduledSms.total_pages;
                $scope.getAll();
              };
              $scope.backwardSMS = function () {
                $scope.initialSMS -= 1;
                $scope.pageSMS -= 1;
                $scope.getAll();
              };
              $scope.fastbackwardSMS = function () {
                $scope.initialSMS = 0;
                $scope.pageSMS = 1;
                $scope.getAll();
              };
              $scope.forwardMail = function () {
                $scope.initialMail += 1;
                $scope.pageMail += 1;
                $scope.getAll();
              };
              $scope.fastforwardMail = function () {
                $scope.initialMail = ($scope.scheduledMail.total_pages - 1);
                $scope.pageMail = $scope.scheduledMail.total_pages;
                $scope.getAll();
              };
              $scope.backwardMail = function () {
                $scope.initialMail -= 1;
                $scope.pageMail -= 1;
                $scope.getAll();
              };
              $scope.fastbackwardMail = function () {
                $scope.initialMail = 0;
                $scope.pageMail = 1;
                $scope.getAll();
              };

              $scope.getAll();
            }])
})();
