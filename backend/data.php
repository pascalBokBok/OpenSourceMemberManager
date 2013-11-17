<?php
//Defining the data structure that we handle.

/**
    JSON definition of data.
    Could be used to create an SQL database table.
*/
  
$memberDataFieldsJSON = '[
        {
            "name": "id",
            "type": "integer",
            "required":true,
            "editable":false,
            "caption":"ID",
            "properties":["autoincrement"]
        },{
            "name": "name",
            "type": "text",
            "required":true,
            "editable":true,
            "caption":"Name",
            "properties":[]
        },{
            "name": "surname",
            "type": "text",
            "required":true,
            "editable":true,
            "caption":"Surname",
            "properties":[]
        },{
            "name": "email_address",
            "type": "email",
            "required":false,
            "editable":true,
            "caption":"Email address",
            "properties":[]
        },{
            "name": "street",
            "type": "text",
            "required":false,
            "editable":true,
            "caption":"Street",
            "properties":[]
        },{
            "name": "postal_code",
            "type": "text",
            "required":false,
            "editable":true,
            "caption":"Postal code",
            "properties":[]
        },{
            "name": "city",
            "type": "text",
            "required":false,
            "editable":true,
            "caption":"City",
            "properties":[]
        },{
            "name": "country",
            "type": "text",
            "required":false,
            "editable":true,
            "caption":"Country",
            "properties":[]
        },{
            "name": "group",
            "type": "select",
            "select":["EPP","AIP"],
            "required":false,
            "editable":true,
            "caption":"Group",
            "properties":[]
        },{
            "name": "telephone",
            "type": "text",
            "required":false,
            "editable":true,
            "caption":"Telephone",
            "properties":[]
        },{
            "name": "last_payment_year",
            "type": "text",
            "required":false,
            "editable":true,
            "caption":"Last Payment year",
            "properties":[]
        }
    ]';

$memberDataJSON = '{
    "name": "members",
    "caption":"Members",
    "identifier_field" : "id",
    "caption_fields" : ["name","surname"],
    "fields" : '.$memberDataFieldsJSON.'
}';    

$dataJSON = '{
    "application_name":"Open Source Member Manager",
    "data_structures": { "persons": '.$memberDataJSON.',
                         "roles": {   "name":             "roles",
                                      "caption":          "Roles",
                                      "identifier_field": "name",
                                      "caption_fields":   ["name"],
                                      "fields":           [{
                                                             "name": "name",
                                                             "type": "text",
                                                             "required":true,
                                                             "editable":true,
                                                             "caption":"Name",
                                                             "properties":[]
                                                            }
                                                           ]
                                      },
                         "role_members": {
                                      "name":             "roles_members",
                                      "caption":          "Role Members",
                                      "identifier_field": ["person", "role"],
                                      "caption_fields":   ["person", "role"],
                                      "fields":           [{
                                                             "name": "person",
                                                             "type": "integer",
                                                             "required":true,
                                                             "editable":false,
                                                             "caption":"Person ID",
                                                             "properties":[],
                                                             "references": ["persons", "id"]
                                                            },{
                                                             "name": "role",
                                                             "type": "text",
                                                             "required":true,
                                                             "editable":false,
                                                             "caption":"Role",
                                                             "properties":[],
                                                             "references": ["roles", "name"]
                                                            }
                                                           ]
                                      }
                          }
}';

$fieldType2sqliteType = array("text"=>"text",
                         "email"=>"text",
                         "integer"=> "integer",
                         "date"=>"integer",
                         "select"=>"text");

/** assuming we are dealing with trusted input here. */
function createDatabaseTables($structJSON, $typeTrans){
    $struct = json_decode($structJSON);
    $sql = "";
    foreach ($struct->data_structures as $table){
        $sql .= createDatabaseTable($table, $typeTrans);
    }
    return $sql;
}

function createDatabaseTable($table,$typeTrans){
    $cols = array();
    foreach ($table->fields as $field){
        $myCol = "'".$field->name."' ".$typeTrans[$field->type];
        if ($field->name==$table->identifier_field){
            //FIXME handle combined primary key
            $myCol .= ' PRIMARY KEY';
        } else if ($field->required){
            $myCol .= " NOT NULL";
        }
        foreach ($field->properties as $prop){
            $myCol .= " ".$prop;
        }
        $cols[] = $myCol;
    }
    $createTableSQL = "CREATE TABLE ".$table->name." (
        ".implode($cols,",\n")."
    );";
    return $createTableSQL;
}

?>