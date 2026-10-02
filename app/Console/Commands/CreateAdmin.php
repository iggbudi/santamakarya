<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Membuat akun admin melalui input interaktif tanpa password di argumen command.';

    public function handle(): int
    {
        $values = [
            'name' => $this->ask('Nama admin'),
            'email' => $this->ask('Email admin'),
            'password' => $this->secret('Password (minimal 12 karakter)'),
        ];
        $validator = Validator::make($values, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }
        $admin = new User($values);
        $admin->forceFill(['is_admin' => true])->save();
        $this->info('Akun admin berhasil dibuat. Login melalui /admin.');

        return self::SUCCESS;
    }
}
