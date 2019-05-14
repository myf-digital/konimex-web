<header class="main-header">
    <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>BI</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>S</b>print</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!--
                      <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        -->
                        <span class="hidden-xs"><?php echo $_SESSION['user'] ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">

                            <p>
                                <?php echo $_SESSION['user'] ?>
                                <small><?php echo $_SESSION['user_credential'] ?></small>
                                <small><?php echo $_SESSION['satker'] ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <!--
                              <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                              </div>
                              <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                              </div>
                              <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                              </div>
                                -->
                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <!--

                                <button id="btn-profile" class="btn btn-default btn-flat">Profile</button>
                                -->
                            </div>
                            <div class="pull-right">
                                <button id="btn-logout" class="btn btn-default btn-flat">Sign out</button>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
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
            console.log(url);
            $.getJSON(url,  function (response) {
                window.location.replace(common.baseURL())
            });
        });

    })()

</script>