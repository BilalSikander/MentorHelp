<?php
/**
 * Template to display site footer
 */

// Theme authors may render something before.
do_action('etb_before_render_site_footer', $footer_template_id);

// Print template content.
echo Elementor\Plugin::$instance->frontend->get_builder_content($footer_template_id, false);

// Theme author may render something after.
do_action('etb_after_render_site_footer', $footer_template_id);

wp_footer();

?>
</body>
</html>
