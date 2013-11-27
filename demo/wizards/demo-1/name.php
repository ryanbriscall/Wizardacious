<fieldset>
    <legend>Your Name</legend>
    <div class="control-group">
        <label 
            class="control-label" 
            for="<?php echo $this->get_form_field_id('fullname'); ?>"
        >Name</label>

        <div class="controls">
            <input 
                placeholder="Enter your name" 
                name="<?php echo $this->get_form_field_name('fullname'); ?>" 
                id="<?php echo $this->get_form_field_id('fullname'); ?>" 
                value="<?php echo $this->get_form_field('fullname'); ?>"
                class="required" 
                type="text"
            >

            <span class="help-block">Please enter your name.</span>
        </div>
    </div>
</fieldset>
