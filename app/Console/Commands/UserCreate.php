<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Class UserCreate
 * @package App\Console\Commands
 */
class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            'first_name' => $this->ask('First name'),
            'last_name' => $this->ask('Last name'),
            'phone' => $this->ask('Phone'),
            'password' => bcrypt($this->ask('Password')),
            'role' => $this->choice('Role', [
                User::ROLE_USER,
                User::ROLE_TRAINER,
                User::ROLE_ORGANIZATION_ADMIN,
                User::ROLE_ADMIN,
            ]),
        ];

        /**
         * @var User $user
         */
        $user = User::create($data);
        $user->assignRole($data['role']);

        $this->info('Success!');
        return true;
    }
}
