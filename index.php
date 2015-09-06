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
    </head>
    <body>
        <div id="head">
            <h1>Open Source Member Manager</h1>
            <div id="menu">
                <span class="entry" id="addMemberButton">Add member</span>
                <span class="entry" id="importExportButton">Import/export</span>
            </div>
        </div>
        <div id="content">
            <div id="addMember" class="jqmWindow"><h2>Add new member</h2></div>
            <div id="editMember" class="jqmWindow"></div>
            <h2>Members</h2>
            <ul id="memberList"></ul>
            <hr>
            <p align="center"><a href="https://github.com/pascalBokBok/OpenSourceMemberManager" target="_blank">Open Source Member Manager</a></p>
        </div><!-- end content-->
        <!-- others -->
        <div id="importCsvDiv" class="jqmWindow" style="display:none">
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
