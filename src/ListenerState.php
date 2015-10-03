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


final class ListenerState
{
    /**
     * @var callable
     */
    private $listener;

    /**
     * @var string[]
     */
    private $attachedToEvents;

    /**
     * @var Event[]
     */
    private $dispatchedEvents = [];

    /**
     * @var int
     */
    private $state;

    /**
     * @var bool
     */
    private $purge;

    /**
     * @param callable $listener
     * @param int $state
     * @param string[] $attachedToEvents
     */
    public function __construct($listener, $state, ...$attachedToEvents)
    {
        $this->listener = $listener;
        $this->purge = true;
        if ($state >= EventDispatcher::NO_PURGE) {
            $this->purge = false;
            $state -= EventDispatcher::NO_PURGE;
        }
        $this->state = $state;
        $this->attachedToEvents = $attachedToEvents;
    }

    /**
     * @return callable
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return string[]
     */
    public function getAttachedToEvents()
    {
        return $this->attachedToEvents;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * @return Event[]
     */
    public function getDispatchedEvents()
    {
        return $this->dispatchedEvents;
    }

    /**
     * @param string $eventName
     * @param Event $dispatchedEvent
     * @return self
     */
    public function addDispatchedEvent($eventName, Event $dispatchedEvent)
    {
        if ($this->state === EventDispatcher::USE_ALL) {
            $this->dispatchedEvents[$eventName][] = $dispatchedEvent;
        } elseif ($this->state === EventDispatcher::USE_LAST) {
            $this->dispatchedEvents[$eventName] = $dispatchedEvent;
        } elseif ($this->state === EventDispatcher::USE_FIRST && !isset($this->dispatchedEvents[$eventName])) {
            $this->dispatchedEvents[$eventName] = $dispatchedEvent;
        }

        $this->dispatch();

        return $this;
    }

    /**
     * Try to dispatch the event and clear the event cache
     *
     * @return void
     */
    public function dispatch()
    {
        if ($this->isDispatchable()) {

            $sorted = [];

            foreach($this->attachedToEvents as $eventName)
            {
                $sorted[$eventName] = $this->dispatchedEvents[$eventName];
            }

            call_user_func($this->listener, $sorted);
            if ($this->purge) {
                $this->dispatchedEvents = [];
            }
        }
    }

    /**
     * @return bool
     */
    public function isDispatchable()
    {
        foreach ($this->attachedToEvents as $eventName) {
            if (!isset($this->dispatchedEvents[$eventName])) {
                return false;
            }
        }

        return true;
    }


}