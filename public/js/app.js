var app = angular.module('laravelTestWork', ['ui.bootstrap', 'chart.js', 'ngResource']);

app
    .controller('MainCtrl', MainCtrl)
    .controller("LineCtrl", LineCtrl)
    .controller('DataTableCtrl', DataTableCtrl)
    .controller('EditOrderCtrl', EditOrderCtrl)
    .factory('mySharedService', mySharedService)
    .factory('Order', function($resource){
        return $resource('/order/:id', {id:'@id'}, {}, {
            'update': {method: 'PUT', params: {id: ''}}
        });
    })
    .factory('Client', function($resource){
        return $resource('/client/:id', {id:'@id'});
    })
    .factory('Product', function($resource){
        return $resource('/product/:id', {id:'@id'});
    });


function mySharedService($rootScope) {
    var sharedService = {};

    sharedService.search = '';

    sharedService.prepForBroadcast = function(queryString) {
        this.search = queryString;
        this.broadcastItem();
    };

    sharedService.broadcastItem = function() {
        $rootScope.$broadcast('handleBroadcast');
    };

    return sharedService;
}

function MainCtrl($scope, Order, mySharedService, $rootScope){

    $rootScope.pageLoader = true;
    $scope.searchText = '';
    $scope.searchType = 'all';

    $scope.$on('handleBroadcast', function() {
        $scope.search = mySharedService.search;
    });

    $scope.searchSubmit = function () {

        mySharedService.prepForBroadcast($scope.searchType + ':' + $scope.searchText);

        Order.get({
            sortedBy: $scope.sortReverse? 'desc' : 'asc',
            orderBy: $scope.sortType,
            search: $scope.searchType + ':' + $scope.searchText
        }).$promise.then(function(respons) {
            $scope.orders = respons.data;
            $scope.bigTotalItems = respons.total;
            $scope.numPages = respons.last_page;
            $scope.bigPerPage = respons.per_page;
            $scope.bigCurrentPage = respons.current_page;
        });

    }
}

function DataTableCtrl($scope, $http, Order, mySharedService, $uibModal) {

    $scope.orders = [];

    $scope.sortType     = 'orders.id';
    $scope.sortReverse  = false;

    $scope.maxSize = 5;
    $scope.bigTotalItems = 0;
    $scope.bigCurrentPage = 0;
    $scope.bigPerPage = 0;
    $scope.numPages = 0;

    $scope.search = 'all:';

    $scope.$on('handleBroadcast', function() {
        $scope.search = mySharedService.search;
        getOrder(1);
    });

    function getOrder(page) {
        Order.get({
            sortedBy: $scope.sortReverse? 'desc' : 'asc',
            orderBy: $scope.sortType,
            search: $scope.search,
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

    $scope.edit = function (item) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: 'EditOrderCtrl',
            resolve: {
                items: item
            }
        });

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {

        });
    };

    $scope.delete = function (item) {
        if (confirm("Are you sure you want to delete the order?")) {
            Order.delete({id: item.id}).$promise.then(
                function () {
                    getOrder($scope.bigCurrentPage);
                }
            );
        }
    };

    $scope.sortBy = function(fieldForSort){
        $scope.sortType = fieldForSort;
        $scope.sortReverse = !$scope.sortReverse;

        Order.get({
            sortedBy: $scope.sortedBy(),
            orderBy: $scope.sortType,
            page: $scope.bigCurrentPage,
            search: $scope.search
        }).$promise.then(function(data) {
            $scope.orders = data.data;
        });
    };

    $scope.$watch( 'bigCurrentPage', $scope.setPage );

}

function EditOrderCtrl($scope, $uibModalInstance, items, $http, Order, Client, Product) {

    $scope.clients = [];
    $scope.products = [];
    $scope.edit = {};
    $scope.orders = items;

    Client.get().$promise.then(function(respons) {
        $scope.clients = respons.data;
    });

    $scope.getProducts = function (product) {
         Product.get({search: product}).$promise.then(function(respons) {
             $scope.products = respons.data;
         });
        return $scope.products;
    };

    $scope.edit.created_at = items.created_at * 1000;
    $scope.edit.client_name = $scope.orders.client.name;
    $scope.edit.product_name = $scope.orders.product.name;

    $scope.ok = function () {

        $http.put('/order/' + $scope.orders.id, {
            created_at: $scope.edit.created_at/1000,
            client_name: $scope.edit.client_name,
            product_name: $scope.edit.product_name
        }).then(function (respons) {
            items.client = respons.data.data.client;
            items.product = respons.data.data.product;
        });

        $uibModalInstance.close($scope.selected);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.inlineOptions = {
        minDate: new Date()
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        showButtonBar:false,
        startingDay: 1,
        showWeeks:false
    };

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

        return [year, month, day].join('-');
    }

    $scope.open = function() {
        $scope.popup.opened = true;
    };

    $scope.setDate = function(year, month, day) {
        $scope.dt = new Date(year, month, day);
    };

    $scope.format = 'yyyy-MM-dd';

    $scope.popup = { opened: false };
}

function LineCtrl($scope, $http, mySharedService, $rootScope) {

    function initLegend() {
        $scope.labels = [];
        $scope.series = [];
        $scope.data = [];
        $scope.lineBase64 = '';
    }
    initLegend();

    $scope.search = '';

    function getLineData() {
        $http.get('/order/line' + $scope.search).then(function (respons) {
            if(respons.status == 200){

                initLegend();

                $scope.labels = Object.keys(respons.data.labels).map(function (key) {
                    return formattedDate(respons.data.labels[key])}
                );
                $scope.series = Object.keys(respons.data.series);

                var i = 0;
                Object.keys(respons.data.series).map(function (key) {
                    Object.keys(respons.data.series[key]).forEach(function(keyOrder) {
                        $scope.data[i] = $scope.data[i]||[];
                        $scope.data[i].push(respons.data.series[key][keyOrder]);
                    });
                    i++;
                });

                $rootScope.pageLoader = false;

            } else {
                alert('Not results');
                initLegend();
            }
        });
    };

    getLineData();

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

    $scope.sendEmail = function(){
        $scope.lineBase64 = document.getElementById("line").toDataURL();

        $http.post('/email' + $scope.search, {
            image: $scope.lineBase64
        }).then(function () {
            alert('Message has been sent. Please check your inbox.');
        });

    };

    $scope.$on('handleBroadcast', function() {
        $scope.search = '?search=' + mySharedService.search;
        getLineData();
    });

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