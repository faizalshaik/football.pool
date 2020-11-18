<link href="<?php echo base_url('assets/plugins/custombox/css/custombox.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js'); ?>"></script>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="page-title">Office Staffs</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="btn-group pull-right">
                                <div class="m-b-30">
                                    <button id="addToTable" class="btn btn-default waves-effect waves-light" onclick="addNew();">Add Staff <i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Password</th>
                                    <th>User Email</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        </div> <!-- container -->
    </div> <!-- content -->


<!-- dialog -->

<div id="eidt-modal" class="modal-demo col-sm-12" style="padding: 0px !important;">
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title">Edit Staff</h4>
    <div class="custom-modal-text text-left">
        <div class="profile-detail card-box">
            <form class="form-horizontal" role="form" style="width:480px;">
                <input type="hidden" id="Id" />
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">User Id</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="user_id" name="user_id">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Password</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="password" name="password">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Email</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="email" name="email">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">First Name</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="firstname" name="firstname">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Last Name</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="lastname" name="lastname">
                    </div>
                </div>

                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Phone</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="phone" name="phone">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Address</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="address" name="address">
                    </div>
                </div>
                <div>
                    <hr>
                    <button type="button" class="btn btn-pink btn-custom btn-rounded waves-effect waves-light" onclick="onSave();">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
<script type="text/javascript">
    function initTable(tagId, cols, dataUrl) {
        var tblObj = $(tagId).DataTable({
            dom: "lfBrtip",
            buttons: [{
                extend: "copy",
                className: "btn-sm"
            }, {
                extend: "csv",
                className: "btn-sm"
            }, {
                extend: "excel",
                className: "btn-sm"
            }, {
                extend: "pdf",
                className: "btn-sm"
            }, {
                extend: "print",
                className: "btn-sm"
            }],
            responsive: !0,
            processing: true,
            serverSide: false,
            sPaginationType: "full_numbers",
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    first: '<i class="fa fa-angle-double-left"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            //Set column definition initialisation properties.
            columnDefs: cols,
            ajax: {
                url: dataUrl,
                type: "POST",
            },
        });
        return tblObj;
    }
    var tableName = "<?php echo $table; ?>";
    var tbl;

    tbl = initTable("#table1",
        [{
                targets: [0], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [1], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [2], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }, {
                targets: [3], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }, {
                targets: [4], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }, {
                targets: [5], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }, {
                targets: [6], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }, {
                targets: [7], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }, {
                targets: [8], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [-1], //last column
                orderable: false, //set not orderable
                className: "actions dt-center"
            }
        ], "<?php echo site_url('Cms_api/get_staffs') ?>");

    var $dom = {
        Id: $("#Id"),
        user_id: $("#user_id"),
        password: $("#password"),
        email: $("#email"),
        firstname: $("#firstname"),
        lastname: $("#lastname"),
        staff: $("#staff"),
        phone: $("#phone"),
        address: $("#address")
    }

    function clearData() {
        $dom.Id.val("");
        $dom.user_id.val("");
        $dom.password.val("");
        $dom.email.val("");
        $dom.firstname.val("");
        $dom.lastname.val("");
        $dom.staff.val("");
        $dom.phone.val("");
        $dom.address.val("");
    }

    function addNew() {
        clearData();
        Custombox.open({
            target: "#eidt-modal",
            effect: "fadein",
            overlaySpeed: "200",
            overlayColor: "#36404a"
        });
    }

    function onEdit(_idx) {
        clearData();
        $.ajax({
            url: "<?php echo site_url('Cms_api/getDataById') ?>",
            data: {
                Id: _idx,
                tbl_Name: tableName
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $dom.Id.val(data.Id);
                $dom.user_id.val(data.user_id);
                $dom.password.val(data.password);
                $dom.email.val(data.email);
                $dom.firstname.val(data.firstname);
                $dom.lastname.val(data.lastname);
                $dom.staff.val(data.staff);
                $dom.phone.val(data.phone);
                $dom.address.val(data.address);
                Custombox.open({
                    target: "#eidt-modal",
                    effect: "fadein",
                    overlaySpeed: "200",
                    overlayColor: "#36404a"
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }

    function onDelete(_idx) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this user information!",
            type: "error",
            showCancelButton: true,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Remove',
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "<?php echo site_url('Cms_api/delData') ?>",
                    data: {
                        Id: _idx,
                        tbl_Name: tableName
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        swal("Remove!", "", "success");
                        tbl.ajax.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        swal("Error!", "", "error");
                    }
                });
            }
        });
    }

    function onSave() {
        Custombox.close();
        $.ajax({
            url: "<?php echo site_url('Cms_api/edit_staff') ?>",
            data: {
                Id: $dom.Id.val(),
                user_id: $dom.user_id.val(),
                password: $dom.password.val(),
                email: $dom.email.val(),
                firstname: $dom.firstname.val(),
                lastname: $dom.lastname.val(),
                staff: $dom.staff.val(),
                phone: $dom.phone.val(),
                address: $dom.address.val(),
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tbl.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }
</script>