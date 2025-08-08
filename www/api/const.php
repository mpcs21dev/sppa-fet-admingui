<?php
define("DB_ENGINE", "POSTGRES"); // valid values: "MSSQL" | "SQLITE" | "POSTGRES" | "MYSQL" | "MARIADB"
define("DB_SAVE_IMAGE", "");   // valid values: "BLOB" | "BASE64" | ""
define("DB_DEL_IMAGE", true);  // delete image when updating or deleting record
define("DEF_SCHEMA","public");
//define("FIREBASE_JSON","firebase.json");
define("CHECK_RIGHT", false);
define("DS","/"); // directory separator

define("DB_DATA",0);
define("DB_RIGHT",1);
define("DB_LOG",2);

define("LEVEL_USER",1);
define("LEVEL_ADMIN",5);
define("LEVEL_DEV", 99);

$RESEND_URL = getenv('RESEND_URL') ?? "http://sppafet-admin-net/sppa-fet/admin/resend";

$ERR = array(
    "",
    "Invalid function call",            // 1
    "Invalid api call",                 // 2
    "Unidentified function call",       // 3
    "Unidentified api call",            // 4
    "Unsupported api version",          // 5
    "Function not registered in requested version",
    "API source file missing",          // 7
    "Table User not empty",             // 8
    "",                                 // 9
    "Session not registered",           // 10
    "Invalid UserID or Bad Password",   // 11
    "Invalid Old Password",             // 12
    "This is ROOT-only function",       // 13
    "Unsupported file upload",          // 14
    "",                                 // 15
    "User ROOT is read-only",           // 16
    "No changes to data",               // 17
    "User can't reset password",        // 18
    "",
    "Record already sent",              // 20
    "User not owned this topic",        // 21
    "Messaging error",                  // 22
    "",
    "",
    "Not authorized to modify this entry",  // 25
    "User not authorized",                  // 26
    "",
    "",
    "",
    "API result not success", // 30
    "API result parsing error",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "Access Denied" // 40
);

$VERSIONS = array("1");
$FUNCTIONS = new stdClass();
$FUNCTIONS->v1 = array(
    "holiday",
    "config",
    "trx",
    "wsc",
    "ref",
    "user",
        
    "getChallange",
    "login",
    "logout",
    "passwd",
    "init-root",
    "isLogged",
    "info",
    "migrate"
);

