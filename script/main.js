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
function afterFormSubmit(){
    refreshMemberlist();
    $('#memberForm').trigger('reset').find('input:enabled').first().focus();
    newMemberInitiate();
}

function editMemberInitiate(id){
    var fillInMemberData = function(data) {
        console.log(data);
        for(var i in memberStruct){
            var el = memberStruct[i];
            var input = $('#memberForm>:input[name='+el.name+']');
            var value = data[el.name];
            switch (el.type){
                case "select":
                    input.attr('value',"later...");
                    break;
                case "checkbox":
                    input.attr('checked',data[el.name]?'checked':'false');
                default:
                    input.attr('value',data[el.name]);
            }
        }
    };
    apiCall('getMember',fillInMemberData,{'id':id});
    $('#memberForm').off('submit').on('submit',function(){
        apiCall('updateMember',afterFormSubmit,$('#memberForm').serializeArray());
        return false;
    });
}
function newMemberInitiate(){
    $('#memberForm').off('submit').one('submit',function(){
        apiCall('addNewMember',afterFormSubmit,$('#memberForm').serializeArray());
        return false;
    });
}
function createMemberForm(data){
    var memberFields = JSON.parse(data);
    $('#memberFormArea').append(createForm('memberForm',memberFields));
    newMemberInitiate();
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
        form.append(input).append('<br>');
    }
    form.append('<input type="submit">');
    return form;
}
$(document).ready(init);