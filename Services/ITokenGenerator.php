<?php

namespace Services;

interface ITokenGenerator
{
    public function generateToken(int $length): string;
}