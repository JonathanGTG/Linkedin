<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class RemainingTablesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $now = now()->toDateTimeString();

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Users (50 fake users)
        // ═══════════════════════════════════════════════════════════════
        $userIds = DB::table('users')->pluck('id')->toArray();
        for ($i = 0; $i < 50; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'avatar' => null,
                'headline' => $faker->jobTitle(),
                'role' => 'user',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $this->command->info('✓ 50 users created');

        // Get existing catalog data
        $courseIds = DB::table('courses')->pluck('id')->toArray();
        $videoIds = DB::table('videos')->pluck('id')->toArray();
        $chapterIds = DB::table('chapters')->pluck('id')->toArray();

        if (empty($courseIds) || empty($videoIds)) {
            $this->command->warn('No courses/videos found. Run DatabaseSeeder first.');
            return;
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Organizations (5)
        // ═══════════════════════════════════════════════════════════════
        $orgIds = [];
        foreach (['Acme Corp', 'TechVentures', 'EduGlobal', 'DataWorks', 'CloudNine'] as $name) {
            $orgIds[] = DB::table('organizations')->insertGetId([
                'name' => $name, 'status' => 'active',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Libraries (3)
        // ═══════════════════════════════════════════════════════════════
        $libIds = [];
        foreach ([['Jakarta Public Library','JPL','ID'],['NY Public Library','NYPL','US'],['London Library','LDN','GB']] as [$n,$c,$cc]) {
            $libIds[] = DB::table('libraries')->insertGetId([
                'name'=>$n,'library_code'=>$c,'country_code'=>$cc,'status'=>'active',
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Plans (4)
        // ═══════════════════════════════════════════════════════════════
        $planIds = [];
        foreach ([['free','Free','monthly',0],['pro-m','Pro Monthly','monthly',2999],['pro-y','Pro Yearly','yearly',23999],['team','Team','monthly',4999]] as [$code,$name,$period,$price]) {
            $planIds[] = DB::table('plans')->insertGetId([
                'code'=>$code,'name'=>$name,'billing_period'=>$period,'price_cents'=>$price,'currency'=>'USD',
                'created_at'=>$now,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Coupons (5)
        // ═══════════════════════════════════════════════════════════════
        $couponIds = [];
        foreach ([['WELCOME20',20,null],['SAVE50',50,null],['FLAT10',null,1000],['NEWYEAR',30,null],['STUDENT',40,null]] as [$code,$pct,$amt]) {
            $couponIds[] = DB::table('coupons')->insertGetId([
                'code'=>$code,'percent_off'=>$pct,'amount_off_cents'=>$amt,'currency'=>'USD',
                'starts_at'=>now()->subMonth(),'ends_at'=>now()->addMonths(6),'max_redemptions'=>1000,
                'created_at'=>$now,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Certificate Templates (3)
        // ═══════════════════════════════════════════════════════════════
        $certTemplateIds = [];
        foreach (['Standard Certificate','Professional Certificate','Course Completion'] as $name) {
            $certTemplateIds[] = DB::table('certificate_templates')->insertGetId([
                'name'=>$name,'template_html'=>'<div class="cert"><h1>'.$name.'</h1><p>{{user_name}}</p><p>{{course_title}}</p></div>',
                'created_at'=>$now,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Notification Templates (8)
        // ═══════════════════════════════════════════════════════════════
        $notifTemplateIds = [];
        foreach ([
            ['in_app','enrollment_complete'],['in_app','new_review'],['in_app','quiz_passed'],['in_app','certificate_ready'],
            ['email','welcome'],['email','enrollment_complete'],['push','new_course'],['push','reminder'],
        ] as [$ch,$code]) {
            $notifTemplateIds[] = DB::table('notification_templates')->insertGetId([
                'channel'=>$ch,'code'=>$code,
                'template_json'=>json_encode(['title'=>ucfirst(str_replace('_',' ',$code)),'body'=>'Your {{event}} notification']),
                'created_at'=>$now,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Recommendation Models (3), Storage Objects (50)
        // ═══════════════════════════════════════════════════════════════
        $recModelIds = [];
        foreach ([['collaborative_filter','v1'],['content_based','v2'],['hybrid','v1']] as [$n,$v]) {
            $recModelIds[] = DB::table('recommendation_models')->insertGetId([
                'name'=>$n,'version'=>$v,'created_at'=>$now,
            ]);
        }

        $storageIds = [];
        for ($i = 0; $i < 50; $i++) {
            $storageIds[] = DB::table('storage_objects')->insertGetId([
                'provider'=>$faker->randomElement(['s3','gcs','local']),
                'uri'=>'media/'.$faker->uuid().'.'.$faker->randomElement(['mp4','vtt','srt','pdf']),
                'mime_type'=>$faker->randomElement(['video/mp4','text/vtt','application/pdf']),
                'size_bytes'=>$faker->numberBetween(10000,500000000),
                'checksum'=>md5($faker->uuid()),
                'created_at'=>$now,
            ]);
        }
        $this->command->info('✓ Layer 1 done (orgs, libs, plans, coupons, templates, storage)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 2: Dependencies on Layer 1
        // ═══════════════════════════════════════════════════════════════

        // Auth identities
        foreach (array_slice($userIds, 0, 20) as $uid) {
            DB::table('auth_identities')->insert([
                'user_id'=>$uid,'provider'=>$faker->randomElement(['google','linkedin']),
                'provider_user_id'=>$faker->uuid(),'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // Org domains & members
        $orgMemberIds = [];
        foreach ($orgIds as $oi => $orgId) {
            DB::table('organization_domains')->insert([
                'organization_id'=>$orgId,'domain'=>strtolower(str_replace(' ','',$faker->company())).'.com',
                'verified_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
            ]);
            $members = array_slice($userIds, $oi * 8, 8);
            foreach ($members as $uid) {
                $orgMemberIds[] = DB::table('organization_members')->insertGetId([
                    'organization_id'=>$orgId,'user_id'=>$uid,'status'=>'active','joined_at'=>$now,
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Org teams
        $teamIds = [];
        foreach ($orgIds as $orgId) {
            foreach (['Engineering','Marketing'] as $t) {
                $teamIds[] = DB::table('organization_teams')->insertGetId([
                    'organization_id'=>$orgId,'name'=>$t,'created_at'=>$now,
                ]);
            }
        }

        // Org SSO
        foreach ($orgIds as $orgId) {
            DB::table('org_sso_configs')->insert([
                'organization_id'=>$orgId,'sso_type'=>'saml','metadata_json'=>json_encode(['entity_id'=>'https://sso.example.com']),
                'enabled'=>true,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // Library branches & patrons
        $patronIds = [];
        foreach ($libIds as $li => $libId) {
            DB::table('library_branches')->insert([
                'library_id'=>$libId,'name'=>'Main Branch','address_json'=>json_encode(['city'=>$faker->city()]),
                'created_at'=>$now,
            ]);
            $patUsers = array_slice($userIds, $li * 5, 5);
            foreach ($patUsers as $uid) {
                $patronIds[] = DB::table('library_patrons')->insertGetId([
                    'library_id'=>$libId,'user_id'=>$uid,'patron_external_id'=>'PAT-'.$faker->numerify('####'),
                    'created_at'=>$now,
                ]);
            }
        }

        // Devices
        $deviceIds = [];
        foreach (array_slice($userIds, 0, 30) as $uid) {
            $deviceIds[] = DB::table('devices')->insertGetId([
                'user_id'=>$uid,'device_type'=>$faker->randomElement(['desktop','mobile','tablet']),
                'device_fingerprint_hash'=>md5($uid.$faker->uuid()),
                'created_at'=>$now,
            ]);
        }

        // Media assets
        $mediaIds = [];
        foreach (array_slice($storageIds, 0, 30) as $sid) {
            $mediaIds[] = DB::table('media_assets')->insertGetId([
                'storage_object_id'=>$sid,'duration_seconds'=>$faker->numberBetween(60,3600),
                'width'=>1920,'height'=>1080,'created_at'=>$now,
            ]);
        }

        // Plan features
        foreach ($planIds as $pi => $pid) {
            foreach ([['offline_access',$pi>0?'true':'false'],['certificate',$pi>0?'true':'false'],['max_courses',$pi==0?'5':'unlimited']] as [$fc,$fv]) {
                DB::table('plan_features')->insert(['plan_id'=>$pid,'feature_code'=>$fc,'feature_value'=>$fv,'created_at'=>$now,'updated_at'=>$now]);
            }
        }

        // Entitlements
        $entIds = [];
        for ($i = 0; $i < 10; $i++) {
            $entIds[] = DB::table('entitlements')->insertGetId([
                'resource_type'=>$faker->randomElement(['course','topic']),
                'resource_id'=>$faker->randomElement($courseIds),
                'starts_at'=>now()->subMonths(3),'ends_at'=>now()->addYear(),
                'created_at'=>$now,
            ]);
        }

        // Subscriptions
        $subIds = [];
        foreach (array_slice($userIds, 0, 20) as $uid) {
            $subIds[] = DB::table('subscriptions')->insertGetId([
                'subject_type'=>'user','subject_id'=>$uid,'plan_id'=>$faker->randomElement($planIds),
                'status'=>$faker->randomElement(['active','trialing','canceled']),
                'trial_ends_at'=>now()->addDays(14),'current_period_start'=>now()->subMonth(),
                'current_period_end'=>now()->addMonth(),'cancel_at_period_end'=>false,
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // Consents & Data Requests
        foreach (array_slice($userIds, 0, 30) as $uid) {
            foreach (['marketing','analytics','cookies'] as $ct) {
                DB::table('consents')->insert([
                    'user_id'=>$uid,'consent_type'=>$ct,'granted'=>$faker->boolean(80),
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        foreach (array_slice($userIds, 0, 5) as $uid) {
            DB::table('data_requests')->insert([
                'user_id'=>$uid,'request_type'=>$faker->randomElement(['export','deletion']),
                'status'=>'completed','completed_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 2 done (auth, orgs, libs, devices, media, subs, consents)');

        // Call part 2
        $this->call(RemainingTablesSeederPart2::class, false, [
            'userIds' => $userIds,
            'courseIds' => $courseIds,
            'videoIds' => $videoIds,
            'orgIds' => $orgIds,
            'orgMemberIds' => $orgMemberIds,
            'teamIds' => $teamIds,
            'libIds' => $libIds,
            'patronIds' => $patronIds,
            'entIds' => $entIds,
            'planIds' => $planIds,
            'subIds' => $subIds,
            'couponIds' => $couponIds,
            'certTemplateIds' => $certTemplateIds,
            'notifTemplateIds' => $notifTemplateIds,
            'recModelIds' => $recModelIds,
            'storageIds' => $storageIds,
            'mediaIds' => $mediaIds,
            'deviceIds' => $deviceIds,
        ]);
    }
}
