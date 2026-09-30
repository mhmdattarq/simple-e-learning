@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('icon', 'ri-compass-3-line')
@section('badge-icon', 'ri-file-search-line')
@section('icon-bg', 'bg-warning-subtle text-warning')

@section('message')
    {{ $exception->getMessage() ?: 'Halaman, kelas, atau berkas yang Anda tuju tidak ditemukan atau mungkin telah dipindahkan ke tautan lain.' }}
@endsection
