<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the admin role to a user';

    /**
     * Execute the console command.
     */
    //
    public function handle()
    {
        $this->info("Available Users:");
        $users = User::all(['id', 'name', 'email']);

        if ($users->isEmpty()) {
            $this->error('No users found.');
            return 1;
        }

        // Display user table
        $this->table(['ID', 'Name', 'Email'], $users->toArray());

        // Ask for user ID
        $userId = $this->ask('Enter the ID of the user to make admin');

        $user = User::find($userId);

        if (! $user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        // Create the role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Assign the role
        $user->assignRole($role);

        $this->info("User {$user->name} has been assigned the 'admin' role.");
        return 0;
    }
}
