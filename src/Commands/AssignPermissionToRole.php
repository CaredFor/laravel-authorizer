<?php


namespace Benwilkins\Authorizer\Commands;


use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Illuminate\Console\Command;

class AssignPermissionToRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizer:assign
        {permission? : The handle of the permission}
        {role? : The handle of the role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a permission to a role';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $permission = $this->getPermission();
        $role = $this->getRole();

        $role->grantPermission($permission);
    }

    private function getPermission()
    {
        $class = Authorizer::getClass('permission');

        if (!$handle = $this->argument('permission')) {
            $this->choice(
                'Which permission?',
                $class::all('handle')->map(function($item, $key) { return $item->handle; })->toArray()
            );
        }

        return $class::findByHandle($handle);
    }

    private function getRole()
    {
        $class = Authorizer::getClass('role');

        if (!$handle = $this->argument('role')) {
            $this->choice(
                'Which role are you assigning the permission to?',
                $class::all('handle')->map(function($item, $key) { return $item->handle; })->toArray()
            );
        }

        return $class::findByHandle($handle);
    }
}