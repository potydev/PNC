@extends('layout')
@section('breadcrumb-parent')
    <li>
        @section('breadcrumb-parent')
        <li class="inline-flex items-center">
            <a href="/data-master" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 22">
                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                 </svg>
               Pengaturan Jadwal
            </a>
        </li>
        @endsection
    </li>
@endsection
{{-- @section('breadcrumb-item')
    <li>
        <div class="flex items-center">
            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <span class="text-sm font-medium text-gray-500">Program Studi</span>
        </div>
    </li>
@endsection --}}

@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 p-5">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Program Studi</h1>
        @include('layouts.breadcrumb')
    </div>

    <div class="mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-gray-50 border border-gray-200">
    </div>
</div>

@endsection


