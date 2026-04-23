<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Constant;
use App\Models\Employee;
use App\Models\WorkData;
use Illuminate\Http\Request;

class ConstantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constants = Constant::get();
        return view('dashboard.pages.constants',compact('constants'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        foreach($request->except('_token') as $key => $value){
            Constant::updateOrCreate([
                'key' => $key,
            ],[
                'value' => $value,
            ]);
        }
        return redirect()->route('dashboard.constants.index')->with('success','تم تحديث القيم');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if($request->state_effectiveness){
            Constant::findOrFail($request->state_effectiveness)->delete();
        }
        return redirect()->route('dashboard.constants.index')->with('danger','تم حذف القيمة المحددة');
    }
}
