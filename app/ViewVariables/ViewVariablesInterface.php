<?php

namespace App\ViewVariables;

interface ViewVariablesInterface
{
    public function getName(): string;
    public function getValue(): array;
}