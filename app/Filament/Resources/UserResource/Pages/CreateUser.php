<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $isAdmin = (bool) ($data['is_admin'] ?? false);
        unset($data['is_admin']);

        /** @var User $user */
        $user = static::getModel()::make($data);
        $user->is_admin = $isAdmin;
        $user->save();

        SecurityAuditLog::record(
            eventType: 'user.created',
            severity: $isAdmin ? 'warning' : 'info',
            context: [
                'created_user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'created_by' => auth()->id(),
            ]
        );

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
