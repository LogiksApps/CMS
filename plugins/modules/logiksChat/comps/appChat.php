<?php
if(!defined('ROOT')) exit('No direct script access allowed');
return;
?>
<style>
.appChat {
    max-width: 500px;
}
</style>
<i class="fa fa-comments appChatIcon" aria-hidden="true"></i>
<div class="appChat">
    <div class="chat-area">
        <div class="chat-area-header">
            <div class="msg ">
                <div class="msg-profile group">
                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                        <path d="M12 2l10 6.5v7L12 22 2 15.5v-7L12 2zM12 22v-6.5"></path>
                        <path d="M22 8.5l-10 7-10-7"></path>
                        <path d="M2 15.5l10-7 10 7M12 2v6.5"></path>
                    </svg>
                </div>
                <div class="msg-detail">
                    <div class="msg-username">Chat Box</div>
                    
                </div>
            </div>
            <div class="chat-area-group">
                <i class="las la-ellipsis-v"></i>
            </div>
        </div>
        <div class="chat-area-main">
            <div class="chat-msg">
                <div class="chat-msg-profile">
                    <img class="chat-msg-img" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3364143/download+%283%29+%281%29.png" alt="" />
                    <div class="chat-msg-date">Message seen 1.22pm</div>
                </div>
                <div class="chat-msg-content">
                    <div class="chat-msg-text">Luctus et ultrices posuere cubilia curae.</div>
                    <div class="chat-msg-text">
                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3364143/download+%281%29.png" />
                    </div>
                    <div class="chat-msg-text">Neque gravida in fermentum et sollicitudin ac orci phasellus egestas. Pretium lectus quam id leo.</div>
                </div>
            </div>
            <div class="chat-msg owner">
                <div class="chat-msg-profile">
                    <img class="chat-msg-img" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3364143/download+%281%29.png" alt="" />
                    <div class="chat-msg-date">Message seen 1.22pm</div>
                </div>
                <div class="chat-msg-content">
                    <div class="chat-msg-text">Sit amet risus nullam eget felis eget. Dolor sed viverra ipsum😂😂😂</div>
                    <div class="chat-msg-text">Cras mollis nec arcu malesuada tincidunt.</div>
                </div>
            </div>
            <div class="chat-msg">
                <div class="chat-msg-profile">
                    <img class="chat-msg-img" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3364143/download+%282%29.png" alt="">
                    <div class="chat-msg-date">Message seen 2.45pm</div>
                </div>
                <div class="chat-msg-content">
                    <div class="chat-msg-text">Aenean tristique maximus tortor non tincidunt. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae😊</div>
                    <div class="chat-msg-text">Ut faucibus pulvinar elementum integer enim neque volutpat.</div>
                </div>
            </div>
            <div class="chat-msg owner">
                <div class="chat-msg-profile">
                    <img class="chat-msg-img" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3364143/download+%281%29.png" alt="" />
                    <div class="chat-msg-date">Message seen 2.50pm</div>
                </div>
                <div class="chat-msg-content">
                    <div class="chat-msg-text">posuere eget augue sodales, aliquet posuere eros.</div>
                    <div class="chat-msg-text">Cras mollis nec arcu malesuada tincidunt.</div>
                </div>
            </div>
            <div class="chat-msg">
                <div class="chat-msg-profile">
                    <img class="chat-msg-img" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3364143/download+%2812%29.png" alt="" />
                    <div class="chat-msg-date">Message seen 3.16pm</div>
                </div>
                <div class="chat-msg-content">
                    <div class="chat-msg-text">Egestas tellus rutrum tellus pellentesque</div>
                </div>
            </div>
            
        </div>
        <div class="chat-area-footer">
            <div class="dropdownBox">
                <ul>
                    <li>
                        <a href="#">
                            <i class="fa fa-file" aria-hidden="true"></i>
                        </a>
                        <span>Document</span>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-camera" aria-hidden="true"></i>
                        </a>
                        <span>Camera</span>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-picture-o" aria-hidden="true"></i>
                        </a>
                        <span>Image</span>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-video-camera" aria-hidden="true"></i>
                        </a>
                        <span>Video</span>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-headphones" aria-hidden="true"></i>
                        </a>
                        <span>Audio</span>
                    </li>
                    
                </ul>
            </div>
            <div class="dropdownIcon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-paperclip">
                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
            </svg>
          </div>
            <div class="sendBox">
                <input type="text" placeholder="Type something here..." />
                <i data-v-e08301e8="" class="fa fa-paper-plane"></i>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $(".appChatIcon").click(function() {
      $("body").toggleClass("openChat");
    });
});
</script>