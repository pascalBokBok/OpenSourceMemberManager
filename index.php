<!DOCTYPE html>
<html>
	<head>
	<meta content="text/html; charset=UTF-8" http-equiv="content-type">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js">
// 		For privacy reasons you should consider downloading your own copy from jquery.com/
	</script>
	<link rel="stylesheet" href="style/style.css">
	<script type="text/javascript" src="script/main.js"></script>
	</head>
	<body>
		<h1>Open Source Member Database</h1>
		<h2>Members</h2>
		Members in the database register:<br>
		<ul id="memberList"></ul>
		<h2>Add a new member</h2>
		<form id="addMemberForm">
			<label for="name">Name</label><input id="name" type="text" name="name" autofocus required><br>
			<label for="email_address">Email address</label><input id="email_address" type="text" name="email_address" required><br>
			<input type="submit">
		</form>
	</body>
</html>