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
function deleteMember(id){
    if (confirm ("Are you sure you want to delete?")){
        apiCall('deleteMember',refreshMemberlist,{id:id})
    }
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
    apiCall("getMemberFields",createMemberForms);
    $('#editMember').jqm();
}
function createMemberForms(data){
    var memberFields = JSON.parse(data);
    //Create and Initiate new member form.
    $('#addMember').append(createForm('newMemberForm',memberFields));
    $('#newMemberForm').submit(function(){
        apiCall('addNewMember',afterFormSubmit,$('#newMemberForm').serializeArray());
        return false;
    });
    //Create edit member form.
    $('#editMember').html(createForm('editMemberForm',memberFields));
    $('#editMemberForm').submit(function(){
        apiCall('updateMember',function(){refreshMemberlist();$('#editMember').jqmHide()},$('#editMemberForm').serializeArray());
        return false;
    });
}
function afterFormSubmit(){
    refreshMemberlist();
    $('#newMemberForm').trigger('reset');
    $('#newMemberForm').find('input:enabled').first().focus();
}

function editMemberInitiate(id){
    var fillInMemberData = function(data) {
        for(var i in memberStruct){
            var el = memberStruct[i];
            var input = $('#editMemberForm>:input[name='+el.name+']');
            var value = data[el.name];
            switch (el.type){
                case "checkbox":
                    input.attr('checked',data[el.name]?'checked':'false');
                default:
                    input.attr('value',data[el.name]);
            }
        }
        $('#editMember').jqmShow();
    };
    apiCall('getMember',fillInMemberData,{'id':id});
}
function createForm(id,elements){
    memberStruct = elements;
    var form = $('<form>').attr('id',id);
    var autoFocusSet = false;
    for (var i=0;i<elements.length;i++){
        var e = elements[i];
        var input = $('<input>').attr('name',e.name);
        if (e.editable==false){
            input.attr('type','hidden');
        } else {
            form.append( $('<label>').html(e.caption) );
            switch (e.type){
                case "select":
                    input.attr('value',"later...");
                    break;
                case "checkbox":
                case "email":
                    input.attr('type',e.type);
                    break;
                case "integer":
                default:
                    input.attr('type',"text");
            }
            if (e.required){
                input.attr('required','required');
            }
        }
        form.append(input);
        if (e.editable){
            form.append('<br>');
        }
    }
    form.append('<input type="submit">');
    return form;
}
$(document).ready(init);