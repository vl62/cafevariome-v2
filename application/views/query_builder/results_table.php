<script type="text/javascript">
// Add popover function from Twitter Bootstrap (can't load it from the main cafevariome.js for the div that this table is displayed in)
$(function (){
	$("[rel=popover]").popover({placement:'right', trigger:'hover', animation:'true', delay: { show: 50, hide: 300 }});
});
</script>

<div class="container">
	<div class="row-fluid">
		<div class="span12 pagination-centered">
			<div class="well-group">
				<?php if ( empty($query_results)): ?>
				<p>There is no data present in this installation! Data needs to be added through the administrator interface.</p>
				<?php else: ?>
				<?php if ( ! $this->config->item('show_sources_in_discover')): ?>
				<!--<h3>Variant Counts</h3><hr>-->
				<?php endif; ?>
				<table class="table table-hover table-bordered table-striped" id="discovertable">
					<thead>
						<tr>
							<?php if ( $this->config->item('show_sources_in_discover')): ?>
							<th align="center" class="title">Source</th>
							<?php endif; ?>
							<th colspan="2" align="center" class="title">openAccess</th>
							<th colspan="2" align="center" class="title">linkedAccess</th>
							<th colspan="2" align="center" class="title">restrictedAccess</th>
						</tr>
					</thead>
					<tbody>
						<?php
//						ksort($counts);
						foreach ( $query_results as $source => $count ):
						?>
						<tr>
							<?php if ( $this->config->item('show_sources_in_discover')): ?>
							<td><a rel="popover" data-content="Click for a description of this source (opens in a new window)." data-original-title="Source Information" href="<?php echo base_url('discover/source/' . $source); ?>" target="_blank"><?php echo $sources_full[$source]; ?></a></td>
							<?php endif; ?>
							<td><?php if ( array_key_exists('openAccess', $count )) { if ( $count['openAccess'] > $this->config->item('variant_count_cutoff') ) { echo $count['openAccess']; } else { ?> <a href="#" rel="popover" data-content="<?php echo $this->config->item('variant_count_cutoff_message'); ?>" data-original-title="Variants"><i class="icon-question-sign"></i></a> <?php }} else { echo "0";}?></td>
							<td> 
								<?php if ( array_key_exists('openAccess', $count )): ?>
									<?php if ( $count['openAccess'] > $this->config->item('variant_count_cutoff') ): ?>
										<a rel="popover" data-content="Click to access these variants." data-original-title="Access Variants"> <input type="image" onclick="javascript:variantOpenAccessRequest('<?php echo urlencode($term);?>', '<?php echo $source;?>', '<?php echo $sources_full[$source];?>', '<?php echo $count['openAccess'];?>')" src="<?php echo base_url('resources/images/cafevariome/request.png');?>"></a>
									<?php else: ?>
										<a href="#" rel="popover" data-content="<?php echo $this->config->item('variant_count_cutoff_message'); ?>" data-original-title="Variants"><i class="icon-question-sign"></i></a>
									<?php endif; ?>
								<?php else: ?>
									<a rel="popover" data-content="Sorry, there are no variants of this type available." data-original-title="Access Variants"> <?php echo img(base_url('resources/images/cafevariome/cross.png'));?></a>
								<?php endif; ?>
							</td>
							<td><?php if ( array_key_exists('linkedAccess', $count )) { echo $count['linkedAccess'];} else { echo "0";}?></td>
							<td>
								<?php if ( array_key_exists('linkedAccess', $count )): ?>
									<a href="<?php echo base_url(); ?>discover/variants/<?php echo urlencode($term); ?>/<?php echo $source;?>/linkedAccess" target="_blank" rel="popover" data-content="Click to access these variants." data-original-title="Access Variants"> <?php echo img(base_url('resources/images/cafevariome/request.png'));?></a>
								<?php else: ?>
									<a rel="popover" data-content="Sorry, there are no variants of this type available." data-original-title="Access Variants"> <?php echo img(base_url('resources/images/cafevariome/cross.png'));?></a>
								<?php endif; ?>
							</td>
							<td><?php if ( array_key_exists('restrictedAccess', $count )) { if ( $count['restrictedAccess'] > $this->config->item('variant_count_cutoff') ) { echo $count['restrictedAccess'];} else { ?> <a href="#" rel="popover" data-content="<?php echo $this->config->item('variant_count_cutoff_message'); ?>" data-original-title="Variants"><i class="icon-question-sign"></i></a> <?php }} else { echo "0";}?></td>
							<td>
								<?php if ( array_key_exists('restrictedAccess', $count )) : ?>
									<?php if ( $access_flag[$source] ): ?>
										<?php if ( $count['restrictedAccess'] > $this->config->item('variant_count_cutoff') ): ?>
											<a rel="popover" data-content="Click to access these restrictedAccess variants." data-original-title="Access Variants"> <input type="image" onclick="javascript:variantRestrictedAccessRequest('<?php echo urlencode($term);?>', '<?php echo $source;?>', '<?php echo $sources_full[$source];?>', '<?php echo $count['restrictedAccess'];?>')" src="<?php echo base_url('resources/images/cafevariome/request.png');?>"></a>
										<?php else: ?>
											<a href="#" rel="popover" data-content="<?php echo $this->config->item('variant_count_cutoff_message'); ?>" data-original-title="Variants"><i class="icon-question-sign"></i></a>
										<?php endif; ?>
									<?php else: ?>
											<?php if ( $source_types[$source] == "api" ): ?>
												<?php echo anchor($source_info[$source]['uri'] . "/discover/variants/$term/" . $node_source[$source] . "/restrictedAccess", img(array('src' => base_url('resources/images/cafevariome/cafevariome_node.png'),'border'=>'0','alt'=>'Request Data')),array('class'=>'imglink', 'target' => '_blank', 'rel' => "popover", 'data-content' => "Click to access these variants on the remote node. N.B. All access control to these variants is controlled by the remote node.", 'data-original-title' => "Access Node Variants")); ?>
											<?php elseif ( $source_types[$source] == "central" ): ?>
												<?php echo anchor("http://www.cafevariome.org/discover/variants/$term/" . $central_source[$source] . "/restrictedAccess", img(array('src' => base_url('resources/images/cafevariome/cafevariome_node.png'),'border'=>'0','alt'=>'Request Data')),array('class'=>'imglink', 'target' => '_blank', 'rel' => "popover", 'data-content' => "Click to access these variants in Cafe Variome Central. N.B. All access control to these variants is controlled by Cafe Variome Central.", 'data-original-title' => "Access CV Central Variants")); ?>
											<?php else: ?>
												<?php if ( $count['restrictedAccess'] > $this->config->item('variant_count_cutoff') ): ?>
													<?php echo anchor("discover/variants/$term/$source/restrictedAccess", img(array('src' => base_url('resources/images/cafevariome/request-icon.png'),'border'=>'0','alt'=>'Request Data')),array('class'=>'imglink', 'target' => '_blank', 'rel' => "popover", 'data-content' => "Click to request access to these variants (requires login).", 'data-original-title' => "Access Variants")); ?>
												<?php else: ?>
													<a href="#" rel="popover" data-content="<?php echo $this->config->item('variant_count_cutoff_message'); ?>" data-original-title="Variants"><i class="icon-question-sign"></i></a>
												<?php endif; ?>
											<?php endif; ?>
									<?php endif; ?>	
								<?php else: ?>
									<a rel="popover" data-content="Sorry, there are no variants of this type available." data-original-title="Access Variants"> <?php echo img(base_url('resources/images/cafevariome/cross.png'));?></a>
								<?php endif; ?>
										
							</td>
						<?php
						endforeach;
//						echo anchor("discover/variants/$term/$source/restrictedAccess", img(array('src'=> base_url('resources/images/cafevariome/request-icon.png'),'border'=>'0','alt'=>'Request Data')),array('class'=>'imglink', 'target' => '_blank'));
//						echo anchor("discover/variants/$term/$source/restrictedAccess", img(base_url('resources/images/cafevariome/request-icon.png')));
						?>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
				<br />
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<hr>

