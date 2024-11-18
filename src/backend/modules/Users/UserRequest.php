<?php

namespace Users;

use App\Rules\CnpjRule;
use App\Utils\Formatter;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Illuminate\Validation\Rules\Password;
use Permissions\Permission;
use SalesPoints\SalesPoint;
use StorePlans\Enums\PlanOptionsEnum;
use StorePlans\StorePlan;
use Stores\Store;
use Users\Enums\UserRoleEnum;

class UserRequest extends Request
{
    public function validateToRegister()
    {
        return [
            User::NAME => ['string', 'required'],
            User::EMAIL => [
                'string',
                'required',
                Rule::unique(User::TABLE, User::EMAIL),
            ],
            User::PASSWORD => [
                'required',
                'string',
                Password::min(6),
                'confirmed'
            ],
        ];
    }

    public function validateToLogin()
    {
        return [
            User::EMAIL => 'required',
            User::PASSWORD => 'required',
        ];
    }

    public function validateToIndex()
    {
        return [
            User::NAME => 'string|nullable',
            User::EMAIL => 'string|nullable',
            User::ROLE => 'string|nullable',
            User::PERMISSION_ID => 'int|nullable',
        ];
    }

    public function validateToStore()
    {
        return [
            User::EMAIL => [
                'required',
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL),
            ],
            User::NAME => 'required',
            User::ROLE => [
                'required',
                Rule::enum(UserRoleEnum::class),
            ],
            User::PASSWORD => 'nullable|string|min:6',
            User::LOGIN_TIME => 'nullable|integer',
            User::EXPIRES_IN => 'nullable|date_format:d/m/Y H:i',
            User::PERMISSION_ID => ['required', 'int', Rule::exists(Permission::TABLE, Permission::ID)],
            User::STORE_ID => ['nullable', 'int', Rule::exists(Store::TABLE, Store::ID)],
            User::SALES_POINT_ID => ['nullable', 'int', Rule::exists(SalesPoint::TABLE, SalesPoint::ID)],
        ];
    }

    public function validateToUpdate()
    {
        $pendingPassword = User::STATUS_PENDING_PASSWORD;
        $active = User::STATUS_ACTIVE;
        $blocked = User::STATUS_BLOCKED;
        $blockedByTime = User::STATUS_BLOCKED_BY_TIME;

        return [
            User::EMAIL => [
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL)->ignore($this->route('user')),
            ],
            User::NAME => '',
            User::ROLE => [Rule::enum(UserRoleEnum::class)],
            User::STATUS => "in:$pendingPassword,$active,$blocked,$blockedByTime",
            User::PASSWORD => 'nullable|string|min:6',
            User::LOGIN_TIME => 'nullable|integer',
            User::EXPIRES_IN => 'nullable|date_format:d/m/Y H:i',
            User::PERMISSION_ID => ['int', Rule::exists(Permission::TABLE, Permission::ID)],
            User::STORE_ID => ['nullable', 'int', Rule::exists(Store::TABLE, Store::ID)],
            User::SALES_POINT_ID => ['nullable', 'int', Rule::exists(SalesPoint::TABLE, SalesPoint::ID)],
        ];
    }

    public function validateToForgotPassword()
    {
        return [
            User::EMAIL => 'required|email',
        ];
    }

    public function validateToResetPassword()
    {
        return [
            User::PASSWORD => [
                'required',
                'string',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed'
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        // if ($this->input(User::CPF)) {
        //     $data[User::CPF] = Formatter::onlyNumbers($this->input(User::CPF));
        // }

        $this->merge($data);
    }
}
