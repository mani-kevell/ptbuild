<div class="container">

        <?php if($pageVars["route"]["action"] == "login"){ ?>
            <div class="col-sm-8 col-md-9 clearfix main-container signup-position">
                <h2 class="text-uppercase text-light"><a href="/"> PTBuild - Pharaoh Tools </a></h2>
                <div class="row clearfix no-margin">
                    <h5 class="text-uppercase text-light" style="margin-top: 15px;">
                        Login
                    </h5>
                    <p style="color: #ff6312; margin-left: 137px;" id="login_error_msg"></p>
                    <form class="form-horizontal custom-form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label text-left">User Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="login_username" placeholder="User Name">
                                <span style="color:#FF0000;" id="login_username_alert"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label text-left">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="login_password" placeholder="Password">
                                <span style="color:#FF0000;" id="login_password_alert"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" onclick="submit_login();" class="btn btn-info">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
                <p>
                    ---------------------------------------<br/>
                    Visit www.pharaohtools.com for more
                </p>

            </div>
        <?php }
         if($pageVars["route"]["action"] == "registration"){ ?>
            <div class="col-sm-8 col-md-9 clearfix main-container signup-position">
                <h2 class="text-uppercase text-light"><a href="/"> PTBuild - Pharaoh Tools </a></h2>
                <div class="row clearfix no-margin">
                    <h5 class="text-uppercase text-light" style="margin-top: 15px;">
                        Registration
                    </h5>
                    <p style="color: #ff6312; margin-left: 137px;" id="registration_error_msg"></p>
                    <form class="form-horizontal custom-form">
                        <div class="form-group">
                            <label for="login_username" class="col-sm-2 control-label text-left">User Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="login_username" placeholder="User Name">
                                <span style="color:#FF0000;" id="login_username_alert"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="login_email" class="col-sm-2 control-label text-left">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="login_email" placeholder="User Email">
                                <span style="color:#FF0000;" id="login_email_alert"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="login_password" class="col-sm-2 control-label text-left">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="login_password" placeholder="Password">
                                <span style="color:#FF0000;" id="login_password_alert"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="login_password_match" class="col-sm-2 control-label text-left">Retype Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="login_password_match" placeholder="Password">
                                <span style="color:#FF0000;" id="login_password_match_alert"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" onclick="submit_registration();" class="btn btn-info">Sign up</button>
                            </div>
                        </div>
                    </form>
                </div>
                <p>
                    ---------------------------------------<br/>
                    Visit www.pharaohtools.com for more
                </p>

            </div>
        <?php } ?>


    </div><!---->

<!-- /.container -->

