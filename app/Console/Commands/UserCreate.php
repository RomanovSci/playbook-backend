<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

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
        /**
         * @var User $user
         */
        $user = new User();
        $user->first_name = $this->ask('First name');
        $user->last_name = $this->ask('Last name');
        $user->phone = $this->ask('Phone');
        $user->password = bcrypt($this->ask('Password'));
        $user->save();
        $user->assignRole($this->choice('Role', [
            User::ROLE_USER,
            User::ROLE_COACH,
            User::ROLE_PLAYGROUND_ADMIN,
            User::ROLE_ADMIN,
        ]));

        $this->info('Success!');
        return true;
    }
}
