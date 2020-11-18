<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">
			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="page-title">Modify Personal Data</h4>
				</div>
			</div>
			<hr>

			<!-- profile -->
			<div class="row">
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
											<input name="email" parsley-trigger="change" required="" placeholder="Enter email" class="form-control" id="emailAddress" type="email" value="<?php echo $user->email; ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label" for="pass1">Password*</label>
										<div class="col-lg-8">
											<input id="pass1" placeholder="Password" required="" class="form-control" type="password" name="password" value="<?php echo $user->password; ?>">
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
			</div><!-- profile -->
		</div> <!-- container -->
	</div> <!-- content -->