<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Menu
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Menu</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Menu</h3>
                </div>

                <form id="fm-menu" role="form" method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="parent_id">Parent Menu</label>
                            <select id="parent-id" name="parent_id" class="form-control" placeholder="Parent Id"></select>
                        </div>
                        <div class="form-group">
                            <label for="menu_name">Menu Name</label>
                            <input name="menu_name" class="form-control" placeholder="Menu Name">
                        </div>
                        <div class="form-group">
                            <label for="module_name">Module Name</label>
                            <input name="module_name" class="form-control" placeholder="Module Name, put # only if you want to group menu">
                        </div>
                        <div class="form-group">
                            <label for="seq_number">Seq Number</label>
                            <input name="seq_number" class="form-control" placeholder="Seq Number" type="number">
                        </div>
                        <div class="form-group">
                            <label for="menu_icon">Menu Icon</label>
                            <input id="menu-icon" name="menu_icon" class="form-control" placeholder="Menu Icon">
                        </div>
                        <div class="form-group">
                            <label for="type_menu">Type Menu</label>
                            <input name="type_menu" class="form-control" placeholder="Type Menu">
                        </div>

                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/app_menu/menu-form.js' ?>"></script>
