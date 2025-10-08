<?php
function initdb() {
    $sql_1 = "CREATE TABLE wsc_log (id integer primary key AUTOINCREMENT, msg varchar(100), tgl text)";
    $sql_2 = "CREATE TABLE wsc_box (
        id integer primary key AUTOINCREMENT,
        appId varchar(10),
        totalCpu varchar(10),
        userPercent varchar(10),
        systemPercent varchar(10),
        idlePercent varchar(10),
        totalMemory varchar(10),
        userMemory varchar(10),
        systemMemory varchar(10),
        idleMemory varchar(10),
        lastUpdate text
    )";
    $sql_3 = "CREATE TABLE wsc_err (id integer primary key AUTOINCREMENT, msg varchar(100), sql varchar(100), prm varchar(100), tgl text)";
    $sql_4 = "CREATE TABLE wsc_stat (
        'id' integer primary key AUTOINCREMENT,
        'appId' varchar(20),
        'rfoRequest' integer,
        'approved' integer,
        'rejected' integer,
        'initiator' integer,
        'trade' integer,
        'error' integer,
        'send' integer,
        'lastUpdate' text
    )";
    $sql_5 = "CREATE TABLE wsc_login (
        'id' INTEGER primary key AUTOINCREMENT,
        'appId' VARCHAR(10),
        'login' INTEGER,
        'lastUpdate' text
    );";
    $sql_6 = "CREATE TABLE wsc_session (
    	'id' INTEGER primary key AUTOINCREMENT,
    	'uid' VARCHAR(50),
    	'tick' integer
    );";
    $sql_7 = "CREATE TABLE wsc_uilogin (
        'id' INTEGER primary key AUTOINCREMENT,
        'user_id' INTEGER,
        'user_uid' text,
        'ip1' text,
        'ip2' text,
        'ip3' text,
        'msg' text,
        'lastUpdate' text
    )";
    try {
        DBX(2)->run($sql_1);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
    try {
        DBX(2)->run($sql_2);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
    try {
        DBX(2)->run($sql_3);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
    try {
        DBX(2)->run($sql_4);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
    try {
        DBX(2)->run($sql_5);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
    try {
        DBX(2)->run($sql_6);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
    try {
        DBX(2)->run($sql_7);
    } catch (Exception $e) {
        echo($e->getMessage()."\n");
    }
}