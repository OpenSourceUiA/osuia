<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


$template = $this->get_template();
$file = $this->get_file();

?>

<div id="optionbar" class="optionbar">
	<ul class="alignleft">
		<li class="no-border-left"><a class="mymail-icon undo disabled" title="<?php esc_html_e( 'undo', 'mymail' )?>">&nbsp;</a></li>
		<li><a class="mymail-icon redo disabled" title="<?php esc_html_e( 'redo', 'mymail' )?>">&nbsp;</a></li>
		<?php if ( !empty( $modules ) ): ?>
		<li><a class="mymail-icon clear-modules" title="<?php esc_html_e( 'remove modules', 'mymail' )?>">&nbsp;</a></li>
		<?php endif;?>
		<?php if ( current_user_can( 'mymail_see_codeview' ) ): ?>
		<li><a class="mymail-icon code" title="<?php esc_html_e( 'toggle HTML/code view', 'mymail' )?>">&nbsp;</a></li>
		<?php endif;?>
		<?php if ( current_user_can( 'mymail_change_plaintext' ) ): ?>
		<li><a class="mymail-icon plaintext" title="<?php esc_html_e( 'toggle HTML/Plain-Text view', 'mymail' )?>">&nbsp;</a></li>
		<?php endif;?>
		<li class="no-border-right"><a class="mymail-icon preview" title="<?php esc_html_e( 'preview', 'mymail' )?>">&nbsp;</a></li>
	</ul>
	<ul class="alignright">
		<li><a class="mymail-icon dfw" title="<?php esc_html_e( 'Distraction-free edit mode', 'mymail' )?>">&nbsp;</a></li>
		<?php if ( $templates && current_user_can( 'mymail_save_template' ) ): ?>
		<li><a class="mymail-icon save-template" title="<?php esc_html_e( 'save template', 'mymail' )?>">&nbsp;</a></li>
		<?php endif;?>
		<?php if ( $templates && current_user_can( 'mymail_change_template' ) ):
	$single = count( $templates ) == 1;
$currenttemplate = array( $template => $templates[$template] );
unset( $templates[$template] );
$templates = $currenttemplate + $templates;

?>
			<li class="current_template <?php if ( $single ) {
	echo 'single';
}
?>"><span class="change_template" title="<?php echo esc_attr( sprintf( __( 'Your currently working with %s', 'mymail' ), '"' . $all_files[$template][$file]['label'] . '"' ) ); ?>"><?php echo esc_html( $all_files[$template][$file]['label'] ); ?></span>
				<div class="dropdown">
					<div class="ddarrow"></div>
					<div class="inner">
						<h4><?php esc_html_e( 'Change Template', 'mymail' )?></h4>
						<ul>
							<?php
$current = $template . '/' . $file;
foreach ( $templates as $slug => $data ) {
?>
								<li><?php if ( !$single ): ?><a class="template"><?php echo esc_html( $data['name'] ) ?><i class="version"><?php echo esc_html( $data['version'] ); ?></i></a><?php endif;?>
								<ul <?php if ( $template == $slug ) {
		echo ' style="display:block"';
	}
	?>>
						<?php
	foreach ( $all_files[$slug] as $name => $data ) {
		$value = $slug . '/' . $name;
?>
								<li><a class="file<?php if ( $current == $value ) {
			echo ' active';
		}
		?>" <?php if ( $current != $value ) {
			echo 'href="//' . add_query_arg( array( 'template' => $slug, 'file' => $name, 'message' => 2 ), $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) . '"';
		}
		?>><?php echo esc_html( $data['label'] ); ?></a></li>
							<?php
	}
?>
								</ul>
							</li>
							<?php
}
?>
					</ul>
				</div>
			</div>
		</li>
		<?php endif;?>
	</ul>
</div>
<div id="mymail_template_save" style="display:none;">
<div class="mymail_template_save">
		<div class="inner">

			<p>
			<label><?php esc_html_e( 'Name', 'mymail' );?><br><input type="text" class="widefat" id="new_template_name" placeholder="<?php esc_html_e( 'template name', 'mymail' );?>" value="<?php echo $all_files[$template][$file]['label']; ?>"></label>
			</p>
			<p>
			<label><input type="radio" name="new_template_overwrite" checked value="0"> <?php esc_html_e( 'save as a new template file', 'mymail' );?></label><br>
			<label><input type="radio" name="new_template_overwrite" value="1"> <?php esc_html_e( 'overwrite', 'mymail' );?>
			<select id="new_template_saveas_dropdown">
			<?php

$options = '';
foreach ( $all_files[$template] as $name => $data ) {

	$value = $template . '/' . $name;
	$options .= '<option value="' . $value . '" ' . selected( $current, $value, false ) . '>' . esc_attr( $data['label'] . ' (' . $name . ')' ) . '</option>';
}

echo $options;

?>
			</select>
			</label>
			</p>
			<?php if ( !empty( $modules ) ): ?>
			<p>
			<label><input type="checkbox" id="new_template_modules" value="1"> <?php printf( __( 'include original modules from %s', 'mymail' ), '&quot;' . $all_files[$template][$file]['label'] . '&quot' );?></label> <span class="help" title="<?php esc_html_e( 'will append the existing modules to your custom ones', 'mymail' )?>">(?)</span><br>
			<label><input type="checkbox" id="new_template_active_modules" value="1"> <?php esc_html_e( 'show custom modules by default', 'mymail' );?></label><br>
			</p>
			<?php endif;?>

		</div>
		<div class="foot">
			<p class="description alignleft">&nbsp;<?php printf( __( 'based on %1$s from %2$s', 'mymail' ), '<strong>&quot;' . $all_files[$template][$file]['label'] . '&quot;</strong>', '<strong>&quot;' . $all_files[$template][$file]['name'] . '&quot;</strong>' ); ?>
			</p>
			<button class="button button-primary save-template"><?php esc_html_e( 'Save', 'mymail' );?></button>
			<button class="button save-template-cancel"><?php esc_html_e( 'Cancel', 'mymail' );?></button>
			<span class="spinner" id="new_template-ajax-loading"></span>
		</div>
</div>
</div>
