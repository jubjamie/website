<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\View;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class GalleryController extends Controller
{
	/**
	 * Display an index of all the FB galleries.
	 * @param \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb
	 * @return \Illuminate\Http\Response
	 */
	public function index(LaravelFacebookSdk $fb)
	{
		// Make the request for all the albums
		$response = $fb->get('/BackstageTechnicalServices/albums?fields=id,count,name,type&limit=500', $fb->getApp()->getAccessToken()->getValue());

		// Get the list of albums
		$albums = $response->getDecodedBody()['data'];

		// Remove any albums which aren't 'normal'
		foreach($albums as $key => $album) {
			if($album['type'] != 'normal') {
				unset($albums[$key]);
			}
		}
		array_filter($albums);

		// Render
		return View::make('gallery.index')->withAlbums($albums);
	}

	/**
	 * View an album.
	 * @param                                               $id
	 * @param \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb
	 * @return \Illuminate\Http\Response
	 */
	public function show($id, LaravelFacebookSdk $fb)
	{
		// Get the album details
		$fb->setDefaultAccessToken($fb->getApp()->getAccessToken()->getValue());
		$response = $fb->get("/{$id}?fields=id,count,name");
		$album    = $response->getDecodedBody();

		// Get the album photos
		$response = $fb->get("/{$id}/photos?fields=images,link,name,source&limit=500");
		$photos   = $response->getDecodedBody()['data'];

		return View::make('gallery.album')->withAlbum($album)->withPhotos($photos);
	}
}
