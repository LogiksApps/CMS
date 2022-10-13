<div class="background-wrap">
    <div class="background"></div>
</div>

<form id="accesspanel" action="{_service('auth')}" method="POST">
    {if isset($ERROR_MSG) }
    <div class='alert alert-danger'>
        {$ERROR_MSG}
        <span class="close" data-dismiss="alert">&times;</span>
    </div>
    {/if}
    <h1 id="litheader">
        <img src='media/logos/logiks.png' />
        <span> {$APPS_NAME} {$APPS_VERS}</span>
    </h1>
    <div class="inset">
        <p>
            <input type="text" name="userid" id="email" placeholder="Email address">
        </p>
        <p>
           <input class="form-control" placeholder="Password" name="password" type="password" value="">
        </p>
        <!--<div style="text-align: center;">-->
        <!--  <div class="checkboxouter">-->
        <!--    <input type="checkbox" name="rememberme" id="remember" value="Remember">-->
        <!--    <label class="checkbox"></label>-->
        <!--  </div>-->
        <!--  <label for="remember">Remember me for 14 days</label>-->
        <!--</div>-->
        <input class="loginLoginValue" type="hidden" name="service" value="login" />
    </div>
    <p class="p-container">
        <input type="submit" name="Login" id="go" value="Submit">
    </p>
    <!--<a class='forgotLink' href='{_link("forgotpwd")}'>Forgot Password</a>-->
</form>

<script>
    if(top!=window) top.location.reload();
    $(function() {
        $(".alert .close").click(function() {
            $(this).closest(".alert").detach();
        });
    });
</script>