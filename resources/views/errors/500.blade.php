@extends('errors.layout')

@section('title', 'Terjadi Gangguan Sistem')
@section('code', '500')
@section('icon', 'ri-server-line')
@section('badge-icon', 'ri-bug-line')
@section('icon-bg', 'bg-danger-subtle text-danger')

@section('message')
    Terjadi kendala teknis internal pada server. Tim pengelola teknis BKPSDM telah menerima pencatatan sistem dan sedang menindaklanjuti perbaikan ini.
@endsection
