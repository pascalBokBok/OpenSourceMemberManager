<?php 
/** Declared as php variables for avoiding double maintenace */
$db_schema_members = "create table members (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,email_address TEXT UNIQUE, last_enrollment text)";
?>