function refreshMemberList(){
	$.getJSON('api.php',{action:'getMemberList'},function(data){
		var items = [];
		$.each(data, function(key, val) {
			items.push('<li id="' + key + '">' + val["name"] + ' - '+val["email_address"]+' <a href="javascript:void(0)" onclick="editMemberInitiate('+val["id"]+')">✎</a> <a href="javascript:void(0)" onclick="deleteMember('+val["id"]+')">❌</a></li>');
		});
		$('#memberList').replaceWith($('<ul/>',{id:'memberList',html: items.join('')}));
	});
}
function init(){
	refreshMemberList();
	$('#addMemberForm').submit(function(){
		$.get('api.php?action=addNewMember&' + $('#addMemberForm').serialize(),function(){
			refreshMemberList();
			$('#addMemberForm').trigger('reset')
		});
		return false;
	})
}
function deleteMember(id){
	$.get('api.php?action=deleteMember&id='+id,refreshMemberList);
}
function editMemberInitiate(id){
	alert('You won the implementation :-)');
}


$(document).ready(init);