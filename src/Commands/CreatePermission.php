<?php


namespace Benwilkins\Authorizer\Commands;


use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Illuminate\Console\Command;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizer:permission
        {handle? : The handle for the permission} 
        {displayName? : The display name of the permission} 
        {guard? : The name of the guard (blank for default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new permission';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $class = Authorizer::getClass('permission');
        $handle = $this->argument('handle') ?: $this->ask('Handle');
        $name = $this->argument('displayName') ?: $this->ask('Display Name');
        $guard = $this->argument('guard') ?: $this->ask('Guard (blank for default)', config('authorizer.default_guard'));

        if (!$handle || !$name) {
            $this->error('Both the handle and the name are required to create a role.');
            return;
        }

        $permission = $class::create([
            'handle' => $handle,
            'display_name' => $name,
            'guard' => $guard
        ]);

        $this->info("Permission `{$permission->handle}` created.");
    }
}