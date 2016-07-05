var app = angular.module('laravelTestWork', ['ui.bootstrap', 'chart.js', 'ngResource']);

app
    .controller('MainCtrl', MainCtrl)
    .controller("LineCtrl", LineCtrl)
    .controller('DataTableCtrl', DataTableCtrl)
    .factory('Order', function($resource){
        return $resource('/order/:id?orderBy=:orderBy&sortedBy=:sortedBy&page=:page', {
            id:'@id',
            orderBy: '@orderBy',
            sortedBy: '@sortedBy',
            page: '@page'
        });
    });


function MainCtrl($scope, $http){
    $scope.searchText = '';
    $scope.searchType = 'all';
    $scope.searchSubmit = function () {
        console.log($scope.searchText);
        console.log($scope.searchType);
    }
}

function DataTableCtrl($scope, $http, Order) {

    $scope.orders = [];

    $scope.sortType     = 'id';
    $scope.sortReverse  = false;

    $scope.maxSize = 5;
    $scope.bigTotalItems = 0;
    $scope.bigCurrentPage = 0;
    $scope.bigPerPage = 0;
    $scope.numPages = 0;

    function getOrder(page) {
        Order.get({
            sortedBy: $scope.sortReverse? 'desc' : 'asc',
            orderBy: $scope.sortType,
            page: page
        }).$promise.then(function(respons) {
            $scope.orders = respons.data;
            $scope.bigTotalItems = respons.total;
            $scope.numPages = respons.last_page;
            $scope.bigPerPage = respons.per_page;
            $scope.bigCurrentPage = respons.current_page;
        });
    }

    getOrder($scope.bigCurrentPage);

    $scope.setPage = function (pageNo) {
        getOrder(pageNo);
    };

    $scope.sortedBy = function(){
        return $scope.sortReverse? 'desc' : 'asc';
    };

    $scope.sortBy = function(fieldForSort){
        $scope.sortType = fieldForSort;
        $scope.sortReverse = !$scope.sortReverse;

        Order.get({
            sortedBy: $scope.sortedBy(),
            orderBy: $scope.sortType,
            page: $scope.bigCurrentPage
        }).$promise.then(function(data) {
            $scope.orders = data.data;
        });
    };

    $scope.$watch( 'bigCurrentPage', $scope.setPage );

}

function LineCtrl($scope, $http) {


    $scope.labels = [];
    $scope.series = [];
    $scope.data = [];

    function getLineData() {
        $http.get('/order/line').then(function (respons) {
            if(respons.status == 200){
                $scope.labels = Object.keys(respons.data.labels).map(function (key) {return formattedDate(respons.data.labels[key])});
                $scope.series = Object.keys(respons.data.series);

                var i = 0;
                Object.keys(respons.data.series).map(function (key) {
                    Object.keys(respons.data.series[key]).forEach(function(keyOrder) {
                        $scope.data[i] = $scope.data[i]||[];
                        $scope.data[i].push(respons.data.series[key][keyOrder]);
                    });
                    i++;
                });
            }
        });
    };
    getLineData();

    $scope.lineBase64 = '';

    $scope.onClick = function (points, evt) {
        console.log(points, evt);
    };
    $scope.datasetOverride = [{ yAxisID: 'y-axis-1' }, { yAxisID: 'y-axis-2' }];
    $scope.options = {
        scales: {
            yAxes: [
                {
                    id: 'y-axis-1',
                    type: 'linear',
                    display: true,
                    position: 'left'
                },
                {
                    id: 'y-axis-2',
                    type: 'linear',
                    display: true,
                    position: 'right'
                }
            ]
        }
    };

    $scope.setImageLine = function(){
        $scope.lineBase64 = document.getElementById("line").toDataURL();
    }

    function formattedDate(date) {

        if(date instanceof(Date)){
            var d = date;
        } else {
            date = date * 1000;
            var d = new Date(date || Date.now());
        }
        var month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [day, month, year].join('/');
    }

};

/**
 *  Section Factory Start
 **/

function Order($resource) {
    return $resource('/order/:id', { id: '@_id' }, {
        update: {
            method: 'PUT'
        }
    });
}

/**
 *  Section Factory End
 **/