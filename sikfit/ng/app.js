var app = angular.module('sikfit', ['ngRoute']);

app.config(['$routeProvider', function($routeProvider){
	$routeProvider.when("/", {
		templateUrl: "ng/views/home.html",
		controller: "HomeController"
	}).otherwise({
		redirectTo: "/"
	});
}]);

app.controller('HomeController', function(feed, $scope){

	$scope.feed = {};
	$scope.mobil_feed = {};
	var mobil_data;

	feed.get_today_feed().success(function(data){
		mobil_data = data;
		data.list[data.keys[0]].active = "active-day";
		$scope.feed = data;
		console.log($scope.feed);
		//$scope.mobil_feed = $scope.get_mobil_feed();
		//$scope.get_mobil_feed(mobil_data);
		console.log($scope.feed);
	});

	$scope.get_mobil_feed = function(mobil_data){
		var mobil_data = $scope.feed;
		mobil_data.list[mobil_data.keys[0]].active = "active-day";
		//data.keys.sort(function(a, b){return a-b});
		var list = [];
		for(var i in mobil_data.keys){
			list.push(mobil_data.list[mobil_data.keys[i]]);
		}
		mobil_data.list = list;
		$scope.mobil_feed = mobil_data;
	}


	$scope.old_day = 0;
	$scope.go_old_day = function(){
		$scope.old_day++;
		feed.get_date_feed($scope.old_day).success(function(data){
			$scope.feed = data;
		});
	}

	$scope.go_new_day = function(){
		if($scope.old_day < 1){
			return false;
		}
		$scope.old_day--;
		feed.get_date_feed($scope.old_day).success(function(data){
			$scope.feed = data;
			if($scope.old_day == 0){
				$scope.feed.list[$scope.feed.keys[0]].active = "active-day";
			}
		});
	}
	
});

app.factory('feed', ['$http', function($http) {
	return {
		get_today_feed : function(){
			return $http.get("http://sikfit.com/api/feed/get").success(function(data){
					return data;
				}).error(function(err){
					return err;
				});
		},
		get_date_feed : function(page){
			return $http.get("http://sikfit.com/api/feed/get?old_day=" + page).success(function(data){
				return data;
			}).error(function(err){
				return err;
			});	
		}
	}
}]);

app.directive("widget1", function(){
	return {
		restrict : 'E',
		scope : {item : "="},
		templateUrl : "ng/directives/widget1.html"
	}
});
