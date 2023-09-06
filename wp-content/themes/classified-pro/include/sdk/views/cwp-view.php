<?php
function cwp_license_manager_cb() {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	$cwp_license_nonce        = wp_create_nonce( 'cwp_license_form_nonce' );
	$CWP_License_verification = new CWP_License_verification();
	$redirect_url             = $CWP_License_verification->setup_after_url;
	$selector                 = $CWP_License_verification->selector;
	$licensing                = $CWP_License_verification->licensing;
	$root                     = $CWP_License_verification->root;
	$PATH_URL                 = $CWP_License_verification->PATH_URL;
	$addons                   = get_option( 'associated-addons' );
	?>

	<div class="cube-theme-importer" data-redirect_url="<?php echo esc_url( $redirect_url ); ?>"
	     data-selector="<?php echo esc_attr( $selector ); ?>">
		<div class="importer-container-fluid">
			<div class="importer-row">
				<div class="importer-col-md-3">
					<div class="cube-setup-grid-progress">
						<div class="cube-setup-grid-header">
							<div class="main-title">
								<h3><?php echo esc_html__( 'Setup Wizard Progress', 'classified-pro' ); ?></h3>
								<p><?php echo esc_html__( 'Please complete all the setups to make sure the setup is successful.', 'classified-pro' ); ?></p>
							</div>
							<div class="title-image">
								<img
									src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/progress-header.png' ); ?>">
							</div>
						</div>
						<div class="cube-setup-grid-list">
							<ul class="cube-setup-list-theme">
								<li class="progress-list active welcome">
									<div class="check-icons">
										<img class="hide-color"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
										<img class="colored"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
									</div>
									<?php echo esc_html__( 'Welcome', 'classified-pro' ); ?>
								</li>
								<?php if ( $licensing && empty( $addons ) ) { ?>
									<li class="progress-list license <?php if ( isset( $_GET['cwp'] ) && $_GET['cwp'] == 'success' ) {
										echo 'active';
									} ?>">
										<div class="check-icons">
											<img class="hide-color"
											     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
											<img class="colored"
											     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
										</div>
										<?php echo esc_html__( 'License Verification', 'classified-pro' ); ?>
									</li>
								<?php } ?>
								<li class="progress-list plugins <?php if ( isset( $_GET['cwp'] ) && $_GET['cwp'] == 'success' ) {
									echo 'active';
								} ?>">
									<div class="check-icons">
										<img class="hide-color"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
										<img class="colored"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
									</div>
									<?php echo esc_html__( 'Required Plugins', 'classified-pro' ); ?>
								</li>
								<li class="progress-list content <?php if ( isset( $_GET['cwp'] ) && $_GET['cwp'] == 'success' ) {
									echo 'active';
								} ?>">
									<div class="check-icons">
										<img class="hide-color"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
										<img class="colored"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
									</div>
									<?php echo esc_html__( 'Dummy Content', 'classified-pro' ); ?>
								</li>
								<li class="progress-list completed">
									<div class="check-icons">
										<img class="hide-color"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
										<img class="colored"
										     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
									</div>
									<?php echo esc_html__( 'Setup Completed!', 'classified-pro' ); ?>
								</li>
								<li class="progress-list failed hide">
									<div class="check-icons">
										<img
											src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAADI0lEQVRIie2Wz2vcVhCAv5G0Vnrx7ikU5xACJcQttARMyS1yWaQ8hxJqs2DIP5A0x9KSo48hIccU/wEtBLa7IYXuajeGuMf8ILSnJi2UtjRN6cmbS7GzetODZEertWxv8TFzkRjNm2/mzcx7gjdyyCKTGKsx75Ikp4GjmeofrH0ia2s/HRpQg+AIvn8F1UvAOyVmv6C6iufdkm53838DtV4/g8htRI4DA0Q6qK4Df2UmM4gEqC4AVeA3YFn6/QcTAzWKllD9CnCB61h7Q9bWBiWBVXHdL1D9HEhwnIsSx+0DA7VeP4Pj3Af+xXEWJY7XywIrBDmPagt4C5Gz0us93BeoQXCESuUpIjM4TnhQWAHaA/7EdWeLNXXGVvj+laxm14swbTTcMUBBJ73efeAmcIIk+bRoPw5Mu3GAtTdG1GG4wsbGXTXG39EZ47OxcVfDcGXEh7XXgJeIXNoTqFH0Hmnrf5dvEG00XFTnEDlPkrTVGF+N8UmSNiLnUZ3LZ5qt7aB6Uuv12fIMrX0/e/s+r5ZmM8HzloAOsECStEmSNrAAdPC8JWk2kxFfIqkPx/mgHChyLHs+L26FdLubVKufIPJtBloAYra2lnYddtXnIz5LgJoZlx8I+W8i+9up2nKgta9PkOJ6Y3wGgzvAx6Rb20E1YmqqlW+k156dbR8vyoGe92MWeTACazRchsMW2zVz3UVcd5Htmg6HrbGRUT2bvf2QV48Pfhj+DBzF2uMjnRqGK6jO4Xk7NVNjfIbDFiKPpd/fGQ0NghpTU78DL6TfP1WeYRrZKpCejfnI+v0VarUL+QaRbneTWu1CHgZApXIVmEZkteh+PMN0vp4CxxCJspPjwKJh+BHQA/6gWp2VZnNrzwyzDJaBBNWWRtH8hLBvgFc4znIRBntdT+fOLWLt16TX002svVZ6PQVBjUrlKiKfAa9QvSj37t3ZzXbvCziKPkT1NnACeEk6CuuIpOOjOoPIPGCAaeBXHGdZ4vhRmc/9fzGM8bH2MnAZ1ZMlZs8QWWV6+svdtnEi4Ag8DE+hehqRt1OF/o3rPpE4fjaJnzdyqPIfOBNOT3Frp0sAAAAASUVORK5CYII=">
									</div>
									<?php echo esc_html__( 'Setup Failed!', 'classified-pro' ); ?>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="importer-col-md-9">
					<div class="cubewp-theme-importer-main">
						<div class="loader-import-data">
							<img
								src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/Curve-Loading.gif' ); ?>">
						</div>
						<div class="importer-tab <?php if ( ! isset( $_GET['cwp'] ) ) {
							echo 'active';
						} ?>">
							<!---* Welcome page screen *-->
							<?php include $root . '/views/cwp-setup-welcome.php'; ?>
						</div>
						<?php if ( $licensing && empty( $addons ) ) { ?>
							<div class="importer-tab licensing">
								<div class="importer-step-form-lisance">
									<?php include $root . '/views/cwp-setup-header.php'; ?>
									<div class="importer-step-form-lisance-main">
										<img
											src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/step_2_search_icon.png' ); ?>">
										<h2 class="License-Verification-title"><?php echo esc_html__( 'License Verification', 'classified-pro' ); ?></h2>
										<p class="License-Verification-des"><?php echo esc_html__( 'Please verify your purchase by entering the license key available in your account.', 'classified-pro' ); ?></p>
										<form method="post" class="verify-license" id="cwp_license_verification">
											<input type="hidden" name="cwp_license_form_meta_nonce"
											       value="<?php echo esc_attr( $cwp_license_nonce ); ?>"/>
											<div class="input-key-theme">
												<label
													for="cwp_license_key"> <?php echo esc_html__( 'Enter Your Purchase Key', 'classified-pro' ); ?> </label>
												<input required class="form-input-install" id="cwp_license_key"
												       type="text" name="cwp_license_key" value=""
												       placeholder="<?php esc_html_e( 'Put your Envato product license key here..', 'classified-pro' ); ?>"/>
											</div>
											<div class="input-key-theme">
												<label
													for="cwp_user_email"> <?php echo esc_html__( 'Email Address (If you dont want to use admin email)', 'classified-pro' ); ?> </label>
												<input id="cwp_user_email" type="text" name="cwp_user_email"
												       class="form-input-install" value=""
												       placeholder="<?php echo esc_html__( 'This email will be used for managing CubeWP account on cubewp.com', 'classified-pro' ); ?>"/>
											</div>
											<div class="input-key-theme">
												<input type="submit" id="cwp_license_submit" name="submit"
												       value="<?php echo esc_html__( 'Verify License', 'classified-pro' ); ?>">
											</div>
										</form>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="importer-tab plugins">
							<div class="importer-step-form-lisance">
								<?php include $root . '/views/cwp-setup-header.php'; ?>
								<div class="importer-step-form-lisance-main">
									<img class="plugin-rocket"
									     src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/step_3_rocket_icon.png' ); ?>">
									<h2 class="plugin-required-title"><?php echo esc_html__( 'Required Plugins Installation', 'classified-pro' ); ?></h2>
									<p class="plugin-required-des"><?php echo esc_html__( 'These plugins are required to import dummy content', 'classified-pro' ); ?></p>
									<div class="verify-license">
										<div class="cube-setup-grid-list">
											<ul class="cube-setup-list-theme">
												<?php
												if ( empty( $addons ) ) {
													$addons = array();
												}
												if ( function_exists( 'cwp_theme_required_plugins' ) ) {
													$theme_required = cwp_theme_required_plugins();
													if ( ! empty( $theme_required ) && is_array( $theme_required ) ) {
														foreach ( $theme_required as $theme_req ) {
															if ( isset( $theme_req['slug'] ) && isset( $theme_req['base'] ) && isset( $theme_req['name'] ) ) {
																$theme_req['cwp-source'] = $theme_req['cwp-source'] ?? 'no';
																if ( ! in_array( $theme_req['slug'], $addons ) && $theme_req['cwp-source'] != 'yes' ) {
																	?>
																	<li class="progress-list active"
																	    id="parent-<?php echo esc_attr( $theme_req['slug'] ); ?>">
																		<div class="check-icons">
																			<?php
																			if ( isset( $theme_req['class_exists'] ) && ! class_exists( $theme_req['class_exists'] ) ) { ?>
																				<input type="checkbox"
																				       id="<?php echo esc_attr( $theme_req['slug'] ); ?>"
																				       name="cwp-plugins-installation"
																				       data-base="<?php echo esc_attr( $theme_req['base'] ); ?>"
																				       data-source="<?php echo isset( $theme_req['source'] ) ? esc_attr( $theme_req['source'] ) : ''; ?>"
																				       value="<?php echo esc_attr( $theme_req['slug'] ); ?>"
																				       name="cwp-plugins-installation"
																				       checked <?php if ( isset( $theme_req['required'] ) && $theme_req['required'] == 'yes' ) {
																					echo 'disabled';
																				} ?>>
																			<?php } else { ?>
																				<img class="colored"
																				     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
																			<?php } ?>
																		</div>
																		<?php
																		echo esc_html( $theme_req['name'] );
																		?>
																	</li>
																	<?php
																}
															}
														}
													}
												}
												if ( isset( $addons ) ) {
													foreach ( $addons as $addon ) {
														?>
														<li class="progress-list active"
														    id="parent-<?php echo esc_attr( $addon ); ?>">
															<div class="check-icons">
																<?php
																$addon_data  = get_option( $addon );
																$license_key = get_option( $addon . '_key' );
																$pluginDir   = WP_PLUGIN_DIR . '/' . $addon;
																$base        = str_replace( '-addon', '', str_replace( '-pro', '', $addon ) );

																$file_name = str_replace( '-', ' ', $base );
																$file_name = str_replace( 'cubewp', 'CubeWp', $file_name );
																$base      = str_replace( ' ', '_', ucwords( $file_name ) );
																if ( $addon == 'cubewp-framework' ) {
																	$base = 'CubeWp';
																}
																if ( ! class_exists( $base . '_Load' ) ) { ?>
																	<?php if ( isset( $addon_data ) && ! empty( $addon_data ) ) { ?>
																		<input type="checkbox"
																		       id="<?php echo esc_attr( $addon ); ?>"
																		       name="cwp-plugins-installation"
																		       value="<?php echo esc_attr( $addon ); ?>"
																		       data-order="<?php echo esc_attr( $addon_data->payment_id ); ?>"
																		       data-license="<?php echo esc_attr( $license_key ); ?>"
																		       data-download_id="<?php echo esc_attr( $addon_data->item_id ); ?>"
																		       data-cwp_source="yes"
																		       name="cwp-plugins-installation" checked>
																	<?php } else { ?>
																		<input type="checkbox"
																		       id="<?php echo esc_attr( $addon ); ?>"
																		       name="cwp-plugins-installation"
																		       value="<?php echo esc_attr( $addon ); ?>"
																		       name="cwp-plugins-installation" checked>
																	<?php } ?>
																<?php } else { ?>
																	<img class="colored"
																	     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
																<?php } ?>
															</div>
															<?php
															$plugin_name = str_replace( '-addon', ' ', str_replace( '-pro', ' ', $addon ) );
															$plugin_name = ucwords( str_replace( '-', ' ', $plugin_name ) );
															echo esc_html( $plugin_name );
															?>
														</li>
													<?php }
												} ?>
											</ul>
										</div>
										<h4 class="verify-title"></h4>
										<p class="verify-name"></p>
									</div>
									<div id="install-plugins"
									     data-cubeframework="<?php if ( class_exists( 'CubeWp_Load' ) ) {
										     echo esc_attr( 'enabled' );
									     } ?>"><?php if ( empty( $addons ) ) { ?><?php echo esc_html__( 'Install Plugins', 'classified-pro' ); ?><?php } else { ?><?php echo esc_html__( 'Continue to Import Process', 'classified-pro' ); ?><?php } ?></div>
								</div>
							</div>
						</div>
						<div
							class="importer-tab dummy_content <?php if ( isset( $_GET['cwp'] ) && $_GET['cwp'] == 'success' ) {
								echo 'active';
							} ?>">
							<div class="importer-step-form-lisance">
								<?php
								include $root . '/views/cwp-setup-header.php';
								$selector = ltrim( $selector, $selector[0] );


								$post_max_size = ini_get('post_max_size');
								$max_execution_time = (int) ini_get('max_execution_time');
								$max_input_vars = (int) ini_get('max_input_vars');
								$memory_limit = ini_get('memory_limit');
								if ( str_contains( $post_max_size, 'G' ) ) {
									$post_max_size = (int) filter_var($post_max_size, FILTER_SANITIZE_NUMBER_INT) * 1024;
								} else {
									$post_max_size = (int) filter_var($post_max_size, FILTER_SANITIZE_NUMBER_INT);
								}
								if ( str_contains( $memory_limit, 'G' ) ) {
									$memory_limit = (int) filter_var($memory_limit, FILTER_SANITIZE_NUMBER_INT) * 1024;
								} else {
									$memory_limit = (int) filter_var($memory_limit, FILTER_SANITIZE_NUMBER_INT);
								}

								$good_to_import = false;
								if ( $post_max_size >= 128 && $max_execution_time >= 300 && $max_input_vars >= 3000 && $memory_limit >= 256 ) {
									$good_to_import = true;
								}

								?>
								<div class="importer-step-form-lisance-main">
									<img class="plugin-rocket"
									     src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/step_3_cloth_icon.png' ); ?>">
									<h2 class="plugin-required-title"><?php echo esc_html__( 'Import Dummy Content for Theme', 'classified-pro' ); ?></h2>
									<p class="<?php echo esc_attr( $selector ); ?>"></p>
									<h3><?php esc_html_e( 'Recommended Resources', 'classified-pro' ); ?></h3>
									<div class="verify-license">
										<div class="cube-setup-grid-list">
											<ul class="cube-setup-list-theme">
												<li class="progress-list active">
													<div class="check-icons">
														<?php
														if ( $post_max_size >= 128 ) {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
															<?php
														}else {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
															<?php
														}
														?>
													</div>
													<?php esc_html_e( 'Minimum post_max_size: 128M', 'classified-pro' ); ?>
												</li>
												<li class="progress-list active">
													<div class="check-icons">
														<?php
														if ( $max_execution_time >= 300 ) {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
															<?php
														}else {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
															<?php
														}
														?>
													</div>
													<?php esc_html_e( 'Minimum max_execution_time: 300', 'classified-pro' ); ?>
												</li>
												<li class="progress-list active">
													<div class="check-icons">
														<?php
														if ( $max_input_vars >= 3000 ) {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
															<?php
														}else {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
															<?php
														}
														?>
													</div>
													<?php esc_html_e( 'Minimum max_input_vars: 3000', 'classified-pro' ); ?>
												</li>
												<li class="progress-list active">
													<div class="check-icons">
														<?php
														if ( $memory_limit >= 256 ) {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">
															<?php
														}else {
															?>
															<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD9UlEQVRIiaWWT0wcdRTHP29mKQnLgWBslZZWCssOS4INbpCbIYJpaxAORtPiwfjn5MFDD72ZeFPPXjUcmtpqD4USUUHX2NRE2EoImZ0ZICEV200TjLaMErbuPg87uw5bGtjt9zLJ7/fe7/N7b36/93tQg2zbPmDb9oFafGUvA1U1XNftF5ERYBA4AhwEEJG7qnobmCkUCle7urrmRKRQM9B13ZPAR8CzoeFN4BbQADQBzaHNLajq+UQiMVMVcHFxMVpfXz8OvBoMXVfVC6p6LZFIZMO2juM8YxjGUKFQeFtEng+GL/u+/1YymfxnT6DneYdVdQo4ASwD71uW9c2jdlzhe0pVPwWOA78Cw5Zl3QnbGJWRhWDT+Xz+uf3CAOLx+HQul0sC00AvcC2dTjc8Ehik8QQwmc1mX+nu7vb3Cyupp6fnT9/3R1R1FuiNRqOfhefLKQ0OyDSwXFdX19fe3n6vWlhYa2trTblcbl5VO0RkMB6Pfw9BhKpqUDyNGIbxXq2wTCbztOM4H9q2/VRbW9tfqnouWP8TVZUy0HXdfopH/3pnZ+dsLTDXdVsMw/hRRD4wTXMQwLKsSeBnoHdlZaWvDBSR0cBvvBbY0tLSIVWdATqBK77vXy7NichFAFUdKQOBFwEVkW9riayuru4nEUkAX2Wz2TPJZPJBad40zckAOBgGHgU24vH47ceMbGxgYODfsE1HR8c64IvIUQAjKMJPAH9UC4tEIj8EkV3xff9sOLIK3VHVJ23bPhC+hzt25nneadd177uu+0al915p3EUCsLW1pUZ3d3cO2KD4CpSlqveAemA8DHVdtwVIEaQxm82erUzjLjoMbCSTyQelCNeBptXV1YMlC8uybgCvAfkSdBfYmb1gmUzmGMWX5Tf4/9DMAOTz+ZGwsWVZE2Eo8Es1MAARGQ6+M2Wgqk4E37FKhwrokWpgAIZhjAXAifJg8KovuK6rQU19SI7jvOA4zrlUKhXZDwjA87zRYM10qbSVi7fjOC8FF98zTbMvFovd3+/Cu8m27WbTNOeB4w8Vb4Curq7vgC+BeD6fv6SqZq2wVCoViUQilyg+xBdLsB1AgGg0+iYwD5zyPG9qbW2tqVqYbdvNLS0tX6vqkKou+L7/bnh+B7C1tXULGFXVBeDk9vb2nOd5p/cL8zxv1DTNeVUdAm6q6suVfc2uTVQ6nW5obGz8HHg9GLoBXDBNcyoWi/0ets1kMsdEZNgwjDFV7QcQkS82Nzff2VcTFdby8vJgoVD4mGJ/UtLfFAuFQfGahHuWmyJyPvzPqgJC8co4jtMnIiMiMigirap6KIjkrqquq+qsaZpXY7HYnIjoXmtWrcdp9f8Dw/HvVPOuwA4AAAAASUVORK5CYII=">
															<?php
														}
														?>
													</div>
													<?php esc_html_e( 'Minimum memory_limit: 256M', 'classified-pro' ); ?>
												</li>
											</ul>
										</div>
									</div>
									<?php if ( ! $good_to_import ) { ?>
										<p><?php echo esc_html__( 'Please match all recommended resources otherwise your import process might be disturbed.', 'classified-pro' ); ?></p>
									<?php } ?>
									<div>
										<button class="button-primary cwp_import_demo" name="cwp_import_demo">
											<?php echo esc_html__( 'Import Data', 'classified-pro' ); ?>
										</button>
									</div>
									<div class="cwp_import"></div>
									<div><a class="cwp_skip_import"
									        href="<?php echo admin_url( 'admin.php?page=cube_wp_dashboard' ); ?>"><?php echo esc_html__( 'Skip Import', 'classified-pro' ); ?></a>
									</div>
								</div>
							</div>
						</div>
						<div class="importer-tab success">
							<div class="importer-step-form-lisance">
								<?php include $root . '/views/cwp-setup-header.php'; ?>
								<div class="importer-step-form-lisance-main">
									<img class="plugin-rocket"
									     src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/step_3_cloth_icon.png' ); ?>">
									<h2 class="plugin-required-title"><?php echo esc_html__( 'Congrats! Setup Completed.', 'classified-pro' ); ?></h2>
									<p class="plugin-required-des"><?php echo esc_html__( 'You will be auto redirected to the CubeWP Dashboard in…', 'classified-pro' ); ?></p>
									<div class="importer-timer">
										<div class="importer-time countdown">6</div>
										<span><?php echo esc_html__( 'seconds', 'classified-pro' ); ?></span>
									</div>
								</div>
							</div>
						</div>
						<div class="importer-tab failed">
							<div class="importer-step-form-lisance">
								<?php include $root . '/views/cwp-setup-header.php'; ?>
								<div class="importer-step-form-lisance-main">
									<img class="plugin-rocket"
									     src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/failed-setup.png' ); ?>">
									<h2 class="plugin-required-title"><?php echo esc_html__( 'Sorry! There was an issue.', 'classified-pro' ); ?></h2>
									<p class="plugin-required-des"></p>
									<div class="importer-failed-lists">
										<ul>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}