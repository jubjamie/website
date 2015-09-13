@extends('emails.base')
@include('emails.partials.table')

@section('title', 'Alison,')

@section('content')
    <p>This email has been generated to inform you that Backstage has accepted a new off campus booking with an external client. The details are as follows:</p>
    <table @yield('_table')>
        <tr>
            <td><strong>Event Name:</strong></td>
            <td>{{ $event_name }}</td>
        </tr>
        <tr>
            <td><strong>Event Date(s):</strong></td>
            <td>{{ $event_dates }}</td>
        </tr>
        <tr>
            <td><strong>Backstage Event Manager:</strong></td>
            <td>{!! $em !!}</td>
        </tr>
        <tr>
            <td><strong>Client:</strong></td>
            <td>{{ $client }}</td>
        </tr>
        <tr>
            <td><strong>Location:</strong></td>
            <td>{{ $venue_type }}</td>
        </tr>
        <tr>
            <td><strong>Venue:</strong></td>
            <td>{{ $venue }}</td>
        </tr>
        <tr>
            <td><strong>Additional Information:</strong></td>
            <td>{!! nl2br($description) !!}</td>
        </tr>
    </table>
    <p>If you have any questions about this event, reply to this email to speak to the Backstage Production Manager, or speak to any member of the Backstage Committee.</p>
@endsection