<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: Amazon Feed by IT Guild
Description: Create your own custom amazon feeds.
Version: 1.0
Author: Eugene Gorelikov (web@itguild.pro)
Author URI: http://itguild.pro
License: GPLv2 or later
Text Domain: itg-amazon-feed
*/


//adding product_types custom taxonomy for our amazon items
function itg_register_amznfeed_categories() {

	$labels = array(
		'name'                       => _x( 'Feed Categories', 'taxonomy general name', 'itg-amazon-feed' ),
		'singular_name'              => _x( 'Feed Category', 'taxonomy singular name', 'itg-amazon-feed' ),
		'search_items'               => __( 'Search Feed Category', 'itg-amazon-feed' ),
		'all_items'                  => __( 'All Feed Categories', 'itg-amazon-feed' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Feed Category', 'itg-amazon-feed' ),
		'update_item'                => __( 'Update Feed Category', 'itg-amazon-feed' ),
		'add_new_item'               => __( 'Add New Feed Category', 'itg-amazon-feed' ),
		'new_item_name'              => __( 'New Feed Category Name', 'itg-amazon-feed' ),
		'separate_items_with_commas' => __( 'Separate Feed Categories with commas', 'itg-amazon-feed' ),
		'add_or_remove_items'        => __( 'Add or remove Feed Categories', 'itg-amazon-feed' ),
		'choose_from_most_used'      => __( 'Choose from the most used Feed Categories', 'itg-amazon-feed' ),
		'not_found'                  => __( 'No Feed Categories found.', 'itg-amazon-feed' ),
		'menu_name'                  => __( 'Feed categories', 'itg-amazon-feed' ),
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'publicly_queryable' 	=> false,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'public'                => true,
		/*'rewrite'               => array( 'slug' => 'feed_category' ),*/
	);

	register_taxonomy( 'feed_category', 'amznfeed', $args );
}
add_action( 'init', 'itg_register_amznfeed_categories', 0 );

//adding custom amznfeed custom post type
function itg_register_amznfeed_items(){
	register_post_type( 'amznfeed',
		array(
			'labels' => array(
				'name' => __( 'Amazon Feed Products', 'itg-amazon-feed'),
				'menu_name' => __( 'Amazon Feed Products', 'itg-amazon-feed'),
				'singular_name' => __( 'Feed Product' , 'itg-amazon-feed'),
			),
		'public' => true,
		'query_var' => true,
		'publicly_queryable' => false,
		'has_archive' => false,
		'show_ui' => true,
		'menu_icon' => 'dashicons-list-view',
		'hierarchical' => false,
		/*'taxonomies' => array ('feed_category'),*/ /* redundant */
		/*'rewrite'  => array( 'slug' => 'amznfeed' ),*/
		)
	);
}
add_action( 'init', 'itg_register_amznfeed_items', 0 );


//add our taxonomy into post_type
/*
function itg_add_feed_types_to_feeds (){
	register_taxonomy_for_object_type( 'feed_category', 'amznfeed' );
}
add_action( 'init', 'itg_add_feed_types_to_feeds', 0 );
*/

function itg_amznfeed_add_feed_tamplate( $template ){


	if( is_tax('feed_category')){
		$template = __DIR__ .'/templates/taxonomy-feed_category.php';
	}

	//var_dump($template);

	return $template;
}
add_filter('template_include', 'itg_amznfeed_add_feed_tamplate');

//check if proper template has already been picked in the theme or child theme directories
function itg_amznfeed_verify_feed_tamplate( $template_path ){

	//Get template name
	$template = basename($template_path);

	if( 1 == preg_match('/^taxonomy-feed_category((-(\S*))?).php/',$template) ){
		return true;
	}
	return false;
}

//create metabox with feed values for products
function itg_amznfeed_add_meta_boxes ( $post ){
	add_meta_box( 'itg_amazon_feed_props_meta_box', __( 'Product properties', 'itg-amazon-feed' ), 'itg_amznfeed_product_feed_build_meta_box', 'amznfeed', 'normal', 'high' );
}
add_action( 'add_meta_boxes_amznfeed', 'itg_amznfeed_add_meta_boxes' );


//build feed properties metabox
function itg_amznfeed_product_feed_build_meta_box( $post ){
	//nonce
	wp_nonce_field( basename( __FILE__ ), 'itg_amnfeed_meta_box_nonce' );

	//meta values
	$amzn_product_url = get_post_meta($post->ID, 'amzn_product_url', true);
	$amzn_product_headline = get_post_meta($post->ID, 'amzn_product_headline', true);
	$amzn_product_summary = get_post_meta($post->ID, 'amzn_product_summary', true);
	$amzn_product_rank = get_post_meta($post->ID, 'amzn_product_rank', true);
	$amzn_product_award = get_post_meta($post->ID, 'amzn_product_award', true);
	$amzn_product_rating = get_post_meta($post->ID, 'amzn_product_rating', true);
	$amzn_product_rating_value = get_post_meta($post->ID, 'amzn_product_rating_value', true);
	$amzn_product_apply_to_variants = get_post_meta($post->ID, 'amzn_product_apply_to_variants', true);
	$amzn_product_best_rating_value = get_post_meta($post->ID, 'amzn_product_best_rating_value', true);
	$amzn_product_worst_rating_value = get_post_meta($post->ID, 'amzn_product_worst_rating_value', true);
	//$amzn_index_content = get_post_meta($post->ID, 'amzn_index_content', true);

	?>
	<div class='inside'>
		<h3><?php _e( 'Product URL', 'itg-amazon-feed' ); ?> (amzn:productURL)</h3>
		<p>
			<input type="url" name="amzn_product_url" value="<?php echo $amzn_product_url; ?>" required /><br>
			<span class="description"><?php _e('<strong>Required</strong>. Child element of amzn:products and is required if the <amzn:product> element is used. This element contains the Amazon product URL and should be stripped of all tracking parameters.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Product Headline', 'itg-amazon-feed' ); ?> (amzn:productHeadline)</h3>
		<p>
			<input type="text" maxlength="40" name="amzn_product_headline" value="<?php echo $amzn_product_headline; ?>"  /><br>
			<span class="description"><?php _e('Optional (but strongly encouraged). Use this element to provide an editorial headline for a product. As a best practice, we recommend that this element contain less than 40 characters and be wrapped in a CDATA section. Example: "The Best Running Shoe for Most Women" or “The Best Gaming Laptop Under $500”.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Product Summary', 'itg-amazon-feed' ); ?> (amzn:productSummary)</h3>
		<p>
			<textarea maxlength="200" cols="100" name="amzn_product_summary"  /><?php echo $amzn_product_summary; ?></textarea><br>
			<span class="description"><?php _e('Optional (but strongly encouraged). Use this element to provide a brief description of the recommended product. See Figure 1 in the appendix for an example of how this information can be used. As a best practice, we recommend keeping this content to less than 200 characters and be wrapped in a CDATA section. If you have more content, please add it as a paragraph below the product card. Example: "When you’re looking for top-of-the-line sound quality, these headphones are second to none”.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Product rank', 'itg-amazon-feed' ); ?> (amzn:rank)</h3>
		<p>
			<input type="text" maxlength="3" name="amzn_product_rank" value="<?php echo $amzn_product_rank; ?>"  /><br>
			<span class="description"><?php _e('Optional. Child element of <amzn:product> that specifies the relative rank ofeach product associated with the content. For example, the top recommendation within an article reviewing “The Best 4K TVs” would have a value of “1”, while the fifth ranked product in the article would have a value of “5”. Higher rankings represent lower recommendation status. A rank of “1” will always denote the top recommended product. In the absence of this element Amazon will set a rank of “0” to all products. <strong>WARNING:</strong> be careful when assigning the rank to a product assigned to several feeds: a rank conflict with unexpected results may occur!', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Product award', 'itg-amazon-feed' ); ?> (amzn:award)</h3>
		<p>
			<input type="text" maxlength="24" name="amzn_product_award" value="<?php echo $amzn_product_award; ?>"  /><br>
			<span class="description"><?php _e('Optional (but strongly encouraged). Child element of <amzn:product>. Use this element to provide a short editorial designation for the product. When present, data in this element will be added to products in the form of a badge or subheading. See Figure 1 in the appendix for an example of how data from this element can be used. As a best practice, this value should be less than 25 characters and wrapped in a CDATA section. Examples: “Best Overall”, “Our Pick”, “Best Value”, “Editor’s Choice”. Note: This element should be considered mandatory for an article to be eligible for Amazon search.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Product rating', 'itg-amazon-feed' ); ?> (amzn:rating)</h3>
		<p>
			<select name="amzn_product_rating">
				<option value="true" <?php echo $amzn_product_rating == 'true' ? 'selected' : ''; ?> >Show</option>
				<option value="false" <?php echo $amzn_product_rating == 'true' ? '' : 'selected'; ?>>Hide</option>
			</select><br>
			<span class="description"><?php _e('Optional. Child element of <amzn:product>. This is a container element holding the overall numeric rating information for the product. This value can be complementary to the <amzn:award> element. For example, a product can have a <amzn:ratingValue> of “9.5” (out of 10) as well as an <amzn:award> of “Editor’s Pick”.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Product rating value', 'itg-amazon-feed' ); ?> (amzn:ratingValue)</h3>
		<p>
			<input type="number" step="0.1" name="amzn_product_rating_value" value="<?php echo $amzn_product_rating_value; ?>"  /><br>
			<span class="description"><?php _e('Optional. Child element of <amzn:rating>. The numeric rating you have assigned to the product (note: this is not the Amazon customer reviews rating, but rather the rating you provide). Examples: “85” or “3.5””. ', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Apply to variations', 'itg-amazon-feed' ); ?> (amzn:applyToVariants)</h3>
		<p>
			<select name="amzn_product_apply_to_variants">
				<option value="True" <?php echo $amzn_product_apply_to_variants == 'True' ? 'selected' : ''; ?> >True</option>
				<option value="False" <?php echo $amzn_product_apply_to_variants == 'True' ? '' : 'selected'; ?>>False</option>
			</select><br>
			<span class="description"><?php _e('Optional. Child element of <amzn:rating>. Set this field to “True” if the <amzn:ratingValue> should be applied to variations of the product. For example, if the rating for the streaming version of Finding Nemo should also be applied to the blu-ray version, set this element to “True”.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Best rating value', 'itg-amazon-feed' ); ?> (amzn:bestRating)</h3>
		<p>
			<input type="number" max="100" min="1" step="1" name="amzn_product_best_rating_value" value="<?php echo $amzn_product_best_rating_value; ?>"  /><br>
			<span class="description"><?php _e('Optional. Child element of <amzn:rating>. The highest value allowed in your rating system. For example, if your rating scale is 1-100, this value should be set to “100”.', 'itg-amazon-feed'); ?></span>
		</p>
		<h3><?php _e( 'Worst rating value', 'itg-amazon-feed' ); ?> (amzn:worstRating)</h3>
		<p>
			<input type="number" max="100" min="1" step="1" name="amzn_product_worst_rating_value" value="<?php echo $amzn_product_worst_rating_value; ?>"  /><br>
			<span class="description"><?php _e('Optional. Child element of <amzn:rating>. The lowest value allowed in this rating system. For example, if your rating system is 1-100, this value should be set to “1”.', 'itg-amazon-feed'); ?></span>
		</p>
	</div>
	<?php
}

//save metabox
function itg_amznfeed_save_meta_box_data( $post_id ){
	// verify taxonomies meta box nonce
	if ( !isset( $_POST['itg_amnfeed_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['itg_amnfeed_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	// store custom fields values or clear the DB out of empty postmeta values
	if ( isset( $_REQUEST['amzn_product_url'] ) ) {
		update_post_meta( $post_id, 'amzn_product_url', sanitize_text_field( $_POST['amzn_product_url'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_url');
	}
	if ( isset( $_REQUEST['amzn_product_headline'] ) ) {
		update_post_meta( $post_id, 'amzn_product_headline', sanitize_text_field( $_POST['amzn_product_headline'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_headline');
	}
	if ( isset( $_REQUEST['amzn_product_summary'] ) ) {
		update_post_meta( $post_id, 'amzn_product_summary', sanitize_text_field( $_POST['amzn_product_summary'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_summary');
	}
	if ( isset( $_REQUEST['amzn_product_rank'] ) ) {
		update_post_meta( $post_id, 'amzn_product_rank', sanitize_text_field( $_POST['amzn_product_rank'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_rank');
	}
	if ( isset( $_REQUEST['amzn_product_award'] ) ) {
		update_post_meta( $post_id, 'amzn_product_award', sanitize_text_field( $_POST['amzn_product_award'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_award');
	}
	if ( isset( $_REQUEST['amzn_product_rating'] ) ) {
		update_post_meta( $post_id, 'amzn_product_rating', sanitize_text_field( $_POST['amzn_product_rating'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_rating');
	}
	if ( isset( $_REQUEST['amzn_product_rating_value'] ) ) {
		update_post_meta( $post_id, 'amzn_product_rating_value', sanitize_text_field( $_POST['amzn_product_rating_value'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_rating_value');
	}
	if ( isset( $_REQUEST['amzn_product_apply_to_variants'] ) ) {
		update_post_meta( $post_id, 'amzn_product_apply_to_variants', sanitize_text_field( $_POST['amzn_product_apply_to_variants'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_apply_to_variants');
	}
	if ( isset( $_REQUEST['amzn_product_best_rating_value'] ) ) {
		update_post_meta( $post_id, 'amzn_product_best_rating_value', sanitize_text_field( $_POST['amzn_product_best_rating_value'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_best_rating_value');
	}
	if ( isset( $_REQUEST['amzn_product_worst_rating_value'] ) ) {
		update_post_meta( $post_id, 'amzn_product_worst_rating_value', sanitize_text_field( $_POST['amzn_product_worst_rating_value'] ) );
	} else {
		delete_post_meta ($post_id, 'amzn_product_worst_rating_value');
	}	
}
add_action( 'save_post_amznfeed', 'itg_amznfeed_save_meta_box_data' );


/* product feed custom fields */
function itg_amznfeed_feed_category_taxonomy_custom_fields($tag) {
	$t_id = $tag->term_id; // Get the ID of the term you're editing
	$term_meta = get_term_meta( $t_id );

?>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_channel_title"><?php _e('Channel title', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="text" name="term_meta[amzn_feed_category_channel_title]" id="term_meta[amzn_feed_category_channel_title]" size="255" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_channel_title'][0] ? $term_meta['amzn_feed_category_channel_title'][0] : ''; ?>"><br />
		<span class="description"><?php _e('<strong>Required</strong>. The title of your channel should be the same as the title of your website. If you have multiple brands or websites, provide a RSS feed for each.  Example: Amazon could have several feeds corresponding to “Amazon”, “IMDB”, and “Goodreads”.', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_channel_image_url"><?php _e('Channel image URL', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="url" name="term_meta[amzn_feed_category_channel_image_url]" id="term_meta[amzn_feed_category_channel_image_url]" size="255" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_channel_image_url'][0] ? $term_meta['amzn_feed_category_channel_image_url'][0] : ''; ?>"><br />
		<span class="description"><?php _e('Optional. The logo for the channel. Example: https://amazon.com/images/amazon-logo.jpg', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_author"><?php _e('Feed Author', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="text" name="term_meta[amzn_feed_category_author]" id="term_meta[amzn_feed_category_author]" size="255" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_author'][0] ? $term_meta['amzn_feed_category_author'][0] : ''; ?>" required><br />
		<span class="description"><?php _e('<strong>Required</strong>. The name of the person, or entity, who wrote the article. Use multiple <author> elements for content with more than one author. Example: John Smith', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_description"><?php _e('Brief feed description', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="text" name="term_meta[amzn_feed_category_description]" id="term_meta[amzn_feed_category_description]" size="512" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_description'][0] ? $term_meta['amzn_feed_category_description'][0] : ''; ?>"><br />
		<span class="description"><?php _e('<strong>Required</strong>. A brief description of the RSS channel. Example: RSS feed for amazon.com', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_canonical_url"><?php _e('Canonical URL', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="url" name="term_meta[amzn_feed_category_canonical_url]" id="term_meta[amzn_feed_category_canonical_url]" size="512" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_canonical_url'][0] ? $term_meta['amzn_feed_category_canonical_url'][0] : ''; ?>" required><br />
		<span class="description"><?php _e('<strong>Required</strong>. The canonical URL for the content on your website. This element will serve as the content’s unique identifier in Amazon’s system. The link should not contain any query parameters. Example: https://amazon.com/article/example-article.com ', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_hero_image_url"><?php _e('Hero Image URL', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="url" name="term_meta[amzn_feed_category_hero_image_url]" id="term_meta[amzn_feed_category_hero_image_url]" size="512" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_hero_image_url'][0] ? $term_meta['amzn_feed_category_hero_image_url'][0] : ''; ?>"><br />
		<span class="description"><?php _e('Optional (but strongly encouraged). The hero image for the content. As a best practice, the image should be high-resolution, with width of at least 1000px. Example: https://amazon.com/images/article-hero-image.jpg', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_hero_image_caption"><?php _e('Hero Image Caption', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="text" name="term_meta[amzn_feed_category_hero_image_caption]" id="term_meta[amzn_feed_category_hero_image_caption]" size="512" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_hero_image_caption'][0] ? $term_meta['amzn_feed_category_hero_image_caption'][0] : ''; ?>"><br />
		<span class="description"><?php _e('Optional. The caption for the hero image wrapped in a CDATA section'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_subtitle"><?php _e('Amazon feed subtitle', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="text" name="term_meta[amzn_feed_category_subtitle]" id="term_meta[amzn_feed_category_subtitle]" size="1024" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_subtitle'][0] ? $term_meta['amzn_feed_category_subtitle'][0] : ''; ?>"><br />
		<span class="description"><?php _e('Optional. This element contains an optional sub-heading for an article wrapped in a CDATA section.'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_feed_category_intro_paragraph"><?php _e('Intro paragraph', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<input type="textarea" rows="4" name="term_meta[amzn_feed_category_intro_paragraph]" id="term_meta[amzn_feed_category_intro_paragraph]" size="1024" style="width:100%;" value="<?php echo $term_meta['amzn_feed_category_intro_paragraph'][0] ? $term_meta['amzn_feed_category_intro_paragraph'][0] : ''; ?>"><br />
		<span class="description"><?php _e('Optional. This element contains an optional sub-heading for an article wrapped in a CDATA section.', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="amzn_index_content"><?php _e('Index content (amzn:indexContent)', 'itg-amazon-feed'); ?></label>
	</th>
	<td>
		<select name="term_meta[amzn_index_content]">
			<option value="True" <?php echo $term_meta['amzn_index_content'][0] == 'True' ? '' : 'selected'; ?> >True</option>
			<option value="False" <?php echo $term_meta['amzn_index_content'][0] == 'False' ? 'selected' : ''; ?>>False</option>
		</select><br>
		<span class="description"><?php _e('<strong>Required</strong>. The value of this element determines if content is eligible to be indexed in external search engines such as Google and Bing. If the value is set to “False”, Amazon will populate the robots metatag value of “noindex”.', 'itg-amazon-feed'); ?></span>
	</td>
</tr>
<?php
}

// Add the fields to the "feed_category" taxonomy, using our callback function
add_action( 'feed_category_edit_form_fields', 'itg_amznfeed_feed_category_taxonomy_custom_fields', 10, 2 );


// A callback function to save our extra taxonomy field(s)
function itg_amznfeed_save_taxonomy_custom_fields( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$term_meta = get_option( "taxonomy_term_$term_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ){
			if ( isset( $_POST['term_meta'][$key] ) ){
				$term_meta[$key] = $_POST['term_meta'][$key];
				update_term_meta($term_id, $key, sanitize_text_field($_POST['term_meta'][$key]));
			} else {
				delete_term_meta ($term_id, $key);
			}
		}
		update_term_meta($term_id, 'amzn_pubdate', current_time( 'r' ) );
		//save the option array
		//update_option( "taxonomy_term_$term_id", $term_meta );
	}
}
// Save the changes made on the "feed_category" taxonomy, using our callback function
add_action( 'edited_feed_category', 'itg_amznfeed_save_taxonomy_custom_fields', 10, 2 );

//register feed page template
function itg_amznfeed_add_page_template ($templates) {
	$templates['page-amazon-feed.php'] = 'Amazon Feed';
	return $templates;
}
add_filter ('theme_page_templates', 'itg_amznfeed_add_page_template');



//create redirect to plugin template directory
function itg_amznfeed_redirect_page_template ($template) {
	$post = get_post(); 
	$page_template = get_post_meta( $post->ID, '_wp_page_template', true ); 
	if ('page-amazon-feed.php' == basename ($page_template )){
		$template = WP_PLUGIN_DIR . '/itg-amazon-feed/templates/page-amazon-feed.php';
	}
		
	// for WP < v4.7
	if ('page-amazon-feed.php' == basename ($template)){
		$template = WP_PLUGIN_DIR . '/itg-amazon-feed/templates/page-amazon-feed.php';
	}
	
	return $template;
}
add_filter ('page_template', 'itg_amznfeed_redirect_page_template');

?>