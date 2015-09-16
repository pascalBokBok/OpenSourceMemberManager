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
        if (data.error_code == 401){ //unauthorized
            authenticate();
        } else if (data.error) {
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
//     if (data.length>0){
//         var keys Object.keys(val);
//     }
    $.each(data, function(key, val) {
        var id = val.id;
        delete val.id;
        var row = '<tr><td><a href="javascript:void(0)" onclick="editMemberInitiate('+id+')">✎</a> </td>';
        for (var fieldName in val) {
            row += '<td>' + val[fieldName] + '</td>';
        }
        row += '<td><a href="javascript:void(0)" onclick="deleteMember('+id+')">❌</a></td></tr>';
          items.push(row);
    });
    $('#memberList').replaceWith($('<table/>',{id:'memberList',html: items.join('')}));
}
function buildPage(){
    $('#addMemberButton').click(addMemberForm);
    $('#addMember').jqm();
    $('#editMember').jqm();
    $('#importCsv').jqm().jqmAddTrigger("#importExportButton");
    //always do a check if data is well protected.
    testDatabaseProtection();
}

function refreshData(){
    refreshMemberlist();
    apiCall("getMemberFields",createMemberForms);
}

function init(){
    buildPage();
    refreshData();
}
function authenticate(){
    apiCall("authenticate",refreshData,{/*password:prompt("What is your password?")*/});
}
$(document).ready(apiCall('testAuthenticated',init));

function testDatabaseProtection(){
    /** The database is in a subfolder of the public interface folder. It is protected by a htaccess file.
        To ensure privacy we test if the protection works and alert if it is not the case.
     */
    $.ajax({
        type: "GET",
        url: "backend/db.sqlite3",
        data: '',
        complete: function(e, xhr, settings){
            if(e.status === 403){
                console.log('All is well, the database is protected by the webserver.');
            }else {
                alert("The database is not properly protected by the webserver. There is a problem with htaccess settings.");
            }
        }
    });
}
function createMemberForms(memberFields){
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
