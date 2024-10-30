<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly */ ?>
<div class="wrap" id="cssor">
	<?php settings_errors(); ?>
    <!--<h1>Cssor</h1>-->
    <h2></h2>
	<form action="options.php" method="post">
		<?php 
			settings_fields( 'cssor' );
	    	do_settings_sections( 'cssor' );
	    	$cssor_style = esc_attr( get_option('cssor_style') ); 
			$cssor_minify = esc_attr( get_option('cssor_minify') );
			$cssor_load = esc_attr( get_option('cssor_load') );
			$cssor_dependency = esc_attr( get_option('cssor_dependency') );
	    ?>
		<div class="cssor-heading">
	    	<div class="cssor-title">
	    		<h2>Cssor</h2>
	    	</div>
	    	<div class="cssor-submit">
	    		<?php submit_button(); ?>
	    	</div>
	    </div>
	    <div class="cssor-wrap">
	    	<div class="cssor-editor">
	    		<div class="ace-wrapper">
					<div id="editor"><?php echo $cssor_style; ?></div>
					<textarea name="cssor_style"></textarea>
				</div>
	    	</div>
	    	<div class="cssor-options">
	    		<h4><?php _e('Minify', 'cssor'); ?></h4>
				<label for="cssor-type">
					<input type="checkbox" name="cssor_minify" id="cssor-type" <?php checked($cssor_minify, 'on'); ?>>
					<?php _e('Minify CSS', 'cssor'); ?>
				</label>
				<p class="description">(<?php _e('Recommended', 'cssor'); ?>)<br><?php _e('Reduce size and remove comments.', 'cssor'); ?></p>
				<?php /*
				<p class="description">(Recommended)<br>Reduce size and remove comments.</p>
				<h4>Dependency</h4>
    			<label for="cssor_dependency">
    				<input type="text" name="cssor_dependency" id="cssor-dependency" value="<?php echo $cssor_dependency; ?>">
    			</label>
    			<p class="description">(Optional)<br>An list of registered stylesheet handles this stylesheet depends on separated with comma.</p>
	    		*/ ?>
	    	</div>
	   	</div>
    </form>
</div>
<script>
	// Ace initialisation
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/github");
    editor.session.setMode("ace/mode/css");
    var textarea = jQuery('textarea[name="cssor_style"]');
	editor.getSession().on("change", function () {
	    textarea.val(editor.getSession().getValue());
	});
	jQuery(document).ready(function() {
		textarea.val(editor.getSession().getValue());
	});
	// enable autocompletion and snippets
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true,
        showPrintMargin: false,
    });
</script>