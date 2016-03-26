<?php
/*
|--------------------------------------------------------------------------
| Training routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the training functions.
|
*/

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