<?php

namespace App\Http\Controllers\Backend\Admin\Configurations;

use App\Http\Controllers\Controller;
use App\Mail\EmailManager;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Mail;

class SettingController extends Controller
{

    # constructor
    public function __construct()
    {
        $this->middleware(['permission:general_settings'])->only('generalSetting');
        $this->middleware(['permission:order_settings'])->only(['orderSetting']);
        $this->middleware(['permission:smtp_settings'])->only(['smtpSettings', 'testEmail']);
        $this->middleware(['permission:payment_methods'])->only(['payment_method', 'payment_method_update']);
        $this->middleware(['permission:social_media_login'])->only(['social_login']);
    }

    # general settings
    public function generalSetting()
    {
        return view('backend.admin.configurations.general.index');
    }

    # general settings
    public function orderSetting()
    {
        return view('backend.admin.configurations.orders.index');
    }

    # update general settings
    public function update(Request $request)
    {
        foreach ($request->types as $key => $type) {
            if ($type == 'systemName') {
                overWriteEnvFile('APP_NAME', $request[$type]);
            }
            if ($type == 'timezone') {
                overWriteEnvFile('APP_TIMEZONE', $request[$type]);
            }
            if ($type == 'appMode') {
                overWriteEnvFile('APP_MODE', $request[$type]);
            } else {
                $lang = null;
                if (gettype($type) == 'array') {
                    $lang = array_key_first($type);
                    $type = $type[$lang];
                    $business_settings = SystemSetting::where('type', $type)->where('lang_key', $lang)->first();
                } else {
                    $business_settings = SystemSetting::where('type', $type)->first();
                }

                if ($business_settings != null) {
                    if (gettype($request[$type]) == 'array') {
                        $business_settings->value = json_encode($request[$type]);
                    } else {
                        $business_settings->value = $request[$type];
                    }
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    if (gettype($request[$type]) == 'array') {
                        $business_settings->value = json_encode($request[$type]);
                    } else {
                        $business_settings->value = $request[$type];
                    }
                    # $business_settings->lang_key  = $lang;
                    $business_settings->save();
                }
            }
        }
        cacheClear();
        if ($request->ajax()) {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => null
            ];
        } else {
            flash(translate("Settings updated successfully"))->success();
            return back();
        }
    }


    # smtp settings
    public function smtpSettings()
    {
        return view('backend.admin.configurations.smtp.index');
    }

    # test email
    public function testEmail(Request $request)
    {
        $array['view'] = 'emails.newsletter';
        $array['subject'] = "SMTP Test";
        $array['from'] = config('app.mail_from_address)');
        $array['content'] = "This is a test email.";
        try {
            Mail::to($request->email)->queue(new EmailManager($array));
        } catch (\Exception $e) {
            dd($e);
        }

        flash(translate('An email has been sent.'))->success();
        return back();
    }

    # update env values
    public function envKeyUpdate(Request $request)
    {
        foreach ($request->types as $key => $type) {
            overWriteEnvFile($type, $request[$type]);
        }
        cacheClear();
        flash(translate("Settings updated successfully"))->success();
        return back();
    }

    # return form of payment methods
    public function payment_method(Request $request)
    {
        return view('backend.admin.configurations.payment.payment_method');
    }

    # update payment methods
    public function payment_method_update(Request $request)
    {
        if ($request->types) {
            foreach ($request->types as $key => $type) {
                overWriteEnvFile($type, $request[$type]);
            }
        }

        $business_settings_sandbox = SystemSetting::where('type', $request->payment_method . '_sandbox')->first();

        if ($business_settings_sandbox != null) {
            if ($request->has($request->payment_method . '_sandbox')) {
                $business_settings_sandbox->value = 1;
                $business_settings_sandbox->save();
            } else {
                $business_settings_sandbox->value = 0;
                $business_settings_sandbox->save();
            }
        } else {
            $business_settings_sandbox = new SystemSetting;
            $business_settings_sandbox->type = $request->payment_method . '_sandbox';
            if ($request->has($request->payment_method . '_sandbox')) {
                $business_settings_sandbox->value = 1;
                $business_settings_sandbox->save();
            } else {
                $business_settings_sandbox->value = 0;
                $business_settings_sandbox->save();
            }
        }

        # active / inactive
        $business_settings = SystemSetting::where('type', $request->payment_method . '_activation')->first();
        if ($business_settings == null) {
            $business_settings = new SystemSetting;
            $business_settings->type = $request->payment_method . '_activation';
            if ($request->has($request->payment_method . '_activation')) {
                $business_settings->value = 1;
                $business_settings->save();
            } else {
                $business_settings->value = 0;
                $business_settings->save();
            }
        } else {
            $business_settings->type = $request->payment_method . '_activation';
            if ($request->has($request->payment_method . '_activation')) {
                $business_settings->value = 1;
                $business_settings->save();
            } else {
                $business_settings->value = 0;
                $business_settings->save();
            }
        }

        cacheClear();
        flash(translate("Settings updated successfully"))->success();
        return back();
    }

    # social login view
    public function social_login()
    {
        return view('backend.admin.configurations.social.social');
    }
        public function delivery_partner_settings(){
        return view('backend.admin.configurations.delivery-partner.index');
    }
}
