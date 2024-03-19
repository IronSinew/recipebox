<?php

namespace App\Console\Commands;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Console\Command;

/** @codeCoverageIgnore  */
class AddAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('What is the name of the user?');
        $email = $this->ask('What is the email for the user?');
        $password = $this->secret('What is the password?');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role' => UserRoleEnum::ADMIN,
        ]);

        if (! $user->wasRecentlyCreated) {
            $this->error("Uh oh! Couldn't create the user");

            return;
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $this->info("{$user->name} has been created!");
    }
}
