<?php if ( have_posts() ) : while ( have_posts() ) : the_post() ?>

	<?php cgt_locate_template('cgt/post-header.php'); ?>
	
	<div class="doc-content">
			<table class="profile-fields">
				<tr>
					<td class="label">
					Title: 
					</td>
	
					<td class="data">
						<?php the_title(); ?>
					</td>
	
				</tr>
				<tr>
					<td class="label">
					Content: 
					</td>
	
					<td class="data">
						<?php the_content(); ?> 
					</td>
				</tr>
			</table>
	</div>
	
	<div class="doc-meta">
		
		<?php echo get_the_term_list( get_the_ID(), 'group_cat', "Group Category: ", ", " ) ?>
	
	</div>


	<?php // cgt_locate_template('cgt/comments.php'); ?>
	
<?php endwhile; endif ?>
