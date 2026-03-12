@extends('layouts.app')

@section('content')
    <div class="px-4 py-4 sm:ml-64">
        <div class="mb-4 sm:p-8 bg-white border border-gray-200 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
        <div class="mb-2 sm:p-8 bg-white border border-gray-200 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
