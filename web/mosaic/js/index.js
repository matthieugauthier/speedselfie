var app = angular.module('app', ['ngMaterial', 'angularGrid']);

app.controller('MainCtrl', function ($scope, $http, $q,$interval) {
    var vm = this;
    $scope.card = {};
    $scope.card.title = 'test';

    vm.shots = [];
    vm.loadingMore = false;

    vm.loadMoreShots = function () {

        if (vm.loadingMore) return;

        // var deferred = $q.defer();
        vm.loadingMore = true;
        var promise = $http.get('/api/mosaic');
        promise.then(function (data) {

            vm.shots = data.data;
            vm.loadingMore = false;
            //setTimeout(vm.loadMoreShots(),5000);

        }, function () {
            vm.loadingMore = false;
        });
        return promise;
    };

    vm.loadMoreShots();
    $interval(function () {vm.loadMoreShots();},5000);

});
