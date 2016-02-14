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
 * @date       14.02.16
 */

namespace AValnar\EventDispatcher;


final class SubscriberConfiguration
{
    /**
     * @var array
     */
    private $events;

    /**
     * @var int
     */
    private $useType;

    /**
     * @var int
     */
    private $weight;

    /**
     * SubscriberConfiguration constructor.
     * @param array $events
     * @param int $useType
     * @param int $weight
     */
    public function __construct(array $events, int $useType, int $weight)
    {
        $this->events = $events;
        $this->useType = $useType;
        $this->weight = $weight;
    }

    /**
     * @return array
     */
    public function getEvents() : array
    {
        return $this->events;
    }

    /**
     * @return int
     */
    public function getUseType() : int
    {
        return $this->useType;
    }

    /**
     * @return int
     */
    public function getWeight() : int
    {
        return $this->weight;
    }


}