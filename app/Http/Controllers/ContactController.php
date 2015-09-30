<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\ContactAccidentRequest;
use App\Http\Requests\ContactBookRequest;
use App\Http\Requests\ContactEnquiryRequest;
use App\Http\Requests\ContactFeedbackRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class ContactController extends Controller
{
	/**
	 * Set up the middleware.
	 */
	public function __construct()
	{
		$this->middleware('auth', [
			'only' => [
				'getAccident',
				'postAccident',
			],
		]);
		parent::__construct();
	}

	/**
	 * Display the enquiries form.
	 * @return  \Illuminate\Http\Response
	 */
	public function getEnquiries()
	{
		return View::make('contact.enquiries');
	}

	/**
	 * Process the enquiries form.
	 * @param   ContactEnquiryRequest $request
	 * @return  \Illuminate\Http\Response
	 */
	public function postEnquiries(ContactEnquiryRequest $request)
	{
		// Strip tags and create array of necessary data
		$forename = explode(' ', $request->get('name'));
		$data     = $request->only('name', 'email', 'phone') + [
				'content'  => $request->stripped('message'),
				'forename' => array_shift($forename),
			];

		// Send the enquiry
		Mail::queue('emails.contact.enquiry', $data, function ($message) use ($data) {
			$message->to('bts@bath.ac.uk')
			        ->from($data['email'], $data['name'])
			        ->subject('General enquiry');
		});

		// Send the confirmation
		Mail::queue('emails.contact.enquiry_receipt', $data, function ($message) use ($data) {
			$message->to($data['email'], $data['name'])
			        ->subject('Your enquiry to BTS');
		});

		// Flash message
		Flash::success('Enquiry sent', 'You should receive a confirmation soon.');

		return redirect(route('home'));
	}

	/**
	 * Display the book form.
	 * @return  \Illuminate\Http\Response
	 */
	public function getBook()
	{
		return View::make('contact.book');
	}

	/**
	 * Process the book form.
	 * @param   ContactBookRequest $request
	 * @return  \Illuminate\Http\Response
	 */
	public function postBook(ContactBookRequest $request)
	{
		// Get the posted data
		$data = $request->stripped('event_name', 'event_venue', 'event_description', 'event_dates', 'show_time', 'event_access', 'event_club', 'contact_name',
			'contact_email', 'contact_phone', 'additional');

		// Send the booking request
		Mail::queue('emails.contact.book', $data, function ($message) use ($data) {
			$message->to('bts@bath.ac.uk')
			        ->from($data['contact_email'], $data['contact_name'])
			        ->subject("Booking Request - {$data['event_name']} ({$data['event_dates']})");
		});

		// Send the receipt
		Mail::queue('emails.contact.book_receipt', $data, function ($message) use ($data) {
			$message->to($data['contact_email'], $data['contact_name'])
			        ->from('bts@bath.ac.uk', 'Backstage Technical Services')
			        ->subject("Booking Request Receipt - {$data['event_name']} ({$data['event_dates']})");
		});

		// Flash message
		Flash::success('Booking request sent', 'You should receive a receipt of your booking shortly.');

		return redirect(route('home'));
	}

	/**
	 * Get the booking terms and conditions.
	 * @param   \Illuminate\Http\Request $request
	 * @return  Response
	 */
	public function getBookTerms(Request $request)
	{
		return $request->ajax()
			? View::make('partials.app.modal.inner', [
				'content' => View::make('contact._book_terms'),
				'header'  => 'Terms and Conditions for the Provision of Services',
				'footer'  => '<div class="text-center"><button class="btn btn-success" id="btn_terms_accept" style="margin: 0;"><span class="fa fa-check"></span><span>I have read and accept these terms</span></button></div>',
			])
			:
			View::make('contact.book_terms');
	}

	/**
	 * Display the feedback form.
	 * @return  \Illuminate\Http\Response
	 */
	public function getFeedback()
	{
		return View::make('contact.feedback');
	}

	/**
	 * Process the feedback form.
	 * @param   ContactFeedbackRequest $request
	 * @return  \Illuminate\Http\Response
	 */
	public function postFeedback(ContactFeedbackRequest $request)
	{
		// Strip the tags
		$data = $request->stripped('event', 'feedback');

		// Send the email
		Mail::queue('emails.contact.feedback', $data, function ($message) {
			$message->to('bts@bath.ac.uk')
			        ->subject('Event feedback');
		});

		// Flash message
		Flash::success('Feedback sent');

		return redirect(route('home'));
	}

	/**
	 * Display the accident report form.
	 * @return \Illuminate\Http\Response
	 */
	public function getAccident()
	{
		return View::make('contact.accident');
	}

	/**
	 * Process the accident report form.
	 * @param ContactAccidentRequest $request
	 * @return \Illuminate\Http\Request
	 */
	public function postAccident(ContactAccidentRequest $request)
	{
		// Get data for emails
		$data = $request->stripped('location', 'date', 'time', 'details', 'severity', 'absence_details', 'injured_name', 'contact_name', 'contact_email', 'contact_phone', 'person_type', 'person_type_other');

		// Send the email
		Mail::queue('emails.contact.accident', $data, function ($message) use ($data) {
			$message->to('safety@bts-crew.com')
			        ->to('bts@bath.ac.uk')
			        ->to('P.Hawker@bath.ac.uk')
			        ->to('A.J.Fleet@bath.ac.uk')
			        ->subject('** BTS Accident Report **')
			        ->from($data['contact_email'], $data['contact_name']);

		});

		// Send the receipt
		Mail::queue('emails.contact.accident_receipt', $data, function ($message) use ($data) {
			$message->to($data['contact_email'], $data['contact_name'])
			        ->from('safety@bts-crew.com', 'Backstage')
			        ->subject('** BTS Accident Report Receipt **');
		});

		// Flash
		Flash::success('Accident report sent', 'You should receive a receipt shortly.');

		return redirect(route('home'));
	}
}
