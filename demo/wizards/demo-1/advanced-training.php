<fieldset>
    <legend>Advanced Training</legend>
    <p>Package includes.</p>
    <div class="control-group">
        <div class="controls">
            <label class="checkbox">
                <input 
                    <?php echo (
                        $this->get_form_field('basic-newsletter') == '' 
                        ? 'checked="checked"' 
                        : ''
                    ); ?> 
                    name="<?php echo $this->get_form_field_name('advanced-videos'); ?>" 
                    id="<?php echo $this->get_form_field_id('advanced-videos'); ?>" 
                    type="checkbox"
                > Receive Videos
            </label>
        </div>
    </div>
</fieldset>