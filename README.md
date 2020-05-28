# A Wizard PHP library

A library for handling a series of steps in a "wizard" fashion (using PHP), in
an HTML/JavaScript/AJAX environment.

[View Demo](http://ggz.rf.gd/wizardacious/demo)

## Features

* Branching. (Recursive)
* Stateful. (Session)
* Navigation. (Logical)
* Tracking. (Step)
* Progress. (% Complete)

## Table of Contents

- <a href="#usage">Usage</a>
    - <a href="#initialization">Initialization</a>
    - <a href="#render">Render</a>
    - <a href="#navigating">Navigating</a>
- <a href="#usage (continued)">Usage (Continued)</a>
    - <a href="#initialization">Initialization</a>
    - <a href="#render">Render</a>
- <a href="#properties">Properties</a>
- <a href="#methods">Methods</a>
- <a href="#changelog">Changelog</a>
- <a href="#license">License</a>

## Usage

### Initialization

1. Include the library:

   ```php
   include_once wizardacious.php
   ```

2. Setup your config (that will be passed as parameter for the initialization).

   ```php
   $wizard_config = array(
       'steps' => array(
           'name' => array(
               'file' => 'name.php'
           ),
           'thank-you' => array(
               'file' => 'thank-you.php'
           )
       ),
       'tree' => array(
           'name',
           'thank-you'
       )
   );
   ```

    In the example (above), we've setup our configuration to have two steps: `name`, and `thank-you`.

    **Note:** You must have at least two steps, because the last step is the *thank you* step.

3. Declare a variable to an instance of the library, passing in your config:

   ```php
   $my_wizard = new Wizardacious($wizard_config);
   ```

### Render

1. Print the wizard onto the screen:

   ```php
   echo $my_wizard->render();
   ```

### Navigating

1. Fill in the answer to the first step.

   ```php
   $my_wizard->set_form_field('fullname', 'Bob');
   ```

2. Proceed to the next step, then render (display the wizard).

   ```php
   $my_wizard->next();
   ```

3. Print the wizard onto the screen:

   ```php
   echo $my_wizard->render();
   ```

## Usage (continued)

You may be in a situation where you want to initialize the library without passing any parameters.

### Initialization:

1. Include the library:

   ```php
   include_once wizardacious.php
   ```

2. Declare a variable to an instance of the library:

   ```php
   $my_wizard = new Wizardacious();
   ```

3. Configure the steps:

   ```php
   $my_wizard->set_steps(array(
       'name' => array(
           'file' => 'name.php'
       ),
       'thank-you' => array(
           'file' => 'thank-you.php'
       )
   ));
   ```
4. Configure the tree:

   ```php
   $my_wizard->set_tree(array(
       'name',
       'thank-you'
   ));
   ```

5. Setup the wizard:

    **Note:** We set the `steps` and `tree` manually, therefore we must now execute the `initialize_wizard()` method.

   ```php
   $my_wizard->initialize_wizard();
   ```

### Render

1. Print the wizard onto the screen:

   ```php
   echo $my_wizard->render();
   ```

## Properties

You can customize any of the following properties:

- `$alias` -- Short-name for the wizard.  It is used in a variety of ways; see wizardacious.php for details.
- `$session_name` -- Short-name of the session; for Wizardacious wizards.
- `$step_path` -- The path to use for loading step files.
- `$step` -- Short-name of the step.  Defined in the array config of steps.
- `$prev_step` -- Step name of the previous step (in sequence).
- `$next_step` -- Step name of the next step (in sequence).
- `$step_idx` -- Step index number of the current step (in sequence).
- `$step_count` -- Total number of steps.
- `$breadcrumbs` -- Breadcrumb trail of steps, from user.
- `$steps` -- Steps config.  See demo for details.
- `$tree` -- Steps tree config.  See demo for details.
- `$step_by_step` -- Render(display) one step at a time. 
- `$show_next_step` -- Display "Next" button, in navigation.
- `$show_prev_step` -- Display "Back" button, in navigation.
- `$show_progress` -- Show Progress (% Complete)
- `$show_tracking` -- Show step name in a hidden input.  Used for tracking the current step.
- `$embed_navigation` -- Place navigation HTML within the step HTML, or keep it outside.
- `$navigation_file` -- The default view (HTML) used for the navigation.
- `$prev_step_html` -- The default HTML used for the "Previous" button.
- `$next_step_html` -- The default HTML used for the "Next" button.
- `$disabled_prev_step_html` -- The default HTML used for the "Previous" button as disabled.
- `$disabled_next_step_html` -- The default HTML used for the "Next" button as disabled.
- `$form` -- Saved (submitted) form fields and their values.
- `$navigating` -- Navigating. (Special)  Used for internal purposes.
- `$cycle` -- An array of parameters used when cycling through the tree.

## Methods

You can use any of the following methods:

- `initialize_wizard()` -- Initialize Wizard
- `get_session_name()` -- Get the session name.
- `set_session_name($name)` -- Set the session name.
- `set_session($alias = '', $data = array())` -- Set the session (specific wizard session).
- `get_session($alias = '')` -- Get the session (specific wizard session).
- `reset_session()` -- Resets the session.
- `set_alias($alias)` -- Set the alias.
- `get_alias()` -- Get the alias.
- `set_step($step)` -- Set the step.
- `get_step()` -- Get the step.
- `step_exists($step)` -- Check if step exists (in the steps collection).
- `set_step_idx($step_idx = 1)` -- Set the step index number.
- `get_step_idx()` -- Get the step index number.
- `set_step_count($step_count = 0)` -- Set the step count.
- `get_step_count()` -- Get the step count.
- `increase_step_count()` -- Increase the step count by +1.
- `decrease_step_count()` -- Decrease the step count by -1.
- `get_step_array($step = '')` -- Get a step (array).
- `get_step_file()` -- Get the 'file' parameter (of the current step).
- `get_step_path()` -- Get the path for including step files(relative paths).
- `set_next_step($next_step = '')` -- Set the next step.
- `get_next_step()` -- Get the previous step.
- `set_prev_step($prev_step = '')` -- Set the previous step.
- `get_prev_step()` -- Get the previous step.
- `set_steps($steps = array())` -- Set the steps.
- `get_steps()` -- Get the steps.
- `set_tree($tree = array())` -- Set the tree of steps.
- `get_tree()` -- Get the tree of steps.
- `set_breadcrumbs($breadcrumbs = array())` -- Set the breadcrumbs.
- `get_breadcrumbs()` -- Get the breadcrumbs.
- `breadcrumb_exists($crumb)` -- Check if a breadcrumb exists (in the breadcrumbs collection).
- `add_breadcrumb($crumb)` -- Add a breadcrumb (to the breadcrumbs collection).
- `remove_breadcrumb($crumb)` -- Remove a breadcrumb (from the breadcrumbs collection).
- `set_navigating($navigating = '')` -- Set the navigating (to either 'next' or 'back').
- `get_navigating()` -- Get the navigating (either 'next' or 'back').
- `is_navigating_forward()` -- Check if currently navigating forward ('next').
- `is_navigating_backward()` -- Check if currently navigating backward ('back').
- `navigating_forward()` -- Short-hand method for setting navigating forward ('next').
- `navigating_backward()` -- Short-hand method for setting navigating backward ('back').
- `set_form($form = array())` -- Set the form (collection of fields).
- `get_form()` -- Get the form (collection of fields).
- `set_form_field($field, $value)` -- Set the navigating (to either 'next' or 'back').
- `get_form_field($field, $default = '')` -- Get a form field's value (by field name), with option for default value.
- `get_form_field_name($field)` -- Get a form field's markup name (for 'name' attribute of input element).
- `get_form_field_id($field)` -- Get a form field's markup id (for 'id' attribute of input element).
- `in_form($key, $value)` -- Check if a field exists in the form (collection of fields).
- `get_progress()` -- Get a calculate percentage of progress completed of steps of wizard.
- `cycle_wizard()` -- Cycle (traverse) through the tree.  (Used for internal purposes) 
- `next()` -- Moves to the next step of the wizard.
- `prev()` -- Moves to the previous step of the wizard.
- `update_form()` -- Updates the form (from $_POST).
- `save()` -- Saves some session information (for the wizard).
- `reset()` -- Resets the wizard.
- `render_navigation()` -- Proccess and return the generated markup(HTML) for the navigation.
- `render()` -- Proccess and return the generated markup(HTML) for the wizard.
- `render_hidden_form()` -- Get the HTML (for hidden form), and print it out.
- `debug()` -- Print out some debug information.

## Changelog

### 1.0.0

 - Initial release.

## License

Wizardacious is licensed under the MIT, see the `LICENSE` file for more details.
