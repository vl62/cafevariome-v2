<div class="container">
	<!--<div class="container-fluid">-->
	<div class="row-fluid">
		<div class="span8 offset3">
			<h1>Delete Network Group</h1>
			<p>Are you sure you want to delete the group <?php // echo $group->name; ?></p>

			<?php echo form_open("groups/delete_network_group/" . $group_id); ?>

			<p>
				<label for="confirm">Yes:</label>
				<input type="radio" name="confirm" value="yes" checked="checked" />
				<br />
				<br />
				<label for="confirm">No:</label>
				<input type="radio" name="confirm" value="no" />
			</p>
			<br />
			<?php echo form_hidden($csrf); ?>
			<?php echo form_hidden(array('id' => $group_id)); ?>

			<p><button type="submit" name="submit" class="btn btn-primary"><i class="icon-remove icon-white"></i>  Delete Group</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url() . "groups";?>" class="btn" ><i class="icon-step-backward"></i> Go back</a></p></p>
			
		</div>
	</div>
</div>
<?php echo form_close(); ?>