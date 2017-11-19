@extends('layouts.app')

@section('titulo')
  <h4>Cartera / <a href="{{ URL::to('reportes') }}">Reportes</a></h4>
  @endsection

@section('content')

  <!--link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css"-->

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
  <h1>Pagos último mes</h1>
  <div class="row row-md-6">
    {!! Form::open(['method'=>'GET','url'=>'reportes/pagos_ultimo_mes','class'=>'navbar-form navbar-left','role'=>'search'])  !!}
        {{ Form::input('number', 'search', null, array('class'=>'form-control', 'placeholder'=>'Documento')) }}
        {{ Form::submit('Buscar', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}
    
      <div class="alert alert-info">{{$message}}</div>
      
  </div>

  <div class="table-inverse table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr style="background-color:lightgrey; font-weight:bold">
                <td>Fecha pago</td>
                <td>Documento</td>
                <td>Usuario</td>
                <td>Fecha deuda</td>
                <td>Valor pagado</td>
                <td>Estado deuda</td>
                <!--td colspan="2"></td-->
            </tr>
        </thead>
        <tbody>
        @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago->created_at }}</td>
                <td>{{ $pago->deuda->user->id_tipo }}</td>
                <td>{{ $pago->deuda->user->name }}</td><!--td>{{ $pago->id_usuario }}</td-->
                <td>{{ $pago->deuda->created_at }}</td>
                <td>${{ number_format($pago->valor) }}</td>
                <td>{{ $pago->deuda->estado }}</td>
            </tr>
        @endforeach
          <tr style="background-color:lightgrey; font-weight:bold">
            <td><h4>Total</h4></td>
            <td colspan='3'></td>
            <td><h4>${{ number_format($total_pagado) }}</h4></td>
            <td colspan='1'></td>
          </tr>
        </tbody>
    </table>
  </div>
</div>
@endsection