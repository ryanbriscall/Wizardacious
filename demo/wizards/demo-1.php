<?php
/* Wizard alias
 *
 * Declare our wizard alias name, for convenience sake.
 *
 * Note: We don't use the word "wizard" anywhere in our alias name (such as
 *       "my_wizard" for example) because we already take care of that by 
 *       appending "_wizard" automatically.
 * 
 */
$wizard_alias = 'my';

/* Wizard config (partial)
 *
 * Note: You'll notice that we're only setting a few parameters in our config,
 *       because for demonstration purposes.  You may be in a situation where 
 *       you want to do this, and to set the steps and tree afterwards.
 *
 */
$wizard_config = array(
    'alias' => $wizard_alias, 
    'step_path' => 'wizards/demo-1',
    'show_progress' => true
);

/* Wizard steps (config)
 *
 * Now we'll setup our steps config to set to our wizard.
 *
 */
$wizard_steps = array(
    'name' => array(
        'file' => 'name.php'
    ),
    'account' => array(
        'file' => 'account.php'
    ),
    'basic-training' => array(
        'file' => 'basic-training.php'
    ),
    'advanced-training' => array(
        'file' => 'advanced-training.php'
    ),
    'vip-training' => array(
        'file' => 'vip-training.php'
    ),
    'thank-you' => array(
        'file' => 'thank-you.php',
    ),
);

/* Wizard tree (config)
 *
 * Now we'll setup our tree (of steps) config to set to our wizard.
 *
 */
$wizard_tree = array(
    'name',
    'account',
    array(
        'root' => 'account-type',
        'branches' => array(
            'basic' => array(
                'basic-training',
            ),
            'premium' => array(
                'basic-training',
                'advanced-training',
                'vip-training',
            )
        )
    ),
    'thank-you'
);

// CUSTOM: Vars
$trackingcode = (isset($_GET['trackingcode']) && $_GET['trackingcode']) ? (int)$_GET['trackingcode'] : 1234567890;

// -----------------------------------------------------------------------------

// Declare/Initialize our Wizard
$my_wizard = new Wizardacious($wizard_config);

// Manually set the steps (should you have reason to do so)
$my_wizard->set_steps($wizard_steps); // See config file for details.

// Manually set the tree (should you have reason to do so)
$my_wizard->set_tree($wizard_tree); // See config file for details.

// CUSTOM: Pre-set some fields (form)
$my_wizard->set_form_field('trackingcode', $trackingcode); // See _config.php
$my_wizard->set_form_field('ipaddress', $_SERVER['REMOTE_ADDR']);
// END - CUSTOM: Pre-set

/* Process/save the wizard configuration.
 *
 * Note: You don't need to do this if you're not doing anything custom (above)
 *       and you're passing steps and tree within $wizard_config
 */
$my_wizard->initialize_wizard();