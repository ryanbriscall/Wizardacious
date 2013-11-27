<?php
/**
 * PHP "Wizardacious" Library
 *
 * A library for handling a wizard of steps with multiple branches and to 
 * navigate forward and backward.
 *
 *
 * NOTICE OF LICENSE
 *
 * (c) Ryan Briscall <ryanbriscall@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author      Ryan Briscall <ryanbriscall@gmail.com>
 * @copyright   Copyright (c) 2013, Dekker Designs
 * @license     (see above)
 * @link        http://www.ryanbriscall.com/ Author Page
 * @link        http://www.github.com/ryanbriscall/wizardacious Project Page
 * @since       Version 1.0
 *
 *
 */
class Wizardacious {
    const VERSION = '1.0';

    /** 
     * Alias
     *
     * Short-name for the wizard.  It is used in a variety of ways, such as:
     * 1) As a parameter passed into initialization of this library.
     * 2) Saving to sessions: $_SESSION[$session_name][$alias]
     * 3) Retrieval via POST: $_POST[$alias]
     * 4) Prefixing field ids: id="{$alias}_{field}"
     * 5) Prefixing field names: name="{$alias}[{field}]"
     * 6) Prefixed step id for wrapper div: id="{$alias}_{field}"
     * 7) JavaScript wizard() calls: wizard($alias, ...);
     *
     * @var string
     */
    var $alias = 'my';

    /**
     * Session name
     *
     * Short-name of the session; for Wizardacious wizards.
     *
     * @var string
     */
    var $session_name = 'wizard';

    /**
     * Step (content) path.
     *
     * The path to use for loading step files.
     *
     * @var string
     */
    var $step_path = '';

    /**
     * Step
     *
     * Short-name of the step.  Defined in the array config of steps.
     *
     * @var string
     */
    var $step = '';

    /**
     * Previous Step
     *
     * Step name of the previous step (in sequence).
     *
     * @var string
     */
    var $prev_step = '';

    /**
     * Next Step
     *
     * Step name of the next step (in sequence).
     *
     * @var string
     */
    var $next_step = '';

    /**
     * Step Index
     *
     * Step index number of the current step (in sequence).
     *
     * @var int
     */
    var $step_idx = 1;

    /**
     * Total Steps
     *
     * Total number of steps.
     *
     * @var int
     */
    var $step_count = 0;

    /**
     * Breadcrums
     *
     * Breadcrumb trail of steps, from user.
     *
     * @var array
     */
    var $breadcrumbs = array();

    /**
     * Steps
     *
     * Steps config.  See demo for details.
     *
     * @var array
     */
    var $steps = array();

    /**
     * Tree
     *
     * Steps tree config.  See demo for details.
     *
     * @var array
     */
    var $tree = array();

    /**
     * Step-by-step
     *
     * Render(display) one step at a time.  
     * Note: This must be set to true, because there is no support for displaying
     *       all steps at once ("false") at this time.
     *
     * @var boolean
     */
    var $step_by_step = true;

    /**
     * Show Next Step
     *
     * Display "Next" button, in navigation.
     *
     * @var boolean
     */
    var $show_next_step = true;

    /**
     * Show Previous Step
     *
     * Display "Back" button, in navigation.
     *
     * @var boolean
     */
    var $show_prev_step = true;

    /**
     * Show Progress
     *
     * If set to "true", a cycle count will occur and a percentage (of progress 
     * completed) number will be displayed as a hidden input with the value of the percentage.
     * 
     * @var boolean
     */
    var $show_progress = false;

    /**
     * Show Tracking
     *
     * Show step name in a hidden input.  Used for tracking the current step.
     *
     * @var boolean
     */
    var $show_tracking = true;

    /**
     * Embed Navigation
     *
     * Place navigation HTML within the step HTML, or keep it outside.
     *
     * @var boolean
     */
    var $embed_navigation = false;

    /**
     * Navigation view (HTML)
     *
     * The default view (HTML) used for the navigation.
     *
     * @var string
     */
    var $navigation_file = '_navigation.php';

    /**
     * Previous Step HTML
     *
     * The default HTML used for the "Previous" button.
     *
     * @var string
     */
    var $prev_step_html = '<button class="wizard-back" type="button">Back</button>';

    /**
     * Next Step HTML
     *
     * The default HTML used for the "Next" button.
     *
     * @var string
     */
    var $next_step_html = '<button class="wizard-next" type="button">Next</button>';

    /**
     * Disabled Previous Step HTML
     *
     * The default HTML used for the "Previous" button as disabled.
     *
     * @var string
     */
    var $disabled_prev_step_html = '<button disabled="disabled" class="wizard-back disabled">Back</button>';

    /**
     * Disabled Next Step HTML
     *
     * The default HTML used for the "Next" button as disabled.
     *
     * @var string
     */
    var $disabled_next_step_html = '<button disabled="disabled" class="wizard-next disabled">Next</button>';

    /**
     * Form
     *
     * Saved (submitted) form fields and their values.
     *
     * @var array
     */
    var $form = array(); 

    /** 
     * Navigating
     *
     * Set when a user is currently navigating to the next or previous step.
     * Used when cycling through the tree to determine whether we continue 
     * cycling through or to stop.
     *
     * @var string
     */
    var $navigating = '';

    /**
     * Cycle
     * 
     * An array of parameters used when cycling through the tree.
     * See clear_cycle() for parameter details.
     *
     * @var array
     */
    var $cycle = array(); 

    // -------------------------------------------------------------------------

    /** 
     * Construct
     *
     * Initialize with passed parameters (if any), and halt on missing "alias"
     * parameter.
     *
     * Set the current step.
     * Set the breadcrumbs.
     * Set the form.
     * Set the alias.
     * Initialize the wizard via initialize_wizard() method (below).
     *
     * @param array $params
     */
    public function __construct($params = array()) {
        if ($params) { 
            if (!is_array($params)) $params = array('alias' => $params);

            // Config
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $method = 'set_'.$key;
                    if (method_exists($this, $method)) { $this->$method($val); }
                    else { $this->$key = $val; }
                }
            }
        }

        // Session
        if ($wizard = $this->get_session()) {
            $this->set_step($wizard['step']); // Set step from Session.
            $this->set_breadcrumbs($wizard['breadcrumbs']); // Set breadcrumbs from Session.
            $this->set_form($wizard['form']); // Set form from Session.
        }

        // Setup
        if ($this->get_steps() && $this->get_tree()) $this->initialize_wizard();
    }

    // -------------------------------------------------------------------------

    /**
     * Initialize Wizard
     *
     * Check for steps, and halt if none found.
     * Check for tree, and halt if none found.
     * Check for step.  If empty, then use first step of tree.
     * Check for step in breadcrumbs.  If none, then add breadcrumb step.
     * Cycle the wizard.
     * Save.
     *
     * @return self
     */
    public function initialize_wizard() {
        if (!$steps = $this->get_steps()) die('ERROR: No steps found to initialize with.');
        if (!$tree = $this->get_tree()) die('ERROR: No tree found to initialize with.');
        if (!$step = $this->get_step()) { $step = $tree[0]; $this->set_step($step); }
        if (!$this->breadcrumb_exists($step)) $this->add_breadcrumb($step);

        $this->cycle_wizard();
        $this->save();
        return $this;
    }

    // -------------------------------------------------------------------------

    /**
     * Get the session name.
     *
     * @return string
     */
    public function get_session_name() { return $this->session_name; }

    /**
     * Set the session name.
     *
     * @param string $name
     *
     * @return self
     */
    public function set_session_name($name) { $this->session_name = $name; return $this; }

    /**
     * Set the session (specific wizard session).
     *
     * @param string $alias
     * @param array $data
     *
     * @return self
     */
    public function set_session($alias = '', $data = array()) {
        if (!$alias) $alias = $this->get_alias();
        if (!$alias) die('ERROR: Unable to set wizard session because alias is not set.');
        $session_name = $this->get_session_name();
        $_SESSION[$session_name][$alias] = $data;
        return $this;
    }

    /**
     * Get the session (specific wizard session).
     *
     * @param string $alias
     *
     * @return array
     */
    public function get_session($alias = '') {
        if (!$alias) $alias = $this->get_alias();
        if (!$alias) die('ERROR: Unable to get wizard session because alias is not set.');
        $session_name = $this->get_session_name();
        $wizards = isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : array();
        $wizard = isset($wizards[$alias]) ? $wizards[$alias] : array();
        return $wizard;
    }

    /**
     * Resets the session.
     *
     * @return self
     */
    public function reset_session($alias = '') {
        if (!$alias) $alias = $this->get_alias();
        if (!$alias) die('ERROR: Unable to reset wizard session because alias is not set.');
        $session_name = $this->get_session_name();
        unset($_SESSION[$session_name][$alias]);
        return $this;
    }

    // -------------------------------------------------------------------------

    /**
     * Set the alias.
     *
     * @param string $alias
     *
     * @return self
     */
    public function set_alias($alias) { 
        $this->alias = $alias; 
        return $this; 
    }

    /**
     * Get the alias.
     *
     * @return string
     */
    public function get_alias() { return $this->alias; }

    /**
     * Set the step.
     *
     * @param string $step
     *
     * @return self
     */
    public function set_step($step) { 
        if ($step != '*') { $this->step = $step; } 
        return $this; 
    }

    /**
     * Get the step.
     *
     * @return string
     */
    public function get_step() { return $this->step; }

    /**
     * Check if step exists (in the steps collection).
     *
     * @param string $step
     *
     * @return boolean
     */
    public function step_exists($step) { 
        $steps = $this->get_steps(); 
        return (isset($steps[$step]) ? true : false); 
    }

    /**
     * Set the step index number.
     *
     * @param int $step
     *
     * @return self
     */
    public function set_step_idx($step_idx = 1) { 
        $this->step_idx = $step_idx; 
        return $this; 
    }

    /**
     * Get the step index number.
     *
     * @return int
     */
    public function get_step_idx() { return $this->step_idx; }

    /**
     * Set the step count.
     *
     * @param int $step
     *
     * @return self
     */
    public function set_step_count($step_count = 0) { 
        $this->step_count = $step_count; 
        return $this; 
    }

    /**
     * Get the step count.
     *
     * @return int
     */
    public function get_step_count() { return $this->step_count; }

    /**
     * Increase the step count by +1.
     *
     * @return self
     */
    public function increase_step_count() { 
        return $this->set_step_count(($this->get_step_count()+1)); 
    }

    /**
     * Decrease the step count by -1.
     *
     * @return self
     */
    public function decrease_step_count() { 
        return $this->set_step_count(($this->get_step_count()-1)); 
    }

    /**
     * Get a step (array).
     *
     * @param string $step
     *
     * @return array
     */
    public function get_step_array($step = '') {
        $steps = $this->get_steps();
        if (!$step) $step = $this->get_step();
        if (!$this->step_exists($step)) die('ERROR: Step "'.$step.'" not found.');
        return $steps[$step];
    }

    /**
     * Get the 'file' parameter (of the current step).
     *
     * @return string
     */
    public function get_step_file() {
        $step_array = $this->get_step_array();
        return $step_array['file'];
    }

    /**
     * Get the path for including step files(relative paths).
     *
     * @return string
     */
    public function get_step_path() { return $this->step_path; }

    /**
     * Set the next step.
     *
     * @param string $next_step
     *
     * @return self
     */
    public function set_next_step($next_step = '') { 
        $this->next_step = $next_step; 
        return $this; 
    }

    /**
     * Get the previous step.
     *
     * @return string
     */
    public function get_next_step() { return $this->next_step; }

    /**
     * Set the previous step.
     *
     * @param string $prev_step
     *
     * @return self
     */
    public function set_prev_step($prev_step = '') { 
        $this->prev_step = $prev_step; 
        return $this; 
    }

    /**
     * Get the previous step.
     *
     * @return string
     */
    public function get_prev_step() { return $this->prev_step; }

    /**
     * Set the steps.
     *
     * @param array $steps
     *
     * @return self
     */
    public function set_steps($steps = array()) { 
        $this->steps = $steps; 
        return $this; 
    }

    /**
     * Get the steps.
     *
     * @return array
     */
    public function get_steps() { return $this->steps; }

    /**
     * Set the tree of steps.
     *
     * @param array $tree
     *
     * @return self
     */
    public function set_tree($tree = array()) { 
        $this->tree = $tree; 
        return $this; 
    }

    /**
     * Get the tree of steps.
     *
     * @return array
     */
    public function get_tree() { return $this->tree; }

    /**
     * Set the breadcrumbs.
     *
     * @param array $breadcrumbs
     *
     * @return self
     */
    public function set_breadcrumbs($breadcrumbs = array()) { $this->breadcrumbs = $breadcrumbs; return $this; }

    /**
     * Get the breadcrumbs.
     *
     * @return array
     */
    public function get_breadcrumbs() { return $this->breadcrumbs; }

    /**
     * Check if a breadcrumb exists (in the breadcrumbs collection).
     *
     * @param string $crumb
     *
     * @return boolean
     */
    public function breadcrumb_exists($crumb) { 
        $breadcrumbs = $this->get_breadcrumbs();
        if (!in_array($crumb, $breadcrumbs)) return false;
        return true;
    }

    /**
     * Add a breadcrumb (to the breadcrumbs collection).
     *
     * @param string $crumb
     *
     * @return self
     */
    public function add_breadcrumb($crumb) { 
        if ($crumb != '*') { 
            $breadcrumbs = $this->get_breadcrumbs();
            $breadcrumbs[] = $crumb;
            $this->set_breadcrumbs($breadcrumbs);
        }
        return $this;
    }

    /**
     * Remove a breadcrumb (from the breadcrumbs collection).
     *
     * @param string $crumb
     *
     * @return self
     */
    public function remove_breadcrumb($crumb) { 
        $breadcrumbs = $this->get_breadcrumbs();
        foreach ($breadcrumbs AS $k => $v) { if ($v == $crumb) { $unset_key = $k; break; }}
        if ($unset_key) { 
            unset($breadcrumbs[$unset_key]);
            $this->set_breadcrumbs($breadcrumbs);
        }
        return $this;
    }

    /**
     * Set the navigating (to either 'next' or 'back').
     *
     * @param string $navigating
     *
     * @return self
     */
    public function set_navigating($navigating = '') { 
        $this->navigating = $navigating; 
        return $this; 
    }

    /**
     * Get the navigating (either 'next' or 'back').
     *
     * @return string
     */
    public function get_navigating() { return $this->navigating; }

    /**
     * Check if currently navigating forward ('next').
     *
     * @return boolean
     */
    public function is_navigating_forward() { 
        return ($this->get_navigating() == 'next' ? true : false); 
    }

    /**
     * Check if currently navigating backward ('back').
     *
     * @return boolean
     */
    public function is_navigating_backward() { return ($this->get_navigating() == 'back' ? true : false); }

    /**
     * Short-hand method for setting navigating forward ('next').
     *
     * @return boolean
     */
    public function navigating_forward() { $this->set_navigating('next'); return $this; }

    /**
     * Short-hand method for setting navigating backward ('back').
     *
     * @return boolean
     */
    public function navigating_backward() { $this->set_navigating('back'); return $this; }

    /**
     * Set the form (collection of fields).
     *
     * @param array $form
     *
     * @return self
     */
    public function set_form($form = array()) { 
        $this->form = $form; 
        return $this; 
    }

    /**
     * Get the form (collection of fields).
     *
     * @return array
     */
    public function get_form() { return $this->form; }

    /**
     * Set the navigating (to either 'next' or 'back').
     *
     * @param string $navigating
     *
     * @return self
     */
    public function set_form_field($field, $value) { 
        $this->form[$field] = $value; 
        return $this; 
    }

    /**
     * Get a form field's value (by field name), with option for default value.
     *
     * @param string $field
     * @param string $default
     *
     * @return string
     */
    public function get_form_field($field, $default = '') { 
        $form = $this->get_form(); 
        return (isset($form[$field]) ? $form[$field] : $default); 
    }

    /**
     * Get a form field's markup name (for 'name' attribute of input element).
     *
     * @return string
     */
    public function get_form_field_name($field) { 
        return $this->get_alias()."[$field]"; 
    }

    /**
     * Get a form field's markup id (for 'id' attribute of input element).
     *
     * @return array
     */
    public function get_form_field_id($field) { 
        return $this->get_alias()."_$field";
    }

    /**
     * Check if a field exists in the form (collection of fields).
     *
     * @param string $key
     * @param string $value
     *
     * @return boolean
     */
    public function in_form($key, $value) { 
        $form = $this->get_form();
        return (array_key_exists($key, $form) && $form[$key] == $value) ? true : false;
    }

    // -------------------------------------------------------------------------

    /**
     * Get a calculate percentage of progress completed of steps of wizard.
     *
     * @return int
     */
    public function get_progress() { 
        return (round(($this->get_step_idx() * 100) / $this->get_step_count()));
    }

    // -------------------------------------------------------------------------

    public function clear_cycle() {
        $this->cycle = array(
            'index' => -1, // Also used for when Counting.
            'count' => false, // Counting only: Yes or no.
            'step_count' => 0, // Total Count # of steps.
            'break' => 0, // Full breaking from Cycle.
            'continue' => 0, // Full continue through Cycle.
            'branch' => '', 
            'branchlevel' => 1,
            'branchtrail' => array(),
            'breadcrumbs' => array(),
            'navigated' => 0,
            'prev_step' => $this->get_prev_step(), 
            'current_step' => $this->get_step(), 
            'next_step' => $this->get_next_step()
        );
    }

    public function cycle_count_wizard() { 
        $this->clear_cycle();
        $this->cycle_set_count(true);
        $breadcrumbs = $this->get_breadcrumbs();
        $this->cycle();
        $this->set_breadcrumbs($breadcrumbs);
        // Save
        $this->set_step_count($this->cycle_get_step_count());
        $this->clear_cycle();
    }

    public function cycle_wizard() { 
        $this->clear_cycle();
        $this->cycle();

        /*
        First let's check if we've finished stepping through a group of branches that is incomplete.
        If neither of the branches (of the branch group (for the root step)) is found in the form, 
        then we want to "break"
        */
        if ($this->cycle_branchtrail_exists() && $this->cycle_branch_is_incomplete()) { 
            $this->cycle_set_next_step('*');
        }

        // Save
        $this->set_step_idx(($this->cycle_get_index()+1));
        $this->set_prev_step($this->cycle_get_prev_step());
        $this->set_step($this->cycle_get_step());
        $this->set_next_step($this->cycle_get_next_step());

        $this->clear_cycle();

        // Count
        if ($this->show_progress) $this->cycle_count_wizard();

        return $this;
    }
    public function cycle_branch($branch) { 
        $this->cycle['branch'] = $branch;
        if (isset($this->cycle['branchtrail'][$branch]) && $this->cycle['branchtrail'][$branch] != '') return; // Hack: Because when loop through next sibling branches of the same branch group, we don't want to override a value that may already be set/exists.
        $this->cycle['branchtrail'][$branch] = '';
    }
    public function cycle_set_branch($branch) { $this->cycle['branch'] = $step; return $this; }
    public function cycle_get_branch() { return $this->cycle['branch']; }
    public function cycle_get_branch_key() { return $this->cycle['branch'][0]; }
    public function cycle_get_branch_value() { return $this->cycle['branch'][1]; }
    public function cycle_branchtrail_exists() { return (isset($this->cycle['branchtrail'][$this->cycle_get_branch()]) ? true : false); }
    public function cycle_branch_is_incomplete() { 
        if (!$this->cycle_branchtrail_exists()) return false;
        return ($this->cycle['branchtrail'][$this->cycle_get_branch()] == '' ? true : false);
    }
    public function cycle_set_index($index = 0) { $this->cycle['index'] = $index; return $this; }
    public function cycle_get_index() { return $this->cycle['index']; }
    public function cycle_increase_index() { $this->cycle_set_index(($this->cycle_get_index()+1)); return $this; }
    public function cycle_decrease_index() { $this->cycle_set_index(($this->cycle_get_index()-1)); return $this; }
    public function cycle_set_count($count = false) { $this->cycle['count'] = $count; return $this; }
    public function cycle_get_count() { return $this->cycle['count']; }
    public function cycle_is_counting() { return $this->cycle_get_count(); }
    public function cycle_set_step_count($step_count = 0) { $this->cycle['step_count'] = $step_count; return $this; }
    public function cycle_get_step_count() { return $this->cycle['step_count']; }
    public function cycle_increase_step_count() { $this->cycle_set_step_count(($this->cycle_get_step_count()+1)); return $this; }
    public function cycle_decrease_step_count() { $this->cycle_set_step_count(($this->cycle_get_step_count()-1)); return $this; }
    public function cycle_add_branch($branch) { }
    public function cycle_set_step($step) { $this->cycle['current_step'] = $step; return $this; }
    public function cycle_get_step() { return $this->cycle['current_step']; }
    public function cycle_set_next_step($step) { 
        if ($step == '?') $step = $this->cycle_get_step();
        $this->cycle['next_step'] = $step; 
        return $this; 
    }
    public function cycle_get_next_step() { return $this->cycle['next_step']; }
    public function cycle_set_prev_step($step) { 
        if ($step == '?') $step = $this->cycle_get_step();
        $this->cycle['prev_step'] = $step;
        return $this; 
    }
    public function cycle_get_prev_step() { return $this->cycle['prev_step']; }
    public function cycle_set_breadcrumbs($breadcrumbs = array()) { $this->cycle['breadcrumbs'] = $breadcrumbs; return $this; }
    public function cycle_get_breadcrumbs() { return $this->cycle['breadcrumbs']; }
    public function cycle_get_breadcrumb_by_idx($idx) { 
        $breadcrumbs = $this->cycle_get_breadcrumbs();
        return (isset($breadcrumbs[$idx]) ? $breadcrumbs[$idx] : '?');
    }
    public function cycle_breadcrumb_exists($crumb) { 
        $breadcrumbs = $this->cycle_get_breadcrumbs();
        if (!in_array($crumb, $breadcrumbs)) return false;
        return true;
    }
    public function cycle_add_breadcrumb($crumb) { 
        if ($crumb != '*') { 
            $breadcrumbs = $this->cycle_get_breadcrumbs();
            $breadcrumbs[] = $crumb;
            $this->cycle_set_breadcrumbs($breadcrumbs);
        }
        return $this; 
    }
    public function cycle_remove_breadcrumb($crumb) { 
        $breadcrumbs = $this->cycle_get_breadcrumbs();
        foreach ($breadcrumbs AS $k => $v) { if ($v == $crumb) { 
            $unset_key = $k; break; 
            unset($breadcrumbs[$unset_key]);
            $this->cycle_set_breadcrumbs($breadcrumbs);
        }}
        return $this;
    }
    public function cycle_step($step) {
        $this->cycle_set_prev_step($this->cycle_get_step());
        $this->cycle_set_step($step);
        $this->cycle_set_next_step($this->cycle_get_breadcrumb_by_idx($this->cycle_get_index()+1));
        $this->cycle_add_breadcrumb($step);
    }
    public function cycle_step_back() {
        $this->cycle_set_next_step($this->cycle_get_step());
        $this->cycle_set_step($this->cycle_get_prev_step());
        $this->cycle_set_prev_step($this->cycle_get_breadcrumb_by_idx($this->cycle_get_index()-1));
        $this->cycle_remove_breadcrumb($this->cycle_get_step());
    }

    /**
     * Cycle (traverse) through the tree; determine previous, current, and next 
     * "steps" based on navigation; update breadcrumbs and such as needed.
     *
     * @return self
     */
    public function cycle($node = array(), $level = 0) {
        if (!$node) $node = $this->get_tree();

        $breadcrumbs = $this->get_breadcrumbs();

        $level++;

        foreach ($node AS $key => $part) {

            if ($this->cycle['break']) { 
                if (!$this->cycle_is_counting()) { break; } // Full break.  
            }

            // Branch (Root)
            if (is_array($part)) {
                $branch_parent = $part;
                $this->cycle_branch($branch_parent['root']);

                // Loop through branch(s)
                $branch_is_complete = 0;
                foreach ($branch_parent['branches'] AS $branch_value => $branch_steps) {
                    // Branch found in form.
                    if ($this->in_form($this->cycle_get_branch(), $branch_value)) { 
                        $branch_is_complete = 1;
                        $this->cycle['branchtrail'][$this->cycle_get_branch()] = $branch_value;

                        $this->cycle['branchlevel']++;
                        $this->cycle($branch_steps, $level); 
                        $this->cycle['branchlevel']--; 
                    }
                }

                if (!$branch_is_complete) {
                    if ($branch_field_value = $this->get_form_field($this->cycle_get_branch())) { 
                        $this->cycle['branchtrail'][$this->cycle_get_branch()] = $branch_field_value;
                    }
                }
            }
            // Step
            else { 
                if ($this->cycle_is_counting()) $this->cycle_increase_step_count();

                /*
                Check $part directly (before increasing our cycle count) if breadcrumb doesn't exist, 
                because this is how we have the cycle to continue one step further than the current step, 
                in order to determin a "next_step"
                */
                if (!$this->breadcrumb_exists($part)) { 
                    if ($this->is_navigating_forward() && $this->cycle['navigated'] == 0) { 
                        /*
                        First let's check if we've finished stepping through a group of branches and that they are incomplete.
                        If neither of the branches (of the branch group (for the root step)) is found in the form, 
                        then we want to "break"
                        */
                        
                        if ($this->cycle_branch_is_incomplete()) { 
                            $this->cycle['break'] = 1;
                            if (!$this->cycle_is_counting()) { break; }
                        }


                        $this->add_breadcrumb($part);
                        $this->cycle['navigated'] = 1;
                    }
                    else if ($this->is_navigating_backward() && $this->cycle['navigated'] == 0) { 
                        $this->remove_breadcrumb($this->cycle_get_step());
                        $this->cycle['navigated'] = -1;
                    }
                    else {
                        /* 
                        Only need to update the cycle's next_step, because it was "?" before.
                        No need to increase cycle index/counter, because we don't treat this "next_step" as a counted step.
                        */
                        $this->cycle_set_next_step($part); 
                        $this->cycle['break'] = 1;
                        if (!$this->cycle_is_counting()) { break; }
                    }
                }

                /*
                Breadcrumb exists (assuming so) because otherwise we would "break" (above).
                But, we may have removed a breadcrumb and continued anyways due to navigation, 
                so let's check if we navigated.
                */
                // No forward navigation, or forward navigation.
                if ($this->cycle['navigated'] >= 0) { 
                    $this->cycle_increase_index();
                    $this->cycle_step($part);
                }
                // Backward navigation, so let's update cycle params, then "break" (because we break when a breadcrumb doesn't exist).
                else { 
                    $this->cycle_decrease_index();
                    $this->cycle_step_back(); // We don't pass $part here because we're on a "next_step" state right now, not "current_step"
                    $this->cycle['break'] = 1;
                    break;
                }

            }
        }

        // Allow the ability to go Back from Thank-you (last) page/step.
        if ($level == 1) { // Clean: Dirty because 1) Duplicate code here, and 2) relies on the fact that there is only one thank-you page and it must be at level 1.
            if ($this->is_navigating_backward() && $this->cycle['navigated'] == 0) { 
                $this->remove_breadcrumb($this->cycle_get_step());
                $this->cycle_decrease_index();
                $this->cycle_step_back(); // W
            }
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Moves to the next step of the wizard.
     *
     * @return self
     */
    public function next() {
        $this->update_form();
        $this->navigating_forward();
        $this->cycle_wizard();
        $this->save();
        return $this;
    }

    /**
     * Moves to the previous step of the wizard.
     *
     * @return self
     */
    public function prev() {
        $this->update_form();
        $this->navigating_backward();
        $this->cycle_wizard();
        $this->save();
        return $this;
    }

    // -------------------------------------------------------------------------

    /**
     * Updates the form (from $_POST).
     *
     * @return self
     */
    public function update_form() {
        $post = (isset($_POST) && isset($_POST[$this->get_alias()]) && $_POST[$this->get_alias()]) ? $_POST[$this->get_alias()] : array();
        if (!$post) return $this;
        foreach ($post AS $k => $v) { $this->set_form_field($k, $v); }
        return $this;
    }

    /**
     * Saves some session information (for the wizard).
     *
     * @return self
     */
    public function save() {
        return $this->set_session($this->get_alias(), array(
            'alias' => $this->get_alias(),
            'step' => $this->get_step(),
            'breadcrumbs' => $this->get_breadcrumbs(),
            'form' => $this->get_form()
        ));
    }

    /**
     * Resets the wizard.  
     *
     * @return self
     */
    public function reset() {
        return $this->reset_session();
    }

    // -------------------------------------------------------------------------

    /**
     * Proccess and return the generated markup(HTML) for the navigation.
     *
     * @return string
     */
    public function get_navigation_html() {
        $step = $this->get_step();
        $prev_step = $this->get_prev_step();
        $next_step = $this->get_next_step();

        $html = '';

        $navigation_path = $this->get_step_path().'/'.$this->navigation_file;
        $navigation_file_exists = @file_exists($navigation_path) ? true : false;

        if ($navigation_file_exists) ob_start(); 

        /* Short-cut helper variables (for using inside the navigation view).
         * These are optional, because you can use $this to call properties as 
         * well instead.
         *
         * Note: You'll notice we're also using these short-cut help variables 
         *       for our logic below (for when navigation file doesn't exist).
         */
        $alias = $this->get_alias();
        $show_prev_step = $this->show_prev_step;
        $show_next_step = $this->show_next_step;
        $prev_step_is_disabled = ($prev_step == $step) ? true : false;
        $next_step_is_disabled = ($next_step == $step) ? true : false;

        if (!$navigation_file_exists) {
            $html_prev = ($prev_step_is_disabled) 
            ? $this->disabled_prev_step_html 
            : $this->prev_step_html;

            $html_next = ($next_step_is_disabled) 
            ? $this->disabled_next_step_html 
            : $this->next_step_html;

            // $html_prev = str_replace('{prev}', 'wizard(\''.$alias.'\', \'prev\');', $html_prev);
            // $html_next = str_replace('{next}', 'wizard(\''.$alias.'\', \'next\');', $html_next);
            $html = '<div class="navigation">'
            .($show_prev_step ? $html_prev : '')
            .($show_next_step ? $html_next : '')
            .'</div>';
        }
        else { 
            include $navigation_path; 

            $html .= ob_get_contents();

            ob_end_clean(); 
        }

        return $html;
    }


    /**
     * Return the generated markup(HTML) for the navigation.
     *
     * @return string
     */
    public function render_navigation() { 
        return $this->get_navigation_html();
    }


    /**
     * Proccess and return the generated markup(HTML) for the wizard.
     *
     * @return string
     */
    public function get_html() {
        if ($this->step_by_step) {
            ob_start(); 
            include $this->get_step_path().'/'.$this->get_step_file(); 
            $html = '';
            $html .= '<div id="'.$this->get_alias().'_'.$this->get_step().'" class="step '.$this->get_step().'">';
            $html .= ob_get_contents();
            if ($this->embed_navigation) { 
                $html .= $this->get_navigation_html(); 
                $html .= '</div>';
            }
            else { 
                $html .= '</div>';
                $html .= $this->get_navigation_html(); 
            }
            ob_end_clean(); 
            if ($this->show_progress) $html .= '<input type="hidden" id="'.$this->get_form_field_id('progress').'" value="'.$this->get_progress().'" />';
            if ($this->show_tracking) $html .= '<input type="hidden" id="'.$this->get_form_field_id('tracking').'" value="'.$this->get_step().'" />';
            return $html;
        }
        else { 
            die('ERROR: No function to cycle through wizard tree and render all steps.');
        }
    }

    /**
     * Return the generated markup(HTML) for the wizard.
     *
     * @return string
     */
    public function render() { 
        return $this->get_html();
    }

    /**
     * Proccess and return the generated markup(HTML) for a hidden form (of all fields).
     *
     * @return string
     */
    public function get_hidden_form_html() { 
        $html = '';
        $form = $this->get_form();
        foreach ($form AS $k => $v) {
            $html .= '<input type="hidden" id="'.$this->get_alias().'_'.$k.'" name="'.$k.'" value="'.$v.'" />'."\n";
        }
        return $html;
    }

    /**
     * Return the generated markup(HTML) for hidden form.
     *
     * @return string
     */
    public function render_hidden_form() { 
        return $this->get_hidden_form_html(); 
    }

    // -------------------------------------------------------------------------

    /**
     * Print out some debug information.
     *
     * @return string
     */
    public function debug() {
        return'<div id="debug">Debug information:<br><pre>'.
            'VERSION = '.Wizardacious::VERSION."\n".
            'prev_step = '.$this->get_prev_step()."\n" . 
            'step = '.$this->get_step()."\n" . 
            'next_step = '.$this->get_next_step()."\n" . 
            'step_count = '.$this->get_step_count()."\n" .
            'step_idx = '.$this->get_step_idx()."\n" . 
            'cycle = '.print_r($this->cycle, true)."\n" .
            'breadcrumbs = '.print_r($this->breadcrumbs, true)."\n" .
            'form = '.print_r($this->form, true)."\n" .
            '$_POST = '.print_r($_POST, true)."\n" .
            '$_SESSION = '.print_r($_SESSION, true)."\n" .
            '</pre></div>';
    }
}
