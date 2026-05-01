<?php get_header(); ?>
<main id="primary">
    <div class="container section">
        <?php while ( have_posts() ) : the_post(); ?>
        <?php lumina_breadcrumb(); ?>
        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h1 style="margin-bottom:2rem;"><?php the_title(); ?></h1>
            <div class="container--narrow" style="padding:0;">
                <?php the_content(); ?>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer();
