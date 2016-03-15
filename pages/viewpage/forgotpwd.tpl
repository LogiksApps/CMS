<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">CMS Login</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="{_service("auth")}" method="POST" autocomplete=off>
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="E-mail" name="userid" type="email" autofocus>
                            </div>
                            <button type="submit" class="btn btn-lg btn-info btn-block">Reset Password</button>
                        </fieldset>
                    </form>
                    <a class='pull-right' href='{_link("login")}'>Login</a>
                </div>
            </div>
        </div>
    </div>
</div>