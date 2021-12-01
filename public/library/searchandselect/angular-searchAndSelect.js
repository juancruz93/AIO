angular.module('angular-search-and-select', []).directive('searchandselect', function ($rootScope) {
    return {
        replace: true,
        restrict: 'E',
        scope: {
            values: "=",
            selecteditem: "=",
            key: "@",
            onscroll: "&",
            totalrecords: "="
        },
        templateUrl: ´<div class="searchandselect" ng-class="{ active: showList }">
    <div class="header" ng-click="show()">
        <b>{{selecteditem[key]}}</b>
        <span class="pull-right glyphicon" ng:class="{true:'glyphicon-chevron-up', false:'glyphicon-chevron-down'}[showList]"></span>
    </div>
    <div class="search">
        <div class="input-group">
            <input type="text" ng-model="searchKey" class="form-control" placeholder="Type 3 characters to start search" ng-change="textChanged(searchKey)">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-search"></i></button>
            </span>
        </div><!-- /input-group -->
        <div class="text-right nomargin nopadding"><small>Showing records 1 to {{values.length}} of {{totalrecords}}</small></div>

    </div>
    <ul class="dropdown">       
        <li ng-repeat="item in values" ng-click="selectItem(item)" ng-if="values.length > 0">
            <span>{{item[key]}}</span>
            <i class="glyphicon glyphicon-ok" ng-show="isActive(item)"></i>
        </li>
        <li ng-if="values.length == 0">
            No Records
        </li>
    </ul>
</div>´,
        link: function (scope, elm, attr) {

            scope.showList = false;

            scope.selectItem = function (item) {
                scope.selecteditem = item;
                scope.showList = false;
            };

            scope.isActive = function (item) {
                return item[scope.key] === scope.selecteditem[scope.key];
            };

            scope.textChanged = function (searchKey) {
                if (searchKey.length === 0 || searchKey.length > 2) {
                    scope.onscroll({
                        searchKey: searchKey,
                        pagenumber: 1
                    });
                }

            };

            scope.show = function () {
                scope.showList = !scope.showList;
            };

            $rootScope.$on("documentClicked", function (inner, target) {

                var isSearchBox = ($(target[0]).is(".searchandselect")) || ($(target[0]).parents(".searchandselect").length > 0);

                if (!isSearchBox)
                    scope.$apply(function () {
                        scope.showList = false;
                    });
            });

            elm.find(".dropdown").bind('scroll', function () {
                var currentItem = $(this);
                if (currentItem.scrollTop() + currentItem.innerHeight() >= currentItem[0].scrollHeight) {

                    if (!scope.pagenumber) scope.pagenumber = 2;
                    else
                        scope.pagenumber = scope.pagenumber + 1;

                    scope.onscroll({
                        searchKey: scope.searchKey,
                        pagenumber: scope.pagenumber
                    });
                }
            });

        }
    };
});




