<div class="wrap">
	<?php //echo "<pre>"; var_dump($leads); echo "<pre>";  ?>
	<div id="icon-options-general" class="icon32"><br /></div>
	<h1>IDX MailChimp Sync</h1>
	<form method="post" action="options.php">
		<div class="container-fluid">
			<div class="row">
				<h3>Sync IDX Leads to MailChimp List <!-- <span class="dashicons dashicons-image-rotate"></span> --></h3> 
				<div class="col-md-6">
					<h3>IDX Leads</h3>
					<table id="table-idx-leads" class="table">
						<thead>
							<tr>
								<th><input type="checkbox" name="all" id="checkall" />Sync</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!is_null($leads)): ?>
							<?php foreach ($leads as $lead): ?>
								<tr>
									<td data-id-idx="<?php echo $lead->id; ?>">
										<input name="is-sync-idx" type="checkbox" value="">
									</td>
									<td data-firstname-idx="<?php echo $lead->firstName; ?>"><?php echo $lead->firstName; ?></td>
									<td data-lastname-idx="<?php echo $lead->lastName; ?>"><?php echo $lead->lastName; ?></td>
									<td data-email-idx="<?php echo $lead->email; ?>"><?php echo $lead->email; ?></td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
								<p>No have leads</p>
						<?php endif ?>							
						</tbody>
					</table>
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-2">
							<h3>MailChimp</h3>
						</div>
						<div class="col-md-3 list-mailchimp">								
								<select name="" id="list-subs" class="form-control" autocomplete="off">
									<option value="empty">Select a list..</option>
									<?php if (!empty($lists->lists)): ?>
										<?php foreach ($lists->lists as $list): ?>
											<option value="<?php echo $list->id;?>"><?php echo $list->name ?></option>
										<?php endforeach ?>										
									<?php endif ?>
								</select>							
						</div>
						<div id="load-members" class="col-md-3">
							<img src="<?php echo get_home_url(); ?>/wp-includes/js/tinymce/skins/lightgray/img/loader.gif" height="23px" alt="" >
						</div>
					</div> 
					<table id="table-members" class="table table-striped">
						 <thead>
							<tr>								
								<th>Email</th>
								<th>First Name</th>
								<th>Last Name</th>
							</tr>
						</thead>
						<tbody id="result-members-mailchimp">
							
						</tbody>	
					</table>
				</div>
			</div>
			
		</div>
		
		
		<p class="submit">
			<button name="sub-idx-to-mailc" id="sub-idx-to-mailc" class="btn btn-lg button-primary">Sync Now</button>
		</p>
	</form>
</div> <!-- .wrap -->
