<?php

namespace Xend\WordPress\Events;

/**
 * Events Interface
 *
 * @author Dagan
 */
interface EventsInterface {

    /**
     * Registers a Callable to Be Executed When a WordPress Action Occurs
     *
     * @param string $action The action to hook to
     * @param callable $callable The callable to execute
     * @param int $priority The priority of execution. Lower numbers are executed earlier.
     * @return \Xend\WordPress\Events
     * @throws \Xend\WordPress\Events\Exception
     */
    public function addAction($action, $callable, $priority = 10);
    
    /**
     * Trigger a WordPress Action
     * @param string $action
     * @param array  $args
     */
    public function doAction($actions, array $args = array());

    /**
     * Registers a Callable to Filter a WordPress Value
     *
     * @param string $filter The filter to hook to
     * @param callable $callable The callable to register
     * @param int $priority The priority of execution. Lower numbers are executed earlier
     * @return \Xend\WordPress\Events
     * @throws \Xend\WordPress\Events\Exception
     */
    public function addFilter($filter, $callable, $priority = 10);
    
    /**
     * Apply a WordPress Filter
     * @param string $filter
     * @param mixed  $value
     * @return mixed
     */
    public function applyFilters($filter, $value);
}
