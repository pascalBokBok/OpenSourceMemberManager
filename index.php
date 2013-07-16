<!DOCTYPE html>
<html>
    <head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <script src="http://code.jquery.com/jquery-1.9.1.min.js">
// 		For privacy reasons you should consider downloading your own copy from jquery.com/
    </script>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/jqModal.css">
    <script type="text/javascript" src="script/main.js"></script>
    <script type="text/javascript" src="script/jqModal.js"></script>
    </head>
    <body>
        <h1>Open Source Member Database</h1>
        <div id="menu">
            <a href="javascript:void(0)" onclick="$('#addMember').toggle()">Add member</a>
            <a href="javascript:void(0)" id="importCsvButton">Import CSV</a>
        </div>
        <div id="addMember"></div>
        <div id="editMember" class="jqmWindow"></div>
        <h2>Members</h2>
        <ul id="memberList"></ul>
        
        <div id="importCsvDiv" class="jqmWindow" style="display:none">
            <p>Imports a CSV file with fieldsnames on first row, ";" as separator, "\" as escape-character. File encoding: ISO 8859-15</p>
            <form action="import.php" method="post" enctype="multipart/form-data">
                <label for="importCsvFileName">CSV file to import:</label>
                <input type="file" id="importCsvFileName" name="importFile"><br>
                <input type="submit">
            </form>
        </div>
    </body>
</html>