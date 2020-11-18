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
                    <h4 class="page-title">Result Summary</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-danger btn-custom btn-rounded waves-effect waves-light" onclick="onSearch();">Search</button>
                            </div>
                            <h3 class="panel-title">Result Summary</h3>
                        </div>
                        <div class="panel-body" style="background-color:lightblue;">
                            <form class="form-horizontal" role="form">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Week</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="week" id="week" data-style="btn-default btn-custom" onchange="onChangeWeek();">
                                                    <?php foreach ($weeks as $week) { ?>
                                                    <option value="<?php echo $week->week_no; ?>" <?php if ($curWeekNo == $week->week_no) echo 'selected'; ?>><?php echo $week->week_no; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group has-success">
                                            <label class="col-md-9 control-label">Same Bet Repeated</label>
                                            <div class="col-md-3">
                                                <input class="vertical-spin form-control" type="text" id="bet_repeated" name="bet_repeated" data-bts-min="0" data-bts-max="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">TSN</label>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" id="tsn" name="tsn">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">AMT</label>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" id="amt" name="amt">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Bet Above</label>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" id="bet_above" name="bet_above">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group has-success">
                                            <label class="control-label col-sm-3">From</label>
                                            <div class="col-sm-9">
                                                <div class="input-daterange input-group" id="date-range">
                                                    <input type="text" class="form-control" name="start" id="start" />
                                                    <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                    <input type="text" class="form-control" name="end" id="end" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Bet Status</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="bet_status" id="bet_status" data-style="btn-default btn-custom">
                                                    <option value="Any" selected>Any</option>
                                                    <option value="Win">Win</option>
                                                    <option value="Lost">Lost</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Agent</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="agent" id="agent" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach ($agents as $agent) { ?>
                                                    <option value="<?php echo $agent->Id; ?>"><?php echo $agent->user_id; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Terminal</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="terminal" id="terminal" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach ($terminals as $terminal) { ?>
                                                    <option value="<?php echo $terminal->Id; ?>"><?php echo $terminal->terminal_no; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Unders</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="under" id="under" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach ($unders as $under) { ?>
                                                    <option value="<?php echo $under->under; ?>"><?php echo $under->name; ?></option>
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
                                    <th>Sales</th>
                                    <th>Total Sale</th>
                                    <th>Total Payable to Agents</th>
                                    <th>Win</th>
                                    <th>Total Winning</th>
                                    <th>Bal Agents</th>
                                    <th>Bal Company</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Total Sales</th>
                                    <th>Total Payable</th>
                                    <th>Win</th>
                                    <th>Total Win</th>
                                    <th>Bal Agent</th>
                                    <th>Bal Company</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table3" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Staff</th>
                                    <th>Agent</th>
                                    <th>terminal NO</th>
                                    <th>Sales</th>
                                    <th>Payable</th>
                                    <th>Total Payable</th>
                                    <th>Win</th>
                                    <th>Total Win</th>
                                    <th>Bal Agent</th>
                                    <th>Bal Company</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table4" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>BetID</th>
                                    <th>Player</th>
                                    <th>Option</th>
                                    <th>Under</th>
                                    <th>Game List</th>
                                    <th>Score List</th>
                                    <th>APL</th>
                                    <th>Amount Staked</th>
                                    <th>Status</th>
                                    <th>Win Result</th>
                                    <th>Winning</th>
                                    <th>TSN</th>
                                    <th>TerminalID</th>
                                    <th>Agent</th>
                                    <th>Repeat</th>
                                    <th>Bet Time</th>
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


    <script type="text/javascript">
        jQuery('#start').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            // use24hours: true
        });
        jQuery('#end').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            // use24hours: true
        });

        function initTable(tagId, cols, dataUrl, serverSide = false) {
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
                serverSide: serverSide,
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
        var tbl1, tbl2, tbl3, tbl4;

        tbl1 = initTable("#table1",
            [], "<?php echo site_url('Cms_api/get_result_summary0') . '/' . $curWeekNo; ?>");

        tbl2 = initTable("#table2",
            [], "<?php echo site_url('Cms_api/get_null_list') ?>");

        tbl3 = initTable("#table3",
            [], "<?php echo site_url('Cms_api/get_null_list') ?>");

        tbl4 = initTable("#table4",
            [], "<?php echo site_url('Cms_api/get_result_bets_list') . '/' . $curWeekNo; ?>", true);


        function onSearch() {
            week = document.getElementById('week').value;
            repeat = Number(document.getElementById('bet_repeated').value);
            ticketNo = document.getElementById('tsn').value;
            if (ticketNo == "") ticketNo = "null";

            amt = Number(document.getElementById('amt').value);
            betAbove = Number(document.getElementById('bet_above').value);
            startDt = document.getElementById('start').value;
            if (startDt == "") startDt = "null";

            endDt = document.getElementById('end').value;
            if (endDt == "") endDt = "null";

            betStatus = document.getElementById('bet_status').value;
            agent = document.getElementById('agent').value;
            terminal = document.getElementById('terminal').value;
            under = document.getElementById('under').value;

            let params = '/' + week + '/' + repeat + '/' + ticketNo + '/' + amt + '/' + betAbove + '/' + startDt + '/' +
                endDt + '/' + betStatus + '/' + agent + '/' + terminal + '/' + under;


            tbl1.ajax.url("<?php echo site_url('Cms_api/get_result_summary0') ?>" + params);
            tbl1.ajax.reload();

            tbl4.ajax.url("<?php echo site_url('Cms_api/get_result_bets_list') ?>" + params);
            tbl4.ajax.reload();
        }

        function onChangeWeek() {
            week = document.getElementById('week').value;
            $.ajax({
                url: "<?php echo site_url('Cms_api/getWeek') ?>",
                data: {
                    week: week
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    document.getElementById("start").value = data.start_at;
                    document.getElementById("end").value = data.close_at;
                },
                error: function(jqXHR, textStatus, errorThrown) {}
            });
        }
        onChangeWeek();        
    </script>