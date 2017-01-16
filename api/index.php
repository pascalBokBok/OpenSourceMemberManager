<?php
session_start();
require 'flight/Flight.php';
require '../backend/database.php';

Flight::route('POST /members/', function(){
    $json = Flight::request()->getBody();
    $obj = json_decode($json,true);
    addNewMember($obj);
});

Flight::route('GET /members/@id', function($id){
    getMembers($id);
});

Flight::route('GET /memberfields', function(){
    getMemberFields();
});

Flight::route('GET /members', function(){
    getMembers();
});

Flight::route('PUT /members/@id', function($id){
    $json = Flight::request()->getBody();
    $obj = json_decode($json,true);
    updateMember($obj);
});

Flight::route('DELETE /members/@id', function($id){
    deleteMember($id);
});

Flight::start();















?>
