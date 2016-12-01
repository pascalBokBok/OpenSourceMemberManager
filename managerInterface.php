<div id="head">
    <h1>Open Source Member Manager</h1>
    <div id="menu">
        <span class="entry" id="addMemberButton">Add member</span>
        <span class="entry" id="importExportButton">Import/export</span>
    </div>
</div>
<div id="content">
    <h2>Members</h2>
    <ul id="memberList"></ul>
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