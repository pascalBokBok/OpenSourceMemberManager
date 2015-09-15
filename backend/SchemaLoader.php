<?php
/** 
 * SchemaLoader contains the database structure. 
 * 
 */
class SchemaLoader {
    private static $filePath = 'backend/data_structure.json';
    private static $fieldType2sqliteType = array("text"=>"text",
        "email"=>"text",
        "integer"=> "integer",
        "date"=>"integer",
        "select"=>"text");
    
    public static function getSchemaJSON(){
        return file_get_contents(self::$filePath);
    }
    /** get the database structure
     associative: if want data structure in associative array instead of object.
     */
    public static function getSchema($associative=false){
        return json_decode(self::getSchemaJSON(),$associative);
    }
    /** return an SQL string to create the database structure */
    public static function createDatabaseSchemaSQL(){
        $schema = self::getSchema();
        $sql = "";
        foreach ($schema->data_structures as $table){
            $sql .= self::createDatabaseTable($table);
        }
        return $sql;
    }
    
    /** takes a table struture and returns a sql create statement */
    public static function createDatabaseTable($table){
        $cols = array();
        foreach ($table->fields as $field){
            $myCol = "'".$field->name."' ".self::$fieldType2sqliteType[$field->type];
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
    
}
?>