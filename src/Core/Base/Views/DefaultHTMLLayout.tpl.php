<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>PTBuild - Pharaoh Tools</title>

    <!-- Bootstrap core CSS -->
    <link href="Assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="Assets/css/style.css" rel="stylesheet">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>


    <![endif]-->
    <!--  sign up add -->

    <script src="/index.php?control=AssetLoader&action=show&module=Signup&type=js&asset=signup.js"></script>
    <script src="/index.php?control=AssetLoader&action=show&module=PostInput&type=js&asset=jquery.min.js"></script>
    <script src="/index.php?control=AssetLoader&action=show&module=PostInput&type=js&asset=jquery-ui.min.js"></script>
    <style>
        body{
            padding-top: 72px;
        }
    </style>
</head>

<body>
<input type="hidden" id="base_url" value="http://www.ptbuild.tld">
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/index.php?control=Index&action=show"><img src="/index.php?control=AssetLoader&action=show&module=PostInput&type=image&asset=5.png" class="navbar-img" /></a>
            <a class="navbar-brand" href="/index.php?control=Index&action=show">PTBuild</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <?php if($pageVars["route"]["control"] == "Signup"){ ?>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="/index.php?control=Signup&action=login">Login</a></li>
                    <li><a href="/index.php?control=Signup&action=registration">Registration</a></li>
                </ul>

                <form class="navbar-form navbar-right" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-info">Submit</button>
                </form>

            </div><!-- /.navbar-collapse -->
        <?php }else{?>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!--
                    <li class="active"><a href="/index.php?control=BuildConfigure&action=new">New Pipeline</a></li>
                    <li><a href="#">History</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Others <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                            <li class="divider"></li>
                            <li><a href="#">One more separated link</a></li>
                        </ul>
                    </li>
                    <li><a href="/index.php?control=Signup&action=logout">Logout</a></li>
                    -->
                </ul>
                <form class="navbar-form navbar-right" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-info">Submit</button>
                </form>
            </div><!-- /.navbar-collapse -->
        <?php } ?>

    </div><!-- /.container-fluid -->
</nav>

<?php echo $this->renderMessages($pageVars); ?>
<?php echo $templateData; ?>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/index.php?control=AssetLoader&action=show&module=PostInput&type=js&asset=bootstrap.min.js"></script>
<script>
    $(function() {
        $('#slide-submenu').on('click', function() {
            $(this).closest('.list-group').fadeOut('slide', function() {
                $('.mini-submenu').fadeIn();
            });
        });

        $('.mini-submenu').on('click', function() {
            $(this).next('.list-group').toggle('slide');
            $('.mini-submenu').hide();
        });

        $.ajax({
            type: 'POST',
            url: $('#base_url').val() + '/index.php?control=Signup&action=login-status',
            data: {
                url:document.URL
            },
            dataType: "json",
            success: function(result)
            {
                if(result.status == false){
                    window.location.assign($('#base_url').val() + '/index.php?control=Signup&action=login');
                }
            }
        });
    })


</script>
</body>
</html>
