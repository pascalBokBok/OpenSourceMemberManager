function apiCall(action, func, args){
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
    $.getJSON('api.php',params,function (data){
                if (data.error) {
                    alert(data.error_msg);
                } else {
                    func(data["payload"]);
                }
        });
}
function refreshMemberlist(){
    apiCall('getMemberList',renderMemberList);
}
function renderMemberList(data){
    var items = [];
    $.each(data, function(key, val) {
        items.push('<li id="' + key + '">' + val["name"] + ' - '+val["email_address"]+' <a href="javascript:void(0)" onclick="editMemberInitiate('+val["id"]+')">✎</a> <a href="javascript:void(0)" onclick="deleteMember('+val["id"]+')">❌</a></li>');
    });
    $('#memberList').replaceWith($('<ul/>',{id:'memberList',html: items.join('')}));
}
function init(){
    refreshMemberlist()
    apiCall("getMemberFields",createMemberForm);
}
function deleteMember(id){
    if (confirm ("Are you sure you want to delete?")){
        apiCall('deleteMember',refreshMemberlist,{id:id})
    }
}
function editMemberInitiate(id){
    alert('You won the implementation :-)');
}
function createMemberForm(data){
    var memberFields = JSON.parse(data);
    $('#createMember').append(createForm('addMemberForm',memberFields));
    $('#addMemberForm').submit(function(){
        apiCall('addNewMember',function(){refreshMemberlist(),$('#addMemberForm').trigger('reset')},$('#addMemberForm').serializeArray());
        return false;
    });
}
function createForm(id,elements){
    var form = $('<form>').attr('id',id);
    for (var i=0;i<elements.length;i++){
        var e = elements[i];
        form.append( $('<label>').html(e.caption) );
        var input = $('<input>').attr('name',e.name)
        switch (e.type){
            case "select":
                input.attr('value',"later...");
                break;
            case "email":
                input.attr('type',e.type);
                break;
            case "integer":
            default:
                input.attr('type',"text");
        }
        if (e.editable==false){
            input.attr('disabled','disabled');
        }
        if (e.required){
            input.attr('required','required');
        }
        form.append(input).append('<br>');
    }
    form.append('<input type="submit">');
    return form;
}
$(document).ready(init);