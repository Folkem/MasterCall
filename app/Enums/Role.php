<?php

namespace App\Enums;

enum Role: string
{
    case Client = 'client';
    case Master = 'master';
    case Admin = 'admin';
}
