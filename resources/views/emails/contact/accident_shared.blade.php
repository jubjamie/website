@include('emails.partials.table')

<table @yield('_table')>
    <tr>
        <td><strong>Location:</strong></td>
        <td>{{ $location }}</td>
    </tr>
    <tr>
        <td><strong>Date:</strong></td>
        <td>{{ $date }}</td>
    </tr>
    <tr>
        <td><strong>Time:</strong></td>
        <td>{{ $time }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top"><strong>Details:</strong></td>
        <td>{!! nl2br($details) !!}</td>
    </tr>
    <tr>
        <td><strong>Severity:</strong></td>
        <td>
            {!! \App\Http\Requests\ContactAccidentRequest::$Severities[$severity] !!}
        </td>
    </tr>
    @if($absence_details)
        <tr>
            <td style="vertical-align: top"><strong>Absence Details:</strong></td>
            <td>{!! nl2br($absence_details) !!}</td>
        </tr>
    @endif
    <tr>
        <td><strong>Person injured:</strong></td>
        <td>{{ $injured_name }}</td>
    </tr>
    <tr>
        <td><strong>Contact person:</strong></td>
        <td>{{ $contact_name }}</td>
    </tr>
    <tr>
        <td><strong>Contact email:</strong></td>
        <td><a href="mailto:{{ $contact_email }}">{{ $contact_email }}</a></td>
    </tr>
    <tr>
        <td><strong>Contact phone:</strong></td>
        <td>{{ $contact_phone }}</td>
    </tr>
    <tr>
        <td><strong>Person category:</strong></td>
        <td>{{ $person_type == 'other' ? $person_type_other : \App\Http\Requests\ContactAccidentRequest::$PersonTypes[$person_type] }}</td>
    </tr>
</table>