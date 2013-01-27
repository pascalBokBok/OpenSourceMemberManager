<!DOCTYPE html>
<html>
	<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js">
// 		For privacy reasons you should consider downloading your own copy from jquery.com/
	</script>
	<script language="javascript">
	function refreshMemberList(members){
		$.getJSON('api.php',{action:'getMemberList'},function(data){
			var items = [];
			$.each(data, function(key, val) {
				items.push('<li id="' + key + '">' + val["name"] + '</li>');
			});
			$('#memberList').replaceWith($('<ul/>',{id:'memberList',html: items.join('')}));
		});
	}
	function init(){
		refreshMemberList();
		$('#addMemberForm').submit(function(){
			$.get('api.php?action=addNewMember&' + $('#addMemberForm').serialize(),function(){
				refreshMemberList();
			});
			return false;
		})
	}
	
	$(document).ready(init);
	</script>
	<link rel="stylesheet" href="style/style.css">
	</head>
	<body>
		<h1>Open Source Member Database</h1>
		<h2>Members</h2>
		The following members are in the database register:<br>
		<ul id="memberList"></ul>
		<h2>Add a new member</h2>
		<form id="addMemberForm">
			<label for="name">Name</label><input id="name" type="text" name="name"><br>
			<label for="email_address">Email address</label><input id="email_address" type="text" name="email_address"><br>
			<input type="submit">
		</form>
		<h2>Update member</h2>
<!-- 		member select: <select></select> -->
		<div id="updateMember"></div>
	</body>
</html>