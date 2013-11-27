<fieldset>
    <legend>Basic Training</legend>
    <p>This package offers the option of receiving monthly newsletters.</p>
    <div class="control-group">
        <div class="controls">
            <label class="checkbox">
                <input 
                    <?php echo (
                        $this->get_form_field('basic-newsletter') == '' 
                        ?: 'checked="checked"'
                    ); ?> 
                    name="<?php echo $this->get_form_field_name('basic-newsletter'); ?>" 
                    id="<?php echo $this->get_form_field_id('basic-newsletter'); ?>" 
                    type="checkbox"
                > Receive Newsletter
            </label>
        </div>
    </div>
</fieldset>