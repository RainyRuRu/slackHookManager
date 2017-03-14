<?php
return [
    "trac_auth" => "token",
    "trac_api_url" => "https://issue.kkcorp/trac/jsonrpc",
    "trac_owner" => "your name",
    "time_interval" => 5,
    "plugin" => [
        "LittleBot\\Job\\Plugin\\Message\\Plugin",
        "LittleBot\\Job\\Plugin\\Trac\\Plugin",
    ]
];