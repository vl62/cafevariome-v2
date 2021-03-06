<div class="container">
	<div class="row">  
		<div class="span6">  
			<ul class="breadcrumb">  
				<li>  
					<a href="<?php echo base_url() . "admin";?>">Dashboard Home</a> <span class="divider">></span>  
				</li>
				<li>
					<a href="<?php echo base_url() . "federated_settings";?>">Federated Settings</a> <span class="divider">></span>
				</li>
				<li class="active">Create Network</li>
			</ul>  
		</div>  
	</div>
	<div class="row-fluid">
		<div class="span9 offset2 pagination-centered">
			<div class="well">
				<h2>Create Network</h2>
				<p>Please enter the network information below.</p>
                                <b><strong id="createNetworkError" style="color: red;" class="hide"><?php // echo validation_errors(); ?></strong></b>
				<?php echo form_open("federated_settings/create_network", array('name' => 'createNetwork')); ?>
				<p>
					Network Name:<br />
					<?php echo form_input($name); ?>
                                <div class="hide" id="errText" style="color: red;"></div>
                                        <br />
					<small>(no spaces allowed but underscores and dashes are accepted, <br />uppercase characters will be converted to lowercase)</small>
				</p>
                                <p><button type="submit" name="submit" onclick="create_network();" class="btn btn-primary"><i class="icon-file icon-white"></i>  Add Network</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url() . "federated_settings"; ?>" class="btn" ><i class="icon-step-backward"></i> Go back</a></p>
				<?php 
				if (isset($result)) {
					echo "<p>Network was successfully added</p>";
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
