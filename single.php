<?php get_header(); ?>
<main id="primary">
    <div class="container section">
        <?php while ( have_posts() ) : the_post(); ?>
        <?php lumina_breadcrumb(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="max-width:740px;margin:0 auto;">
            <header style="margin-bottom:2.5rem;">
                <div style="font-family:var(--font-mono);font-size:0.6875rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--stone-400);margin-bottom:1rem;">
                    <?php echo get_the_date(); ?> &mdash; <?php the_category( ', ' ); ?>
                </div>
                <h1><?php the_title(); ?></h1>
            </header>
            <?php if ( has_post_thumbnail() ) : ?>
            <div style="margin-bottom:2.5rem;border-radius:var(--radius-lg);overflow:hidden;">
                <?php the_post_thumbnail( 'lumina-wide' ); ?>
            </div>
            <?php endif; ?>
            <div class="post-content" style="font-size:1rem;color:var(--stone-800);line-height:1.8;">
                <?php the_content(); ?>
            </div>
            <footer style="margin-top:3rem;padding-top:1.5rem;border-top:1px solid var(--stone-100);">
                <?php the_tags( '<div style="display:flex;flex-wrap:wrap;gap:.5rem;font-size:.75rem;">', '', '</div>' ); ?>
            </footer>
        </article>
        <?php if ( comments_open() ) comments_template(); ?>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer();
