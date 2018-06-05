<?php


namespace Benwilkins\Authorizer\Commands;


use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Illuminate\Console\Command;

class CreateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizer:role
        {handle? : The handle for the role} 
        {displayName? : The display name of the role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new role';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $class = Authorizer::getClass('role');
        $handle = $this->argument('handle') ?: $this->ask('Handle');
        $name = $this->argument('displayName') ?: $this->ask('Display Name');

        if (!$handle || !$name) {
            $this->error('Both the handle and the name are required to create a role.');
            return;
        }

        $role = $class::create([
            'handle' => $handle,
            'display_name' => $name,
        ]);

        $this->info("Role `{$role->handle}` created.");
    }
}