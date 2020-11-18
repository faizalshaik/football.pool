<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">
			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="page-title">Dashboard</h4>
					<p class="text-muted page-title-alt">Welcome to Admin Pannel!</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 col-lg-4">
					<div class="widget-bg-color-icon card-box fadeInDown animated">
						<div class="bg-icon bg-icon-primary pull-left">
							<i class="md  md-attach-money text-primary"></i>
						</div>
						<div class="text-right">
							<h3 class="text-dark"><b class="counter"><?php echo $total_bets; ?></b></h3>
							<p class="text-muted">Total Bets</p>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>

				<div class="col-md-6 col-lg-4">
					<div class="widget-bg-color-icon card-box">
						<div class="bg-icon bg-icon-pink pull-left">
							<i class="md   md-account-child text-pink"></i>
						</div>
						<div class="text-right">
							<h3 class="text-dark"><b class="counter"><?php echo $total_agents; ?></b></h3>
							<p class="text-muted">Total Agents</p>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>

				<div class="col-md-6 col-lg-4">
					<div class="widget-bg-color-icon card-box">
						<div class="bg-icon bg-icon-info pull-left">
							<i class="md  md-perm-device-info text-info"></i>
						</div>
						<div class="text-right">
							<h3 class="text-dark"><b class="counter"><?php echo $total_terminals; ?></b></h3>
							<p class="text-muted">Total Terminals</p>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="panel panel-color panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Finance Dashboard for the Week</h3>
					</div>
					<div class="panel-body" style="background-color:lightblue;">
						<div class="row">
							<div class="col-md-6 col-lg-3">
								<div class="widget-bg-color-icon card-box fadeInDown animated">
									<div class="bg-icon bg-icon-pink pull-left">
										<i class="md  md-account-balance text-danger"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_sale; ?></b></h3>
										<p class="text-muted">Total Sales Amount</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>

							<div class="col-md-6 col-lg-3">
								<div class="widget-bg-color-icon card-box">
									<div class="bg-icon bg-icon-primary pull-left">
										<i class="md md-add-shopping-cart text-primary"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_payable; ?></b></h3>
										<p class="text-muted">Total Commission paid to Agents</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>

							<div class="col-md-6 col-lg-3">
								<div class="widget-bg-color-icon card-box">
									<div class="bg-icon bg-icon-success pull-left">
										<i class="md  md-stars text-success"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_win; ?></b></h3>
										<p class="text-muted">Winnings paid to Agents</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>

							<div class="col-md-6 col-lg-3">
								<div class="widget-bg-color-icon card-box">
									<div class="bg-icon bg-icon-purple pull-left">
										<i class="md  md-account-balance-wallet text-purple"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_profit; ?></b></h3>
										<p class="text-muted">Balance Profit or Loss</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="row">
				<div class="panel panel-color panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Void Bets Dashboard for the Week</h3>
					</div>
					<div class="panel-body" style="background-color:lightblue;">
						<div class="row">
							<div class="col-md-6 col-lg-4">
								<div class="widget-bg-color-icon card-box fadeInDown animated">
									<div class="bg-icon bg-icon-info pull-left">
										<i class="md md-list text-info"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_reqs; ?></b></h3>
										<p class="text-muted">Total Requests</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>

							<div class="col-md-6 col-lg-4">
								<div class="widget-bg-color-icon card-box">
									<div class="bg-icon bg-icon-primary pull-left">
										<i class="md md-done text-primary"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_approved; ?></b></h3>
										<p class="text-muted">Aprroved Requests</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>

							<div class="col-md-6 col-lg-4">
								<div class="widget-bg-color-icon card-box">
									<div class="bg-icon bg-icon-danger pull-left">
										<i class="md md-clear text-danger"></i>
									</div>
									<div class="text-right">
										<h3 class="text-dark"><b class="counter"><?php echo $total_dismissed; ?></b></h3>
										<p class="text-muted">Dismissed Requests</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>

						</div>

					</div>
				</div>
			</div>
			<!-- profile -->
			<!-- <div class="row">
		                <div class="col-sm-12">
		                    <div class="card-box">
		                        <h4 class="m-t-0 header-title"> My Account </h4>
		                        <p class="text-muted font-13 m-b-30">
		                            You can modify admin account here.
		                        </p>
		                        <div class="row">
		                            <div class="col-lg-9">
		                                <form action="<?php echo base_url() . 'Cms/updateAccount' ?>" data-parsley-validate="" novalidate="" class="form-horizontal" method="post">
		                                    <div class="form-group">
		                                        <label class="col-lg-4 control-label" for="emailAddress">
		                                            Email address*
		                                        </label>
		                                        <div class="col-lg-8">
		                                            <input name="email" parsley-trigger="change" required="" placeholder="Enter email" class="form-control" id="emailAddress" type="email">
		                                        </div>
		                                    </div>
		                                    <div class="form-group">
		                                        <label class="col-lg-4 control-label" for="pass1">Password*</label>
		                                        <div class="col-lg-8">
		                                            <input id="pass1" placeholder="Password" required="" class="form-control" type="password" name="password">
		                                        </div>
		                                    </div>
		                                    <div class="form-group">
		                                        <label class="col-lg-4 control-label" for="passWord2">
		                                            Confirm Password *
		                                        </label>
		                                        <div class="col-lg-8">
		                                            <input data-parsley-equalto="#pass1" required="" placeholder="Password" class="form-control" id="passWord2" type="password">
		                                        </div>
		                                    </div>
		                                    <div class="form-group text-left m-b-10">
		                                        <div class="col-lg-8 col-lg-offset-4">
		                                            <button class="btn btn-primary waves-effect waves-light" type="submit">
		                                                Submit
		                                            </button>
		                                        </div>
		                                    </div>
		                                </form>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div> -->
			<!-- profile -->
		</div> <!-- container -->
	</div> <!-- content -->