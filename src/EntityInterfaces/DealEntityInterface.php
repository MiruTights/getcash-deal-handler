<?php

namespace Src\EntityInterfaces;
interface DealEntityInterface
{
    public function setStatus(string $status):void;

    public function setQuantity(int $quantity):void;

    public function setLocation(string $location):void;

    public function setName(string $name):void;

    public function setContact(string $contact):void;

    public function setType(string $type):void;

    public function addContactServiceMessage():void;

    public function setEmail(string $email):void;

    public function setClosingReason(string $reason):void;

}