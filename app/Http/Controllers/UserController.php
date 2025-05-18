<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\mohs;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use DB;
use Hash;
class UserController extends Controller
{
    function __construct()
    {
    $this->middleware('permission:أدارة المستخدمين', ['only' => ['index','store','create','edit','update','destroy','show']]);
    
    
    }
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index(Request $request)
{
    $data = User::orderBy('id', 'DESC')->get(); // جلب جميع السجلات بدون تقسيم
    return view('users.index', compact('data'))->with('i', 0); // الرقم التسلسلي يبدأ من 0
}
/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/
public function create()
{
$roles = Role::pluck('name','name')->all();
return view('users.Add_user',compact('roles'));
}
/**
* Store a newly created resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @return \Illuminate\Http\Response
*/
public function store(Request $request)
{
    // تحقق من صحة البيانات
    $this->validate($request, [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|same:confirm-password',
        'roles_name' => 'required'
    ]);

    // تخزين البيانات وتشفير كلمة المرور
    $input = $request->all();
    $input['password'] = Hash::make($input['password']);

    // إنشاء المستخدم الجديد
    $user = User::create($input);

    // تعيين الأدوار للمستخدم الجديد
    $user->assignRole($request->input('roles_name'));

    // إعادة التوجيه مع رسالة نجاح
    return redirect()->route('users.index')
        ->with('success', 'User created successfully');
}

/**
* Display the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function show($id)
{
$user = User::find($id);
return view('users.show',compact('user'));
}
/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function edit($id)
{
$user = User::find($id);
$roles_name = Role::pluck('name','name')->all();
$userRole = $user->roles->pluck('name','name')->all();
return view('users.edit',compact('user','roles_name','userRole'));
}
/**
* Update the specified resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function update(Request $request, $id)
{
    // Validate the incoming data
    $this->validate($request, [
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $id,
        'roles_name' => 'required',
        'Status' => 'required',
    ]);

    // Exclude password fields entirely
    $input = $request->all();

    // Start a database transaction
    DB::transaction(function () use ($input, $id, $request) {
        $user = User::findOrFail($id);

        // Update user data
        $user->update($input);

        // Delete previous roles and assign the new roles
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles_name'));

        // Update additional fields
        $user->roles_name = $request->input('roles_name');
        $user->Status = $request->input('Status'); // Activate the user
        $user->save();

        // Log the operation
        Log::info('User updated successfully. User ID: ' . $id);
    });

    // Redirect with a success message
    return redirect()->route('users.index')
        ->with('success', 'تم تحديث المستخدم بنجاح');
}
/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function destroy(Request $request)
{
    $id = $request->user_id;
    $user = user::where('id', $id)->first();
    $user->Delete();
    session()->flash('delete_invoice');
    return redirect('/users');
}
public function showChangePasswordForm($id)
{
    $user = User::findOrFail($id);
    return view('users.change-password', compact('user'));
}
public function changePassword(Request $request, $id)
{
    // التحقق من البيانات المدخلة
    $this->validate($request, [
        'password' => 'required|min:6|same:confirm-password',
    ]);

    // تحديث كلمة المرور
    $user = User::findOrFail($id);
    $user->password = Hash::make($request->password);
    $user->save();

    // رسالة النجاح
    return redirect()->back()->with('success', 'تم تغيير كلمة المرور بنجاح');
}
}