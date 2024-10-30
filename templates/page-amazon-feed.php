<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

    /* Template Name: Amazon Feed */
	//get all amazon feed categories
	$tax_args = array(
		'post_type' => 'amznfeed',
		'tax_query' => array(
			'taxonomy' => 'feed_category',
		),
	);
	$feed_cats = new WP_Query( $tax_args );
	
	
$args = array(
	'hide_empty' => false,
	'taxonomy'  => 'feed_category',
);
$terms = get_terms( $args );
//var_dump ($terms);

?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:amzn="https://amazon.com/ospublishing/1.0/">
	<channel>
		<title><?php echo get_the_title(); ?></title>
		<link><?php echo get_the_permalink(); ?></link>
		<description><?php echo wp_strip_all_tags(get_the_content(), true); ?></description>
		<language>en-us</language>
		<amzn:rssVersion>1.0</amzn:rssVersion>
		<lastBuildDate><?php echo get_the_modified_date('r'); ?></lastBuildDate>
		<?php if (count($terms) > 0): 
				foreach ($terms as $feed_category){ 
				$amzn_feed_props = get_term_meta ($feed_category->term_id); ?>
	<item>
				<title><?php echo $feed_category->name; ?></title>
				<link><?php echo $amzn_feed_props['amzn_feed_category_canonical_url'][0]; ?></link>
				<?php if ($amzn_feed_props['amzn_feed_category_hero_image_url'][0]) { ?><amzn:heroImage><?php echo $amzn_feed_props['amzn_feed_category_hero_image_url'][0]; ?></amzn:heroImage><?php } ?>
				
				<?php if ($amzn_feed_props['amzn_feed_category_hero_image_caption'][0]) { ?><amzn:heroImageCaption><?php echo $amzn_feed_props['amzn_feed_category_hero_image_caption'][0]; ?></amzn:heroImageCaption><?php } ?>
			
				<?php if ($amzn_feed_props['amzn_feed_category_subtitle'][0]) { ?><amzn:subtitle><?php echo $amzn_feed_props['amzn_feed_category_subtitle'][0]; ?></amzn:subtitle><?php } ?>
			
				<?php if ($amzn_feed_props['amzn_feed_category_intro_paragraph'][0]) { ?><amzn:introText><?php echo $amzn_feed_props['amzn_feed_category_intro_paragraph'][0]; ?></amzn:introText><?php } ?>
				
				<pubDate><?php echo $amzn_feed_props['amzn_pubdate'][0]; ?></pubDate>
				<author><?php echo $amzn_feed_props['amzn_feed_category_author'][0]; ?></author>
				<content:encoded><![CDATA[<?php echo $feed_category->description; ?>]]></content:encoded>
				<amzn:indexContent><?php echo $amzn_feed_props['amzn_index_content'][0]; ?></amzn:indexContent>
				
				<?php
					$amzn_feed_items_args = array(
						'post_type' => 'amznfeed',
						'tax_query' => array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'feed_category',
								'field'    => 'term_id',
								'terms'    => $feed_category->term_id
							)
						),
					);

					$query = new WP_Query($amzn_feed_items_args);

					if ( $query->have_posts() ) : ?>
		<amzn:products>
			<?php while ( $query->have_posts() ) : ?>
				<amzn:product> 
				<?php
					$query->the_post();
					
					$amzn_product_props = get_post_meta(get_the_id());
					//var_dump ($amzn_product_props);
					//var_dump ($post);
				?>
				<amzn:productURL><?php echo $amzn_product_props['amzn_product_url'][0]; ?></amzn:productURL>
									<?php if ($amzn_product_props['amzn_product_headline'][0]) { ?><amzn:productHeadline><?php echo $amzn_product_props['amzn_product_headline'][0]; ?></amzn:productHeadline><?php } ?>
						
									<?php if ($amzn_product_props['amzn_product_summary'][0]) { ?><amzn:productSummary><![CDATA[<?php echo $amzn_product_props['amzn_product_summary'][0]; ?>]]></amzn:productSummary><?php } ?>
						
									<?php if ($amzn_product_props['amzn_product_rank'][0]) { ?><amzn:rank><?php echo $amzn_product_props['amzn_product_rank'][0]; ?></amzn:rank><?php } ?>
						
									<?php if ($amzn_product_props['amzn_product_award'][0]) { ?><amzn:award><?php echo $amzn_product_props['amzn_product_award'][0]; ?></amzn:award><?php } ?>
											
									<?php if ($amzn_product_props['amzn_product_rating'][0] == "true") { ?><amzn:rating>
										<?php if ($amzn_product_props['amzn_product_rating_value'][0]) { ?><amzn:ratingValue><?php echo $amzn_product_props['amzn_product_rating_value'][0]; ?></amzn:ratingValue><?php } ?>
												
										<?php if ($amzn_product_props['amzn_product_apply_to_variants'][0]) { ?><amzn:applyToVariants><?php echo $amzn_product_props['amzn_product_apply_to_variants'][0]; ?></amzn:applyToVariants><?php } ?>
												
										<?php if ($amzn_product_props['amzn_product_best_rating_value'][0]) { ?><amzn:bestRating><?php echo $amzn_product_props['amzn_product_best_rating_value'][0]; ?></amzn:bestRating><?php } ?>
												
										<?php if ($amzn_product_props['amzn_product_worst_rating_value'][0]) { ?><amzn:worstRating><?php echo $amzn_product_props['amzn_product_worst_rating_value'][0]; ?></amzn:worstRating><?php } ?>
								
									</amzn:rating><?php } ?>
		
							</amzn:product>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				
				<?php endif; ?>
				 <?php wp_reset_postdata();	?>
				</amzn:products>
			</item>
			<?php } // end of foreach ?>
		
		<?php endif; ?>
	</channel>
</rss>