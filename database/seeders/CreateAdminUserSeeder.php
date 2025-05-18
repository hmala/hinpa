<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class CreateAdminUserSeeder extends Seeder
{
/**
* Run the database seeds.
*
* @return void
*/
public function run()
{
$user = User::create([
'name' => 'حسام عبد الستار',
'email' => 'abc.majd.mh@gmail.com',
'password' => bcrypt('Mala@20'),
'roles_name' => ["Super-Admin"],
'Status' => 'مفعل',
'fckid' => '4510',
'fcktid' => '20',


]);
$role = Role::create(['name' => 'Super-Admin']);
$permissions = Permission::pluck('id','id')->all();
$role->syncPermissions($permissions);
$user->assignRole([$role->id]);
}
}  