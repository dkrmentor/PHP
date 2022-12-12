<?php
/**
 * Template Name: Filter Template
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
get_header(); ?>

<form action="<?php echo site_url() ?>/filter-page" method="POST" id="filter">
	<?php
		if( $terms = get_terms( array( 'taxonomy' => 'category', 'orderby' => 'name' ) ) ) : 
 
			echo '<select name="categoryfilter"><option value="">Select category...</option>';
			foreach ( $terms as $term ) :
				echo '<option value="' . $term->term_id . '">' . $term->name . '</option>'; // ID of the category as the value of an option
			endforeach;
			echo '</select>';
		endif;
	?>
	<button>Apply filter</button>
	<input type="hidden" name="action" value="myfilter">
</form>
<div id="response">

<?php
echo $_POST["categoryfilter"];
// THIS LOOP WILL SHOW ALL POSTS BY DEFAULT
$args = array(
    'post_type' => 'stocks',
    'posts_per_page' => -1,
    'cat' => $_POST["categoryfilter"]
);
   
$the_query = new WP_Query( $args ); ?>
   
    <?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
   
          <h2><?php the_title(); ?></h2>
  
    <?php endwhile; endif; ?>
   
<?php wp_reset_postdata(); ?>

</div>

<?php
get_footer(); ?>
