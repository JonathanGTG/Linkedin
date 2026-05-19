CREATE DATABASE IF NOT EXISTS lms_linkedin_learning_clone
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE lms_linkedin_learning_clone;

CREATE TABLE topics (
  topic_id VARCHAR(64) PRIMARY KEY,
  slug VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_topics_slug (slug),
  KEY idx_topics_name (name)
) ENGINE=InnoDB;

CREATE TABLE courses (
  course_id VARCHAR(64) PRIMARY KEY,
  topic_id VARCHAR(64) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  level VARCHAR(32) NULL,
  duration_text VARCHAR(64) NULL,
  duration_seconds INT NOT NULL DEFAULT 0,
  release_date DATE NULL,
  rating DECIMAL(2,1) NULL,
  rating_count INT NULL,
  learner_count INT NOT NULL DEFAULT 0,
  description LONGTEXT NULL,
  url VARCHAR(2048) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_courses_slug (slug),
  KEY idx_courses_topic (topic_id),
  KEY idx_courses_release_date (release_date),
  KEY idx_courses_rating (rating),
  CONSTRAINT fk_courses_topic FOREIGN KEY (topic_id) REFERENCES topics(topic_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE skills (
  skill_id VARCHAR(64) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_skills_name (name)
) ENGINE=InnoDB;

CREATE TABLE course_skills (
  course_id VARCHAR(64) NOT NULL,
  skill_id VARCHAR(64) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (course_id, skill_id),
  KEY idx_course_skills_skill (skill_id),
  CONSTRAINT fk_course_skills_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
  CONSTRAINT fk_course_skills_skill FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE include_types (
  include_type_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  label VARCHAR(255) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_include_types_label (label)
) ENGINE=InnoDB;

CREATE TABLE course_includes (
  course_id VARCHAR(64) NOT NULL,
  include_type_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (course_id, include_type_id),
  KEY idx_course_includes_include (include_type_id),
  CONSTRAINT fk_course_includes_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
  CONSTRAINT fk_course_includes_type FOREIGN KEY (include_type_id) REFERENCES include_types(include_type_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE instructors (
  instructor_id VARCHAR(64) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  info VARCHAR(512) NULL,
  bio LONGTEXT NULL,
  link VARCHAR(2048) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  KEY idx_instructors_name (name)
) ENGINE=InnoDB;

CREATE TABLE course_instructors (
  course_id VARCHAR(64) NOT NULL,
  instructor_id VARCHAR(64) NOT NULL,
  role VARCHAR(32) NULL,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (course_id, instructor_id),
  KEY idx_course_instructors_instructor (instructor_id),
  CONSTRAINT fk_course_instructors_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
  CONSTRAINT fk_course_instructors_instructor FOREIGN KEY (instructor_id) REFERENCES instructors(instructor_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE chapters (
  chapter_id VARCHAR(64) PRIMARY KEY,
  course_id VARCHAR(64) NOT NULL,
  position INT NOT NULL DEFAULT 0,
  title VARCHAR(255) NOT NULL,
  video_count INT NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_chapters_course_position (course_id, position),
  KEY idx_chapters_course (course_id),
  CONSTRAINT fk_chapters_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lessons (
  lesson_id VARCHAR(64) PRIMARY KEY,
  chapter_id VARCHAR(64) NULL,
  course_id VARCHAR(64) NOT NULL,
  position INT NOT NULL DEFAULT 0,
  title VARCHAR(255) NOT NULL,
  duration_text VARCHAR(64) NULL,
  duration_seconds INT NOT NULL DEFAULT 0,
  lesson_type VARCHAR(32) NOT NULL DEFAULT 'video',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_lessons_course_position (course_id, position),
  KEY idx_lessons_chapter (chapter_id),
  KEY idx_lessons_course (course_id),
  CONSTRAINT fk_lessons_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE SET NULL,
  CONSTRAINT fk_lessons_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE storage_objects (
  storage_object_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  provider VARCHAR(64) NOT NULL,
  uri VARCHAR(2048) NOT NULL,
  mime_type VARCHAR(255) NULL,
  size_bytes BIGINT NULL,
  checksum VARCHAR(128) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_storage_provider (provider)
) ENGINE=InnoDB;

CREATE TABLE media_assets (
  media_asset_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  storage_object_id BIGINT NOT NULL,
  duration_seconds INT NULL,
  width INT NULL,
  height INT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_media_assets_storage (storage_object_id),
  CONSTRAINT fk_media_assets_storage FOREIGN KEY (storage_object_id) REFERENCES storage_objects(storage_object_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE media_renditions (
  media_rendition_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  media_asset_id BIGINT NOT NULL,
  codec VARCHAR(64) NULL,
  bitrate INT NULL,
  width INT NULL,
  height INT NULL,
  uri VARCHAR(2048) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_media_renditions_asset (media_asset_id),
  CONSTRAINT fk_media_renditions_asset FOREIGN KEY (media_asset_id) REFERENCES media_assets(media_asset_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lesson_media (
  lesson_id VARCHAR(64) PRIMARY KEY,
  media_asset_id BIGINT NULL,
  playback_policy VARCHAR(64) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  CONSTRAINT fk_lesson_media_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE,
  CONSTRAINT fk_lesson_media_asset FOREIGN KEY (media_asset_id) REFERENCES media_assets(media_asset_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE captions (
  caption_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  lesson_id VARCHAR(64) NOT NULL,
  locale VARCHAR(32) NOT NULL,
  format VARCHAR(16) NOT NULL,
  storage_object_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_captions_lesson_locale_format (lesson_id, locale, format),
  KEY idx_captions_storage (storage_object_id),
  CONSTRAINT fk_captions_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE,
  CONSTRAINT fk_captions_storage FOREIGN KEY (storage_object_id) REFERENCES storage_objects(storage_object_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transcripts (
  transcript_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  lesson_id VARCHAR(64) NOT NULL,
  locale VARCHAR(32) NOT NULL,
  storage_object_id BIGINT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_transcripts_lesson_locale (lesson_id, locale),
  CONSTRAINT fk_transcripts_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE,
  CONSTRAINT fk_transcripts_storage FOREIGN KEY (storage_object_id) REFERENCES storage_objects(storage_object_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE transcript_segments (
  transcript_segment_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  transcript_id BIGINT NOT NULL,
  start_ms INT NOT NULL,
  end_ms INT NOT NULL,
  text TEXT NOT NULL,
  KEY idx_transcript_segments_transcript (transcript_id),
  CONSTRAINT fk_transcript_segments_transcript FOREIGN KEY (transcript_id) REFERENCES transcripts(transcript_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE exercise_files (
  exercise_file_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  lesson_id VARCHAR(64) NOT NULL,
  title VARCHAR(255) NOT NULL,
  storage_object_id BIGINT NOT NULL,
  checksum VARCHAR(128) NULL,
  size_bytes BIGINT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_exercise_files_lesson (lesson_id),
  CONSTRAINT fk_exercise_files_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE,
  CONSTRAINT fk_exercise_files_storage FOREIGN KEY (storage_object_id) REFERENCES storage_objects(storage_object_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE users (
  user_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(320) NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  locale VARCHAR(32) NOT NULL DEFAULT 'id-ID',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE auth_identities (
  auth_identity_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  provider VARCHAR(32) NOT NULL,
  provider_user_id VARCHAR(255) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_auth_provider_user (provider, provider_user_id),
  KEY idx_auth_user (user_id),
  CONSTRAINT fk_auth_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE organizations (
  organization_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3)
) ENGINE=InnoDB;

CREATE TABLE organization_domains (
  organization_domain_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  domain VARCHAR(255) NOT NULL,
  verified_at DATETIME(3) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_org_domains (organization_id, domain),
  KEY idx_org_domains_domain (domain),
  CONSTRAINT fk_org_domains_org FOREIGN KEY (organization_id) REFERENCES organizations(organization_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE organization_members (
  organization_member_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  joined_at DATETIME(3) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_org_member (organization_id, user_id),
  KEY idx_org_members_user (user_id),
  CONSTRAINT fk_org_members_org FOREIGN KEY (organization_id) REFERENCES organizations(organization_id) ON DELETE CASCADE,
  CONSTRAINT fk_org_members_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE organization_teams (
  organization_team_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_org_team (organization_id, name),
  CONSTRAINT fk_org_teams_org FOREIGN KEY (organization_id) REFERENCES organizations(organization_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE organization_team_members (
  organization_team_id BIGINT NOT NULL,
  organization_member_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (organization_team_id, organization_member_id),
  CONSTRAINT fk_org_team_members_team FOREIGN KEY (organization_team_id) REFERENCES organization_teams(organization_team_id) ON DELETE CASCADE,
  CONSTRAINT fk_org_team_members_member FOREIGN KEY (organization_member_id) REFERENCES organization_members(organization_member_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE org_sso_configs (
  org_sso_config_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  sso_type VARCHAR(64) NOT NULL,
  metadata_json JSON NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  KEY idx_org_sso_org (organization_id),
  CONSTRAINT fk_org_sso_org FOREIGN KEY (organization_id) REFERENCES organizations(organization_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE libraries (
  library_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  library_code VARCHAR(64) NOT NULL,
  country_code CHAR(2) NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_libraries_code (library_code)
) ENGINE=InnoDB;

CREATE TABLE library_branches (
  library_branch_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  library_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  address_json JSON NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  CONSTRAINT fk_library_branches_library FOREIGN KEY (library_id) REFERENCES libraries(library_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE library_patrons (
  library_patron_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  library_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  patron_external_id VARCHAR(128) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_library_patron (library_id, user_id),
  CONSTRAINT fk_library_patrons_library FOREIGN KEY (library_id) REFERENCES libraries(library_id) ON DELETE CASCADE,
  CONSTRAINT fk_library_patrons_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE library_cards (
  library_card_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  library_patron_id BIGINT NOT NULL,
  card_number_hash VARBINARY(64) NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_library_cards_patron_hash (library_patron_id, card_number_hash),
  CONSTRAINT fk_library_cards_patron FOREIGN KEY (library_patron_id) REFERENCES library_patrons(library_patron_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE entitlements (
  entitlement_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  resource_type VARCHAR(32) NOT NULL,
  resource_id VARCHAR(64) NOT NULL,
  starts_at DATETIME(3) NULL,
  ends_at DATETIME(3) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_entitlements_resource (resource_type, resource_id),
  KEY idx_entitlements_window (starts_at, ends_at)
) ENGINE=InnoDB;

CREATE TABLE entitlement_rules (
  entitlement_rule_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  entitlement_id BIGINT NOT NULL,
  rule_type VARCHAR(64) NOT NULL,
  rule_json JSON NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_entitlement_rules_entitlement (entitlement_id),
  CONSTRAINT fk_entitlement_rules_entitlement FOREIGN KEY (entitlement_id) REFERENCES entitlements(entitlement_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_entitlements (
  user_id BIGINT NOT NULL,
  entitlement_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (user_id, entitlement_id),
  CONSTRAINT fk_user_entitlements_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_user_entitlements_entitlement FOREIGN KEY (entitlement_id) REFERENCES entitlements(entitlement_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE org_entitlements (
  organization_id BIGINT NOT NULL,
  entitlement_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (organization_id, entitlement_id),
  CONSTRAINT fk_org_entitlements_org FOREIGN KEY (organization_id) REFERENCES organizations(organization_id) ON DELETE CASCADE,
  CONSTRAINT fk_org_entitlements_entitlement FOREIGN KEY (entitlement_id) REFERENCES entitlements(entitlement_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE library_entitlements (
  library_id BIGINT NOT NULL,
  entitlement_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (library_id, entitlement_id),
  CONSTRAINT fk_library_entitlements_library FOREIGN KEY (library_id) REFERENCES libraries(library_id) ON DELETE CASCADE,
  CONSTRAINT fk_library_entitlements_entitlement FOREIGN KEY (entitlement_id) REFERENCES entitlements(entitlement_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE seat_pools (
  seat_pool_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  seat_count INT NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_seat_pools_org_name (organization_id, name),
  CONSTRAINT fk_seat_pools_org FOREIGN KEY (organization_id) REFERENCES organizations(organization_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE seat_assignments (
  seat_assignment_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  seat_pool_id BIGINT NOT NULL,
  organization_member_id BIGINT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  assigned_at DATETIME(3) NULL,
  revoked_at DATETIME(3) NULL,
  UNIQUE KEY uq_seat_assignments_pool_member (seat_pool_id, organization_member_id),
  CONSTRAINT fk_seat_assignments_pool FOREIGN KEY (seat_pool_id) REFERENCES seat_pools(seat_pool_id) ON DELETE CASCADE,
  CONSTRAINT fk_seat_assignments_member FOREIGN KEY (organization_member_id) REFERENCES organization_members(organization_member_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE enrollments (
  enrollment_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  course_id VARCHAR(64) NOT NULL,
  source_type VARCHAR(32) NULL,
  source_id VARCHAR(64) NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  started_at DATETIME(3) NULL,
  completed_at DATETIME(3) NULL,
  last_activity_at DATETIME(3) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_enrollments_user_course (user_id, course_id),
  KEY idx_enrollments_course (course_id),
  CONSTRAINT fk_enrollments_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_enrollments_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lesson_progress (
  enrollment_id BIGINT NOT NULL,
  lesson_id VARCHAR(64) NOT NULL,
  progress_seconds INT NOT NULL DEFAULT 0,
  last_position_seconds INT NOT NULL DEFAULT 0,
  completed TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  PRIMARY KEY (enrollment_id, lesson_id),
  KEY idx_lesson_progress_lesson (lesson_id),
  CONSTRAINT fk_lesson_progress_enrollment FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
  CONSTRAINT fk_lesson_progress_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookmarks (
  bookmark_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  course_id VARCHAR(64) NOT NULL,
  lesson_id VARCHAR(64) NOT NULL,
  position_seconds INT NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_bookmarks_user_course (user_id, course_id),
  CONSTRAINT fk_bookmarks_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_bookmarks_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
  CONSTRAINT fk_bookmarks_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notes (
  note_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  course_id VARCHAR(64) NOT NULL,
  lesson_id VARCHAR(64) NULL,
  position_seconds INT NOT NULL DEFAULT 0,
  note_text LONGTEXT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  KEY idx_notes_user_course (user_id, course_id),
  CONSTRAINT fk_notes_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_notes_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
  CONSTRAINT fk_notes_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE course_reviews (
  course_review_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  course_id VARCHAR(64) NOT NULL,
  rating TINYINT NOT NULL,
  review_text LONGTEXT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'published',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_course_reviews_user_course (user_id, course_id),
  KEY idx_course_reviews_course_rating (course_id, rating),
  CONSTRAINT fk_course_reviews_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_course_reviews_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE review_votes (
  course_review_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  vote VARCHAR(16) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (course_review_id, user_id),
  CONSTRAINT fk_review_votes_review FOREIGN KEY (course_review_id) REFERENCES course_reviews(course_review_id) ON DELETE CASCADE,
  CONSTRAINT fk_review_votes_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE discussion_threads (
  discussion_thread_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  course_id VARCHAR(64) NOT NULL,
  lesson_id VARCHAR(64) NULL,
  user_id BIGINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'open',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  KEY idx_discussion_threads_course (course_id),
  CONSTRAINT fk_discussion_threads_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
  CONSTRAINT fk_discussion_threads_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE SET NULL,
  CONSTRAINT fk_discussion_threads_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE discussion_posts (
  discussion_post_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  discussion_thread_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  body LONGTEXT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'published',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  KEY idx_discussion_posts_thread (discussion_thread_id),
  CONSTRAINT fk_discussion_posts_thread FOREIGN KEY (discussion_thread_id) REFERENCES discussion_threads(discussion_thread_id) ON DELETE CASCADE,
  CONSTRAINT fk_discussion_posts_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE discussion_reactions (
  discussion_post_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  reaction VARCHAR(32) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (discussion_post_id, user_id, reaction),
  CONSTRAINT fk_discussion_reactions_post FOREIGN KEY (discussion_post_id) REFERENCES discussion_posts(discussion_post_id) ON DELETE CASCADE,
  CONSTRAINT fk_discussion_reactions_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quizzes (
  quiz_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  lesson_id VARCHAR(64) NOT NULL,
  title VARCHAR(255) NOT NULL,
  passing_score_percent INT NOT NULL DEFAULT 70,
  time_limit_seconds INT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_quizzes_lesson (lesson_id),
  CONSTRAINT fk_quizzes_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_questions (
  quiz_question_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  quiz_id BIGINT NOT NULL,
  question_type VARCHAR(32) NOT NULL DEFAULT 'single_choice',
  question_text LONGTEXT NOT NULL,
  position INT NOT NULL,
  points INT NOT NULL DEFAULT 1,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_quiz_questions_quiz_position (quiz_id, position),
  CONSTRAINT fk_quiz_questions_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_options (
  quiz_option_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  quiz_question_id BIGINT NOT NULL,
  option_text LONGTEXT NOT NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  position INT NOT NULL,
  UNIQUE KEY uq_quiz_options_question_position (quiz_question_id, position),
  CONSTRAINT fk_quiz_options_question FOREIGN KEY (quiz_question_id) REFERENCES quiz_questions(quiz_question_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_attempts (
  quiz_attempt_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  quiz_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  started_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  completed_at DATETIME(3) NULL,
  score_percent INT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'in_progress',
  KEY idx_quiz_attempts_quiz_user (quiz_id, user_id),
  CONSTRAINT fk_quiz_attempts_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE,
  CONSTRAINT fk_quiz_attempts_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_answers (
  quiz_attempt_id BIGINT NOT NULL,
  quiz_question_id BIGINT NOT NULL,
  quiz_option_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (quiz_attempt_id, quiz_question_id, quiz_option_id),
  CONSTRAINT fk_quiz_answers_attempt FOREIGN KEY (quiz_attempt_id) REFERENCES quiz_attempts(quiz_attempt_id) ON DELETE CASCADE,
  CONSTRAINT fk_quiz_answers_question FOREIGN KEY (quiz_question_id) REFERENCES quiz_questions(quiz_question_id) ON DELETE CASCADE,
  CONSTRAINT fk_quiz_answers_option FOREIGN KEY (quiz_option_id) REFERENCES quiz_options(quiz_option_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE certificate_templates (
  certificate_template_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  template_html LONGTEXT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3)
) ENGINE=InnoDB;

CREATE TABLE certificates (
  certificate_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  enrollment_id BIGINT NOT NULL,
  certificate_template_id BIGINT NULL,
  certificate_code VARCHAR(64) NOT NULL,
  issued_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_certificates_enrollment (enrollment_id),
  UNIQUE KEY uq_certificates_code (certificate_code),
  CONSTRAINT fk_certificates_enrollment FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
  CONSTRAINT fk_certificates_template FOREIGN KEY (certificate_template_id) REFERENCES certificate_templates(certificate_template_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE credential_shares (
  credential_share_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  certificate_id BIGINT NOT NULL,
  provider VARCHAR(64) NOT NULL,
  provider_ref VARCHAR(255) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_credential_shares_certificate (certificate_id),
  CONSTRAINT fk_credential_shares_certificate FOREIGN KEY (certificate_id) REFERENCES certificates(certificate_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE plans (
  plan_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  name VARCHAR(255) NOT NULL,
  billing_period VARCHAR(16) NOT NULL,
  price_cents INT NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_plans_code (code)
) ENGINE=InnoDB;

CREATE TABLE plan_features (
  plan_id BIGINT NOT NULL,
  feature_code VARCHAR(64) NOT NULL,
  feature_value VARCHAR(255) NULL,
  PRIMARY KEY (plan_id, feature_code),
  CONSTRAINT fk_plan_features_plan FOREIGN KEY (plan_id) REFERENCES plans(plan_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE subscriptions (
  subscription_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  subject_type VARCHAR(32) NOT NULL,
  subject_id BIGINT NOT NULL,
  plan_id BIGINT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'trialing',
  trial_ends_at DATETIME(3) NULL,
  current_period_start DATETIME(3) NOT NULL,
  current_period_end DATETIME(3) NOT NULL,
  cancel_at_period_end TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  KEY idx_subscriptions_subject (subject_type, subject_id),
  CONSTRAINT fk_subscriptions_plan FOREIGN KEY (plan_id) REFERENCES plans(plan_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE coupons (
  coupon_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  percent_off INT NULL,
  amount_off_cents INT NULL,
  currency CHAR(3) NULL,
  starts_at DATETIME(3) NULL,
  ends_at DATETIME(3) NULL,
  max_redemptions INT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_coupons_code (code)
) ENGINE=InnoDB;

CREATE TABLE subscription_coupons (
  subscription_id BIGINT NOT NULL,
  coupon_id BIGINT NOT NULL,
  applied_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (subscription_id, coupon_id),
  CONSTRAINT fk_subscription_coupons_subscription FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE CASCADE,
  CONSTRAINT fk_subscription_coupons_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(coupon_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE invoices (
  invoice_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  subscription_id BIGINT NOT NULL,
  amount_cents INT NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  status VARCHAR(32) NOT NULL DEFAULT 'open',
  issued_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  due_at DATETIME(3) NULL,
  CONSTRAINT fk_invoices_subscription FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE invoice_items (
  invoice_item_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  invoice_id BIGINT NOT NULL,
  description VARCHAR(255) NOT NULL,
  amount_cents INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payments (
  payment_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  invoice_id BIGINT NOT NULL,
  provider VARCHAR(64) NOT NULL,
  provider_payment_id VARCHAR(255) NULL,
  amount_cents INT NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  status VARCHAR(32) NOT NULL DEFAULT 'pending',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_payments_invoice (invoice_id),
  CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE refunds (
  refund_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  payment_id BIGINT NOT NULL,
  amount_cents INT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'pending',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  CONSTRAINT fk_refunds_payment FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE devices (
  device_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  device_type VARCHAR(64) NULL,
  device_fingerprint_hash VARCHAR(128) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_devices_user_fingerprint (user_id, device_fingerprint_hash),
  CONSTRAINT fk_devices_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE watch_sessions (
  watch_session_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  lesson_id VARCHAR(64) NOT NULL,
  device_id BIGINT NULL,
  started_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  ended_at DATETIME(3) NULL,
  total_watch_seconds INT NOT NULL DEFAULT 0,
  KEY idx_watch_sessions_user_time (user_id, started_at),
  KEY idx_watch_sessions_lesson_time (lesson_id, started_at),
  CONSTRAINT fk_watch_sessions_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_watch_sessions_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE,
  CONSTRAINT fk_watch_sessions_device FOREIGN KEY (device_id) REFERENCES devices(device_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE playback_events (
  playback_event_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  lesson_id VARCHAR(64) NOT NULL,
  watch_session_id BIGINT NULL,
  event_type VARCHAR(64) NOT NULL,
  position_seconds INT NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_playback_events_user_time (user_id, created_at),
  KEY idx_playback_events_lesson_time (lesson_id, created_at),
  CONSTRAINT fk_playback_events_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_playback_events_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE,
  CONSTRAINT fk_playback_events_session FOREIGN KEY (watch_session_id) REFERENCES watch_sessions(watch_session_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE search_events (
  search_event_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NULL,
  query_text VARCHAR(500) NOT NULL,
  filters_json JSON NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_search_events_user_time (user_id, created_at),
  CONSTRAINT fk_search_events_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE content_events (
  content_event_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NULL,
  event_name VARCHAR(128) NOT NULL,
  entity_type VARCHAR(64) NOT NULL,
  entity_id VARCHAR(64) NOT NULL,
  meta_json JSON NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_content_events_user_time (user_id, created_at),
  KEY idx_content_events_entity_time (entity_type, entity_id, created_at),
  CONSTRAINT fk_content_events_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE trending_items (
  trending_item_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  item_type VARCHAR(64) NOT NULL,
  item_id VARCHAR(64) NOT NULL,
  window VARCHAR(32) NOT NULL,
  score DECIMAL(12,6) NOT NULL DEFAULT 0,
  computed_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_trending_items_window (item_type, window, computed_at)
) ENGINE=InnoDB;

CREATE TABLE recommendation_models (
  recommendation_model_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  version VARCHAR(64) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_recommendation_models (name, version)
) ENGINE=InnoDB;

CREATE TABLE recommendations (
  recommendation_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  recommendation_model_id BIGINT NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_recommendations_user_time (user_id, created_at),
  CONSTRAINT fk_recommendations_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_recommendations_model FOREIGN KEY (recommendation_model_id) REFERENCES recommendation_models(recommendation_model_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE recommendation_items (
  recommendation_id BIGINT NOT NULL,
  item_type VARCHAR(64) NOT NULL,
  item_id VARCHAR(64) NOT NULL,
  score DECIMAL(12,6) NOT NULL DEFAULT 0,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (recommendation_id, item_type, item_id),
  UNIQUE KEY uq_recommendation_items_position (recommendation_id, position),
  CONSTRAINT fk_recommendation_items_recommendation FOREIGN KEY (recommendation_id) REFERENCES recommendations(recommendation_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_feed_items (
  user_feed_item_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  item_type VARCHAR(64) NOT NULL,
  item_id VARCHAR(64) NOT NULL,
  rank_score DECIMAL(12,6) NOT NULL DEFAULT 0,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  seen_at DATETIME(3) NULL,
  KEY idx_user_feed_items_user_time (user_id, created_at),
  CONSTRAINT fk_user_feed_items_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notification_templates (
  notification_template_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  channel VARCHAR(32) NOT NULL,
  code VARCHAR(64) NOT NULL,
  template_json JSON NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_notification_templates (channel, code)
) ENGINE=InnoDB;

CREATE TABLE notifications (
  notification_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  notification_template_id BIGINT NOT NULL,
  payload_json JSON NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'queued',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  read_at DATETIME(3) NULL,
  KEY idx_notifications_user_status_time (user_id, status, created_at),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_notifications_template FOREIGN KEY (notification_template_id) REFERENCES notification_templates(notification_template_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE email_outbox (
  email_outbox_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NULL,
  subject VARCHAR(255) NOT NULL,
  body LONGTEXT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'queued',
  provider_message_id VARCHAR(255) NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_email_outbox_status_time (status, created_at),
  CONSTRAINT fk_email_outbox_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE push_tokens (
  push_token_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  platform VARCHAR(32) NOT NULL,
  token VARCHAR(512) NOT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_push_tokens_platform_token (platform, token),
  CONSTRAINT fk_push_tokens_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  audit_log_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  actor_user_id BIGINT NULL,
  action VARCHAR(128) NOT NULL,
  entity_type VARCHAR(64) NOT NULL,
  entity_id VARCHAR(64) NOT NULL,
  diff_json JSON NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_audit_logs_entity_time (entity_type, entity_id, created_at),
  CONSTRAINT fk_audit_logs_actor FOREIGN KEY (actor_user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE content_moderation_queue (
  content_moderation_queue_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  entity_type VARCHAR(64) NOT NULL,
  entity_id VARCHAR(64) NOT NULL,
  reason VARCHAR(255) NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'open',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  reviewed_by BIGINT NULL,
  reviewed_at DATETIME(3) NULL,
  KEY idx_moderation_status_time (status, created_at),
  CONSTRAINT fk_moderation_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE abuse_reports (
  abuse_report_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  reporter_user_id BIGINT NULL,
  entity_type VARCHAR(64) NOT NULL,
  entity_id VARCHAR(64) NOT NULL,
  reason VARCHAR(255) NOT NULL,
  details LONGTEXT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'open',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  KEY idx_abuse_reports_status_time (status, created_at),
  CONSTRAINT fk_abuse_reports_reporter FOREIGN KEY (reporter_user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE consents (
  consent_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  consent_type VARCHAR(64) NOT NULL,
  granted TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  UNIQUE KEY uq_consents_user_type (user_id, consent_type),
  CONSTRAINT fk_consents_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE data_requests (
  data_request_id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  request_type VARCHAR(32) NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'open',
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  completed_at DATETIME(3) NULL,
  KEY idx_data_requests_status_time (status, created_at),
  CONSTRAINT fk_data_requests_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;
