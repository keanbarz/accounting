<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\voucher;
use Illuminate\Support\Facades\Log;

class all extends Controller
{
    public function save(Request $request){
        $voucher = Voucher::find($request->id);

        if (!$voucher) {
            $voucher = new Voucher();
        }

        switch (Auth::user()->role) {
            case 'Accounting Receiving Clerk':
                $voucher->payee = $request->payee;
                $voucher->particulars = $request->particulars;
                $voucher->amount = $request->amount;
            break;
            case 'Budget Officer':
                $voucher->orsno = $request->orsno;
                $voucher->uacs = $request->uacs;
            break;
        }



        $voucher->save();

        if (!$voucher->remarks){
            $stat = "Received By " . (Auth::user()->name) . " on " . ($voucher->created_at) . "<br>";
            $crud = "created";
        }else {
            $stat = "Updated By " . (Auth::user()->name) . " on " . ($voucher->updated_at)  . "<br>";
            $crud = "updated";
        }
        $voucher->remarks .= $stat;
        $voucher->save();
        return redirect()->back()->with($crud, 'Voucher updated successfully.');
    }

    public function dashboard(){
        switch (Auth::user()->role) {
            case 'Accounting Receiving Clerk':
                $voucher = voucher::orderBy('id', 'desc')->paginate(10);
                return view('dashboard',['voucher'=>$voucher]);
                break;
            case 'Budget Officer':
                $voucher = voucher::orderBy('id', 'desc')->paginate(10);
                return view('dashboard-bo',['voucher'=>$voucher]);
                break;
        }   
    }

    public function delete(Request $request){
                $voucher = voucher::find($request->input('id'))->delete();
                return redirect('/dashboard')->with('deleted', 'Voucher deleted successfully.');

    }

    public function forward(){
        switch (Auth::user()->role) {
            case 'admin':
            case 'Accounting Receiving Clerk':
                $voucher = voucher::orderBy('id', 'desc')->get();
            return view('forward',['voucher'=>$voucher]);
            break;         
        }    
    }

    public function submitforward(Request $request){
        $itemIds = $request->input('data_id', []);

        foreach ($itemIds as $itemId) {
            $selectedValue = $request->input("forward_{$itemId}",3);       
            switch ($selectedValue) {
                case 0:
                    $voucher = voucher::find($itemId);
                    $voucher->save();
                    $voucher->isObligation="1";
                    $voucher->save();
                    $voucher->remarks = $voucher->remarks . "<br>Forwarded for Obligation by " . (Auth::user()->name) . " on " . ($voucher->updated_at);
                    $voucher->save();
                        break;
                case 1:
                    $voucher = voucher::find($itemId);
                    $voucher->save();
                    $voucher->isEntry="1";
                    $voucher->save();
                    $voucher->remarks = $voucher->remarks . "<br>Forwarded for Entry by " . (Auth::user()->name) . " on " . ($voucher->updated_at);
                    $voucher->save();
                        break;
                default:
                    break;
            }
        }
        return redirect()->back()->with('success', 'Form submitted successfully');
    }

}
