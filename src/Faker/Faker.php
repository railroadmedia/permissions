<?php

namespace Railroad\Permissions\Faker;

use Faker\Generator;
use Illuminate\Support\Facades\Hash;

class Faker extends Generator
{
    public function userAbility(array $override = [])
    {
        return array_merge(
            [
                'user_id' => rand(),
                'ability' => $this->word . '-' . $this->word,
                'created_at' => $this->dateTimeThisCentury(),
                'updated_at' => $this->dateTimeThisCentury(),
            ],
            $override
        );
    }

    public function userRole(array $override = [])
    {
        return array_merge(
            [
                'user_id' => rand(),
                'role' => $this->word . '-' . $this->word,
                'created_at' => $this->dateTimeThisCentury(),
                'updated_at' => $this->dateTimeThisCentury(),
            ],
            $override
        );
    }
}