<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\mohs; 
use App\Models\fck; 
use App\Models\fctypes; // هنا استيراد النموذج
// هنا استيراد النموذج
// هنا استيراد النموذج
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
        register as registration;
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8', // الحد الأدنى 8 أحرف
                'regex:/[a-z]/', // حروف صغيرة
                'regex:/[A-Z]/', // حروف كبيرة
                'regex:/[0-9]/', // أرقام
                'regex:/[@$!%*?&]/', // رموز خاصة
                'confirmed', // تأكيد كلمة المرور
            ],  
           'section_id' => ['required', 'integer'],
           'fckt' => ['required', 'integer'],
           'fckn' => ['required', 'integer'],
           
        ]);
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'google2fa_secret' => $data['google2fa_secret'],
            'mohcode'=>$data['section_id'],
            'fcktid'=>$data['fckt'],
            'fckid'=>$data['fckn'],
        ]);
    }
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $google2fa = app('pragmarx.google2fa');
  
        $registration_data = $request->all();
        
        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();
  
        $request->session()->flash('registration_data', $registration_data);
       
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $registration_data['email'],
          
            $registration_data['google2fa_secret']
        );
          
        return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function completeRegistration(Request $request)
    {        
        $request->merge(session('registration_data'));
  
        return $this->registration($request);
    } 
    
    public function showRegistrationForm()
    {  
        $mohs = mohs::all();
        $fctypes = fctypes::all();
        $fckns = collect(); // افتراضيًا نقوم بإرسال قائمة فارغة

        return view('auth.register', compact('mohs', 'fctypes', 'fckns'));
    }

    public function getInstitutions(Request $request)
    {
        try {
            // التحقق من وجود section_id و fckt
            $section_id = $request->input('section_id');
            $fckt = $request->input('fckt');

            if (!$section_id || !$fckt) {
                return response()->json([], 400); // Bad Request إذا كانت البيانات غير مكتملة
            }

            // جلب المؤسسات بناءً على section_id و fckt
            $institutions = fck::where('moh_id', $section_id)
                ->where('fctypesid', $fckt)
                ->get();

            return response()->json($institutions);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            \Log::error('Error fetching institutions: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    
    
}
