<?php
function initdb() {
    $sql_1 = "create table wsc_log (id integer primary key, msg varchar(100), tgl text)";
    $sql_2 = "create table wsc_box (
        id integer primary key,
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
    $sql_3 = "create table wsc_err (id integer primary key, msg varchar(100), sql varchar(100), prm varchar(100), tgl text)";
    $sql_4 = "create table wsc_stat (
        id integer primary key,
        appId varchar(20),
        rfoRequest integer,
        approved integer,
        rejected integer,
        trade integer,
        error integer,
        send integer,
        lastUpdate text
    )";
    $sql_5 = "CREATE TABLE wsc_login (
        'id' INTEGER primary key,
        'appId' VARCHAR(10),
        'login' INTEGER,
        'lastUpdate' TIMESTAMP
    );";
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
}