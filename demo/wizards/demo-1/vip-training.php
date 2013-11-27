<fieldset>
    <legend>Advanced Training</legend>
    <p>Package includes.</p>
    <div class="control-group">
        <div class="controls">
            <label class="checkbox">
                <input 
                    <?php echo (
                        $this->get_form_field('vip-trainer') == '' 
                        ? 'checked="checked"' 
                        : ''
                    ); ?> 
                    name="<?php echo $this->get_form_field_name('vip-trainer'); ?>" 
                    id="<?php echo $this->get_form_field_id('vip-trainer'); ?>" 
                    type="checkbox"
                > Personal Trainer
            </label>
        </div>
    </div>
</fieldset>