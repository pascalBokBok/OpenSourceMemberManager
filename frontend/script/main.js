function apiCall(action, func, args){
//     console.log(args);
    var params = {action:action};
    if ($.isArray(args)){
        for (var i=0;i<args.length;i++){
            params[args[i].name] = args[i].value;
        }
    } else if ($.isPlainObject(args)){
        $.each(args, function(key, val){
            params[key] = val;
        });
    }
    console.log(params);
    $.getJSON('api.php',params,function (data){
                if (data.error) {
                    alert(data.error_msg);
                } else {
                    func(data);
                }
        });
}
function refreshMemberlist(){
    apiCall('getMemberList',renderMemberList);
}
function renderMemberList(data){
    var items = [];
    $.each(data["payload"], function(key, val) {
        items.push('<li id="' + key + '">' + val["name"] + ' - '+val["email_address"]+' <a href="javascript:void(0)" onclick="editMemberInitiate('+val["id"]+')">✎</a> <a href="javascript:void(0)" onclick="deleteMember('+val["id"]+')">❌</a></li>');
    });
    $('#memberList').replaceWith($('<ul/>',{id:'memberList',html: items.join('')}));
}
function init(){
    refreshMemberlist()
    $('#addMemberForm').submit(function(){
        apiCall('addNewMember',function(){refreshMemberlist(),$('#addMemberForm').trigger('reset')},$('#addMemberForm').serializeArray());
        return false;
    });
}
function deleteMember(id){
    if (confirm ("Are you sure you want to delete?")){
        apiCall('deleteMember',refreshMemberlist,{id:id})
    }
}
function editMemberInitiate(id){
    alert('You won the implementation :-)');
}


$(document).ready(init);