<?php

use App\Permission;
use App\ResourceCategory;
use App\ResourceTag;
use App\Role;
use Illuminate\Database\Seeder;

class ResourcesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 * @return void
	 */
	public function run()
	{
		// Insert the permissions
		$role_member    = Role::where('name', 'member')->first()->id;
		$role_committee = Role::where('name', 'committee')->first()->id;
		$role_associate = Role::where('name', 'associate')->first()->id;
		$role_su        = Role::where('name', 'su')->first()->id;
		Permission::create(['name' => 'resources.registered', 'display_name' => 'Registered Users', 'description' => 'All users with an enabled account.'])
		          ->roles()->sync([$role_member, $role_committee, $role_associate, $role_su]);
		Permission::create(['name' => 'resources.member', 'display_name' => 'BTS Members', 'description' => 'All backstage members.'])
		          ->roles()->sync([$role_member, $role_associate, $role_committee]);
		Permission::create(['name' => 'resources.committee', 'display_name' => 'Committee Only', 'description' => 'Committee members only.'])
		          ->roles()->sync([$role_committee]);

		// Insert the default categories
		ResourceCategory::create(['name' => 'Standing Risk Assessments', 'slug' => 'standing-risk-assessments', 'flag' => null]);
		ResourceCategory::create(['name' => 'Standing Method Statements', 'slug' => 'standing-method-statements', 'flag' => null]);
		ResourceCategory::create(['name' => 'Risk Assessments', 'slug' => 'risk-assessments', 'flag' => ResourceCategory::FLAG_RISK_ASSESSMENT]);
		ResourceCategory::create(['name' => 'Event Reports', 'slug' => 'event-reports', 'flag' => ResourceCategory::FLAG_EVENT_REPORT]);
		ResourceCategory::create(['name' => 'Meeting Agendas', 'slug' => 'meeting-agendas', 'flag' => ResourceCategory::FLAG_MEETING_AGENDA]);
		ResourceCategory::create(['name' => 'Meeting Minutes', 'slug' => 'meeting-minutes', 'flag' => ResourceCategory::FLAG_MEETING_MINUTES]);
		ResourceCategory::create(['name' => 'Guides', 'slug' => 'guides', 'flag' => null]);
		ResourceCategory::create(['name' => 'Training Material', 'slug' => 'training', 'flag' => null]);

		// Insert the default tags
		ResourceTag::create(['name' => 'BodySoc', 'slug' => 'bodysoc']);
		ResourceTag::create(['name' => 'BUST', 'slug' => 'bust']);
		ResourceTag::create(['name' => 'BUSMS', 'slug' => 'busms']);
		ResourceTag::create(['name' => 'ICIA', 'slug' => 'icia']);
		ResourceTag::create(['name' => 'Edge', 'slug' => 'edge']);
		ResourceTag::create(['name' => 'Culturals', 'slug' => 'culturals']);
		ResourceTag::create(['name' => 'Off Campus', 'slug' => 'off-campus']);
		ResourceTag::create(['name' => 'On Campus', 'slug' => 'on-campus']);
		ResourceTag::create(['name' => 'External', 'slug' => 'external']);
		ResourceTag::create(['name' => 'Students\' Union', 'slug' => 'students-union']);
		ResourceTag::create(['name' => 'Club Nights', 'slug' => 'club-nights']);
		ResourceTag::create(['name' => 'Coffee House', 'slug' => 'coffee-house']);
		ResourceTag::create(['name' => 'Bars', 'slug' => 'bars']);
		ResourceTag::create(['name' => 'RAG', 'slug' => 'rag']);
		ResourceTag::create(['name' => 'University Hall', 'slug' => 'university-hall']);
		ResourceTag::create(['name' => 'ALT', 'slug' => 'alt']);
		ResourceTag::create(['name' => 'Weston Studio', 'slug' => 'weston-studio']);
		ResourceTag::create(['name' => 'Founders\' Hall', 'slug' => 'founders-hall']);
		ResourceTag::create(['name' => 'Museum of Bath at Work', 'slug' => 'mobaw']);
		ResourceTag::create(['name' => 'Mission Theatre', 'slug' => 'mission-theatre']);
		ResourceTag::create(['name' => 'Assembly Rooms', 'slug' => 'assembly-rooms']);
		ResourceTag::create(['name' => 'Claverton Rooms', 'slug' => 'claverton-rooms']);
		ResourceTag::create(['name' => 'Pump Rooms', 'slug' => 'pump-rooms']);
		ResourceTag::create(['name' => 'Guildhall', 'slug' => 'guildhall']);
		ResourceTag::create(['name' => 'STV', 'slug' => 'stv']);
		ResourceTag::create(['name' => 'Sports Exec', 'slug' => 'sports-exec']);
		ResourceTag::create(['name' => 'The Pavilion', 'slug' => 'pavilion']);
		ResourceTag::create(['name' => 'AGM', 'slug' => 'agm']);
		ResourceTag::create(['name' => 'EGM', 'slug' => 'egm']);
		ResourceTag::create(['name' => 'Crew Meeting', 'slug' => 'crew-meeting']);
		ResourceTag::create(['name' => 'Committee Meeting', 'slug' => 'committee-meeting']);
		ResourceTag::create(['name' => 'Website', 'slug' => 'website']);
		ResourceTag::create(['name' => 'Plug /   Tub', 'slug' => 'plug-tub']);
		ResourceTag::create(['name' => 'SU Level 3', 'slug' => 'level-3']);
		ResourceTag::create(['name' => 'Pyrotechnics', 'slug' => 'pyro']);
		ResourceTag::create(['name' => 'ChaOS', 'slug' => 'chaos']);
		ResourceTag::create(['name' => 'Departmental', 'slug' => 'departmental']);
		ResourceTag::create(['name' => 'BOU', 'slug' => 'bou']);
		ResourceTag::create(['name' => 'BREAK / Elemental', 'slug' => 'break']);
		ResourceTag::create(['name' => 'Election / By-Election', 'slug' => 'election']);
		ResourceTag::create(['name' => 'SU Level 2', 'slug' => 'level-2']);
		ResourceTag::create(['name' => 'Latin & Ballroom', 'slug' => 'bulbs']);
		ResourceTag::create(['name' => 'MusicSoc', 'slug' => 'musicsoc']);
	}
}
