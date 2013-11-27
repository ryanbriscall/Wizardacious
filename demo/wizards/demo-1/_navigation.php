<div class="navigation">
    <?php if ($show_prev_step) { ?>
    <button 
        <?php if ($prev_step_is_disabled) { ?>disabled="disabled"<?php } ?>
        class="btn wizard-back<?php if ($prev_step_is_disabled) { ?> disabled<?php } ?>" 
        type="button" 
        onclick="Wizardacious.action('<?php echo $alias; ?>', 'prev');"
    ><i class="icon-chevron-left"></i> Back</button>
    <?php } ?>

    <?php if ($show_next_step) { ?>
    <button 
        <?php if ($next_step_is_disabled) { ?>disabled="disabled"<?php } ?>
        class="btn wizard-next<?php if ($next_step_is_disabled) { ?> disabled<?php } ?>" 
        type="button" 
        onclick="Wizardacious.action('<?php echo $alias; ?>', 'next');"
    >Next <i class="icon-chevron-right"></i></button>
    <?php } ?>

    <button 
        class="btn wizard-reset" 
        type="button" 
        onclick="Wizardacious.action('<?php echo $alias; ?>', 'reset');"
    ><i class="icon-off"></i> Reset</button>
</div>