<?php
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


final class EventDispatcherImpl implements EventDispatcher
{

    /**
     * Listeners with one or multiple events attached stores by eventName
     *
     * @var ListenerState[][]
     */
    private $listenerStates = [];

    /**
     * @var callable[][]
     */
    private $combinationListeners = [];

    /**
     * Dispatches an event to all registered listeners.
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
    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        if (!isset($this->listenerStates[$eventName])) {
            return $event;
        }

        /** @var ListenerState[] $listenerStates */
        foreach($this->listenerStates[$eventName] as $listenerState)
        {
            $listenerState->addDispatchedEvent($eventName, $event);
        }

        return $event;
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param callable $listener The listener
     *
     * @param int $useEvent Events which should be passed to the listener
     *                            USE_ALL will dispatch all matching event objects
     *                            USE_FIRST will dispatch the first matching event object
     *                            USE_LAST will dispatch the last matching event object
     *                            e.g. The listener will listen to the event combination
     *                            [ foo, bar, baz ].
     *
     * @param string[] $eventNames The events to listen on. You can pass multiple events which
     *                            means that this listener will only trigger if all 3 events have
     *                            been fired at least once.
     *
     * @api
     */
    public function addListener($listener, $useEvent = self::USE_ALL, ...$eventNames)
    {
        if (empty($eventNames)) return;

        // create ListenerState
        $listenerState = new ListenerState($listener, $useEvent, ...$eventNames);

        foreach($eventNames as $eventName)
        {
            if (!isset($this->listenerStates[$eventName])) {
                $this->listenerStates[$eventName] = [];
            }
            array_push($this->listenerStates[$eventName], $listenerState);
        }

        $key = implode('|', $eventNames);

        if (!isset($this->combinationListeners[$key])) {
            $this->combinationListeners[$key] = [];
        }
        array_push($this->combinationListeners[$key], $listener);
    }

    /**
     * Gets the listeners of a specific event
     *
     * @param string[] $eventNames The name of the events
     *
     * @return array The event listeners for the specified event combination
     */
    public function getListeners(...$eventNames)
    {
        if (empty($eventNames)) return [];

        $key = implode('|', $eventNames);
        return $this->combinationListeners[$key];
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string[] $eventNames The name of the events
     *
     * @return bool true if the specified event combination has any listeners, false otherwise
     */
    public function hasListeners(...$eventNames)
    {
        if (empty($eventNames)) return false;

        $key = implode('|', $eventNames);
        return isset($this->combinationListeners[$key]);
    }
}