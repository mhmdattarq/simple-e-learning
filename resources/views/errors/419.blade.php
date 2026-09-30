@extends('errors.layout')

@section('title', 'Sesi Telah Berakhir')
@section('code', '419')
@section('icon', 'ri-time-line')
@section('badge-icon', 'ri-history-line')
@section('icon-bg', 'bg-info-subtle text-info')

@section('message')
    Sesi aktif browser Anda telah kedaluwarsa demi alasan keamanan. Silakan muat ulang halaman atau masuk kembali ke akun Anda.
@endsection
