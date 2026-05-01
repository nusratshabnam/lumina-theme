<?php get_header(); ?>
<main id="primary">
    <div class="container section">
        <?php if ( have_posts() ) :
            lumina_breadcrumb();
            if ( is_home() && ! is_front_page() ) : ?>
                <h1 style="margin-bottom:2.5rem;"><?php single_post_title(); ?></h1>
            <?php elseif ( is_archive() ) : ?>
                <h1 style="margin-bottom:2.5rem;"><?php the_archive_title(); ?></h1>
                <?php the_archive_description( '<div style="color:var(--stone-600);margin-bottom:2rem;">', '</div>' ); ?>
            <?php elseif ( is_search() ) : ?>
                <h1 style="margin-bottom:2.5rem;"><?php printf( __( 'Search: &ldquo;%s&rdquo;', 'lumina' ), get_search_query() ); ?></h1>
            <?php else : ?>
                <h1 style="margin-bottom:2.5rem;"><?php esc_html_e( 'Latest Posts', 'lumina' ); ?></h1>
            <?php endif;

            echo '<div class="grid-3" style="margin-bottom:3rem;">';
            while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'product-card' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="product-card__media">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'lumina-product' ); ?></a>
                    </div>
                    <?php endif; ?>
                    <div class="product-card__info">
                        <div class="product-card__brand"><?php echo get_the_date(); ?></div>
                        <h2 class="product-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p style="font-size:0.875rem;color:var(--stone-600);"><?php the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="btn btn--ghost" style="padding-left:0;">Read More →</a>
                    </div>
                </article>
            <?php endwhile;
            echo '</div>';

            the_posts_pagination( [
                'prev_text' => '← Newer',
                'next_text' => 'Older →',
            ] );

        else : ?>
            <p><?php esc_html_e( 'Nothing found.', 'lumina' ); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer();
