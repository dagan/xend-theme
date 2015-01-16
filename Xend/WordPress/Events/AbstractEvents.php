<?php

namespace Xend\WordPress\Events;

use Xend\WordPress\Events\Exception;

/**
 * Default Events
 *
 * @author Dagan
 */
abstract class AbstractEvents implements \Xend\WordPress\Events\EventsInterface
{
    /**
     * @var array
     */
    protected $_action_hooks = array();

    /**
     * @var array
     */
    protected $_filter_hooks = array();

    public function __call($function, $args)
    {
        // Execute Actions & Filters
        if ('do_' == substr($function, 0, 3)) {
            return $this->_do(substr($function, 3), $args);
        } elseif ('filter_' == substr($function, 0, 7)) {
            return $this->_filter(substr($function, 7), $args);
        }
    }

    /**
     * Registers a Callable to Be Executed When an Action Occurs
     *
     * This method utilizes Xend\WordPress::addAction() to add a hook for each registered
     * action. The first time a hook is registered, addAction() registers itself with
     * Xend\WordPress::addAction() for the same hook with a priority of either 0 or 100,
     * determined by whether the declared priority is less than 100.
     *
     * When an action occurs, Xend\WordPress\EventManager processes it's registered
     * callables twice: The first execution occurs at the start of the action, at which time
     * all callables with a priority less than 100 are processed in order of priority. The
     * second execution occurs after all other registered callables have been executed by
     * WordPress, at which time all callables with a priority greather than or equal to
     * 100 are called in order of priority.
     *
     * NOTE: Xend\WordPress\EventManager asks WordPress to call it at the start and end of
     * each action by registering itself twice, once with a priority of 0 and once with a
     * priority of 100. Depending on the order execution of any plugins, these priorities
     * may or may not ensure that execution occurs at the true beginning and end. However,
     * execution should always occur very near the beginning and very near the end.
     *
     * @param string $action The action to hook to
     * @param callable $callable The callable to execute
     * @param int $priority The priority of execution. Lower numbers are executed earlier.
     * @return \Xend\WordPress\Events
     * @throws \Xend\WordPress\Events\Exception
     */
    public function addAction($action, $callable, $priority = 10)
    {
        if (!function_exists('add_action'))
            throw new Exception('The WordPress function add_action() is not defined.');
        
        if (!is_callable($callable))
                throw new Exception('The second argument passed to addAction() must be a callable.');

        // Force priorities >= 100 to run after all others, including non-Xend functions
        if ($priority < 100) {
            $_priority = 0;
            $_action = $action;
        } else {
            $_priority = 100;
            $_action = "final_xend_$action";
        }

        // If a hook hasn't been set for the action yet, set one
        if (!array_key_exists($_action, $this->_action_hooks)) {
            $this->_action_hooks[$action] = array();
            add_action($action, array($this, "do_$_action"), $_priority, 10);
        }

        $this->_action_hooks[$_action][$priority][] = $callable;

        return $this;
    }
    
    public function doAction($action, array $args = array()) {
        
        if (!function_exists('do_action')) {
            throw new Exception("The native WordPress function do_action() is not defined");
        }
        
        call_user_func_array('do_action', array_merge(array($action), $args));
    }

    protected function _do($action, $args)
    {
        if (array_key_exists($action, $this->_action_hooks)) {
            $priorities = array_keys($this->_action_hooks[$action]);
            sort($priorities);
            foreach ($priorities as $priority) {
                foreach($this->_action_hooks[$action][$priority] as $callable) {
                    call_user_func_array($callable, $args);
                }
            }
        }
    }

    /**
     * Registers a Callable to Filter a WordPress Value
     *
     * This method utilizes Xend\WordPress::addFilter() to add a hook for each registered
     * filter. The first time a hook is registered, addFilter() registers itself with
     * Xend\WordPress::addFilter() for the same hook with a priority of either 0 or 100,
     * determined by whether the declared priority is less than 100.
     *
     * When a filter request occurs, Xend\WordPress\EventManager processes it's registered
     * callables twice: The first execution occurs at the start of the request, at which time
     * all callables with a priority less than 100 are processed in order of priority. The
     * second execution occurs after all other registered callables have been executed by
     * WordPress, at which time all callables with a priority greather than or equal to
     * 100 are called in order of priority.
     *
     * NOTE: Xend\WordPress\EventManager asks WordPress to call it at the start and end of
     * each filter request by registering itself twice, once with a priority of 0 and once
     * with a priority of 100. Depending on the order execution of any plugins, these priorities
     * may or may not ensure that execution occurs at the true beginning and end. However,
     * execution should always occur very near the beginning and very near the end.
     *
     * @param string $filter The filter to hook to
     * @param callable $callable The callable to register
     * @param int $priority The priority of execution. Lower numbers are executed earlier
     * @return \Xend\WordPress\Events
     * @throws \Xend\WordPress\Events\Exception
     */
    public function addFilter($filter, $callable, $priority = 10)
    {
        if (!function_exists('add_filter'))
            throw new Exception('The WordPress function add_filter() is not defined.');
        
        if (!is_callable($callable))
                throw new Exception('The second argument passed to addAction() must be a callable.');

        // Force priorities >= 100 to run after all others, including non-Xend functions
        if ($priority < 100) {
            $_priority = 0;
            $_filter = $filter;
        } else {
            $_priority = 100;
            $_filter = "final_xend_$filter";
        }

        // If a hook hasn't been set for the action yet, set one
        if (!array_key_exists($_filter, $this->_filter_hooks)) {
            $this->_filter_hooks[$filter] = array();
            add_filter($filter, array($this, "filter_$_filter"), $_priority, 10);
        }

        $this->_filter_hooks[$_filter][$priority][] = $callable;

        return $this;
    }
    
    public function applyFilters($filter, $value) {
        
        if (!function_exists('apply_filters')) {
            throw new Exception("The native WordPress function apply_filters() is not defined");
        }
        
        return apply_filters($filter, $value);
    }

    protected function _filter($filter, $args)
    {
        if (array_key_exists($filter, $this->_filter_hooks)) {
            foreach(sort(array_keys($this->_filter_hooks)) as $priority) {
                foreach($this->_filter_hooks[$filter][$priority] as $callable) {
                    $args[0] = call_user_func_array($callable, $args);
                }
            }
        }

        return $args[0];
    }
}
