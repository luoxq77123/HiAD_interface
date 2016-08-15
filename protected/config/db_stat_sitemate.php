<?php

return array(
    'class'=>'application.components.DbConnectionMan',//Specify it,instead of CDbConnection,other options is same as CDbConnection
    'connectionString' => 'mysql:host=localhost;dbname=hm_adms_sm',
    'emulatePrepare' => true,
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'tablePrefix' => '',
    //'schemaCachingDuration'=>3600,
    'enableSlave'=>false,//Read write splitting function is swithable.You can specify this value to false to disable it.
    'slaves'=>array(//slave connection config is same as CDbConnection
        array(
            'connectionString' => 'mysql:host=192.168.1.200;dbname=hm_adms_sm',
            'username' => 'developer',
            'password' => '123456',
        ),
        array(
            'connectionString' => 'mysql:host=192.168.1.200;dbname=hm_adms_sm',
            'username' => 'developer',
            'password' => '123456',
        ),
    )
);