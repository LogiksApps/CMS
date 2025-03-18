<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("logiksAI", "api");

configureLogiksAI("LogiksAI !", "LogiksStudio");

loadModuleComponent("logiksAI", "chatbox");
?>
<style>
.aiChat {
    width: 500px;
}
.chatAreaContainer {
    /*font: 12px monospace;*/
}
.chat-msg-text pre code {
	
}
.chat-msg-text .code-tools {
	padding: 6px 0px;
    text-align: right;
    border-top: 1px solid #CCC;
    margin-top: 11px;
    margin-bottom: -6px;
}
.chat-msg-text .code-tools i {
	
}
</style>
<script>
$(function() {
    initiateLogiksAIChat({});
    
    addLogiksAIEventListener("ON_SEND", prepareMsgContext);
    addLogiksAIEventListener("ON_RECEIVE", renderCodeTools_Response);
});
function prepareMsgContext(msgObj) {
    var currentText = editor.getValue();
    var currentExt = extLang;
    var currentFile = srcFile;
    
    msgObj["filepath"]= currentFile;
    msgObj["lang"]= currentExt;
    msgObj["document"]= currentText;
    
    // msgObj['msg'] = `${msgObj['msg']}`;
    
    return msgObj;
}
function renderCodeTools_Response(msgObj) {
    // console.log(msgObj);
    
    if($(`.chat-msg.server[data-msgid='${msgObj.id}']`).length>0) {
        $(".chat-msg-text pre", `.chat-msg.server[data-msgid='${msgObj.id}']`).append(`<div class='code-tools'><button title='Implement Code' class='btn btn-success' onclick="implementCode(this)"><i class='fa fa-check'></i></button><button title='Copy Code' class='btn btn-default' onclick="copyCode(this)"><i class='fa fa-copy'></i></button><button title='Explain Code' class='btn btn-default' onclick="explainCode(this)"><i class='fa fa-magic'></i></button></div>`);
    }
}

function copyCode(ele) {
    
}
function implementCode(ele) {
    
}
function explainCode(ele) {
    
}
</script>