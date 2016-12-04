<?php
/** 
 * SchemaLoader contains the database structure. 
 * 
 */
class SchemaLoader {
    private static $filePath = __DIR__.'/data_structure.json';
    private static $fieldType2sqliteType = array("text"=>"text",
        "email"=>"text",
        "integer"=> "integer",
        "date"=>"integer",
        "select"=>"text",
        "password"=>"text",
        );
    
    public static function getSchemaJSON(){
        return file_get_contents(self::$filePath);
    }
    /** get the database structure
     associative: if want data structure in associative array instead of object.
     */
    public static function getSchema($associative=false){
        $schema =  json_decode(self::getSchemaJSON(),$associative);
        if ($schema==null){
            throw new Exception("Could not read schema file");
        }
        return $schema;
    }
    /** return an SQL string to create the database structure */
    public static function createDatabaseSchemaSQL(){
        $schema = self::getSchema();
        $sql = "";
        foreach ($schema->data_structures as $table){
            $sql .= self::createDatabaseTable($table);
            $sql .= self::insertDefaultData($table);
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
    
    public static function insertDefaultData($table){
        if (empty($table->default_data)){
            return '';
        }
        $columnNames = [];
        foreach ($table->fields as $field){
            $columnNames[] = '"'.$field->name.'"';
        }
        $sql ='';
        foreach ($table->default_data as $row){
            $myRow = [];
            foreach ($row as $index => $field){
                $fieldType = self::$fieldType2sqliteType[ $table->fields[$index]->type ];
                if ($fieldType == 'text'){
                    $myRow[] = "'".SQLite3::escapeString($field)."'";
                } else {
                    $myRow[] = $field === null ? 'null' : $field;
                }
            }
            $sql .= 'insert into '.$table->name .'('.implode(', ',$columnNames).')'.
                ' values ('.implode (', ',$myRow).');';
        }
        return $sql;
        
        
    }
    
}
?>
