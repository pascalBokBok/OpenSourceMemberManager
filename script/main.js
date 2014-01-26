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
    var handler = function (dataJSON){
        var data = JSON.parse(dataJSON);
        if (data.error) {
            alert(data.error_msg);
        } else {
            func(data["payload"]);
        }
    };
    $.get('api.php',params, handler);
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
        items.push('<tr id="' + key + '"><td>' + 
          val["name"] + '</td><td>' + 
          val["surname"] + '</td><td>' +
          val["email_address"] + '</td><td>' +
          val["street"] + '</td><td>' +
          val["postal_code"] + '</td><td>' +
          val["city"] + '</td><td>' +
          val["country"] + '</td><td>' +
          val["group"] + '</td><td>' +
          val["telephone"] + '</td><td>' +
          val["last_payment_year"] + '</td><td>' +
          '<a href="javascript:void(0)" onclick="editMemberInitiate('+val["id"]+')">✎</a> <a href="javascript:void(0)" onclick="deleteMember('+val["id"]+')">❌</a></td></tr>');
    });
    $('#memberList').replaceWith($('<table/>',{id:'memberList',html: items.join('')}));
}

function init(){
    refreshMemberlist();
    apiCall("getMemberFields",createMemberForms);
    $('#addMemberButton').click(addMemberForm);
    $('#addMember').jqm();
    $('#editMember').jqm();
    $('#importCsvDiv').jqm().jqmAddTrigger("#importExportButton");
}
function authenticate(){
    apiCall("authenticate",init,{password:prompt("What is your password?")})
}
$(document).ready(authenticate);

function createMemberForms(data){
    var memberFields = JSON.parse(data);
    //Create and Initiate new member form.
    $('#addMember').append(createForm('newMemberForm',memberFields));
    $('#newMemberForm').submit(function(){
        apiCall('addNewMember',afterAddMemberSubmit,$('#newMemberForm').serializeArray());
        return false;
    });
    //Create edit member form.
    $('#editMember').html(createForm('editMemberForm',memberFields));
    $('#editMemberForm').submit(function(){
        apiCall('updateMember',function(){refreshMemberlist();$('#editMember').jqmHide()},$('#editMemberForm').serializeArray());
        return false;
    });
}
function afterAddMemberSubmit(){
    refreshMemberlist();
    $('#newMemberForm').trigger('reset');
    $('#newMemberForm').find('input:visible:enabled:first').focus();
}

function addMemberForm(){
    $('#addMember').jqmShow();
    $('#addMember input:visible:first').trigger('focus');
}
function editMemberInitiate(id){
    var fillInMemberData = function(data) {
        $('#editMemberForm').get(0).reset(); //some inputs are not set without the reset.
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
