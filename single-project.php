<?php
/*Template Name: Project Template
*/

get_header(); ?>

    <div class="content-container full-width padding-vert row">
            <?php
            $mypost = array( 'post_type' => 'jlport_project', );
            $loop = new WP_Query( $mypost );
            ?>
            <?php while ( $loop->have_posts() ) : $loop->the_post();?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">

                        <!-- Display featured image in right-aligned floating div -->
                        <div style="float: right; margin: 10px">
                            <?php the_post_thumbnail( array( 300, 213 ) ); ?>
                        </div>

                        <!-- Display Title and Author Name -->
                        <strong>Name of Project: </strong><?php the_title(); ?><br />
                        <strong>URL: </strong>
                        <?php echo esc_html( get_post_meta( get_the_ID(), 'jlport_url', true ) ); ?>
                        <br />


                    </header>

                    <!-- Display movie review contents -->
                    <div class="entry-content"><?php the_content(); ?></div>
                </article>

            <?php endwhile; ?>

    </div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>