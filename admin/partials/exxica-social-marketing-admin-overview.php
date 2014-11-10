<!-- Social Marketing Overview -->
<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Exxica
 * @author    Gaute Rønningen <gaute@exxica.com>
 * @link      http://exxica.com
 * @copyright 2014 Exxica AS
 */
?>
<script type="text/javascript">
	(function ( $ ) {
		"use strict";

		$(function () {
			$(document).ready(function() {
				$.datepicker.setDefaults(
				  	$.extend(
					    {'dateFormat':'dd.mm.yy'},
					    $.datepicker.regional['no']
			  		)
				);

				$('.datepicker').each(function() {
					$(this).datepicker();
				});
			});
		});

	}(jQuery));
</script>
<style>
.inline-edit-row fieldset label span.title {
	width: 100% !important;
}
.inline-edit-row fieldset label span.input-text-wrap {
	margin: 0 !important;
}
#wpfooter {
	display: none !important;
}
</style>
<div class="wrap">

	<h2><?php _e('Marketing overview', $this->name); ?></h2>

	<ul class="subsubsub">
		<li><a href="edit.php?page=exxica-social-marketing-overview"><?php _e('Scheduled and published recently', $this->name); ?></a> | </li>
		<li><a href="edit.php?page=exxica-social-marketing-overview&smtype=log"><?php _e('Publish Log', $this->name); ?></a></li>		
	</ul>
	<form id="sm-filter" method="GET">
		<?php wp_nonce_field('esmoverviewnonce'); ?>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Choose bulk-action', $this->name); ?></label>
				<select name="action" id="bulk-action-selector-top" disabled="disabled">
					<option value="-1" selected="selected"><?php _e('Bulk-actions', $this->name); ?></option>
					<option value="edit" class="hide-if-no-js"><?php _e('Edit', $this->name); ?></option>
					<option value="delete"><?php _e('Delete', $this->name); ?></option>
				</select>
				<input type="submit" name="" id="doaction" class="button action" value="<?php _e('Use', $this->name); ?>" disabled="disabled">
			</div>
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php 
						printf(
							(count($all_items) == 1) ? 
								__('%s item scheduled, ', $this->name) : 
								__('%s items scheduled, ', $this->name),
							count($all_items)
						);

						printf(
							(count($sm_items) == 1 ) ?
								__('%s item shown.', $this->name) :
								__('%s items shown.', $this->name), 
							count($sm_items)
						);
					?>
				</span>
				<span class="pagination-links">
					<a href="edit.php?page=exxica-social-marketing-overview" title="Gå til den første siden" class="first-page<?php echo ($page == 0) ? ' disabled' : ''; ?>">«</a>
					<a href="edit.php?page=exxica-social-marketing-overview<?php if($page !== 0) echo '&page_num='.($page-1); ?>" title="Gå til forrige side" class="prev-page<?php echo ($page == 0) ? ' disabled' : ''; ?>">‹</a>
					<span class="paging-input"><?php printf(__('%d of ', $this->name), $shown_page); ?><span class="total-pages"><?php echo $last_page; ?></span>
				</span>
				<a href="edit.php?page=exxica-social-marketing-overview&page_num=<?php echo ($page == $last_page-1) ? $last_page-1 : $page+1; ?>" title="Gå til neste side" class="next-page<?php echo ($page == $last_page-1) ? ' disabled' : ''; ?>">›</a>
				<a href="edit.php?page=exxica-social-marketing-overview&page_num=<?php echo $last_page-1; ?>" title="Gå til den siste siden" class="last-page<?php echo ($page == $last_page-1) ? ' disabled' : ''; ?>">»</a>
			</div>
		</div>
		<table class="wp-list-table widefat fixed">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
						<label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all', $this->name); ?></label>
						<input id="cb-select-all-1" type="checkbox" disabled="disabled">
					</th>
					<th scope="col" id="title" class="manage-column column-title" style=""><?php _e('Title', $this->name); ?></th>
					<th scope="col" id="source" class="manage-column column-source" style=""><?php _e('Source', $this->name); ?></th>
					<th scope="col" id="channel" class="manage-column column-channel" style=""><?php _e('Channel', $this->name); ?></th>
					<th scope="col" id="date" class="manage-column column-date" style=""><?php _e('Publish Date', $this->name); ?></th>
					<th scope="col" id="image" class="manage-column column-image" style=""><?php _e('Image', $this->name); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($sm_items) !== 0 ) : ?>
					<?php 
					$num = 0;
					foreach($sm_items as $item) : 
						if($item['channel'] == 'Facebook') {
							$info = $wpdb->get_results( "SELECT channel_account FROM ".$wpdb->prefix."exxica_social_marketing_accounts WHERE fb_page_id = ".$item['channel_account'] );
							$i = $info[0];
						} 
						$query = new WP_Query( array( 'post_type' => 'any', 'posts_per_page' => -1, 'order_by' => 'ID', 'order' => 'ASC' ) );
						foreach($query->posts as $post) {
							if($post->ID == $item['post_id']) {
								$p = $post;
								break;
							}
						}
						if(isset($p)) :
							$past_publish_class = ($item['publish_localtime'] < time()) ? " past_publish" : "";
							$alternate_class = ($num % 2) ? " alternate" : "";
							$error_class = ($item['status'] == 1) ? " error" : "";

							$row_class = "post-".$item['id']." format-standard hentry iedit".$past_publish_class.$alternate_class.$error_class;
							$edit_row_class = "inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-post inline-editor";
					?>
					<tr id="post-<?php echo $item['id']; ?>" class="<?php echo $row_class; ?>">
						<th scope="row" class="check-column">
							<label class="screen-reader-text" for="cb-select-<?php echo $item['id']; ?>"><?php echo __('Choose', $this->name).' '.esc_html($item['publish_title']); ?></label>
							<input id="cb-select-<?php echo $item['id']; ?>" type="checkbox" name="post[]" value="<?php echo $item['id']; ?>" disabled="disabled">
							<div class="locked-indicator"></div>
						</th>
						<td class="post-title page-title column-title"<?php if($item['publish_localtime'] < time()) echo ' style="color:#aaa !important;"' ?>>
							<strong>
								<?php if($item['publish_localtime'] >= time()) : ?>
								<a class="row-title" href="post.php?post=<?php echo $item['post_id']; ?>&action=edit" title="<?php echo __('Edit', $this->name); ?> <?php echo esc_html($item['publish_title']); ?>">
									<?php echo esc_html($item['publish_title']); ?>
								</a>
								<?php else : ?>
								<?php echo esc_html($item['publish_title']); ?>
								<?php endif; ?>
							</strong>
							<i style="font-size:0.8em;"><?php echo esc_html($item['publish_description']); ?></i>
							<div class="locked-info">
								<span class="locked-avatar"></span> 
								<span class="locked-text"></span>
							</div>
							<?php if($item['publish_localtime'] >= time()) : ?>
							<script>
							(function ( $ ) {
								"use strict";

								$(function () {
									$(document).ready(function() {
										$('a#editinline-<?php echo $item["id"]; ?>').click(function(e) {
											e.preventDefault();
											$('#post-<?php echo $item["id"]; ?>').hide();
											$('#edit-<?php echo $item["id"]; ?>').show();
										});
										$('a#submitdelete-<?php echo $item["id"]; ?>').click(function(e) {
											e.preventDefault();

											var data = [
												{
													'name' : 'item_id',
													'value' : <?php echo $item['id']; ?>
												},
												{
													'name' : 'post_id',
													'value' : <?php echo $item['post_id']; ?>
												},
												{
													'name' : 'channel',
													'value' : "<?php echo $item['channel']; ?>"
												},
												{
													'name' : '_wpnonce',
													'value' : $('#_wpnonce').val()
												}
											];
											var url = "<?php echo admin_url('admin-ajax.php?action=destroy_overview_data'); ?>";
											$.post(url, data, function(data, xhr, status) {
												window.location.reload();
											});
										});
									});
								});
							}(jQuery));
							</script>
							<div class="row-actions">
								<span class="edit">
									<a href="post.php?post=<?php echo $item['post_id']; ?>&action=edit" title="<?php _e('Edit this item', $this->name); ?>"><?php _e('Edit', $this->name); ?></a> | </span>
									<span class="inline hide-if-no-js"><a href="#" id="editinline-<?php echo $item['id']; ?>" class="editinline" title="<?php _e('Quick Edit', $this->name); ?>"><?php _e('Quick Edit', $this->name); ?></a> | </span>
									<a id="submitdelete-<?php echo $item['id']; ?>" class="submitdelete" title="<?php _e('Delete this item', $this->name); ?>" href="#"><?php _e('Delete publication', $this->name); ?></a></span>
								</span>
							</div>
							<?php else : ?>
							<div class="row-actions">
								<span class="edit">
									<a href="#"><?php _e('Duplicate', $this->name); ?></a>
								</span>
							</div>
							<?php endif; ?>
						</td>
						<td class="source column-source"<?php if($item['publish_localtime'] < time()) echo ' style="color:#aaa !important;"' ?>>
							<?php echo $sources[$p->post_type]; ?>
						</td>
						<td class="channel column-channel"<?php if($item['publish_localtime'] < time()) echo ' style="color:#aaa !important;"' ?>>
							<?php echo ($item['channel'] == 'Facebook') ? $i->channel_account : $item['channel_account']; ?> @ <?php echo $item['channel']; ?>
							<?php if($item['status'] == 1) : ?><br/>
							<span id="error"><?php echo esc_html($item['message']); ?></span>
							<?php endif; ?>
						</td>
						<td class="date column-date"<?php if($item['publish_localtime'] < time()) echo ' style="color:#aaa !important;"' ?>>
							<abbr title="<?php echo date('Y-m-d H:i:s', $item['publish_localtime']); ?>"><?php echo date('d.m.Y', $item['publish_localtime']); ?></abbr>
							<br/>
							<?php echo date('\k\l\. H:i', $item['publish_localtime']); ?>
						</td>	
						<td class="image column-image"<?php if($item['publish_localtime'] < time()) echo ' style="color:#aaa !important;"' ?>>
							<img style="width:auto;max-height:50px;" src="<?php echo $item['publish_image_url']; ?>">
						</td>
					</tr>
					<?php if($item['publish_localtime'] >= time()) : ?>
					<script type="text/javascript">
						(function ( $ ) {
							"use strict";

							$(function () {
								$(document).ready(function() {
									$("input#publish-date-<?php echo $item['id']; ?>").datepicker(
										"setDate", "<?php echo date('d.m.Y', $item['publish_localtime']); ?>"
									);
									$("a#cancel-<?php echo $item['id']; ?>").click(function(e) {
										e.preventDefault();
										$('#edit-<?php echo $item["id"]; ?>').hide();
										$('#post-<?php echo $item["id"]; ?>').show();
									});
									$("a#update-<?php echo $item['id']; ?>").click(function(e) {
										e.preventDefault();
										$('#spinner').show();

										var d = new Date($('input#publish-date-<?php echo $item["id"]; ?>').datepicker("getDate"));
										var d_local = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate(), $('#publish-hour-<?php echo $item["id"]; ?>').val(), $('#publish-minute-<?php echo $item["id"]; ?>').val(), 0));
										var d_utc = new Date(d.getFullYear(), d.getMonth(), d.getDate(), $('#publish-hour-<?php echo $item["id"]; ?>').val(), $('#publish-minute-<?php echo $item["id"]; ?>').val(), 0);
										var data = [
											{
												'name' : 'item_id',
												'value' : <?php echo $item['id']; ?>
											},
											{
												'name' : 'post_id',
												'value' : <?php echo $item['post_id']; ?>
											},
											{
												'name' : 'post_title',
												'value' : $('#publish-title-<?php echo $item["id"]; ?>').val()
											},
											{
												'name' : 'publish_localdate',
												'value' : Math.round(d_local.getTime() / 1000),
												'real' : d_local.toJSON()
											},
											{
												'name' : "publish_utcdate",
												'value' : Math.round(d_utc.getTime() / 1000),
												'real' : d_utc.toJSON()
											},
											{
												'name' : 'channel',
												'value' : "<?php echo $item['channel']; ?>"
											},
											{
												'name' : '_wpnonce',
												'value' : $('#_wpnonce').val()
											}
										];
										var url = "<?php echo admin_url('admin-ajax.php?action=update_overview_data'); ?>";
										$.post(url, data, function(data, xhr, status) {
											window.location.reload();									
										});
									});
								});
							});

						}(jQuery));
					</script>
					<tr id="edit-<?php echo $item['id']; ?>" class="<?php echo $edit_row_class; ?>" style="display:none;">
						<td colspan="6" class="colspanchange">
						<hr class="clear" />
						<fieldset class="inline-edit-col-left">
							<div class="inline-edit-col">
								<h4><?php _e('Quick Edit', $this->name); ?></h4>
								<label>
									<span class="title"><?php _e('Publication text', $this->name); ?></span>
									<span class="input-text-wrap">
										<textarea id="publish-title-<?php echo $item['id']; ?>" rows="1" cols="22" name="post_title" class="ptitle"><?php echo esc_html($item['publish_description']); ?></textarea>
									</span>
								</label>
								<br class="clear">
								
								<div class="hidden">
									<div class="channel"><?php echo $i->channel_account; ?> @ <?php echo $item['channel']; ?></div>
									<div class="publish_image_url"><?php echo $item['publish_image_url']; ?></div>
								</div>

								<br class="clear">

					
							</div>
						</fieldset>	
						<fieldset class="inline-edit-col-center">
							<div class="inline-edit-col">
								<h4>&nbsp;</h4>
								<label>
									<span class="title"><?php _e('Date', $this->name); ?></span>
									<span class="input-text-wrap">
										<input id="publish-date-<?php echo $item['id']; ?>" type="text" name="publish_date" class="pdate datepicker">
									</span>
								</label>
								<br class="clear">
							</div>
						</fieldset>
						<fieldset class="inline-edit-col-right">
							<div class="inline-edit-col">
								<h4>&nbsp;</h4>
								<label>
									<span class="title"><?php _e('Time', $this->name); ?></span>
									<div class="timestamp-wrap">
										<input type="number" id="publish-hour-<?php echo $item['id']; ?>" name="publish_hour" class="phour" value="<?php echo date('H', $item['publish_localtime']); ?>" min="0" max="23">:
										<input type="number" id="publish-minute-<?php echo $item['id']; ?>" name="publish_minute" class="pmin" value="<?php echo date('i', $item['publish_localtime']); ?>" min="0" max="59">
									</div>
								</label>
								<br class="clear">
							</div>
						</fieldset>
						<p class="submit inline-edit-save">
							<a id="cancel-<?php echo $item['id']; ?>" accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft"><?php _e('Cancel', $this->name); ?></a>
							<a href="post.php?post=<?php echo $item['post_id']; ?>&action=edit" class="button-secondary cancel alignleft"><?php _e('Full edit', $this->name); ?></a>
							<?php wp_nonce_field('_inline_edit'); ?>
							<a id="update-<?php echo $item['id']; ?>" accesskey="s" href="#inline-edit" class="button-primary save alignright"><?php _e('Update', $this->name); ?></a>
							<span class="spinner"></span>
							<span class="error" style="display:none"></span>
							<br class="clear">
						</p>
						<hr class="clear" />
						</td>
					</tr>
					<?php endif; ?>
					<?php 
							$num++; 
						endif;
					endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="6"><?php _e('No publications found.', $this->name); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Choose bulk-action', $this->name); ?></label>
				<select name="action" id="bulk-action-selector-top" disabled="disabled">
					<option value="-1" selected="selected"><?php _e('Bulk-actions', $this->name); ?></option>
					<option value="edit" class="hide-if-no-js"><?php _e('Edit', $this->name); ?></option>
					<option value="delete"><?php _e('Delete', $this->name); ?></option>
				</select>
				<input type="submit" name="" id="doaction" class="button action" value="<?php _e('Use', $this->name); ?>" disabled="disabled">
			</div>

			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php 
						printf(
							(count($all_items) == 1) ? 
								__('%s item scheduled, ', $this->name) : 
								__('%s items scheduled, ', $this->name),
							count($all_items)
						);

						printf(
							(count($sm_items) == 1 ) ?
								__('%s item shown.', $this->name) :
								__('%s items shown.', $this->name), 
							count($sm_items)
						);
					?>
				</span>
				<span class="pagination-links">
					<a href="edit.php?page=exxica-social-marketing-overview" title="Gå til den første siden" class="first-page<?php echo ($page == 0) ? ' disabled' : ''; ?>">«</a>
					<a href="edit.php?page=exxica-social-marketing-overview<?php if($page !== 0) echo '&page_num='.($page-1); ?>" title="Gå til forrige side" class="prev-page<?php echo ($page == 0) ? ' disabled' : ''; ?>">‹</a>
					<span class="paging-input"><?php printf(__('%d of ', $this->name), $shown_page); ?><span class="total-pages"><?php echo $last_page; ?></span>
				</span>
				<a href="edit.php?page=exxica-social-marketing-overview&page_num=<?php echo ($page == $last_page-1) ? $last_page-1 : $page+1; ?>" title="Gå til neste side" class="next-page<?php echo ($page == $last_page-1) ? ' disabled' : ''; ?>">›</a>
				<a href="edit.php?page=exxica-social-marketing-overview&page_num=<?php echo $last_page-1; ?>" title="Gå til den siste siden" class="last-page<?php echo ($page == $last_page-1) ? ' disabled' : ''; ?>">»</a>
			</div>
		</div>
	</form>
</div>