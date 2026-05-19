<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class RemainingTablesSeederPart2 extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $now = now()->toDateTimeString();

        $p = $this->command->getOutput(); // won't work via call with params
        // Retrieve params passed from Part1 - we'll re-query instead
        $userIds = DB::table('users')->pluck('id')->toArray();
        $courseIds = DB::table('courses')->pluck('id')->toArray();
        $videoIds = DB::table('videos')->pluck('id')->toArray();
        $orgIds = DB::table('organizations')->pluck('id')->toArray();
        $orgMemberIds = DB::table('organization_members')->pluck('id')->toArray();
        $teamIds = DB::table('organization_teams')->pluck('id')->toArray();
        $libIds = DB::table('libraries')->pluck('id')->toArray();
        $patronIds = DB::table('library_patrons')->pluck('id')->toArray();
        $entIds = DB::table('entitlements')->pluck('id')->toArray();
        $subIds = DB::table('subscriptions')->pluck('id')->toArray();
        $couponIds = DB::table('coupons')->pluck('id')->toArray();
        $certTemplateIds = DB::table('certificate_templates')->pluck('id')->toArray();
        $notifTemplateIds = DB::table('notification_templates')->pluck('id')->toArray();
        $recModelIds = DB::table('recommendation_models')->pluck('id')->toArray();
        $storageIds = DB::table('storage_objects')->pluck('id')->toArray();
        $mediaIds = DB::table('media_assets')->pluck('id')->toArray();
        $deviceIds = DB::table('devices')->pluck('id')->toArray();

        // ═══════════════════════════════════════════════════════════════
        // LAYER 3: Deeper dependencies
        // ═══════════════════════════════════════════════════════════════

        // Org team members
        if (!empty($teamIds) && !empty($orgMemberIds)) {
            foreach ($teamIds as $tid) {
                $members = $faker->randomElements($orgMemberIds, min(3, count($orgMemberIds)));
                foreach ($members as $mid) {
                    DB::table('organization_team_members')->insertOrIgnore([
                        'organization_team_id'=>$tid,'organization_member_id'=>$mid,'created_at'=>$now,'updated_at'=>$now,
                    ]);
                }
            }
        }

        // Library cards
        foreach ($patronIds as $pid) {
            DB::table('library_cards')->insert([
                'library_patron_id'=>$pid,'card_number_hash'=>md5('card-'.$pid),
                'status'=>'active','created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // Entitlement rules + assignments
        foreach ($entIds as $eid) {
            DB::table('entitlement_rules')->insert([
                'entitlement_id'=>$eid,'rule_type'=>'course_access',
                'rule_json'=>json_encode(['allow'=>'full']),'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        foreach (array_slice($userIds, 0, 10) as $i => $uid) {
            if (isset($entIds[$i])) {
                DB::table('user_entitlements')->insertOrIgnore([
                    'user_id'=>$uid,'entitlement_id'=>$entIds[$i],'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        foreach ($orgIds as $i => $oid) {
            if (isset($entIds[$i])) {
                DB::table('org_entitlements')->insertOrIgnore([
                    'organization_id'=>$oid,'entitlement_id'=>$entIds[$i],'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        foreach ($libIds as $i => $lid) {
            if (isset($entIds[$i])) {
                DB::table('library_entitlements')->insertOrIgnore([
                    'library_id'=>$lid,'entitlement_id'=>$entIds[$i],'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Seat pools & assignments
        $seatPoolIds = [];
        foreach ($orgIds as $oid) {
            $seatPoolIds[] = DB::table('seat_pools')->insertGetId([
                'organization_id'=>$oid,'name'=>'Default Pool','seat_count'=>50,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        foreach ($seatPoolIds as $si => $spid) {
            $members = DB::table('organization_members')->where('organization_id',$orgIds[$si])->pluck('id')->take(5);
            foreach ($members as $mid) {
                DB::table('seat_assignments')->insertOrIgnore([
                    'seat_pool_id'=>$spid,'organization_member_id'=>$mid,'status'=>'active',
                    'assigned_at'=>$now,'revoked_at'=>null,'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Media renditions
        foreach (array_slice($mediaIds, 0, 15) as $mid) {
            foreach ([['h264',2500,1920,1080],['h264',1200,1280,720],['h264',600,854,480]] as [$codec,$br,$w,$h]) {
                DB::table('media_renditions')->insert([
                    'media_asset_id'=>$mid,'codec'=>$codec,'bitrate'=>$br,'width'=>$w,'height'=>$h,
                    'uri'=>'renditions/'.$mid.'_'.$h.'p.mp4','created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Subscription coupons & invoices
        $invoiceIds = [];
        foreach (array_slice($subIds, 0, 10) as $si => $sid) {
            if (isset($couponIds[$si % count($couponIds)])) {
                DB::table('subscription_coupons')->insertOrIgnore([
                    'subscription_id'=>$sid,'coupon_id'=>$couponIds[$si % count($couponIds)],
                    'applied_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
            $invoiceIds[] = DB::table('invoices')->insertGetId([
                'subscription_id'=>$sid,'amount_cents'=>$faker->numberBetween(1000,5000),
                'currency'=>'USD','status'=>$faker->randomElement(['paid','open']),
                'issued_at'=>$now,'due_at'=>now()->addDays(30),'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 3 done (teams, cards, entitlements, seats, invoices)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 4-5: Video media, captions, transcripts, payments
        // ═══════════════════════════════════════════════════════════════

        // Video media
        foreach (array_slice($videoIds, 0, 20) as $i => $vid) {
            if (isset($mediaIds[$i])) {
                DB::table('video_media')->insertOrIgnore([
                    'video_id'=>$vid,'media_asset_id'=>$mediaIds[$i],'playback_policy'=>'signed',
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Captions & Transcripts
        $transcriptIds = [];
        foreach (array_slice($videoIds, 0, 15) as $i => $vid) {
            if (isset($storageIds[$i])) {
                DB::table('captions')->insert([
                    'video_id'=>$vid,'locale'=>'en','format'=>'vtt',
                    'storage_object_id'=>$storageIds[$i],'created_at'=>$now,'updated_at'=>$now,
                ]);
                $transcriptIds[] = DB::table('transcripts')->insertGetId([
                    'video_id'=>$vid,'locale'=>'en','storage_object_id'=>$storageIds[$i],
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Transcript segments
        foreach ($transcriptIds as $tid) {
            for ($s = 0; $s < 5; $s++) {
                DB::table('transcript_segments')->insert([
                    'transcript_id'=>$tid,'start_ms'=>$s*10000,'end_ms'=>($s+1)*10000,
                    'text'=>$faker->sentence(),'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Exercise files
        foreach (array_slice($courseIds, 0, 5) as $i => $cid) {
            DB::table('exercise_files')->insert([
                'course_id'=>$cid,'title'=>'Exercise Files - Module '.($i+1),
                'storage_object_id'=>$faker->randomElement($storageIds),
                'checksum'=>md5($cid),'size_bytes'=>$faker->numberBetween(50000,5000000),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // Invoice items & Payments & Refunds
        $paymentIds = [];
        foreach ($invoiceIds as $iid) {
            DB::table('invoice_items')->insert([
                'invoice_id'=>$iid,'description'=>'Monthly subscription','amount_cents'=>2999,'quantity'=>1,
                'created_at'=>$now,'updated_at'=>$now,
            ]);
            $paymentIds[] = DB::table('payments')->insertGetId([
                'invoice_id'=>$iid,'provider'=>'stripe','provider_payment_id'=>'pi_'.$faker->uuid(),
                'amount_cents'=>2999,'currency'=>'USD','status'=>'succeeded','created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        if (!empty($paymentIds)) {
            DB::table('refunds')->insert([
                'payment_id'=>$paymentIds[0],'amount_cents'=>2999,'status'=>'completed','created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 4-5 done (media, captions, transcripts, payments)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 6: Enrollments, Progress, Bookmarks, Notes, Reviews
        // ═══════════════════════════════════════════════════════════════

        // Enrollments (disable triggers temporarily to avoid issues)
        $enrollmentIds = DB::table('enrollments')->pluck('id')->toArray();
        $enrolledPairs = [];
        foreach (array_slice($userIds, 2, 30) as $uid) {
            $userCourses = $faker->randomElements($courseIds, min(3, count($courseIds)));
            foreach ($userCourses as $cid) {
                $key = $uid.'-'.$cid;
                if (isset($enrolledPairs[$key])) continue;
                $enrolledPairs[$key] = true;
                $status = $faker->randomElement(['saved','in_progress','in_progress','completed']);
                $enrollmentIds[] = DB::table('enrollments')->insertGetId([
                    'user_id'=>$uid,'course_id'=>$cid,'status'=>$status,
                    'enrolled_at'=>now()->subDays($faker->numberBetween(1,90)),
                    'completed_at'=>$status==='completed'?now()->subDays($faker->numberBetween(0,30)):null,
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Video progress
        foreach (array_slice($enrollmentIds, 0, 40) as $eid) {
            $enrollment = DB::table('enrollments')->find($eid);
            if (!$enrollment) continue;
            $courseVideos = DB::table('videos')->where('course_id',$enrollment->course_id)->pluck('id')->take(5);
            foreach ($courseVideos as $vid) {
                DB::table('video_progress')->insertOrIgnore([
                    'user_id'=>$enrollment->user_id,'video_id'=>$vid,
                    'detik_terakhir'=>$faker->numberBetween(30,600),'is_completed'=>$faker->boolean(60),
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Bookmarks
        foreach (array_slice($userIds, 0, 15) as $uid) {
            $vid = $faker->randomElement($videoIds);
            $cid = DB::table('videos')->where('id',$vid)->value('course_id');
            if ($cid) {
                DB::table('bookmarks')->insert([
                    'user_id'=>$uid,'course_id'=>$cid,'video_id'=>$vid,
                    'position_seconds'=>$faker->numberBetween(10,300),'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Notes
        foreach (array_slice($userIds, 0, 10) as $uid) {
            $vid = $faker->randomElement($videoIds);
            $cid = DB::table('videos')->where('id',$vid)->value('course_id');
            if ($cid) {
                DB::table('notes')->insert([
                    'user_id'=>$uid,'course_id'=>$cid,'video_id'=>$vid,
                    'position_seconds'=>$faker->numberBetween(10,300),
                    'note_text'=>$faker->paragraph(),'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        // Course reviews
        $reviewIds = [];
        $reviewPairs = [];
        foreach (array_slice($userIds, 2, 25) as $uid) {
            $cid = $faker->randomElement($courseIds);
            $key = $uid.'-'.$cid;
            if (isset($reviewPairs[$key])) continue;
            $reviewPairs[$key] = true;
            $reviewIds[] = DB::table('course_reviews')->insertGetId([
                'user_id'=>$uid,'course_id'=>$cid,'rating'=>$faker->numberBetween(3,5),
                'review_text'=>$faker->paragraph(),'status'=>'published',
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        // Review votes
        foreach (array_slice($reviewIds, 0, 15) as $rid) {
            $voters = $faker->randomElements($userIds, min(3, count($userIds)));
            foreach ($voters as $uid) {
                DB::table('review_votes')->insertOrIgnore([
                    'course_review_id'=>$rid,'user_id'=>$uid,
                    'vote'=>$faker->randomElement(['helpful','not_helpful']),
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        $this->command->info('✓ Layer 6 done (enrollments, progress, bookmarks, notes, reviews)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 7: Discussions
        // ═══════════════════════════════════════════════════════════════
        $threadIds = [];
        for ($i = 0; $i < 15; $i++) {
            $vid = $faker->randomElement($videoIds);
            $cid = DB::table('videos')->where('id',$vid)->value('course_id');
            if (!$cid) continue;
            $threadIds[] = DB::table('discussion_threads')->insertGetId([
                'course_id'=>$cid,'video_id'=>$vid,'user_id'=>$faker->randomElement($userIds),
                'title'=>$faker->sentence(),'status'=>'open','created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        $postIds = [];
        foreach ($threadIds as $tid) {
            for ($p = 0; $p < $faker->numberBetween(1,4); $p++) {
                $postIds[] = DB::table('discussion_posts')->insertGetId([
                    'discussion_thread_id'=>$tid,'user_id'=>$faker->randomElement($userIds),
                    'body'=>$faker->paragraph(),'status'=>'published','created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        foreach (array_slice($postIds, 0, 20) as $pid) {
            DB::table('discussion_reactions')->insertOrIgnore([
                'discussion_post_id'=>$pid,'user_id'=>$faker->randomElement($userIds),
                'reaction'=>$faker->randomElement(['like','helpful','thanks']),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 7 done (discussions)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 8: Quizzes
        // ═══════════════════════════════════════════════════════════════
        $quizIds = [];
        foreach (array_slice($videoIds, 0, 8) as $vid) {
            $quizIds[] = DB::table('quizzes')->insertGetId([
                'video_id'=>$vid,'title'=>'Quiz: '.$faker->sentence(3),
                'passing_score_percent'=>70,'time_limit_seconds'=>600,
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        $allOptionIds = [];
        foreach ($quizIds as $qid) {
            for ($q = 1; $q <= 3; $q++) {
                $qqid = DB::table('quiz_questions')->insertGetId([
                    'quiz_id'=>$qid,'question_type'=>'single_choice',
                    'question_text'=>$faker->sentence().'?','position'=>$q,'points'=>10,
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
                $optionIds = [];
                for ($o = 1; $o <= 4; $o++) {
                    $optionIds[] = DB::table('quiz_options')->insertGetId([
                        'quiz_question_id'=>$qqid,'option_text'=>$faker->sentence(),
                        'is_correct'=>$o===1,'position'=>$o,'created_at'=>$now,'updated_at'=>$now,
                    ]);
                }
                $allOptionIds[$qqid] = $optionIds;
            }
        }

        // Quiz attempts & answers
        foreach (array_slice($quizIds, 0, 5) as $qid) {
            foreach (array_slice($userIds, 2, 5) as $uid) {
                $attemptId = DB::table('quiz_attempts')->insertGetId([
                    'quiz_id'=>$qid,'user_id'=>$uid,'started_at'=>now()->subHours(2),
                    'completed_at'=>now()->subHour(),'score_percent'=>$faker->numberBetween(50,100),
                    'status'=>'completed','created_at'=>$now,'updated_at'=>$now,
                ]);
                $questions = DB::table('quiz_questions')->where('quiz_id',$qid)->pluck('id');
                foreach ($questions as $qqid) {
                    if (isset($allOptionIds[$qqid]) && !empty($allOptionIds[$qqid])) {
                        DB::table('quiz_answers')->insertOrIgnore([
                            'quiz_attempt_id'=>$attemptId,'quiz_question_id'=>$qqid,
                            'quiz_option_id'=>$faker->randomElement($allOptionIds[$qqid]),
                            'created_at'=>$now,'updated_at'=>$now,
                        ]);
                    }
                }
            }
        }

        $this->command->info('✓ Layer 8 done (quizzes)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 9: Certificates
        // ═══════════════════════════════════════════════════════════════
        $completedEnrollments = DB::table('enrollments')->where('status','completed')->pluck('id')->take(15);
        $certIds = [];
        foreach ($completedEnrollments as $eid) {
            $existing = DB::table('certificates')->where('enrollment_id',$eid)->exists();
            if (!$existing) {
                $certIds[] = DB::table('certificates')->insertGetId([
                    'enrollment_id'=>$eid,'certificate_template_id'=>$faker->randomElement($certTemplateIds),
                    'certificate_code'=>'CERT-'.strtoupper(Str::random(12)),
                    'issued_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        foreach (array_slice($certIds, 0, 5) as $cid) {
            DB::table('credential_shares')->insert([
                'certificate_id'=>$cid,'provider'=>$faker->randomElement(['linkedin','twitter']),
                'provider_ref'=>$faker->uuid(),'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 9 done (certificates)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 10: Analytics
        // ═══════════════════════════════════════════════════════════════
        $watchSessionIds = [];
        for ($i = 0; $i < 50; $i++) {
            $watchSessionIds[] = DB::table('watch_sessions')->insertGetId([
                'user_id'=>$faker->randomElement($userIds),'video_id'=>$faker->randomElement($videoIds),
                'device_id'=>!empty($deviceIds)?$faker->randomElement($deviceIds):null,
                'started_at'=>now()->subHours($faker->numberBetween(1,720)),
                'ended_at'=>now()->subHours($faker->numberBetween(0,1)),
                'total_watch_seconds'=>$faker->numberBetween(30,3600),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        foreach (array_slice($watchSessionIds, 0, 30) as $wsid) {
            $ws = DB::table('watch_sessions')->find($wsid);
            if (!$ws) continue;
            foreach (['play','pause','seek','complete'] as $et) {
                DB::table('playback_events')->insert([
                    'user_id'=>$ws->user_id,'video_id'=>$ws->video_id,'watch_session_id'=>$wsid,
                    'event_type'=>$et,'position_seconds'=>$faker->numberBetween(0,600),
                    'event_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        for ($i = 0; $i < 30; $i++) {
            DB::table('search_events')->insert([
                'user_id'=>$faker->randomElement($userIds),
                'query_text'=>$faker->randomElement(['python','javascript','leadership','data science','excel','agile']),
                'filters_json'=>json_encode(['level'=>$faker->randomElement(['Beginner','Intermediate','Advanced'])]),
                'searched_at'=>now()->subDays($faker->numberBetween(0,30)),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        for ($i = 0; $i < 20; $i++) {
            DB::table('content_events')->insert([
                'user_id'=>$faker->randomElement($userIds),
                'event_name'=>$faker->randomElement(['course_view','video_play','share','download']),
                'entity_type'=>$faker->randomElement(['course','video']),
                'entity_id'=>$faker->randomElement(array_merge($courseIds,$videoIds)),
                'meta_json'=>json_encode(['source'=>'web']),
                'event_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 10 done (analytics)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 11: Recommendations & Feed
        // ═══════════════════════════════════════════════════════════════
        for ($i = 0; $i < 10; $i++) {
            DB::table('trending_items')->insert([
                'item_type'=>'course','item_id'=>$faker->randomElement($courseIds),
                'window'=>$faker->randomElement(['daily','weekly','monthly']),
                'score'=>$faker->randomFloat(4,0.5,10),
                'computed_at'=>$now,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        foreach (array_slice($userIds, 0, 10) as $uid) {
            $recId = DB::table('recommendations')->insertGetId([
                'user_id'=>$uid,'recommendation_model_id'=>$faker->randomElement($recModelIds),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
            foreach ($faker->randomElements($courseIds, min(3, count($courseIds))) as $pos => $cid) {
                DB::table('recommendation_items')->insertOrIgnore([
                    'recommendation_id'=>$recId,'item_type'=>'course','item_id'=>$cid,
                    'score'=>$faker->randomFloat(4,0.5,1),'position'=>$pos+1,
                    'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }
        foreach (array_slice($userIds, 0, 15) as $uid) {
            DB::table('user_feed_items')->insert([
                'user_id'=>$uid,'item_type'=>'course','item_id'=>$faker->randomElement($courseIds),
                'rank_score'=>$faker->randomFloat(4,0,1),
                'seen_at'=>$faker->boolean(50)?$now:null,
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 11 done (recommendations & feed)');

        // ═══════════════════════════════════════════════════════════════
        // LAYER 12: Notifications & Moderation
        // ═══════════════════════════════════════════════════════════════
        foreach (array_slice($userIds, 0, 20) as $uid) {
            DB::table('notifications')->insert([
                'user_id'=>$uid,'notification_template_id'=>$faker->randomElement($notifTemplateIds),
                'payload_json'=>json_encode(['message'=>$faker->sentence()]),
                'status'=>$faker->randomElement(['unread','read']),
                'read_at'=>$faker->boolean(40)?$now:null,
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        foreach (array_slice($userIds, 0, 10) as $uid) {
            DB::table('email_outbox')->insert([
                'user_id'=>$uid,'subject'=>$faker->sentence(),
                'body'=>$faker->paragraphs(2,true),'status'=>'sent',
                'provider_message_id'=>'msg_'.$faker->uuid(),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        foreach (array_slice($userIds, 0, 8) as $uid) {
            DB::table('push_tokens')->insertOrIgnore([
                'user_id'=>$uid,'platform'=>$faker->randomElement(['ios','android','web']),
                'token'=>'tok_'.$faker->uuid(),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        for ($i = 0; $i < 15; $i++) {
            DB::table('audit_logs')->insert([
                'actor_user_id'=>$faker->randomElement($userIds),
                'action'=>$faker->randomElement(['create','update','delete','login','logout']),
                'entity_type'=>$faker->randomElement(['course','user','enrollment']),
                'entity_id'=>(string)$faker->numberBetween(1,100),
                'diff_json'=>json_encode(['field'=>'status','old'=>'draft','new'=>'published']),
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        for ($i = 0; $i < 5; $i++) {
            DB::table('content_moderation_queue')->insert([
                'entity_type'=>$faker->randomElement(['review','discussion_post']),
                'entity_id'=>(string)$faker->numberBetween(1,20),
                'reason'=>$faker->randomElement(['spam','inappropriate','harassment']),
                'status'=>$faker->randomElement(['pending','approved','rejected']),
                'reviewed_by'=>$faker->boolean(50)?$faker->randomElement($userIds):null,
                'reviewed_at'=>$faker->boolean(50)?$now:null,
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            DB::table('abuse_reports')->insert([
                'reporter_user_id'=>$faker->randomElement($userIds),
                'entity_type'=>'discussion_post',
                'entity_id'=>(string)$faker->numberBetween(1,20),
                'reason'=>$faker->randomElement(['spam','harassment','inappropriate']),
                'details'=>$faker->sentence(),'status'=>'open',
                'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        $this->command->info('✓ Layer 12 done (notifications, moderation, compliance)');
        $this->command->info('🎉 All 73 tables seeded successfully!');
    }
}
