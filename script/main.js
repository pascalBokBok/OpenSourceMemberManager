
$(document).ready(function(){
    init();
});

function refreshMemberlist(){
    var list = null;
    $.ajax({
        type: 'GET',
        url: 'api/members',
        async: false,
        success: function(data) {
            list = data;
        } 
    });
    return list;
}

function deleteMember(id){
    if (confirm ("Are you sure you want to delete?")){
        $.ajax({
            type: "DELETE",
            url: 'api/members/'+id,
        });
    }
    refreshMemberlist();
}

function renderMemberList(data){
    var items = [];
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
    var returndata = null;
    $.ajax({
        url: 'api/memberfields',
        type: 'get',
        async: false,
        success: function(data) {
            returndata = data;
        } 
    });
    createMemberForms(returndata);
}

function init(){
    buildPage();
    refreshData();
}

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

function formDataToJsonObject(formname){
    var data = $('#'+formname).serialize().split("&");
    var obj={};
    for(var key in data){
        obj[data[key].split("=")[0]] = data[key].split("=")[1];
    }
    return obj;
}

function createMemberForms(memberFields){
    //Create and Initiate new member form.
    $('#addMember').append(createForm('newMemberForm',memberFields));
    $('#newMemberForm').submit(function(){
        $.ajax({
            type: "POST",
            url: 'api/members',
            dataType: "json",
            async: false,
            contentType: "application/json",
            data: JSON.stringify(formDataToJsonObject("newMemberForm"))
        });
        refreshMemberlist();
        return false;
    });
    //Create edit member form.
    $('#editMember').html(createForm('editMemberForm',memberFields));
    $('#editMemberForm').submit(function(){
        $.ajax({
            type: "PUT",
            url: 'api/members/'+$("#editMemberForm input[name=id]").val(),
            dataType: "json",
            async: false,
            contentType: "application/json",
            data: JSON.stringify(formDataToJsonObject("editMemberForm")),
            success: function(data) {
            
            }
        });
        $('#editMember').jqmHide();
        refreshMemberlist();
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
    function fillInMemberData(data) {
//         $('#editMemberForm').get(0).reset(); //some inputs are not set without the reset.
        for(var i in memberStruct){
            var el = memberStruct[i];
            var input = $('#editMemberForm>:input[name='+el.name+']');
            var value = data[0][el.name];
            switch (el.type){
                case "checkbox":
                    input.attr('checked',data[0][el.name]?'checked':'false');
                default:
                    input.attr('value',data[0][el.name]);
            }
        }
        $('#editMember').jqmShow();
    };
    var returndata = null;
    $.ajax({
        url: 'api/members/'+id,
        type: 'get',
        async: false,
        success: function(data) {
            returndata = data;
        } 
    });
    
    fillInMemberData(returndata);
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

var app = angular.module('OSMapp', []);
app.controller('OSMctrl', function($scope) {
    $scope.memberList = refreshMemberlist();
    
    $scope.editMember = function(id){
        editMemberInitiate(id);
    }
    
    $scope.deleteMember = function(id){
        deleteMember(id);
    }
    
    $scope.safeApply = function(fn) {
        var phase = this.$root.$$phase;
        if(phase == '$apply' || phase == '$digest') {
            if(fn && (typeof(fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };
    
});



















