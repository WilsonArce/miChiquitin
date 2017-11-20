<?php

namespace App\Http\Controllers\Cartera;

use App\Models\Cartera\Reportes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

use App\Models\Cartera\Plan_de_pago;
use App\Models\Usuarios\User;
use App\Models\Facturacion\Factura;
use App\Models\Usuarios\Cliente;
use App\Models\Cartera\Deuda;
use App\Models\Cartera\Pago;

use Session;
use Carbon\carbon;
use PDF;

class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('cartera.reportes.index');
        
    }

    public function reporte_deudas()
    {
        //
        $doc = Input::get('search');
        $user = User::where('id_tipo',$doc)->first(); 
        $message = '';

        switch ([$doc, $user]) {
          case is_null($doc):
            $deudas = Deuda::orderBy('created_at','desc')->get();
            $message = 'Ingrese el documento del usuario';
            break;
          case is_null($user):
            $deudas = Deuda::orderBy('created_at','desc')->get();
            $message = 'El usuario no existe!!';
            break;
          case count($user->deuda) == 0:
            $deudas = Deuda::orderBy('created_at','desc')->get();
            $message = 'El usuario '.$user->name.' '.$user->apellidos.' no tiene deudas!!';
            break;
          case !is_null($user):
            $user_id = $user->id;
            $deudas = Deuda::where('id_usuario',$user_id)->orderBy('created_at','desc')->get();
            $message = 'Deudas del usuario '.$user->name.' '.$user->apellidos;
            break;
          default:
            $deudas = Deuda::orderBy('created_at','desc')->get();
            break;
        }

        $total_pagar = 0;
        $total_pagado = 0;
        foreach ($deudas as $deuda) {
          # code...
          $total_pagar += $deuda->valor_a_pagar;
          $total_pagado += $deuda->valor_pagado;
        }
        return view('cartera.reportes.reporte_deudas', compact('deudas','total_pagar','total_pagado','message'));
        
    }

    public function pagos_ultima_semana()
    {

        $doc = Input::get('search');
        $user = User::where('id_tipo',$doc)->first(); 
        $one_week_ago = Carbon::now()->subWeek(1)->toDateString();

        switch ([$doc, $user]) {
          case is_null($doc):
            $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_week_ago)->get();
            $message = 'Ingrese el documento del usuario';
            break;
          case is_null($user):
            $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_week_ago)->get();
            $message = 'El usuario no existe!!';
            break;
          case !is_null($user):
            $user_id = $user->id;
            $data = User::whereId($user_id)->with('deuda.pagos')->get();
            $deuda = $data[0]->deuda;
            switch (is_null($deuda)) {
              case true:
                //dd($deuda);
                $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_week_ago)->get();
                $message = 'El usuario '.$user->name.' '.$user->apellidos.' no tiene deudas!!';
                break;
              case false:
                $pagos_user = $data[0]->deuda->pagos->where('created_at','>=',$one_week_ago);
                //dd($pagos_user);
                if (count($pagos_user) == 0) {
                  $message = 'El usuario '.$user->name.' '.$user->apellidos.' no ha efectuado pagos la última semana!!';
                  $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_week_ago)->get();
                } else {
                  $pagos = $pagos_user;
                  $message = 'Pagos realizados por '.$user->name.' '.$user->apellidos;
                }      
                break;
              default:
                # code...
                break;
            }
            
          default:
            //$pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_week_ago)->get();
            break;
        }

        
        $total_pagado = 0;
        foreach ($pagos as $pago) {
          # code...
          $total_pagado += $pago->valor;
        }
      
        return view('cartera.reportes.pagos_ultima_semana', compact('pagos','total_pagado','message'));

    }

    public function pagos_ultimo_mes()
    {
        //
        $doc = Input::get('search');
        $user = User::where('id_tipo',$doc)->first(); 
        $one_month_ago = Carbon::now()->subMonth(1)->toDateString();

        switch ([$doc, $user]) {
          case is_null($doc):
            $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_month_ago)->get();
            $message = 'Ingrese el documento del usuario';
            break;
          case is_null($user):
            $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_month_ago)->get();
            $message = 'El usuario no existe!!';
            break;
          case !is_null($user):
            $user_id = $user->id;
            $data = User::whereId($user_id)->with('deuda.pagos')->get();
            $deuda = $data[0]->deuda;
            switch (is_null($deuda)) {
              case true:
                //dd($deuda);
                $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_month_ago)->get();
                $message = 'El usuario '.$user->name.' '.$user->apellidos.' no tiene deudas!!';
                break;
              case false:
                $pagos_user = $data[0]->deuda->pagos->where('created_at','>=',$one_month_ago);
                //dd($pagos_user);
                if (count($pagos_user) == 0) {
                  $message = 'El usuario '.$user->name.' '.$user->apellidos.' no ha efectuado pagos el último mes!!';
                  $pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_month_ago)->get();
                } else {
                  $pagos = $pagos_user;
                  $message = 'Pagos realizados por '.$user->name.' '.$user->apellidos;
                }      
                break;
              default:
                # code...
                break;
            }
            
          default:
            //$pagos = Pago::orderBy('created_at','desc')->whereDate('created_at','>=',$one_month_ago)->get();
          break;
        }

        $total_pagado = 0;
        foreach ($pagos as $pago) {
          # code...
          $total_pagado += $pago->valor;
        }
      
        return view('cartera.reportes.pagos_ultimo_mes', compact('pagos','total_pagado','message'));
        
    }

    public function downloadPDF($id = null)
    {
      $data_key = substr($id,0,1);
      $user_key = substr($id,1,strlen($id));
      $one_week_ago = Carbon::now()->subWeek(1)->toDateString();
      $one_month_ago = Carbon::now()->subMonth(1)->toDateString();
      //dd($data_key,$user_key);

      if (strlen($user_key) == 0){
        //dd('No user');
        $deudas = Deuda::orderBy('created_at','desc')->get();
        $pdf = PDF::loadView('cartera.reportes.pdf', compact('deudas'));
        return $pdf->stream('reporte_'.Carbon::now().'.pdf');
      }else{
        $user = User::find($user_key);
        switch ($data_key) {
          case "d"://deuda
              $deudas = Deuda::where('id_usuario',$user_key)->orderBy('created_at','desc')->get();
              $pdf = PDF::loadView('cartera.reportes.pdf', compact('deudas'));
              return $pdf->stream('reporte_'.$user->name.'_'.Carbon::now().'.pdf');
              break;
          case "w"://week
              
              $deudas = Deuda::orderBy('created_at','desc')->whereDate('created_at','>=',$one_week_ago)
                ->search($user_key)->paginate(20);
              $pdf = PDF::loadView('cartera.reportes.pdf', compact('deudas'));
              return $pdf->stream('reporte_'.$user->name.'_'.Carbon::now().'.pdf');
              break;
          case "m"://month
              
              $deudas = Deuda::orderBy('created_at','desc')->whereDate('created_at','>=',$one_month_ago)
                ->search($user_key)->paginate(20);
              $pdf = PDF::loadView('cartera.reportes.pdf', compact('deudas'));
              return $pdf->stream('reporte_'.$user->name.'_'.Carbon::now().'.pdf');
              break;
        }
       
      }
    }

}
