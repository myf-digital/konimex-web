<header class="main-header">
    <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span href="#" data-toggle="offcanvas" role="button" class="logo-mini">
            <img src="<?php echo base_url() ?>assets/images/axion-icon.png" alt="Axion" width="90%">
        </span>
        <!-- logo for regular state and mobile devices -->
        <span href="#" data-toggle="offcanvas" role="button" class="logo-lg">
            <img class="img" src="<?php echo base_url() ?>assets/images/axion.png" alt="Axion" width="200">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a id="navbar_user" href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs"><?php echo $_SESSION['user'] ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">

                            <p>
                                <?php echo $_SESSION['user'] ?>
                                <small><?php echo $_SESSION['user_credential'] ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row"></div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <button id="btn-reset" class="btn btn-default btn-flat">Reset Password</button>
                            </div>
                            <div class="pull-right">
                                <button id="btn-logout" class="btn btn-default btn-flat">Sign out</button>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a id="navbar_setting" href="#" data-toggle="control-sidebar"><i class="fa fa-cog"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<script type="text/javascript">

    (function () {
        const common = new Common();

        $("#btn-logout").click(function () {
            let url = common.baseURL("app_auth/logout");
            $.getJSON(url,  function (response) {
                window.location.replace(common.baseURL())
            });
        });

        $("#btn-reset").click(function () {
            common.direct("change_mypassword");
        });

    })()

</script>
