<?php
function initdb() {
    $sql_1 = "create table wsc_log (id integer primary key AUTOINCREMENT, msg varchar(100), tgl datetime)";
    $sql_2 = "create table wsc_box (
        id integer primary key autoincrement,
        appId varchar(10),
        totalCpu varchar(10),
        userPercent varchar(10),
        systemPercent varchar(10),
        idlePercent varchar(10),
        totalMemory varchar(10),
        userMemory varchar(10),
        systemMemory varchar(10),
        idleMemory varchar(10),
        lastUpdate datetime
    )";
    $sql_3 = "create table wsc_err (id integer primary key AUTOINCREMENT, msg varchar(100), sql varchar(100), prm varchar(100), tgl datetime)";
    $sql_4 = "create table wsc_stat (
        id integer primary key autoincrement,
        appId varchar(20),
        rfoRequest integer,
        approved integer,
        rejected integer,
        trade integer,
        error integer,
        send integer,
        lastUpdate datetime
    )";
    try {
        DBX(1)->run($sql_1);
    } catch (Exception $e) {
        echo($e->getMessage());
    }
    /*
    try {
        DBX(1)->run($sql_2);
    } catch (Exception $e) {
        echo($e->getMessage());
    }
    try {
        DBX(1)->run($sql_3);
    } catch (Exception $e) {
        echo($e->getMessage());
    }
    try {
        DBX(1)->run($sql_4);
    } catch (Exception $e) {
        echo($e->getMessage());
    }
    */
}