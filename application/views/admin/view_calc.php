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
            <div class="row m-b-20">
                <div class="col-sm-4">
                    <h4 class="page-title">Calc sytem</h4>
                </div>
                <div class="col-sm-8">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="isGrouping" onclick="onGrouping()">
                        <label for="checkbox1">Grouping Mode                        
                        </label>
                    </div>
                </div>
            </div>

            <div class="row m-b-20">
                <table id="tbl_group" hidden>
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>                        
                    </thead>
                    <tbody>
                        <tr>
                            <td>Group1</td>
                            <td><input type="text" id="group_1" name="group_1" class="form-control" value="1-2"/></td>
                        </tr>
                        <tr>
                            <td>Group2</td>
                            <td><input type="text" id="group_2" name="group_2" class="form-control" value="1-3,4,5"/></td>
                        </tr>
                        <tr>
                            <td>Group3</td>
                            <td><input type="text" id="group_3" name="group_3" class="form-control" value=""/></td>
                        </tr>
                        <tr>
                            <td>Group4</td>
                            <td><input type="text" id="group_4" name="group_4" class="form-control"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group has-success">
                        <label class="col-md-6 control-label">Under</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="under" id="under" data-style="btn-default btn-custom">
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group has-success">
                        <label class="col-md-6 control-label">Events</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="events" id="events" data-style="btn-default btn-custom" onchange="onChangeEvents();">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5" selected>5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row m-t-20">
                <div class="col-sm-3">
                    <div class="form-group has-success">
                        <label class="col-md-6 control-label">Amount</label>
                        <div class="col-md-6">
                            <input type="number" value="300" id="amt" name="amt" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 text-right">
                    <button class="btn btn-info" onclick="onClac();">Calculate</button>
                </div>
            </div>

            <div class="row m-t-20">
                <div class="col-sm-8">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Result</b></h4>
                        <div class="row">
                            <div class="col-md-12">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Result</label>
                                        <div class="col-md-1">
                                            <label class="control-label  label-info" id="result_state"></label>
                                        </div>
                                        <label class="col-md-2 control-label">Lines</label>
                                        <div class="col-md-1">
                                            <label class="control-label label-info" id="result_lines"></label>
                                        </div>
                                        <label class="col-md-2 control-label">Apl</label>
                                        <div class="col-md-1">
                                            <label class="control-label label-info" id="result_apl"></label>
                                        </div>

                                        <label class="col-md-2 control-label">Won</label>
                                        <div class="col-md-1">
                                            <label class="control-label label-info" id="result_won"></label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row m-t-20">
                <div class="col-sm-8">
                    <div class="card-box table-responsive">
                        <table id="table_bets" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Prize</th>
                                    <th>Win</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-games">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-4">
                    <textarea id="result" rows="8"></textarea>
                </div>
            </div>

        </div> <!-- container -->
    </div> <!-- content -->

    <script type="text/javascript">
        function onChangeEvents() {
            let events = $("#events").val();
            let html = "";

            for (let i = 0; i < events; i++) {
                let id = i + 1;
                let stateStr = '<select id="win_' + id +'">'+
                                '<option value="0">LOST</option>' + 
                                '<option value="1" selected>WIN</option>' + 
                                '<option value="2">PAN</option></select>';
                let tr = "<tr><td>" + id + "</td><td><input type='text' value='" + id + ".0' id='pz_" + id +
                    "'/></td><td>" + stateStr + "</td>";
                html += tr;
            }
            $("#tbody-games").html(html);
        }

        onChangeEvents();

        function onClac() {
            let grouping = 0;
            if($("#isGrouping").prop("checked") == true) 
                grouping = 1;
            if(grouping==1)
            {
                calcGrouping();
                return;
            }

            let amt = parseInt($("#amt").val());
            if (amt <= 0) {
                alert("please input amount");
                return;
            }

            let under = parseInt($("#under").val());
            let events = parseInt($("#events").val());
            if (events < under) {
                alert("evetns must be more than under");
                return;
            }

            let games = [];
            for (let i = 0; i < events; i++) {
                let id = i + 1;
                let pz = parseFloat($("#pz_" + id).val());
                let win = $("#win_" + id).val();
                let game = {
                    no: id,
                    pz: pz,
                    win: win
                };
                games.push(game);
            }
            let data = {
                amt: amt,
                under: under,
                games: games
            };

            $.ajax({
                url: "<?php echo site_url('Calc_api/calcWin') ?>",
                data: JSON.stringify(data),
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    //alert(JSON.stringify(data));                    
                    if (data.status == 200) {
                        //alert(JSON.stringify(data));
                        $("#result").val(JSON.stringify(data));
                        $("#result_state").text(data.message);
                        if (data.data != null) {
                            $("#result_lines").text(data.data.lines);
                            $("#result_apl").text(data.data.apl);
                            $("#result_won").text(data.data.won);
                        } else {
                            $("#result_lines").text('');
                            $("#result_apl").text('');
                            $("#result_won").text('');
                        }

                    } else {
                        swal("Error!", data.message, "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });


        }

        function onGrouping()
        {
            let grouping = 0;
            if($("#isGrouping").prop("checked") == true) 
                grouping = 1;
            if(grouping==1)$("#tbl_group").show();
            else $("#tbl_group").hide();
        }

        function calcGrouping()
        {
            console.log("calcGrouping");
            let amt = parseInt($("#amt").val());
            if (amt <= 0) {
                alert("please input amount");
                return;
            }

            let under = parseInt($("#under").val());
            let events = parseInt($("#events").val());
            if (events < under) {
                alert("evetns must be more than under");
                return;
            }

            let games = [];
            for (let i = 0; i < events; i++) {
                let id = i + 1;
                let pz = parseFloat($("#pz_" + id).val());
                let win = $("#win_" + id).val();
                let game = {
                    no: id,
                    pz: pz,
                    win: win
                };
                games.push(game);
            }


            let groups = [];
            for (let i = 1; i <= 4; i++) {
                let strGroup = $("#group_" + i).val();
                if(strGroup=="")break;
                let data = strGroup.split('-');
                if(data.length !=2)
                {
                    alert("groups format is invalid ex: 1-2,3");
                    return;
                }                

                let group = {
                    under: data[0],
                    games: data[1].split(',')
                };
                groups.push(group);
            }

            if(groups.length==0)
            {
                alert("please input groups");
                    return;
            }


            let data = {
                amt: amt,
                under: under,
                games: games,
                groups:groups
            };

            console.log(data);

            $.ajax({
                url: "<?php echo site_url('Calc_api/calcWinGroup') ?>",
                data: JSON.stringify(data),
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    //alert(JSON.stringify(data));                    
                    if (data.status == 200) {
                        //alert(JSON.stringify(data));
                        $("#result").val(JSON.stringify(data));
                        $("#result_state").text(data.message);
                        if (data.data != null) {
                            $("#result_lines").text(data.data.lines);
                            $("#result_apl").text(data.data.apl);
                            $("#result_won").text(data.data.won);
                        } else {
                            $("#result_lines").text('');
                            $("#result_apl").text('');
                            $("#result_won").text('');
                        }

                    } else {
                        swal("Error!", data.message, "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });


        }
    </script>