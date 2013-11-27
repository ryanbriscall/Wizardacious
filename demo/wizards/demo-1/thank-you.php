<div class="api-loading-content">
    <h3>Please wait...</h3>
    <?php echo $this->render_hidden_form(); ?>
    <input 
        id="<?php echo $this->get_form_field_id('submit'); ?>" 
        name="<?php echo $this->get_form_field_id('submit'); ?>" 
        value="1" 
        type="hidden"
    >
</div>
<div class="error-content hide">
    <h3>Error</h3>
    <p><strong>There was a problem</strong> with your submission.</p>
    <p>Please correct the errors and re-submit.</p>
</div>
<div class="failure-content hide">
    <h3>Failed</h3>
    <p><strong>There was a problem</strong> submitting your request.</p>
    <p>Please try again in a minute.</p>
</div>
<div class="success-content hide">
    <h3>Thank You!</h3>

    <p>Your application has been processed.</p>
</div>
