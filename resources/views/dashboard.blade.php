@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Global Supply Chain Risk Intelligence Platform</h1>
@stop

@section('content')

<div class="row">

<div class="col-lg-3 col-6">
<div class="small-box bg-info">
<div class="inner">
<h3>0</h3>
<p>Countries</p>
</div>
<div class="icon">
<i class="fas fa-globe"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-success">
<div class="inner">
<h3>0</h3>
<p>Suppliers</p>
</div>
<div class="icon">
<i class="fas fa-industry"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-warning">
<div class="inner">
<h3>0</h3>
<p>Shipments</p>
</div>
<div class="icon">
<i class="fas fa-ship"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-danger">
<div class="inner">
<h3>0</h3>
<p>High Risk</p>
</div>
<div class="icon">
<i class="fas fa-exclamation-triangle"></i>
</div>
</div>
</div>

</div>

<div class="card">
<div class="card-header">
<h3 class="card-title">Welcome</h3>
</div>

<div class="card-body">
Selamat datang di <b>Global Supply Chain Risk Intelligence Platform</b>.
</div>

</div>

@stop