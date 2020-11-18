<link href="<?php echo base_url('assets/plugins/custombox/css/custombox.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js'); ?>"></script>

<script src="<?php echo base_url('assets/plugins/moment/moment.js'); ?>"></script>
<link href="<?php echo base_url('assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet'); ?>">
<script src="<?php echo base_url('assets/plugins/timepicker/bootstrap-timepicker.js'); ?>"></script>

<link href="<?php echo base_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>


<link href="<?php echo base_url('assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css" rel="stylesheet'); ?>">
<script src="<?php echo base_url('assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js'); ?>"></script>


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
                    <h4 class="page-title">Settings</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="btn-group pull-right">
                                <div class="m-b-10">
                                    <button id="addToTable" class="btn btn-default waves-effect waves-light" onclick="addNew();">Add Under <i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <h3 class="panel-title">Unders</h3>
                        </div>

                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Unders</th>                                
                                    <th>Name</th>
                                    <th>Commission</th>
                                    <th>Max Stake</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Weeks</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group has-success">
                                    <label class="col-md-3 control-label">Week</label>
                                    <div class="col-md-9">
                                        <select class="selectpicker" name="weeks" id="weeks" data-style="btn-default btn-custom" onchange="onChangeWeek();">
                                            <?php foreach ($weeks as $week) { ?>
                                                <option value="<?php echo $week->Id; ?>" <?php if ($curWeekNo == $week->week_no) echo 'selected'; ?>><?php echo $week->week_no; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">From</label>
                                    <div class="col-sm-9">
                                        <div class="input-daterange input-group" id="date-range-new">
                                            <input type="text" class="form-control" id="start-new" name="start-new" />
                                            <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                            <input type="text" class="form-control" id="end-new" name="end-new" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">TYPES</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="types-new" name="types-new">
                                            <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                        </div><!-- input-group -->
                                    </div>
                                </div>

                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">Time to Void Bets</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="bootstrap-timepicker">
                                                <input class="vertical-spin form-control" value="<?php if ($curweek) echo $curweek->void_bet; ?>" name="void-time-new" id="void-time-new" type="text" data-bts-min="0" data-bts-max="168">
                                            </div>
                                            <span class="input-group-addon bg-custom b-0 text-white"><i class="glyphicon glyphicon-time"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">Min Stake</label>
                                    <div class="col-sm-9">
                                        <input class="vertical-spin form-control" name="min-stake-new" id="min-stake-new" type="text" data-bts-min="0" data-bts-max="1000">
                                    </div>
                                </div>
                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">Max Stake</label>
                                    <div class="col-sm-9">
                                        <input class="vertical-spin form-control" name="max-stake-new" id="max-stake-new" type="text" data-bts-min="0" data-bts-max="1000000">
                                    </div>
                                </div>
                                <hr>
                                <!-- <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onSaveNewWeek();">Save</button>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Current Week</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group has-success">
                                    <label class="col-md-3 control-label">Week</label>
                                    <div class="col-md-9">
                                        <input class="vertical-spin form-control" id="week-no-cur" value="<?php if ($curweek) echo $curweek->week_no; ?>" type="text" data-bts-min="0" data-bts-max="1000">
                                    </div>
                                </div>
                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">From</label>
                                    <div class="col-sm-9">
                                        <div class="input-daterange input-group" id="date-range-cur">
                                            <input type="text" value="<?php if ($curweek) echo $curweek->start_at; ?>" class="form-control" id="start-cur" name="start-cur" />
                                            <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                            <input type="text" value="<?php if ($curweek) echo $curweek->close_at; ?>" class="form-control" id="end-cur" name="end-cur" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">TYPES</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="<?php if ($curweek) echo $curweek->types; ?>" id="types-cur" name="types-cur">
                                            <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                        </div><!-- input-group -->
                                    </div>
                                </div>

                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">Time to Void Bets</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="bootstrap-timepicker">
                                                <input class="vertical-spin form-control" value="<?php if ($curweek) echo $curweek->void_bet; ?>" name="void-time-cur" id="void-time-cur" type="text" data-bts-min="0" data-bts-max="168">
                                            </div>
                                            <span class="input-group-addon bg-custom b-0 text-white"><i class="glyphicon glyphicon-time"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">Min Stake</label>
                                    <div class="col-sm-9">
                                        <input class="vertical-spin form-control" value="<?php if ($curweek) echo $curweek->min_stake; ?>" name="min-stake-cur" id="min-stake-cur" type="text" data-bts-min="0" data-bts-max="1000">
                                    </div>
                                </div>
                                <div class="form-group has-success">
                                    <label class="control-label col-sm-3">Max Stake</label>
                                    <div class="col-sm-9">
                                        <input class="vertical-spin form-control" value="<?php if ($curweek) echo $curweek->max_stake; ?>" name="max-stake-cur" id="max-stake-cur" type="text" data-bts-min="0" data-bts-max="1000000">
                                    </div>
                                </div>
                                <hr>
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onSaveCurWeek();">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <h3 class="panel-title">Bettings (void/active)</h3>
                        </div>
                        <p><b>Week</b> : <?php echo $curWeekSt['week']; ?>   <b>Void</b> : <?php echo $curWeekSt['voids']; ?>   <b>Active</b> : <?php echo $curWeekSt['actives']; ?></p>
                        <p><b>Week</b> : <?php echo $curWeekMSt['week']; ?>   <b>Void</b> : <?php echo $curWeekMSt['voids']; ?>   <b>Active</b> : <?php echo $curWeekMSt['actives']; ?></p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <h3 class="panel-title">PANEL PRIZE</h3>
                        </div>
                        <input type="text" class="form-control" value="<?php echo $panelPrize; ?>"  id="panel-prize" name="panel-prize">
                        <div class="btn-group pull-right m-t-10">
                            <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onSavePanelPrize();">Save</button>
                        </div>

                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <h3 class="panel-title">Max Win</h3>
                        </div>
                        <input type="text" class="form-control" value="<?php echo $maxWin; ?>"  id="max-win" name="max-win">
                        <div class="btn-group pull-right m-t-10">
                            <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onSaveMaxWin();">Save</button>
                        </div>

                    </div>
                </div>
                

            </div>
                        

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Clear Placed Bets</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <ul class="list-inline status-list m-t-20">
                                        <li>
                                            <h3 class="text-primary m-b-5"><?php echo $voidBets; ?></h3>
                                            <p class="text-muted">Total Void Bets</p>
                                        </li>
                                        <li>
                                            <h3 class="text-success m-b-5"><?php echo $curweekVoidBets; ?></h3>
                                            <p class="text-muted">Current Week Void Bets</p>
                                        </li>
                                        <li>
                                            <h3 class="text-danger m-b-5"><?php echo $curweekBets; ?></h3>
                                            <p class="text-muted">Current Week Bets</p>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="col-md-4 btn-group m-t-30">
                                    <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onSave();">Clear All Bets Database</button>
                                </div> -->
                            </div>

                            <form class="form-horizontal" role="form">
                                <div class="row m-t-30">
                                    <div class="col-md-8">
                                        <div class="form-group has-success">
                                            <label class="col-md-4 control-label">Select Week</label>
                                            <div class="col-md-4">
                                                <select class="selectpicker" name="clr_week" id="clr_week" data-style="btn-default btn-custom">
                                                    <!-- <option value="0" selected>ALL</option> -->
                                                    <?php foreach ($weeks as $week) {
                                                        if ($week->week_no != $curWeekNo) { ?>
                                                            <option value="<?php echo $week->week_no; ?>" <?php if ($curWeekNo == $week->week_no) echo 'selected'; ?>><?php echo $week->week_no; ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" type="text" id="clr_token" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onClearDB();">Clear Bets Database</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Add Credits to Teminals</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="row m-t-30">
                                    <div class="form-group has-success">
                                        <label class="col-md-3 control-label">Credits</label>
                                        <div class="col-md-6">
                                            <input class="vertical-spin form-control" type="text" value="10000000" data-bts-min="10000000" data-bts-max="10000000000" id="credit_amount">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-pink btn-custom waves-effect waves-light" onclick="onAddCredit();">Add</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-30 m-b-30 text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-pink btn-custom waves-effect waves-light" onclick="onRemoveCredit();">Remove All Credits to Terminals</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
        <h4 class="custom-modal-title">Edit Under</h4>
        <div class="custom-modal-text text-left">
            <div class="profile-detail card-box">
                <form class="form-horizontal" role="form" style="width:400px;">
                    <input type="hidden" id="underId" />
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Nmae</label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="underName" name="underName">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Under</label>
                        <div class="col-md-9">
                            <input class="form-control" type="number" id="under" name="under">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Commission</label>
                        <div class="col-md-9">
                            <input class="form-control" type="number" step="0.1" id="commission" name="commission">
                        </div>
                    </div>                    
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Max Stake</label>
                        <div class="col-md-9">
                            <input class="form-control" type="number" id="maxStake" name="maxStake">
                        </div>
                    </div>
                    <div>
                        <hr>
                        <button type="button" class="btn btn-pink btn-custom  waves-effect waves-light" onclick="onSave();">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
    <script type="text/javascript">
        jQuery('#start-new').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
        });
        jQuery('#end-new').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            // use24hours: true
        });
        jQuery('#start-cur').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            // use24hours: true
        });
        jQuery('#end-cur').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            // use24hours: true
        });

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
                },
                {
                    targets: [3], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [-1], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ], "<?php echo site_url('Cms_api/get_unders') ?>");

        function addNew() {
            Custombox.open({
                target: "#eidt-modal",
                effect: "fadein",
                overlaySpeed: "200",
                overlayColor: "#36404a"
            });
        }

        var $dom = {
            underId: $("#underId"),
            underName: $("#underName"),
            commission:$("#commission"),
            under: $("#under"),
            maxStake: $("#maxStake")
        }

        function clearData() {
            $dom.underId.val("");
            $dom.underName.val("");
            $dom.under.val("1");
            $dom.commission.val("0.1");
            $dom.maxStake.prop('checked', true);
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
                    $dom.underId.val(data.Id);
                    $dom.underName.val(data.name);
                    $dom.commission.val(data.commission);
                    $dom.under.val(data.under);
                    $dom.maxStake.val(data.max_stake);  
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

        function onEnable(id, status)
        {
            $.ajax({
                    url: "<?php echo site_url('Cms_api/enable_under') ?>",
                    data: {
                        Id: id,
                        status: status
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        tbl.ajax.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    }
                });            
        }

        function onSave() {
            Custombox.close();
            $.ajax({
                url: "<?php echo site_url('Cms_api/edit_under') ?>",
                data: {
                    Id: $dom.underId.val(),
                    name: $dom.underName.val(),
                    commission:$dom.commission.val(),
                    under: $dom.under.val(),
                    max_stake: $dom.maxStake.val()
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

        function onSaveCurWeek() {
            var weekNo = document.getElementById("week-no-cur").value;
            if (weekNo == "" || weekNo <= 0) return;

            var startAt = document.getElementById("start-cur").value;
            var closeAt = document.getElementById("end-cur").value;
            var types = document.getElementById("types-cur").value;
            var voidBet = document.getElementById("void-time-cur").value;
            var minStake = document.getElementById("min-stake-cur").value;
            var maxStake = document.getElementById("max-stake-cur").value;

            $.ajax({
                url: "<?php echo site_url('Cms_api/edit_week/1') ?>",
                data: {
                    week_no: weekNo,
                    start_at: startAt,
                    close_at: closeAt,
                    types: types,
                    void_bet: voidBet,
                    min_stake: minStake,
                    max_stake: maxStake
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {},
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }

        function onSavePanelPrize(){
            var prize = document.getElementById("panel-prize").value;
            $.ajax({
                url: "<?php echo site_url('Cms_api/edit_panel_prize') ?>",
                data: {
                    prize: prize
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {},
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }
        
        function onSaveMaxWin(){
            var maxwin = document.getElementById("max-win").value;
            $.ajax({
                url: "<?php echo site_url('Cms_api/edit_max_win') ?>",
                data: {
                    maxwin: maxwin
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {},
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }        

        // function onSaveNewWeek() {
        //     var weekNo = document.getElementById("week-no-new").value;
        //     if (weekNo == "" || weekNo <= 0) return;

        //     var startAt = document.getElementById("start-new").value;
        //     var closeAt = document.getElementById("end-new").value;
        //     var validity = document.getElementById("validity-new").value;
        //     var voidBet = document.getElementById("void-time-new").value;
        //     var minStake = document.getElementById("min-stake-new").value;
        //     var maxStake = document.getElementById("max-stake-new").value;

        //     $.ajax({
        //         url: "<?php echo site_url('Cms_api/edit_week') ?>",
        //         data: {
        //             week_no: weekNo,
        //             start_at: startAt,
        //             close_at: closeAt,
        //             validity: validity,
        //             void_bet: voidBet,
        //             min_stake: minStake,
        //             max_stake: maxStake
        //         },
        //         type: "POST",
        //         dataType: "JSON",
        //         success: function(data) {

        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             swal("Error!", "", "error");
        //         }
        //     });
        // }

        function onAddCredit() {
            var amount = Number(document.getElementById("credit_amount").value);
            if (amount == 0) return;
            $.ajax({
                url: "<?php echo site_url('Cms_api/addTerminalCredit') ?>",
                data: {
                    amount: amount
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    if (data.status == 200)
                        swal("Success!", "", "success");
                    else
                        swal("Error!", data.message, "error");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }

        function onRemoveCredit() {
            var amount = Number(document.getElementById("credit_amount").value);
            if (amount == 0) return;
            $.ajax({
                url: "<?php echo site_url('Cms_api/removeTerminalCredit') ?>",
                data: {
                    amount: amount
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    if (data.status == 200)
                        swal("Success!", "", "success");
                    else
                        swal("Error!", data.message, "error");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }

        function onClearDB() {

            var week = document.getElementById('clr_week').value;
            var token = document.getElementById('clr_token').value;
            if (token == "") return;

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this week information!",
                type: "error",
                showCancelButton: true,
                cancelButtonClass: 'btn-white btn-md waves-effect',
                confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                confirmButtonText: 'Remove',
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "<?php echo site_url('Cms_api/clearDB') ?>",
                        data: {
                            week: week,
                            token: token
                        },
                        type: "POST",
                        dataType: "JSON",
                        success: function(data) {
                            if (data.status == 200)
                                swal("Remove!", "", "success");
                            else
                                swal("Error!", data.message, "error");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            swal("Error!", "", "error");
                        }
                    });
                }
            });

        }

        function onChangeWeek() {
            weekId = document.getElementById('weeks').value;
            $.ajax({
                url: "<?php echo site_url('Cms_api/getDataById') ?>",
                data: {
                    Id: weekId,
                    tbl_Name: 'tbl_week'
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    document.getElementById("start-new").value = data.start_at;
                    document.getElementById("end-new").value = data.close_at;
                    document.getElementById("types-new").value = data.types;
                    document.getElementById("void-time-new").value = data.void_bet;
                    document.getElementById("min-stake-new").value = data.min_stake;
                    document.getElementById("max-stake-new").value = data.max_stake;
                },
                error: function(jqXHR, textStatus, errorThrown) {

                }
            });
        }

        onChangeWeek();
    </script>