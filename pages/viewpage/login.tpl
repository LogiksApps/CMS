<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <img src='media/logos/logiks.png' />
                </div>
                <div class="panel-body">
                    {if isset($ERROR_MSG) }
                        <div class='alert alert-danger'>
                            {$ERROR_MSG}
                            <span class="close" data-dismiss="alert">&times;</span>
                        </div>
                    {/if}
                    <form role="form" action="{_service("auth")}" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="E-mail" name="userid" type="text" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <!-- <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                </label>
                            </div> -->
                            <!-- Change this to a button or input when using this as a form -->
                            <button type="submit" class="btn btn-lg btn-info btn-block">Login</button>
                        </fieldset>
                    </form>
                    <a class='pull-right' href='{_link("forgotpwd")}'>Forgot Password</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
if(top!=window) top.location.reload();
$(function() {
    $(".alert .close").click(function() {
        $(this).closest(".alert").detach();
    });
});
</script>
