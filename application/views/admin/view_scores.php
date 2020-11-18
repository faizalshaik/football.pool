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
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Scores</h3>
                        </div>
                        <div class="panel-body" style="background-color:lightblue;">
                            <form class="form-horizontal" role="form">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Week</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="week" id="week" data-style="btn-default btn-custom" onchange="onChangeWeek();">
                                                    <!-- <option value="0" selected>ALL</option> -->
                                                    <?php foreach ($weeks as $week) { ?>
                                                        <option value="<?php echo $week->week_no; ?>" <?php if ($curWeekNo == $week->week_no) echo 'selected'; ?>><?php echo $week->week_no; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Game No</th>
                                    <th>Home</th>
                                    <th>Away</th>
                                    <th>Week</th>
                                    <th>Home Score</th>
                                    <th>Away Score</th>
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



        </div> <!-- container -->
    </div> <!-- content -->


    <!-- dialog -->

    <div id="eidt-modal" class="modal-demo col-sm-12" style="padding: 0px !important;">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Close</span>
        </button>
        <h4 class="custom-modal-title">Edit Score</h4>
        <div class="custom-modal-text text-left">
            <div class="profile-detail card-box">
                <form class="form-horizontal" role="form" style="width:400px;">
                    <input type="hidden" id="Id">
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Game No</label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="game_no" name="game_no">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Home</label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="home" name="home">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Away</label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="away" name="away">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Week</label>
                        <div class="col-md-9">
                            <input class="vertical-spin form-control" type="text" id="week" name="week" data-bts-min="1" data-bts-max="1000">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Home Score</label>
                        <div class="col-md-9">
                            <input class="vertical-spin form-control" type="text" id="home_score" name="home_score" data-bts-min="1" data-bts-max="1000">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label">Away Score</label>
                        <div class="col-md-9">
                            <input class="vertical-spin form-control" type="text" id="away_score" name="away_score" data-bts-min="1" data-bts-max="1000">
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <div class="checkbox checkbox-custom">
                            <input id="status" type="checkbox" checked>
                            <label for="status">Active</label>
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
                    targets: [5], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [-1], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ], "<?php echo site_url('Cms_api/get_scores') . '/' . $curWeekNo ?>"
        );

        var $dom = {
            Id: $("#Id"),
            gameNo: $("#game_no"),
            home: $("#home"),
            away: $("#away"),
            week: $("#week"),
            homeScore: $("#home_score"),
            awayScore: $("#away_score"),
            status: $("#status")
        }

        function clearData() {
            $dom.Id.val("");
            $dom.gameNo.val("");
            $dom.home.val("");
            $dom.away.val("");
            $dom.week.val("");
            $dom.homeScore.val("");
            $dom.awayScore.val("");
            $dom.status.prop('checked', true);
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
                    $dom.gameNo.val(data.game_no);
                    $dom.home.val(data.home_team);
                    $dom.away.val(data.away_team);
                    $dom.week.val(data.week_no);
                    $dom.homeScore.val(data.home_score);
                    $dom.awayScore.val(data.away_score);

                    if (data.status == 1)
                        $dom.status.prop('checked', true);
                    else
                        $dom.status.prop('checked', false);

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
            var status = 0;
            if (document.getElementById("status").checked)
                status = 1;

            $.ajax({
                url: "<?php echo site_url('Cms_api/edit_score') ?>",
                data: {
                    Id: $dom.Id.val(),
                    game_no: $dom.gameNo.val(),
                    home_team: $dom.home.val(),
                    away_team: $dom.away.val(),
                    week_no: $dom.week.val(),
                    home_score: $dom.homeScore.val(),
                    away_score: $dom.awayScore.val(),
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

        function onChangeWeek() {
            week = document.getElementById('week').value;
            tbl.ajax.url("<?php echo site_url('Cms_api/get_scores') ?>" + "/" + week);
            tbl.ajax.reload();
        }
    </script>