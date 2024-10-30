<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$feed = get_queried_object();
$amzn_feed_props = get_term_meta($feed->term_id);

//dates are messed up
/*
echo get_the_date('', $feed->term_id);
echo get_the_modified_date('', $feed->term_id );
*/
/*
echo "<pre>";
var_dump (get_queried_object());
var_dump ($amzn_feed_props);
echo "</pre>";
*/
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:amzn="https://amazon.com/ospublishing/1.0/">
	<channel>
		<title><?php echo $amzn_feed_props['amzn_feed_category_channel_title'][0]; ?></title>
		<link><?php echo get_term_link($feed->term_id); ?></link>
		<description><?php echo $amzn_feed_props['amzn_feed_category_description'][0]; ?></description>
		<language>en-us</language>
		<lastBuildDate><?php echo get_the_modified_date('r'); ?></lastBuildDate>
		<amzn:rssVersion>1.0</amzn:rssVersion>
		<?php if ($amzn_feed_props['amzn_feed_category_channel_image_url'][0]) { ?><image><?php echo $amzn_feed_props['amzn_feed_category_channel_image_url'][0]; ?></image><?php } ?>
		
		<item>
			<title><?php echo $feed->name; ?></title>
			<link><?php echo $amzn_feed_props['amzn_feed_category_canonical_url'][0]; ?></link>
			<?php if ($amzn_feed_props['amzn_feed_category_hero_image_url'][0]) { ?><amzn:heroImage><?php echo $amzn_feed_props['amzn_feed_category_hero_image_url'][0]; ?></amzn:heroImage><?php } ?>
			
			<?php if ($amzn_feed_props['amzn_feed_category_hero_image_caption'][0]) { ?><amzn:heroImageCaption><?php echo $amzn_feed_props['amzn_feed_category_hero_image_caption'][0]; ?></amzn:heroImageCaption><?php } ?>
			
			<?php if ($amzn_feed_props['amzn_feed_category_subtitle'][0]) { ?><amzn:subtitle><?php echo $amzn_feed_props['amzn_feed_category_subtitle'][0]; ?></amzn:subtitle><?php } ?>
			
			<?php if ($amzn_feed_props['amzn_feed_category_intro_paragraph'][0]) { ?><amzn:introText><?php echo $amzn_feed_props['amzn_feed_category_intro_paragraph'][0]; ?></amzn:introText><?php } ?>
			
			<pubDate><?php echo $amzn_feed_props['amzn_pubdate'][0]; ?></pubDate>
			<author><?php echo $amzn_feed_props['amzn_feed_category_author'][0]; ?></author>
			<content:encoded><![CDATA[<?php echo $feed->description; ?>]]>
			</content:encoded>
			<amzn:indexContent><?php echo $amzn_feed_props['amzn_index_content'][0]; ?></amzn:indexContent>
<?php
		
if ( have_posts() ) { ?>
				<amzn:products>
	<?php
	while ( have_posts() ) {
		the_post(); 
		$amzn_product_props = get_post_meta(get_the_id());
		//var_dump($product_props);
		?>
				<amzn:product>
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
	<?php	
	} ?>			</amzn:products>
<?php
}
?>
		</item>
	</channel>
</rss>