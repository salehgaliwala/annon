<?php
defined('ABSPATH') || exit;

?>
<footer id="classified-footer" class="classified-footer-style-1">
    <div class="container">
        <div class="d-flex justify-content-center mb-2">
            <div class="classified-footer-">
                <div class="classified-footer-copyright d-flex">
                    &copy;
					<?php
					echo date_i18n( _x('Y', 'copyright date format', 'classified-pro'));
					?>
                    <a href="<?php echo esc_url(home_url('/')); ?>"><p class="ms-1 p-lg"><?php bloginfo('name'); ?></p></a>
                </div>
            </div>

        </div>
        <a class="classified-to-the-top" href="#classified-header">
            <p class="p-lg">
				<?php
				printf(__('To the top %s', 'classified-pro'), '<span class="arrow" aria-hidden="true">&uarr;</span>');
				?>
            </p>
        </a>
		<?php
		if (function_exists('the_privacy_policy_link')) {
			the_privacy_policy_link('<div class="mb-4"><p class="classified-footer-privacy-policy">', '</p></div>');
		}
		?>
        <p class="classified-footer-powered-by p-lg mb-0">
			<?php printf(__('Powered by %s Classified %s %s WordPress %s Theme', 'classified-pro'), '<a class="p-lg" href="' . esc_url(__('https://classified.org/', 'classified-pro')) . '">', '</a>', '<a class="p-lg" href="' . esc_url(__('https://wordpress.org/', 'classified-pro')) . '">', '</a>'); ?>
        </p>
    </div>
</footer>