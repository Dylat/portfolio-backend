<?php

/* ================================== 
	MYSQLI Connectiom 
====================================*/

$DB = new MysqliDb (Array (
                'host' => 'localhost',
                'username' => 'root', 
                'password' => '',
                'db'=> 'blog_api',
                'charset' => 'utf8'));


?>