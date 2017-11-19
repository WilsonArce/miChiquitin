@extends('layouts.app')

@section('titulo')
  <h4>Cartera / <a href="{{ URL::to('reportes') }}">Reportes</a></h4>
  @endsection

@section('content')

  <!--link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css"-->
@if(\Request::is('reportes/reporte_deudas'))
<div class="container">

    <nav class="navbar navbar-inverse">
      <ul class="nav navbar-nav">
          <li><a href="{{ URL::to('reportes/reporte_deudas') }}">Reporte deudas</a></li>
          <li><a href="{{ URL::to('reportes/pagos_ultima_semana') }}">Pagos última semana</a>
          <li><a href="{{ URL::to('reportes/pagos_ultimo_mes') }}">Pagos último mes</a>
      </ul>
    </nav>
  
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif

  <h1>Reporte deudas</h1>
  
  <div class="row">
    
      {!! Form::open(['method'=>'GET','url'=>'reportes/reporte_deudas','class'=>'navbar-form navbar-left','role'=>'search'])  !!}
        {{ Form::input('number', 'search', null, array('class'=>'form-control', 'placeholder'=>'Documento')) }}
        {{ Form::submit('Buscar', array('class' => 'btn btn-primary')) }}
      {{ Form::close() }}
    
    <div class="alert alert-info">{{$message}}</div>
    
  </div>
  
  <div class="table-inverse table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables_paginate">
        <thead>
         
            <tr style="background-color:lightgrey; font-weight:bold">
                <td>Fecha deuda</td>
                <td>Documento</td>
                <td>Usuario</td>
                <td>Fecha factura</td>
                <td>Valor pagado</td>
                <td>Valor a pagar</td>
                <td>Plazo crédito</td>
                <td>Estado</td>
                <!--td colspan="2"></td-->
            </tr>
        </thead>
        <tbody>
        @foreach($deudas as $deuda)
            <tr>
                <td>{{ $deuda->created_at }}</td>
                <td>{{ $deuda->user->id_tipo }}</td>
                <td>{{ $deuda->user->name }}</td><!--td>{{ $deuda->id_usuario }}</td-->
                <td>{{ $deuda->factura->fecha }}</td>
                <td>${{ number_format($deuda->valor_pagado) }}</td>
                <td>${{ number_format($deuda->valor_a_pagar) }}</td>
                <td>{{ $deuda->plazo_credito }}</td>
                <td>{{ $deuda->estado }}</td>
                
            </tr>
        @endforeach
          <tr style="background-color:lightgrey; font-weight:bold">
            <td><h4>Total</h4></td>
            <td colspan='3'></td>
            <td><h4>${{ number_format($total_pagado) }}</h4></td>
            <td><h4>${{ number_format($total_pagar) }}</h4></td>
            <td colspan='2'></td>
          </tr>
        </tbody>
    </table>
  </div>
</div>
@endsection
@else
  false
@endif