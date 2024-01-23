<?php
/**
 * @package Hello_Dolly
 * @version 1.7.2
 */
/*
Plugin Name: Заявки Doctrine
Author: kovaleff
*/
$entityManager = require_once __DIR__."/doctrine/bootstrap.php";
require_once 'RequestsDoctrine.php';

$mainClass = new RequestsDoctrine($entityManager);
$mainClass->init();

register_activation_hook( __FILE__, function() use($entityManager) {
    global $wpdb;

    if($wpdb->get_var("show tables like 'requests'") == NULL){

        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = array(
            $entityManager->getClassMetadata('Request'),
        );
        try {
            $tool->createSchema($classes);
        }catch (PDOException $e){
//            ...
        }
    }
});
