<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Phone;
use Illuminate\Support\MessageBag;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\Activitylog\Models\Activity;
class PhoneNumberController extends Controller
{
    public function index()
    {
        $countries = Http::get('https://api.first.org/data/v1/countries');
        $countries  = $countries['data'];
        return view('phoneNumberCheck',['countries' => $countries]);
    }
    public function phoneNumberValidate(Request $request)
    {
        try {
            $phone = PhoneNumber::make($request->phone, $request->country_code)->formatE164();
            $request->merge(['phone' => $phone]);
            
        } catch (\Throwable $th) {
            $errors = new MessageBag();
            $errors->add('phone', 'Number does not match the provided country.');
            return back()->withErrors($errors);
        }
        $request->validate([
            'phone' => 'required|phone:country_code|unique:phones',
        ],
        [
            'phone.phone' => 'Number Invalide',
            'phone.unique' => 'Already Have',
        ]);

        // Phone::create([
        //     'phone' => $phone,
        //     'country_code' => $request->country_code,
        // ]);


        $phn = Phone::where('phone', '+8801933704177')->first();
        // $phn = new Phone;
        $phn->phone = $phone;
        $phn->country_code = $request->country_code;
        $phn->save();


        // activity()->log('Look mum, I logged something');
        $activity = Activity::all()->last();
        // return $phone;
        return $activity;
        // return back()->with('success','Success');
    }
}
