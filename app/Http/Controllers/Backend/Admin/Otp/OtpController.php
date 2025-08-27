<?php

namespace App\Http\Controllers\Backend\Admin\Otp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtpController extends Controller
{

    # constructor
    public function __construct()
    {
        // $this->middleware(['permission:general_settings'])->only('generalSetting');
    }

    public function configure()
    {
        if (user()->user_type == 'admin') {
            return view('backend.admin.otp.credentials');
        }
    }
}
