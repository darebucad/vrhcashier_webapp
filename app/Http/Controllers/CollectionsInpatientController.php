<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App;
use App\Payment;
use App\ViewPaymentOR;
use App\ViewPatientBill;
use App\ViewPatientBillCharges;
use App\ViewPatientBillTotal;
use App\ViewPayment;
use App\ViewPatientAccount;
use App\ChargeSequence;
use App\PatientAccount;
use App\PatientBill;
use App\ViewBillProfees;
use App\ViewBillRoom;
use App\ViewBillDrugs;
use App\ViewBillSupplies;
use App\ViewBillLab;
use App\ViewBillRad;
use App\ViewBillOR;
use App\ViewBillOxygen;
use App\ViewBillMisc;

use Carbon\Carbon;
use PDF;
use NumberToWords\NumberToWords;



class CollectionsInpatientController extends Controller
{

    public function index() {

    	return view('collections.inpatient.index');
    }

    public function create(Request $request, $id) {
      $user_id = $id;

      $payment_count =  ViewPaymentOR::select('orno')->where('id', $user_id)->count();

      if ($payment_count > 0) {
        $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at', 'id')
          ->where('id', $user_id)
          ->limit(1)
          ->orderBy('created_at', 'desc')
          ->get();

      } else {
        $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at', 'id')
          ->limit(1)
          ->orderBy('created_at', 'desc')
          ->get();
      }

      return view('collections.inpatient.create', compact('payments'));
    }


    /**
     * Search SOA .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchSoa(Request $request) {
      $_token = $request->_token;
      $soa_number = $request->soa_number;
      $billing_id = '';
      $charge_sequence = ChargeSequence::select('sequenceNo', 'chrgcode')->orderBy('sequenceNo', 'ASC')->get();

      $patient_bill = PatientBill::leftjoin('hpatacct AS a', 'a.enccode', '=', 'tbl_billing.enccode')
      ->leftjoin('hadmlog AS ad', 'ad.enccode', '=', 'tbl_billing.enccode')
      ->leftjoin('hperson AS p', 'p.hpercode', '=', 'ad.hpercode')
      ->where('billing_num', $soa_number)
      ->select('tbl_billing.billing_id', 'tbl_billing.enccode', 'tbl_billing.billing_num', 'a.paacctno', 'p.hpercode', 'p.patlast', 'p.patfirst', 'p.patmiddle', 'p.patsuffix')
      ->first();

      // $get_patient_bill = ViewPatientBill::select('billing_id', 'encounter_code', 'soa_number', 'account_number', 'patient_id', 'patient_name')
      // ->where('soa_number', $soa_number)
      // ->first();

      $billing_id = $patient_bill->billing_id;
      // $account_number = $get_patient_bill->account_number;
      // $encounter_code = $get_patient_bill->encounter_code;
      // $patient_id = $get_patient_bill->patient_id;
      // $patient_name = $get_patient_bill->patient_name;
      // $get_account_number = $this::getAccountNumber($account_number, $encounter_code, $patient_id);

      // $data = ViewPatientBillTotal::where('billing_id', $billing_id)->get();

      $bill_profees = ViewBillProfees::where('billing_id', $billing_id)
        ->select(DB::raw('SUM(total) AS total'))
        ->groupBy('billing_id')
        ->first();

      $bill_room = ViewBillRoom::where('billing_id', $billing_id)->first();
      $bill_drugs = ViewBillDrugs::where('billing_id', $billing_id)->first();
      $bill_supplies = ViewBillSupplies::where('billing_id', $billing_id)->first();
      $bill_lab = ViewBillLab::where('billing_id', $billing_id)->first();
      $bill_rad = ViewBillRad::where('billing_id', $billing_id)->first();
      $bill_or = ViewBillOR::where('billing_id', $billing_id)->first();
      $bill_oxygen = ViewBillOxygen::where('billing_id', $billing_id)->first();
      $bill_misc = ViewBillMisc::where('billing_id', $billing_id)->first();

      $bill_breakdown = array(
        'profees' => $bill_profees,
        'room' => $bill_room,
        'drugs' => $bill_drugs,
        'supplies' => $bill_supplies,
        'lab' => $bill_lab,
        'rad' => $bill_rad,
        'or' => $bill_or,
        'oxygen' => $bill_oxygen,
        'misc' => $bill_misc,
      );

      $response = array(
        'bill_total' => 'bill_total',
        'patient_bill' => $patient_bill,
        'charge_sequence' => $charge_sequence,
        'bill_breakdown' => $bill_breakdown,

      );

      return response()->json($response);
    }




    public function searchPatientCharges(Request $request){
      $_token = $request->_token;
      $soa_number = $request->soa_number;
      $billing_id = '';
      $encounter_code = '';
      $patient_id = '';
      $patient_name = '';
      $account_number = '';
      $get_patient_bill = ViewPatientBill::where('soa_number', $soa_number)->get();

      foreach ($get_patient_bill as $value) {
        $billing_id = $value->billing_id;
        $encounter_code = $value->encounter_code;
        $patient_id = $value->patient_id;
        $patient_name = $value->patient_name;
        $account_number = $value->account_number;
        $get_account_number = $this::getAccountNumber($account_number, $encounter_code, $patient_id);
        break;
      }

      $response = array(
        'data' => $get_patient_bill,
        '_token' => $_token,
        'billing_id' => $billing_id,
        'encounter_code' => $encounter_code,
        'patient_id' => $patient_id,
        'patient_name' => $patient_name,
        'account_number' => $get_account_number
      );

      return response()->json($response);
    }

    /**
     * Searching of patient bill records .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autoCompleteSearch(Request $request) {
      $search = $request->term;

      $patient_bill = ViewPatientBill::where('patient_name', 'LIKE', '%'. $search .'%')->limit(10)->get();
      // $patient_bill = ViewPatientBill::all();

      $data = [];
      foreach ($patient_bill as $key => $value) {
        $data[] = [
          'id' => $value->billing_id,
          'encounter_code' => $value->encounter_code,
          'soa_number' => $value->soa_number,
          'patient_id' => $value->patient_id,
          'value' => $value->patient_name,
          'account_number' =>  $this::getAccountNumber($value->account_number, $value->encounter_code, $value->patient_id)
        ];

      }

      return response($data);
    }


    public function getAccountNumber($an, $ec, $pid){
      $current_time = Carbon::now('Asia/Manila');

      if ($an == '' or $an == null or $an == 0) {
        $patient_account = ViewPatientAccount::select('next_account_no_year', 'next_account_no_increment')->first();
        $new_account_number = $patient_account->next_account_no_year . '-' . $patient_account->next_account_no_increment;
        $data = array(
            'paacctno'   => $new_account_number,
            'enccode'   =>  $ec,
            'hpercode'  =>  $pid,
            'upicode'   =>  null,
            'padteas'   =>  $current_time->toDateTimeString(),
            'patmeas'   =>  $current_time->toDateTimeString(),
            'patotchrg' =>  '0',
            'patotprof' =>  '0',
            'patotamt'  =>  '0',
            'patotdisc' =>  '0',
            'patmsstot' =>  '0',
            'panetamt'  =>  '0',
            'papay'     =>  '0',
            'pabal'     =>  '0',
            'pastat'    =>  'A',
            'palock'    =>  'N',
            'datemod'   =>  null,
            'updsw'     =>  'N',
            'confdl'    =>  'N',
            'ptdisc'    =>  '0',
            'paphic'    =>  '0',
            // 'id'        =>  $user_id,
            'created_at'=>  $current_time->toDateTimeString(),
            'updated_at'=>  $current_time->toDateTimeString()
        );

        PatientAccount::insert($data);
      } else {
          $new_account_number = $an;
      }

      return $new_account_number;
    }





        public function checkORDuplicate(Request $request) {
          $_token = $request->_token;
          $or_number = $request->or_number;
          $arrData = $request->arrData;

          $getPaymentData = ViewPayment::where('or_no_prefix', $or_number)->count();

          $data = $getPaymentData;

          $response = array(
            'data' => $data,
            'or_number' => $or_number,
            'arrData' => $arrData
          );

          return response()->json($response);



        }

        /**
         * Store a newly created payment resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function savePayment(Request $request) {
          $_token = $request->_token;
          $array_data = $request->arrData;
          $or_number = $request->or_number;
          $payment_counter = 0;
          $current_time = Carbon::now('Asia/Manila');

          $or_n = substr($or_number, strpos($or_number, "-") + 1);

          foreach($array_data as $item) {
            $data = array(
              'orno' => $or_n,
              'enccode' => $item['encounter_code'],
              'hpercode' => $item['patient_id'],
              'acctno' => $item['account_number'],
              'pcchrgcod' => $item['soa_number'],
              'paystat' => $item['paystat'],
              'paylock' => $item['payment_lock'],
              'updsw' => $item['updsw'],
              'confdl' => $item['confdl'],
              'payment_status' => $item['payment_status'],
              'ordate' => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $item['or_date']))),
              'id' => $item['user_id'],
              'paycode' => $item['payment_mode'],
              'curcode' => $item['currency'],
              'paytype' => $item['payment_type'],
              'amount_paid' => $item['amount_paid'],
              'amount_tendered' => $item['amount_tendered'],
              'amount_change' => $item['amount_change'],
              'bal' => $item['ending_balance'],
              'created_at' => $current_time->toDateTimeString(),
              'advance_payment' => $item['advance_payment'],
              'itemcode' => $item['product_id'],
              'amt' => $item['total'],
              'chrgcode' => $item['product_id'],
              'remarks' => $item['remarks'],
              'payctr' => $payment_counter,
            );

           Payment::insert($data);
           $payment_counter += + 1;
          }

          $response = array('data' => $array_data);

          return response()->json($response);
        }


        public function getPaymentData($id) {
            $payment_data = ViewPayment::where('or_no_prefix', $id)
            ->orderByRaw('payment_counter ASC')
            ->get();

            return $payment_data;
        }

        /**
         * Show pdf official receipt
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function printReceipt($id) {
            $or_number = $id;

            // custom paper = array (0,0,width,length)
            $customPaper = array(0,0,273.6,792);
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($this->convertPaymentDataToHtml($or_number))->setPaper($customPaper);

            // return $pdf->download('outpatient.collection.receipt('.$or_no.').pdf');
            return $pdf->download('inpatient.collection.receipt.pdf');

            // $response = array(
            //     'id' => $or_no
            // );
            // return response()->json($response);
            //PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
            // $pdf = PDF::loadView('collections.outpatient.print');
            // return $pdf->download('official_receipt.pdf');
        }

        public function convertPaymentDataToHtml($id) {
            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            $payment_data = $this->getPaymentData($id);
            $payment_count = ViewPayment::where('or_no_prefix', $id)->count();
            $sub_total = 0;
            $total = 0;
            $decimal_value = 0;
            $output = '';
            $soa_number = '';
            $officer = 'TERESITA T. TAGUINOD';
            $position = 'Supervising Administrative Officer';

            foreach ($payment_data as $value) {
              $soa_number = $value->charge_slip_no;

              $output .= '<html>
              <style>@page {margin-left: 17px; margin-right:27px;}</style></head>
              <body>
              <br>
              <br>
              <br>
              <br>
              <br>
              <p align="center" style="font-family: Helvetica; font-size: 15px; margin-right: -70px; margin-top: -26px;">'.$value->or_no_prefix.'</p>
              <p align="right" style="font-family: Helvetica; font-size: 15px; margin-right: 10px; margin-top: -6px; margin-bottom: -3px">'.$value->or_date.'<br></p>
              <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px; margin-bottom: -7px;">Veterans Regional Hospital</p>
              <p style="font-family: Courier; font-size: 15px; margin-left: 43px;  margin-bottom: 10px">'.$value->patient_name.'</p><br>
              <p style="font-family: Courier; font-size: 12px;">Payment for SOA#'.$soa_number.'</p>';

              break;
            }
            $output .= '<table width="100%">';

            if($payment_count <= 8) {
                foreach ($payment_data as $payment) {
                  $sub_total = $payment->amount;
                  $description = $payment->product_description;
                  $total += $sub_total;

              $output .= '
                <tr style="line-height: 17px;">
                  <td style="font-family: Courier; font-size: 11px; width:255px;">' . $description . '</td>
                  <td style="font-family: Courier; font-size: 12px; margin-right:20px" align="right">' . number_format($sub_total, 2) . '</td>
                </tr>';
                }

                $supplemental_row = (8 - $payment_count);
                $counter = $supplemental_row;
                if($supplemental_row != 0){
                    for($counter; $counter > 0; $counter--) {
                        $output .='
                            <tr style="line-height: 17px; color:white;">
                                <td>.</td>
                            </tr>
                        ';
                    }
                }
            } else {

              foreach ($payment_data as $payment) {
                $amount_paid = $payment->amount_paid;
                $category = 'Hospital Charges';
                // $category = $payment->category;
                $total = $amount_paid;

                $output .='
                  <tr style="line-height: 17px;">
                    <td style="font-family: Helvetica; font-size: 11px; width:195px;">' . $category . '</td>
                    <td style="font-family: Courier; font-size: 15px; margin-right: 20px;" align="right">' . number_format($amount_paid, 2) . '</td>
                  </tr>';

                $supplemental_row = 7;
                $counter = $supplemental_row;
                if($supplemental_row != 0){
                  for($counter; $counter > 0; $counter--) {
                    $output .='
                      <tr style="line-height: 17px; color:white;">
                        <td>.</td>
                      </tr>
                    ';
                  }
                }
                break;
              }
            }

            $decimal_value = Str::substr(($total * 100), -2);
            if($decimal_value == 00){
                // $decimal_value = ' and zero centavo only';
                $decimal_value = ' and 00/100 only';
            }
            else if($decimal_value < 02){
                // $decimal_value = ' and ' . $numberTransformer->toWords($decimal_value) . ' centavo only';
                $decimal_value = ' and ' . $decimal_value . '/100 only';
            }
            else if($decimal_value > 01){
                // $decimal_value = ' and ' . $numberTransformer->toWords($decimal_value) . ' centavos only';
                $decimal_value = ' and ' . $decimal_value . '/100 only';
            }

                // <p align="left" style="font-family: Times New Roman; font-size: 12px; margin-top: -10px">'.$decimal_value.'</p>

            $output .= '</table>
                <p align="right" style="font-family: Courier; font-size: 20px; font-weight: bold; margin-top: 4px; margin-right: 3px"><b>'.number_format($total, 2) .'</b></p>
                <p style="font-family: Courier; font-size: 13px; margin-top: -6px; margin-left: 100px">'.ucwords($numberTransformer->toWords($total)) . $decimal_value .'</p>
                <br>
                <br>
                <br>
                <br>
                <br>
                <p align="right" style="font-family: Arial; font-size: 14px; margin-bottom: -15px; margin-top: -10px; margin-right: 13px">'. $officer .'</p>
                <p align="right" style="font-family: Arial; font-size: 10px; margin-right: 20px">'. $position .'</p>';

            foreach ($payment_data as $key) {
                $output .='<p align="left" style="font-family: Arial; font-size: 14px; margin-top: -6px; margin-left:15px;">'.$key->employee_name.'</p>';
                break;
            }
            return $output;

       }



       /**
        * Retrieve list of inpatient payments in JSON form.
        *
        * @return \Illuminate\Http\Response
        */
       public function getInpatientPaymentData() {

//          SELECT p.enccode,
// p.created_at,
// p.preorno,
// p.pcchrgcod,
// pe.patlast,
// pe.patfirst,
// pe.patsuffix,
// pe.patmiddle,
// p.amt,
// c.name,
// p.payment_status,
// p.advance_payment
//
// FROM hpay AS p
// LEFT JOIN hperson AS pe
// ON pe.hpercode = p.hpercode
// LEFT JOIN cashier_users AS c
// ON c.id = p.id
// DB::raw('SUM(total) AS total')


         $payments = Payment::leftjoin('hperson AS pe', 'pe.hpercode', '=', 'hpay.hpercode')
          ->leftjoin('cashier_users AS c', 'c.id', '=', 'hpay.id')
          ->select('hpay.enccode', 'hpay.created_at', 'hpay.preorno', 'hpay.pcchrgcod', 'pe.patlast', 'pe.patfirst', 'pe.patsuffix', 'pe.patmiddle', DB::raw('SUM(hpay.amt) AS amount'), 'c.name', 'hpay.payment_status', 'hpay.advance_payment')
          // ->distinct()
          ->whereNotNull('hpay.advance_payment')
          ->groupBy('hpay.preorno')
          ->orderBy('hpay.created_at', 'DESC')
          ->get();

         // $payments = ViewPayment::select('or_date', 'or_no_prefix', 'charge_slip_no','patient_name', 'discount', 'amount_paid', 'employee_name', 'status')
         // ->distinct()
         // ->whereNotNull('advance_payment')
         // ->orderByRaw('created_at DESC')
         // ->get();

         $response = array('data' => $payments);
         return response()->json($response);
       }


       /**
        * Cancel selected in-patient payment.
        *
        * @param \Illuminate\Http\Request $request
        * @return \Illuminate\Http\Response
        */
       public function cancelPayment(Request $request, $id){
         $or_number = $id;
           // $or_no = $request->id;
           $user_id = $request->user_id;
           $current_time = Carbon::now('Asia/Manila');

           Payment::where('preorno', $or_number)
           ->update([
               'payment_status' => 'Cancelled',
               // 'id' => $user_id,
               'updated_at' => $current_time->toDateTimeString()
           ]);
           return redirect('/collections/inpatient');
       }

       /**
        * Selected payment will be set to draft .
        *
        * @param \Illuminate\Http\Request $request
        * @return \Illuminate\Http\Response
        */
       public function draftPayment(Request $request, $id){
         $or_number = $id;
           // $or_no = $request->id;
           $user_id = $request->user_id;
           $current_time = Carbon::now('Asia/Manila');

           Payment::where('preorno', $or_number)
           ->update([
               'payment_status' => 'Draft',
               // 'id' => $user_id,
               'updated_at' => $current_time->toDateTimeString()
           ]);
           return redirect('/collections/inpatient');
       }


       /**
        * Mark as paid payment.
        *
        * @param \Illuminate\Http\Request $request
        * @return \Illuminate\Http\Response
        */
       public function markPaid(Request $request, $id){
         $or_number = $id;
           // $or_no = $request->id;
           $user_id = $request->user_id;
           $current_time = Carbon::now('Asia/Manila');

           Payment::where('preorno', $or_number)
           ->update([
               'payment_status' => 'Paid',
               // 'id' => $user_id,
               'updated_at' => $current_time->toDateTimeString()
           ]);
           return redirect('/collections/inpatient');
       }



}
