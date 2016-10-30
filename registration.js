membershipApp.controller('RegistrationController', ['$scope', function($scope){
	$scope.login = function () {
		$scope.message = "Welcome " + $scope.user.email;
	};

	$scope.register = function () {
		$scope.message = "Good job " + $scope.user.firstname + ", you are now a registered user!"
	}
}]);