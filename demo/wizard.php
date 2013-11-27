<?php 
/* Wizard Ajax file.
 *
 * This will be the file for ajax usage; to handle the submitted data (POST),
 * forward and backward navigation, and ultimately the rendering of the Wizard.
 *
 */

// First, let's start the session.
session_start();

// Include the Wizardacious library.
include '../src/lib/wizardacious.php';

// Include our demo wizard; the configuration and initialization of the wizard.
include 'wizards/demo-1.php';

// -----------------------------------------------------------------------------

/* Submit, Navigation, and Render
 *
 * This is where we'll handle the navigation (submitting of step), and 
 * ultimately to render the wizard.
 *
 * Note: You'll notice we're also outputting some debug information as well.
 *
 */

/* Let's sleep for 1 second, just for demonstration purposes; better illustrate 
 * the loading state (animation) we'll be displaying when a new step(screen) is 
 * loading when navigating forward or backward in the wizard.
 *
 */
sleep(1); 

/* Check if POST is occurring.
 *
 * Note: You'll notice that the Wizard won't display (render) unless this POST
 *       occurs.  This is because we're bundling the process--of loading a fresh 
 *       wizard--in with our navigation processes.
 *
 *       This means that if javascript failed (for some reason), then we won't
 *       display the wizard at all.
 *
 */
if (isset($_POST['wizard']) && $_POST['wizard']) {
    $wizard = $_POST['wizard'].'_wizard';

    // Reset
    if (isset($_POST['reset'])) $$wizard->reset();

    // Check if user is navigating.
    if (isset($_POST['nav']) && $_POST['nav']) {
        $nav = $_POST['nav'];
        $$wizard->$nav();

        // Custom hard-coded Hook:
        // Geocode Zip
        /*
        if (isset($_POST[$_POST['wizard']]['firstzip']) && $zip = $_POST[$_POST['wizard']]['firstzip']) { 
            include 'api-zip.php';
            $api = new zipApi();
            if ($response = $api->zip_call($zip)) {
                $data = json_decode($api->zip_call($zip));
                $$wizard->set_form_field('city', $data->city);
                $$wizard->set_form_field('state', $data->state);
                $$wizard->set_form_field('zip', $zip);
                $$wizard->save();
            }
        }
        */
        // End - Custom
    }

    // Render the wizard.
    echo $$wizard->render();

    // Output some debug information
    echo $$wizard->debug();
}

exit; // Sessions