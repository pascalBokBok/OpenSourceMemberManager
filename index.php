<!DOCTYPE html>
<html>
    <head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <script src="https://code.jquery.com/jquery-1.9.1.min.js">
// 		For privacy reasons you should consider downloading your own copy from jquery.com/
    </script>
    <link rel="stylesheet" href="script/bootstrap-3.3.7/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/jqModal.css">
    <script src="script/angular-1.5.8/angular.min.js"></script>
    <script src="script/bootstrap-3.3.7/bootstrap.min.js"></script>
    <script type="text/javascript" src="script/main.js"></script>
    <script type="text/javascript" src="script/jqModal.js"></script>
    </head>
    <body ng-app="OSMapp" ng-controller="OSMctrl">
        <div id="head">
            <h1>Open Source Member Manager</h1>
            <div id="menu">
                <button class="btn btn-default" id="addMemberButton">Add member</button>
                <button class="btn btn-default" id="importExportButton">Import/export</button>
            </div>
        </div>
        <div id="content">
            <h2 style="margin-left:10px;">Members</h2>
            <table>
                <tr><th></th><th>Firstname</th><th>Surname</th><th>Email</th><th>Telephone</th><th>Street</th><th>City</th><th>Postal Code</th><th>Country</th><th>Group</th><th>Paid 2016</th><th></th></tr>
                <tr ng-repeat="member in memberList">
                    <td><a href="javascript:void(0)" ng-click="editMember(member.id)">✎</a></td>
                    <td>{{member.name}}</td>
                    <td>{{member.surname}}</td>
                    <td>{{member.email_address}}</td>
                    <td>{{member.telephone}}</td>
                    <td>{{member.street}}</td>
                    <td>{{member.city}}</td>
                    <td>{{member.postal_code}}</td>
                    <td>{{member.country}}</td>
                    <td>{{member.group}}</td>
                    <td>{{member.paid_2016}}</td>
                    <td><a href="javascript:void(0)" ng-click="deleteMember(member.id)">❌</a></td>
                </tr>
            </table>
            <hr>
            <p align="center"><a href="https://github.com/pascalBokBok/OpenSourceMemberManager" target="_blank">Open Source Member Manager</a></p>
        </div><!-- end content-->
        <!-- others -->
        <div id="addMember" class="jqmWindow"><h2>Add new member</h2></div>
        <div id="editMember" class="jqmWindow"></div>
        <div id="importCsv" class="jqmWindow" style="display:none">
            <p>Imports a CSV file with fieldsnames on first row, ";" as separator, "\" as escape-character. File encoding: ISO 8859-15</p>
            <form action="port.php?action=import" method="post" enctype="multipart/form-data">
                <label for="importCsvFileName">CSV file to import:</label>
                <input type="file" id="importCsvFileName" name="importFile"><br>
                <input type="submit">
            </form>
            <a href="port.php?action=export">Export as CSV.</a>
        </div>
    </body>
</html>
