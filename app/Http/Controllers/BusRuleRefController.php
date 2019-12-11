<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\BusRuleRef;
use Illuminate\Validation\Rule;

class BusRuleRefController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = BusRuleRef::select("rule_name","rule_value")->whereIn("rule_name",array("ios_update_driver","ios_url_driver","ios_version_driver","ios_update_user","ios_url_user","ios_version_user","android_update_driver","android_url_driver","android_version_driver","android_update_user","android_url_user","android_version_user","app_update_msg","cgst","sgst","igst","trusted_contacts_limit","referrer_amount","refer_user","minimum_wallet_balance","twitter_social_link","facebook_social_link","linkedin_social_link","instagram_social_link"))->where("sts_cd",'AC')->get();
        // dd($setting->toArray());
        return view('setting.index', compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator($request->all(),[
            'android_version_user'=> 'required',
            'android_url_user'=> 'required',
            'android_version_driver'=> 'required',
            'android_url_driver'=> 'required',
            'ios_version_user'=> 'required',
            'ios_url_user'=> 'required',
            'ios_version_driver'=> 'required',
            'ios_url_driver'=> 'required',
            'cgst'=> 'required',
            'sgst'=> 'required',
            'igst'=> 'required',
            'trusted_contacts_limit'=> 'required',
            'referrer_amount'=> 'required',
            'minimum_wallet_balance'=> 'required',
            'app_update_msg'=> 'required'
        ]);

        $attr = [
            'android_version_user'=> 'Android User App Version',
            'android_url_user'=> 'Android User App URL',
            'android_update_user'=> 'Android User App Force Update',
            'android_version_driver'=> 'Android Driver App Version',
            'android_url_driver'=> 'Android Driver App URL',
            'android_update_driver'=> 'Android Driver App Force Update',
            'ios_version_user'=> 'iOS User App Version',
            'ios_url_user'=> 'iOS User App URL',
            'ios_update_user'=> 'iOS User App Force Update',
            'ios_version_driver'=> 'iOS Driver App Version',
            'ios_url_driver'=> 'iOS Driver App URL',
            'ios_update_driver'=> 'iOS Driver App Force Update',
            'cgst'=> 'CGST',
            'sgst'=> 'SGST',
            'igst'=> 'IGST',
            'trusted_contacts_limit'=> 'Trusted Contacts Limit',
            'referrer_amount'=> 'Referrer Amount',
            'minimum_wallet_balance'=> 'Minimum Wallet Balance',
            'app_update_msg'=> 'Application Update Message'
        ];

        $validate->setAttributeNames($attr);
        if($validate->fails()){
            return redirect()->route("setting")->withInput($request->all())->withErrors($validate);
        }else{
            BusRuleRef::where("rule_name","android_version_user")->update(['rule_value'=>$request->android_version_user]);
            BusRuleRef::where("rule_name","android_url_user")->update(['rule_value'=>$request->android_url_user]);
            BusRuleRef::where("rule_name","android_update_user")->update(['rule_value'=>$request->android_update_user]);
            BusRuleRef::where("rule_name","android_version_driver")->update(['rule_value'=>$request->android_version_driver]);
            BusRuleRef::where("rule_name","android_url_driver")->update(['rule_value'=>$request->android_url_driver]);
            BusRuleRef::where("rule_name","android_update_driver")->update(['rule_value'=>$request->android_update_driver]);
            BusRuleRef::where("rule_name","ios_version_user")->update(['rule_value'=>$request->ios_version_user]);
            BusRuleRef::where("rule_name","ios_url_user")->update(['rule_value'=>$request->ios_url_user]);
            BusRuleRef::where("rule_name","ios_update_user")->update(['rule_value'=>$request->ios_update_user]);
            BusRuleRef::where("rule_name","ios_version_driver")->update(['rule_value'=>$request->ios_version_driver]);
            BusRuleRef::where("rule_name","ios_url_driver")->update(['rule_value'=>$request->ios_url_driver]);
            BusRuleRef::where("rule_name","ios_update_driver")->update(['rule_value'=>$request->ios_update_driver]);
            BusRuleRef::where("rule_name","cgst")->update(['rule_value'=>$request->cgst]);
            BusRuleRef::where("rule_name","sgst")->update(['rule_value'=>$request->sgst]);
            BusRuleRef::where("rule_name","igst")->update(['rule_value'=>$request->igst]);
            BusRuleRef::where("rule_name","trusted_contacts_limit")->update(['rule_value'=>$request->trusted_contacts_limit]);
            BusRuleRef::where("rule_name","referrer_amount")->update(['rule_value'=>$request->referrer_amount]);
            BusRuleRef::where("rule_name","refer_user")->update(['rule_value'=>$request->refer_user]);
            BusRuleRef::where("rule_name","minimum_wallet_balance")->update(['rule_value'=>$request->minimum_wallet_balance]);
            BusRuleRef::where("rule_name","app_update_msg")->update(['rule_value'=>$request->app_update_msg]);

            BusRuleRef::where("rule_name","twitter_social_link")->update(['rule_value'=>$request->twitter_social_link]);
            BusRuleRef::where("rule_name","facebook_social_link")->update(['rule_value'=>$request->facebook_social_link]);
            BusRuleRef::where("rule_name","linkedin_social_link")->update(['rule_value'=>$request->linkedin_social_link]);
            BusRuleRef::where("rule_name","instagram_social_link")->update(['rule_value'=>$request->instagram_social_link]);
            
            $request->session()->flash('success', 'Setting successfully saved');
            return redirect()->route('setting');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
