<!DOCTYPE html>
<html>
    <head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <script src="https://code.jquery.com/jquery-1.9.1.min.js">
// 		For privacy reasons you should consider downloading your own copy from jquery.com/
    </script>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/jqModal.css">
    <script type="text/javascript" src="script/main.js"></script>
    <script type="text/javascript" src="script/jqModal.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.8/angular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.0/angular-route.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.0/angular-animate.min.js"></script>
    <script src="script/app.js"></script>
    <script src="controllers/registration.js"></script>
    <script src="controllers/success.js"></script>
    </head>
    <body ng-app="membershipApp">
    <!--
        <header>
            <nav class="navigation" ng-include="'views/nav.html'"></nav>
        </header>
        -->
        <div class="page" ng-view>
            <main class="mainArea"></main>
        </div>
    </body>
</html>
