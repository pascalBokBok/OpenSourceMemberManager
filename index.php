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
        </div>
        <div id="addMember"></div>
        <div id="editMember" class="jqmWindow"></div>
        <h2>Members</h2>
        <ul id="memberList"></ul>
    </body>
</html>