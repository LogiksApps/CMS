<?php

loadModuleLib("logiksAI", "api");

$params = [];
$sessID = false;
$appID = LOGIKSAI_APPID;

set_time_limit(0);

$response = sendChatMessage("tell me in one word", $params, $sessID, $appID);

var_dump([$response['data']["RESPONSE"], getLogiksAIError()]);
?>