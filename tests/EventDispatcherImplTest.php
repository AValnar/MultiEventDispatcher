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
use AValnar\EventDispatcher\EventDispatcherImpl;
use AValnar\EventDispatcher\EventSubscriber;

class EventDispatcherImplTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcherImpl
     */
    private $sut;

    private $cakeEventData;

    public function setUp()
    {
        $this->sut = new EventDispatcherImpl();
    }

    public function testDispatchEmpty()
    {
        $this->assertInstanceOf(Event::class, $this->sut->dispatch('cakeEvent'));
    }

    public function testAddListener()
    {
        $listener = [$this, 'onCake'];
        $this->sut->addListener($listener, EventDispatcher::USE_ALL, 'cakeEvent');

        $this->assertEquals(true, $this->sut->hasListeners('cakeEvent'));
        $this->assertEquals([$listener], $this->sut->getListeners('cakeEvent'));

        $listenerTwo = [$this, 'onCakeTwo'];
        $this->sut->addListener($listenerTwo, EventDispatcher::USE_ALL, 'cakeEvent');

        $this->assertEquals(true, $this->sut->hasListeners('cakeEvent'));
        $this->assertEquals([$listener, $listenerTwo], $this->sut->getListeners('cakeEvent'));
    }

    /**
     * @depends testAddListener
     */
    public function testDispatch()
    {
        $listener = [$this, 'onCake'];
        $event = new Event();
        $this->sut->addListener($listener, EventDispatcher::USE_ALL, 'cakeEvent');
        $this->assertSame($event, $this->sut->dispatch('cakeEvent', $event));
        $this->assertSame(['cakeEvent' => [$event]], $this->cakeEventData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidSubscriberWrongFormat()
    {
        $subscriber = new class implements EventSubscriber
        {
            public function getEvents() : array
            {
                return ['onFoo' => 'fooEvent'];
            }
        };
        $this->sut->addSubscriber($subscriber);
    }

    public function testAddSubscriber()
    {
        $subscriber = new class implements EventSubscriber
        {
            public function getEvents() : array
            {
                return [
                    'onCakeOverflow' => [['cakeEvent']],
                    'onPartyEvent' => [['partyEvent']],
                    'onMultiEventEvent' => [['orderEvent', 'orderMailedEvent'], EventDispatcher::USE_LAST]
                ];
            }
        };
        $this->assertEquals(false, $this->sut->hasListeners('cakeEvent'));
        $this->sut->addSubscriber($subscriber);
        $this->assertEquals(true, $this->sut->hasListeners('cakeEvent'));
    }

    /** Helper event listeners */

    /**
     * @param array $events
     */
    public function onCake(array $events)
    {
        $this->cakeEventData = $events;
    }

    /**
     * @param array $events
     */
    public function onCakeTwo(array $events)
    {

    }

}
