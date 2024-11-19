<?php

namespace Guilds;

use Kascat\EasyModule\Core\Request;

/**
 * Class GuildRequest
 */
class GuildRequest extends Request
{
    public function validateToIndex(): array
    {
        return [
            // Allowed properties to filter, ex:
            // Guild::NAME => 'string',
        ];
    }

    public function validateToStore(): array
    {
        return [
            // Property rules to store, ex:
            // Guild::NAME => 'required|string',
        ];
    }

    public function validateToUpdate(): array
    {
        return [
            // Property rules to update, ex:
            // Guild::NAME => 'string',
        ];
    }
}
