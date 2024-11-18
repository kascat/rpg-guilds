<?php

namespace Users\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case API = 'api';
    case ACCOUNT_OWNER = 'account_owner';
}
