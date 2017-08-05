<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=UTF-8" http-equiv="content-type">
        <link rel="stylesheet" href="style/bundled/bootstrap.min.css">
        <link rel="stylesheet" href="style/bundled/jqModal.css">
        <link rel="stylesheet" href="style/style.css">
        <script src="script/bundled/jquery-3.1.1.min.js"></script>
        <script src="script/bundled/angular.min.js"></script>
        <script src="script/bundled/bootstrap.min.js"></script>
        <script src="script/bundled/jqModal.js"></script>
        <script src="script/main.js"></script>
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
                <tr> <!--table headers-->
                    <th><!--buttons--></th>
                    <th ng-repeat="field in memberFields" ng-if="field.editable">
                        {{field.caption}}
                    </th>
                </tr>
                <tr ng-repeat="member in members">
                    <td><a href="javascript:void(0)" ng-click="editMember(member.id)">✎</a></td>
                    <td ng-repeat="field in memberFields" ng-if="field.editable">
                        {{member[field.name]}}
                    </td>
                    <td><a href="javascript:void(0)" ng-click="deleteMember(member.id)">❌</a></td>
                </tr>
            </table>
            <hr>
            <p align="center"><a href="https://github.com/pascalBokBok/OpenSourceMemberManager" target="_blank">Open Source Member Manager</a></p>
        </div><!-- end content-->
        <!-- others -->
        <div id="addMember" class="jqmWindow">
            <h2>Add new member</h2>
        </div>
        <div id="editMember" class="jqmWindow">
        </div>
        <div id="importCsv" class="jqmWindow" style="display:none">
            <div class="alert alert-info">
                <b>CSV file format</b>
                <ul>
                    <li>Fieldsnames on first row.</li>
                    <li>Comma(",") separated values.</li>
                    <li>"\" as escape-character.</li>
                    <li>Encoding: UTF-8</li>
                </ul>
            </div>
            <h2>Import</h2>
            <form action="port.php?action=import" method="post" enctype="multipart/form-data">
                <input type="file" id="importCsvFileName" name="importFile"><br>
                <input type="submit" value="Import">
            </form><br>
            <h2>Export</h2>
            <p><a href="port.php?action=export">Download as CSV.</a></p>
        </div>
    </body>
</html>
