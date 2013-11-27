<fieldset>
    <legend>Your Account</legend>
    <div class="control-group">
        <label 
            for="<?php echo $this->get_form_field_id('account-type'); ?>" 
            class="control-label"
        >Account</label>
        <div class="controls">
            <select 
                id="<?php echo $this->get_form_field_id('account-type'); ?>" 
                name="<?php echo $this->get_form_field_name('account-type'); ?>" 
                class="required valid"
            >
                <option 
                    <?php echo (
                        $this->get_form_field('account-type') == '' 
                        ? 'selected="selected"' 
                        : ''
                    ); ?> 
                    value=""
                >Choose</option>

                <option 
                    <?php echo (
                        $this->get_form_field('account-type') == 'basic' 
                        ? 'selected="selected"' 
                        : ''
                    ); ?>
                    value="basic"
                >Basic</option>

                <option 
                    <?php echo (
                        $this->get_form_field('account-type') == 'premium' 
                        ? 'selected="selected"' 
                        : ''
                    ); ?>
                    value="premium"
                >Premium</option>
            </select>
            <span class="help-block">Premium includes: <em>Basic</em>, <em>Advanced</em>, and <em>VIP</em></span>
        </div>
    </div>
</fieldset>
