<?php
//Defining the data structure that we handle.

/**
    JSON definition of data.
    Could be used to create an SQL database table.
*/
// $user_structure = "CREATE TABLE users(
//     emailAddress text PRIMARY KEY,
//     password text not null,
//     name text not null,
//     priviledges text)
// );";

$dataJSON = file_get_contents("data_structure.json");

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