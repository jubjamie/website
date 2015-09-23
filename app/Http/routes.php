<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Home page
Route::get('/', [
	'as' => 'home',
	function () {
		return App::make('App\Http\Controllers\PagesController')->show('home');
	},
]);

// Committee
Route::group([
	'prefix' => 'committee',
], function () {
	// View
	Route::get('', [
		'as'   => 'committee.view',
		'uses' => 'CommitteeController@view',
	]);
	// Add
	Route::post('add', [
		'as'   => 'committee.add',
		'uses' => 'CommitteeController@store',
	]);
	// Edit
	Route::post('edit', [
		'as'   => 'committee.edit',
		'uses' => 'CommitteeController@update',
	]);
	// Delete
	Route::post('delete', [
		'as'   => 'committee.delete',
		'uses' => 'CommitteeController@destroy',
	]);
});

// Contact forms
Route::group([
	'prefix' => 'contact',
], function () {
	// Enquiries
	Route::get('enquiries', [
		'as'   => 'contact.enquiries',
		'uses' => 'ContactController@getEnquiries',
	]);
	Route::post('enquiries', [
		'as'   => 'contact.enquiries.do',
		'uses' => 'ContactController@postEnquiries',
	]);
	// Book
	Route::get('book', [
		'as'   => 'contact.book',
		'uses' => 'ContactController@getBook',
	]);
	Route::post('book', [
		'as'   => 'contact.book.do',
		'uses' => 'ContactController@postBook',
	]);
	// Book T&Cs
	Route::get('book/terms', [
		"as"   => "contact.book.terms",
		"uses" => "ContactController@getBookTerms",
	]);
	// Feedback
	Route::get('feedback', [
		'as'   => 'contact.feedback',
		'uses' => 'ContactController@getFeedback',
	]);
	Route::post('feedback', [
		'as'   => 'contact.feedback.do',
		'uses' => 'ContactController@postFeedback',
	]);
	// Report accident
	Route::get('accident', [
		'as'   => 'contact.accident',
		'uses' => 'ContactController@getAccident',
	]);
	Route::post('accident', [
		'as'   => 'contact.accident.do',
		'uses' => 'ContactController@postAccident',
	]);
});

// Equipment
Route::group([
	'prefix' => 'equipment',
], function () {
	// Dashboard
	Route::get('', [
		'as'   => 'equipment.dash',
		'uses' => 'EquipmentController@dash',
	]);
	// Asset register
	Route::get('asset', [
		'as'   => 'equipment.assets',
		'uses' => 'EquipmentController@assetRegister',
	]);
	// Repairs DB
	Route::group([
		'prefix' => 'repairs',
	], function () {
		// List
		Route::get('', [
			'as'   => 'equipment.repairs',
			'uses' => 'EquipmentController@repairsDb',
		]);
		// Add
		Route::get('add', [
			'as'   => 'equipment.repairs.add',
			'uses' => 'EquipmentController@getAddRepair',
		]);
		Route::post('add', [
			'as'   => 'equipment.repairs.add.do',
			'uses' => 'EquipmentController@postAddRepair',
		]);
		// View/edit
		Route::get('{id}', [
			'as'   => 'equipment.repairs.view',
			'uses' => 'EquipmentController@view',
		])->where('id', '[\d]+');
		Route::post('{id}', [
			'as'   => 'equipment.repairs.view.do',
			'uses' => 'EquipmentController@update',
		])->where('id', '[\d]+');
	});
});

// Events diary
Route::group([
	'prefix' => 'events',
], function () {
	// Index
	Route::get('', [
		'as'   => 'events.index',
		'uses' => 'EventsController@index',
	]);
	// Diary
	Route::get('diary/{year?}/{month?}', [
		'as'   => 'events.diary',
		'uses' => 'EventsController@diary',
	])->where('year', '[\d]{4}')->where('month', '[\d]{1,2}');
	// View
	Route::group([
		'prefix' => '{id}',
		'where'  => ['id' => '[\d]+'],
	], function () {
		Route::get('', [
			'as'   => 'events.view',
			'uses' => 'EventsController@view',
		]);
		Route::post('volunteer', [
			'as'   => 'events.volunteer',
			'uses' => 'EventsController@toggleVolunteer',
		]);
		Route::post('delete', [
			'as'   => 'events.delete',
			'uses' => 'EventsController@destroy',
		]);
		Route::post('email', [
			'as'   => 'events.email',
			'uses' => 'EventsController@emailCrew',
		]);
		Route::post('{action}', [
			'as'   => 'events.update',
			'uses' => 'EventsController@update',
		]);
		Route::get('finance-email', [
			'uses' => 'EventsController@sendFinanceEmail',
		]);
	});
	// Add
	Route::get('add', [
		'as'   => 'events.add',
		'uses' => 'EventsController@create',
	]);
	Route::post('add', [
		'as'   => 'events.add.do',
		'uses' => 'EventsController@store',
	]);
	// Signup
	Route::get('signup/{tab?}', [
		'as'   => 'events.signup',
		'uses' => 'EventsController@signup',
	])->where('tab', 'em|crew');
	// Member diary
	Route::get('diary/{username}/{year?}/{month?}', [
		'as'   => 'events.memberdiary',
		'uses' => 'EventsController@memberDiary',
	])->where('username', '[\w]+')->where('year', '[\d]{4}')->where('month', '[\d]{1,2}');
	// My diary
	Route::get('my-diary/{year?}/{month?}', [
		'as'   => 'events.mydiary',
		'uses' => 'EventsController@myDiary',
	])->where('year', '[\d]{4}')->where('month', '[\d]{1,2}');
	// Export
	Route::get('export', [
		'as'   => 'events.export',
		'uses' => 'EventsController@export',
	]);
});

// Gallery
Route::group([
	'prefix' => 'gallery',
], function () {
	// Index
	Route::get('', [
		'as'   => 'gallery.index',
		'uses' => 'GalleryController@index',
	]);
	// Album
	Route::get('album/{id}', [
		'as'   => 'gallery.album',
		'uses' => 'GalleryController@show',
	]);
});

// Members
Route::group([
	'middleware' => 'auth',
	'prefix'     => 'members',
], function () {
	// Dashboard
	Route::get('', [
		'as'   => 'members.index',
		'uses' => function () {
			return redirect(route('members.dash'));
		},
	]);
	Route::get('dash', [
		'as'   => 'members.dash',
		'uses' => 'MembersController@dash',
	]);
	// Profile
	Route::get('profile/{username}/{tab?}', [
		'as'   => 'members.profile',
		'uses' => 'MembersController@profile',
	])->where('username', '[\w]+')
	     ->where('tab', 'profile|events|training');
	// My profile
	Route::get('my-profile/{tab?}', [
		'as'   => 'members.myprofile',
		'uses' => 'MembersController@getMyProfile',
	])->where('tab', 'profile|events|training');
	Route::post('my-profile', [
		'as'   => 'members.myprofile.do',
		'uses' => 'MembersController@postMyProfile',
	]);
	Route::post('my-profile/password', [
		'as'   => 'members.myprofile.password',
		'uses' => 'MembersController@updatePassword',
	]);
});

// SU dashboard
Route::get('su-dash', [
	'as'   => 'su.dash',
	'uses' => 'MembersController@dashSU',
]);

// Membership
Route::get('membership', [
	'as'   => 'membership',
	'uses' => 'MembersController@membership',
]);

// Pages
Route::group([
	'prefix' => 'page',
], function () {
	// List
	Route::get('', [
		'as'   => 'page.index',
		'uses' => 'PagesController@index',
	]);
	// Create
	Route::get('create', [
		'as'   => 'page.create',
		'uses' => 'PagesController@create',
	]);
	Route::post('create', [
		'as'   => 'page.store',
		'uses' => 'PagesController@store',
	]);
	Route::group([
		'prefix' => '{slug}',
		'where'  => ['slug' => '[\w-]+'],
	], function () {
		// View
		Route::get('', [
			'as'   => 'page.show',
			'uses' => 'PagesController@show',
		]);
		// Delete
		Route::get('delete', [
			'as'   => 'page.destroy',
			'uses' => 'PagesController@destroy',
		]);
		// Edit
		Route::get('edit', [
			'as'   => 'page.edit',
			'uses' => 'PagesController@edit',
		]);
		Route::post('edit', [
			'as'   => 'page.update',
			'uses' => 'PagesController@update',
		]);
	});
});

// Polls
Route::group([
	'prefix' => 'polls',
], function () {
	Route::get('', [
		'as'   => 'polls.index',
		'uses' => 'PollsController@index',
	]);
	Route::group([
		'prefix' => 'create',
	], function () {
		Route::get('', [
			'as'   => 'polls.create',
			'uses' => 'PollsController@create',
		]);
		Route::post('', [
			'as'   => 'polls.store',
			'uses' => 'PollsController@store',
		]);
		Route::post('addOption', [
			'as'   => 'polls.store.addOption',
			'uses' => 'PollsController@addOption',
		]);
		Route::post('delOption', [
			'as'   => 'polls.store.delOption',
			'uses' => 'PollsController@deleteOption',
		]);
	});
	Route::group([
		'prefix' => '{id}',
		'where'  => ['id' => '[\d]+'],
	], function () {
		Route::get('', [
			'as'   => 'polls.view',
			'uses' => 'PollsController@show',
		]);
		Route::post('vote', [
			'as'   => 'polls.vote',
			'uses' => 'PollsController@castVote',
		]);
		Route::get('delete', [
			'as'   => 'polls.delete',
			'uses' => 'PollsController@delete',
		]);
	});
});

// Training
Route::group([
	'prefix' => 'training',
], function () {
	// Dash
	Route::get('', [
		'as'   => 'training.dash',
		'uses' => function () {
			return redirect(route('training.skills.index'));
		},
	]);
	// Categories
	Route::group([
		'prefix' => 'categories',
	], function () {
		// Create
		Route::post('add', [
			'as'   => 'training.category.add',
			'uses' => 'TrainingController@storeCategory',
		]);
		// Update
		Route::post('{id}/update', [
			'as'   => 'training.category.update',
			'uses' => 'TrainingController@updateCategory',
		])->where('id', '[\d]+');
		// Delete
		Route::post('{id}/delete', [
			'as'   => 'training.category.delete',
			'uses' => 'TrainingController@destroyCategory',
		])->where('id', '[\d]+');
	});
	// Skills
	Route::group([
		'prefix' => 'skills',
	], function () {
		// Index
		Route::get('', [
			'as'   => 'training.skills.index',
			'uses' => 'TrainingController@indexSkills',
		]);
		// Create
		Route::get('add', [
			'as'   => 'training.skills.add',
			'uses' => 'TrainingController@createSkill',
		]);
		Route::post('add', [
			'as'   => 'training.skills.add.do',
			'uses' => 'TrainingController@storeSkill',
		]);
		// View
		Route::get('{id}', [
			'as'   => 'training.skills.view',
			'uses' => 'TrainingController@viewSkill',
		])->where('id', '[\d]+');
		// Update
		Route::post('{id}/update', [
			'as'   => 'training.skills.update',
			'uses' => 'TrainingController@updateSkill',
		])->where('id', '[\d]+');
		// Delete
		Route::post('{id}/delete', [
			'as'   => 'training.skills.delete',
			'uses' => 'TrainingController@destroySkill',
		])->where('id', '[\d]+');
		// Propose
		Route::post('propose', [
			'as'   => 'training.skills.propose',
			'uses' => 'TrainingController@proposeSkill',
		]);
		// Review proposal
		Route::get('proposal', [
			'as'   => 'training.skills.proposal.index',
			'uses' => 'TrainingController@indexProposal',
		]);
		Route::get('proposal/{id}', [
			'as'   => 'training.skills.proposal.view',
			'uses' => 'TrainingController@viewProposal',
		])->where('id', '[\d]+');
		Route::post('proposal/{id}', [
			'as'   => 'training.skills.proposal.do',
			'uses' => 'TrainingController@processProposal',
		])->where('id', '[\d]+');
		// Award skill
		Route::post('award', [
			'as'   => 'training.skills.award',
			'uses' => 'TrainingController@awardSkill',
		]);
		// Revoke
		Route::post('revoke', [
			'as'   => 'training.skills.revoke',
			'uses' => 'TrainingController@revokeSkill',
		]);
		// View log
		Route::get('log', [
			'as'   => 'training.skills.log',
			'uses' => 'TrainingController@viewSkillsLog',
		]);
	});
});

// Quotesboard
Route::group([
	"prefix" => "quotesboard",
], function () {
	Route::get('', [
		'as'   => 'quotes.index',
		'uses' => 'QuotesController@index',
	]);
	Route::post('add', [
		'as'   => 'quotes.add',
		'uses' => 'QuotesController@store',
	]);
	Route::post('delete', [
		'as'   => 'quotes.delete',
		'uses' => 'QuotesController@destroy',
	]);
});

// Users
Route::group([
	'prefix' => 'users',
], function () {
	// List
	Route::get('', [
		'as'   => 'user.index',
		'uses' => 'UsersController@index',
	]);
	Route::get('{modifier?}/{term?}', [
		'uses' => 'UsersController@index',
	])->where('modifier', 'filter|search');
	Route::post('', [
		'as'   => 'user.index.do',
		'uses' => 'UsersController@bulkUpdate',
	]);
	// Create
	Route::get('create', [
		'as'   => 'user.create',
		'uses' => 'UsersController@create',
	]);
	Route::post('create', [
		'as'   => 'user.create.do',
		'uses' => 'UsersController@store',
	]);
	// View
	Route::get('{username}', [
		'as'   => 'user.view',
		'uses' => function ($username) {
			return redirect(route('members.profile', $username));
		},
	])->where('username', '[\w]+');
	// Edit
	Route::get('{username}/edit', [
		'as'   => 'user.edit',
		'uses' => 'UsersController@edit',
	])->where('username', '[\w]+');
	Route::post('{username}/edit', [
		'as'   => 'user.edit.do',
		'uses' => 'UsersController@update',
	])->where('username', '[\w]+');
});

// Authentication
Route::get('login', [
	'as'   => 'auth.login',
	'uses' => 'AuthController@getLogin',
]);
Route::post('login', [
	'as'   => 'auth.login.do',
	'uses' => 'AuthController@postLogin',
]);
Route::get('logout', [
	'as'   => 'auth.logout',
	'uses' => 'AuthController@getLogout',
]);
Route::group([
	'prefix' => 'password',
], function () {
	Route::get('email', [
		'as'   => 'pwd.email',
		'uses' => 'AuthController@getEmail',
	]);
	Route::post('email', [
		'as'   => 'pwd.email.do',
		'uses' => 'AuthController@postEmail',
	]);
	Route::get('reset/{token}', [
		'as'   => 'pwd.reset',
		'uses' => 'AuthController@getReset',
	]);
	Route::post('reset/{token}', [
		'as'   => 'pwd.reset.do',
		'uses' => 'AuthController@postReset',
	]);
});

// Easter eggs
Route::get('im/a/teapot', function () {
	App::abort(418);
});
Route::get('feeling-happy', [
	'middleware' => 'auth.permission:member',
	'uses'       => function () {
		return View::make('eggs.awesome')->with([
			'noNav'      => true,
			'slimFooter' => true,
		]);
	},
]);