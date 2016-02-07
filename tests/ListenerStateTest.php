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
 * @copyright  Copyright (c) 2016, Alexander Schmidt
 * @date       07.02.16
 */

namespace AValnar\Tests\EventDispatcher;


use AValnar\EventDispatcher\Event;
use AValnar\EventDispatcher\EventDispatcher;
use AValnar\EventDispatcher\ListenerState;

class ListenerStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ListenerState
     */
    private $sut;

    private $listener;

    private $eventType = EventDispatcher::USE_ALL;

    private $events = ['partyEvent', 'cakeEvent'];

    private $listenerShouldBeCalled = false;
    private $listenerWasCalledWith;

    public function setUp()
    {
        $this->listener = [$this, 'onMultiEvent'];
        $this->sut = new ListenerState($this->listener, $this->eventType, ...$this->events);
    }

    public function testGetListener()
    {
        $this->assertEquals($this->listener, $this->sut->getListener(), 'Listeners do not Match!');
    }

    public function testGetAttachedToEvents()
    {
        $this->assertEquals($this->events, $this->sut->getAttachedToEvents(), 'Events do not Match!');
    }

    public function testGetState()
    {
        $this->assertEquals($this->eventType, $this->sut->getState(), 'Event state does not match!');
    }

    public function testDispatch()
    {
        $this->listenerShouldBeCalled = false;
        $eventBag = [
            'work' => new Event(),
            'cake' => new Event(),
            'cake2' => new Event(),
            'party' => new Event()
        ];

        $assertEvent = [];
        $this->sut->addDispatchedEvent('workEvent', $eventBag['work']);
        $this->assertContainsEvents($assertEvent);
        $this->assertNotDispatchable();

        $assertEvent = [
            'cakeEvent' => [$eventBag['cake']]
        ];
        $this->sut->addDispatchedEvent('cakeEvent', $eventBag['cake']);
        $this->assertContainsEvents($assertEvent);
        $this->assertNotDispatchable();

        $assertEvent = [
            'cakeEvent' => [$eventBag['cake'], $eventBag['cake2']]
        ];
        $this->sut->addDispatchedEvent('cakeEvent', $eventBag['cake2']);
        $this->assertContainsEvents($assertEvent);
        $this->assertNotDispatchable();

        $assertEvent = [
            'partyEvent' => [$eventBag['party']],
            'cakeEvent' => [$eventBag['cake'], $eventBag['cake2']]
        ];
        $this->listenerShouldBeCalled = true;
        $this->sut->addDispatchedEvent('partyEvent', $eventBag['party']);
        $this->assertContainsEvents([]);
        $this->assertNotDispatchable();
        $this->assertSame(
            $assertEvent,
            $this->listenerWasCalledWith,
            'Dispatched events do not match!'
        );
    }

    public function testKeepFirst()
    {
        $this->sut = new ListenerState($this->listener, EventDispatcher::USE_FIRST, ...$this->events);

        $eventOne = new Event();
        $eventTwo = new Event();

        $this->sut->addDispatchedEvent('partyEvent', $eventOne);
        $this->assertContainsEvents(['partyEvent' => $eventOne]);
        $this->sut->addDispatchedEvent('partyEvent', $eventTwo);
        $this->assertContainsEvents(['partyEvent' => $eventOne]);
    }

    public function testKeepLast()
    {
        $this->sut = new ListenerState($this->listener, EventDispatcher::USE_LAST, ...$this->events);

        $eventOne = new Event();
        $eventTwo = new Event();

        $this->sut->addDispatchedEvent('partyEvent', $eventOne);
        $this->assertContainsEvents(['partyEvent' => $eventOne]);
        $this->sut->addDispatchedEvent('partyEvent', $eventTwo);
        $this->assertContainsEvents(['partyEvent' => $eventTwo]);
        $this->sut->addDispatchedEvent('partyEvent', $eventOne);
        $this->assertContainsEvents(['partyEvent' => $eventOne]);
    }

    public function testNoPurge()
    {
        $this->sut = new ListenerState($this->listener, $this->eventType + EventDispatcher::NO_PURGE, ...$this->events);
        $this->listenerShouldBeCalled = true;
        $this->sut->addDispatchedEvent('partyEvent', new Event())
            ->addDispatchedEvent('cakeEvent', new Event());
        $this->assertNotEquals(
            [],
            $this->sut->getDispatchedEvents(),
            'Events should have not been purged!'
        );
        $this->assertEquals(
            true,
            $this->sut->isDispatchable(),
            'Listener should be dispatchable!'
        );
    }

    public function onMultiEvent(array $events)
    {
        if (!$this->listenerShouldBeCalled) {
            $this->fail('Event listener should have not been called!');
        } else {
            $this->listenerWasCalledWith = $events;
        }
    }

    /** Helper assertions */

    private function assertNotDispatchable()
    {
        $this->assertEquals(
            false,
            $this->sut->isDispatchable(),
            'Listener should not be dispatchable!'
        );
    }

    private function assertContainsEvents($events)
    {
        $this->assertEquals(
            $events,
            $this->sut->getDispatchedEvents(),
            'Fetched events do not match!'
        );
    }
}
