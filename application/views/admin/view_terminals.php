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
                    <h4 class="page-title">Terminals</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="btn-group pull-right m-b-30">
                                <button class="btn btn-success waves-effect waves-light  m-r-10" onclick="enableAll();">Enable All</button>
                                <button class="btn btn-danger waves-effect waves-light m-r-10" onclick="disableAll();">Disable All</button>
                                <button id="addToTable" class="btn btn-default waves-effect waves-light m-r-10" onclick="addNew();">Add Terminal <i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Password</th>
                                    <th>Agent</th>
                                    <th>Credit Limit</th>
                                    <th>Unders</th>
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
    <button type="button" class="close" onclick="Custombox.close(); tbl.ajax.reload();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title">Edit Terminal</h4>
    <div class="custom-modal-text text-left">
        <div class="profile-detail card-box">
            <form class="form-horizontal" role="form" style="width:480px;">
                <input type="hidden" id="Id" value="" />
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">SN</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="sn" name="sn">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Agent</label>
                    <div class="col-md-9">                                            
                        <select class="selectpicker" name="agent" id="agent" data-style="btn-default btn-custom">
                            <option value="0" selected></option>
                            <?php foreach ($agents as $agent) { ?>
                                <option value="<?php echo $agent->Id; ?>"><?php echo $agent->user_id; ?></option>
                            <?php } ?>
                        </select>                        
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Credit Limit</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="credit_limit" name="credit_limit">
                    </div>
                </div>

                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Password</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="password" name="password">
                    </div>
                </div>

                <ul class="list-inline status-list  has-success m-t-20">
                    <li><label class="control-label text-primary">Unders:</label></li>
                    
                    <?php foreach($unders as $under) { ?>
                        <li>
                            <div class="checkbox checkbox-custom">
                                <input id="chk_u<?php echo $under->under; ?>" type="checkbox" checked>
                                <label for="chk_u<?php echo $under->under; ?>">U<?php echo $under->under; ?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>

                <div>                    
                    <button type="button" class="btn btn-pink btn-custom waves-effect waves-light" onclick="onSave();">Save</button>
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
    var tbl, tblOpt;
    var underCount = "<?php echo count($unders);?>"

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
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [3], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [4], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },  
            {
                targets: [-1], //last column
                orderable: false, //set not orderable
                className: "actions dt-center"
            }
        ], "<?php echo site_url('Cms_api/get_terminals') ?>"
    );


    var $dom = {
        Id: $("#Id"),
        sn: $("#sn"),
        password: $("#password"),
        agent: $("#agent"),
        credit_limit: $("#credit_limit")
    }        

    function clearData() {
        $dom.Id.val("");
        $dom.sn.val("");
        $dom.password.val("");
        $dom.agent.val("");
        $dom.credit_limit.val("");

        for(let i = 1; i<=underCount; i++)
            $("#chk_u"+i).prop('checked', true);
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
                $dom.sn.val(data.terminal_no);
                $dom.password.val(data.password);
                $dom.agent.val(data.agent_id);
                $dom.agent.selectpicker('refresh');

                $dom.credit_limit.val(data.credit_limit);
                for(let i=1; i <= underCount; i++)
                {
                    let mask = Math.pow(2, (i-1));
                    if (data.unders & mask) $("#chk_u" + i).prop('checked', true);
                    else $("#chk_u" + i).prop('checked', false);
                }
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
        if($dom.agent.val() == "0") 
            return;

        Custombox.close();
        var unders = 0;
        for(let i =1; i<=underCount; i++)
        {
            let mask = Math.pow(2, (i-1));
            if (document.getElementById("chk_u" + i).checked) unders += mask;
        }
        
        $.ajax({
            url: "<?php echo site_url('Cms_api/edit_terminal') ?>",
            data: {
                Id: $dom.Id.val(),
                terminal_no: $dom.sn.val(),
                password: $dom.password.val(),
                agent_id: $dom.agent.val(),
                credit_limit: $dom.credit_limit.val(),
                unders: unders
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

    function onEnable(id, status)
    {
        $.ajax({
            url: "<?php echo site_url('Cms_api/enable_terminal') ?>",
            data: {
                Id:id,
                status: status
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

    function enableAll()
    {
        $.ajax({
            url: "<?php echo site_url('Cms_api/enable_all_terminal') ?>",
            data: {},
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
    function disableAll()
    {
        $.ajax({
            url: "<?php echo site_url('Cms_api/disable_all_terminal') ?>",
            data: {},
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