<div class="container">
	<!--<div class="container-fluid">-->
	<div class="row-fluid">
		<div class="span8 offset4">
			<h1>Users</h1>
			<p>Below is a list of the users.</p>

			<div id="infoMessage"><?php echo $message; ?></div>

			<table cellpadding=0 cellspacing=10>
				<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email</th>
					<th>Groups</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<?php foreach ($users as $user): ?>
					<tr>
						<td><?php echo $user->first_name; ?></td>
						<td><?php echo $user->last_name; ?></td>
						<td><?php echo $user->email; ?></td>
						<td>
							<?php foreach ($user->groups as $group): ?>
								<?php echo anchor("auth_federated/edit_group/" . $group->id, $group->name); ?><br />
							<?php endforeach ?>
						</td>
						<td><?php echo ($user->active) ? anchor("auth_federated/deactivate/" . $user->id, 'Active') : anchor("auth_federated/activate/" . $user->id, 'Inactive'); ?></td>
						<td><?php echo anchor("auth_federated/edit_user/" . $user->id, 'Edit'); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>

			<p><a href="<?php echo site_url('auth_federated/create_user'); ?>">Create a new user</a> | <a href="<?php echo site_url('auth_federated/create_group'); ?>">Create a new group</a></p>
		</div>
	</div>
</div>