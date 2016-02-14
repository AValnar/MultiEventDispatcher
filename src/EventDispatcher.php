<?php
declare(strict_types = 1);

/**
 * Copyright (C) 2015  Alexander Schmidt
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * @author     Alexander Schmidt <mail@story75.com>
 * @copyright  Copyright (c) 2015, Alexander Schmidt
 * @date       24.09.2015
 */

namespace AValnar\EventDispatcher;


interface EventDispatcher
{
    const USE_ALL = 0;
    const USE_FIRST = 1;
    const USE_LAST = 2;

    const NO_PURGE = 4;


    /**
     * Dispatches an event to all registered listeners. A listener will receive an array
     * of events indexed by their event name.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event $event The event to pass to the event handlers/listeners.
     *                          If not supplied, an empty Event instance is created.
     *
     * @return Event
     *
     * @api
     */
    public function dispatch(string $eventName, Event $event = null) : Event;

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param callable $listener The listener
     *
     * @param int $useEvent Events which should be passed to the listener
     *                            USE_ALL will dispatch all matching event objects
     *                            USE_FIRST will dispatch the first matching event object
     *                            USE_LAST will dispatch the last matching event object
     *                            You can use any of the above in combination with NO_PURGE
     *                            to disable the automatic event clear in a ListenerState
     *
     * @param string[] $eventNames The events to listen on. You can pass multiple events which
     *                            means that this listener will only trigger if all 3 events have
     *                            been fired at least once.
     *
     * @api
     * @deprecated Will be replaced in 2.0 in by addWeightedListener
     */
    public function addListener(
        callable $listener,
        int $useEvent = self::USE_ALL,
        string ...$eventNames
    );

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param callable $listener The listener
     *
     * @param int $useEvent         USE_ALL will dispatch all matching event objects
     *                              USE_FIRST will dispatch the first matching event object
     *                              USE_LAST will dispatch the last matching event object
     *                              You can use any of the above in combination with NO_PURGE
     *                              to disable the automatic event clear in a ListenerState
     *
     * @param int $weight           Set a weight for this listener, lighter ones will be called first
     *
     * @param string[] $eventNames  The events to listen on. You can pass multiple events which
     *                              means that this listener will only trigger if all 3 events have
     *                              been fired at least once.
     *
     * @api
     */
    public function addWeightedListener(
        callable $listener,
        int $useEvent = self::USE_LAST,
        int $weight = 100,
        string ...$eventNames
    );

    /**
     * Adds an event subscriber that listens on the specified events.
     * Internally this will convert all handlers to listeners.
     *
     * @param EventSubscriber $eventSubscriber The subscriber
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function addSubscriber(EventSubscriber $eventSubscriber);

    /**
     * Gets the listeners of a specific event
     *
     * @param string[] $eventNames The name of the events
     *
     * @return array The event listeners for the specified event combination
     */
    public function getListeners(string ...$eventNames) : array;

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string[] $eventNames The name of the events
     *
     * @return bool true if the specified event combination has any listeners, false otherwise
     */
    public function hasListeners(string ...$eventNames) : bool;
}