@extends('emails.layout')

@section('title', 'Welcome to Opportunity Hub')
@section('preheader', 'Thank you for registering on Opportunity Hub.')
@section('heading', 'Welcome to Opportunity Hub')

@section('content')
    <p style="margin:0 0 12px;">Dear {{ trim(($user->firstname ?? '').' '.($user->lastname ?? '')) ?: ($user->full_name ?? 'User') }},</p>

    <p style="margin:0;">Thank you for registering on Opportunity Hub. Please activate your account using the button below.</p>

    @include('emails.partials.button', ['url' => url('/activate-account/'.$user->email), 'label' => 'Activate Account'])
@endsection
