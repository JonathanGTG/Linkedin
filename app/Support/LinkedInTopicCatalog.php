<?php

namespace App\Support;

use Illuminate\Support\Str;

class LinkedInTopicCatalog
{
    public static function typeLabels(): array
    {
        return [
            'B' => 'Business',
            'T' => 'Technology',
            'C' => 'Creative',
        ];
    }

    public static function topicsForType(string $type): array
    {
        return match ($type) {
            'T' => [
                'Artificial Intelligence (AI)',
                'Cloud Computing',
                'Cybersecurity',
                'Data Science',
                'Database Management',
                'DevOps',
                'Hardware',
                'IT Help Desk',
                'Mobile Development',
                'Network and System Administration',
                'Software Development',
                'Web Development',
            ],
            'C' => [
                'AEC',
                'Animation and Illustration',
                'Audio and Music',
                'Graphic Design',
                'Motion Graphics and VFX',
                'Photography',
                'Product and Manufacturing',
                'User Experience',
                'Video',
                'Visualization and Real-Time',
                'Web Design',
            ],
            default => [
                'Artificial Intelligence for Business',
                'Business Analysis and Strategy',
                'Business Software and Tools',
                'Career Development',
                'Customer Service',
                'Diversity, Equity, and Inclusion (DEI)',
                'Finance and Accounting',
                'Human Resources',
                'Leadership and Management',
                'Marketing',
                'Professional Development',
                'Project Management',
                'Sales',
                'Small Business and Entrepreneurship',
                'Training and Education',
            ],
        };
    }

    public static function all(): array
    {
        $items = [];

        foreach (self::typeLabels() as $type => $label) {
            foreach (self::topicsForType($type) as $topic) {
                $items[Str::slug($topic)] = [
                    'title' => $topic,
                    'slug' => Str::slug($topic),
                    'type' => $type,
                    'type_label' => $label,
                    'summary' => self::summary($topic, $label),
                    'aliases' => self::aliases($topic),
                    'subtopics' => self::subtopics($topic),
                ];
            }
        }

        return $items;
    }

    public static function find(string $slug): ?array
    {
        return self::all()[Str::slug($slug)] ?? null;
    }

    public static function aliases(string $topic): array
    {
        $base = trim(preg_replace('/\s*\([^)]*\)/', '', $topic) ?? $topic);

        $aliases = match ($topic) {
            'Artificial Intelligence (AI)' => ['Artificial Intelligence', 'AI', 'Generative AI', 'Machine Learning', 'Prompt Engineering'],
            'Artificial Intelligence for Business' => ['Artificial Intelligence for Business', 'AI for Business', 'Artificial Intelligence', 'AI', 'Generative AI', 'ChatGPT', 'Copilot'],
            'Business Analysis and Strategy' => ['Business Analysis and Strategy', 'Business Analysis', 'Business Strategy', 'Strategy', 'Strategic Planning'],
            'Business Software and Tools' => ['Business Software and Tools', 'Business Software', 'Microsoft Excel', 'Microsoft 365', 'Power BI', 'Productivity Software'],
            'Career Development' => ['Career Development', 'Career Management', 'Job Search', 'Interviewing', 'Resume'],
            'Customer Service' => ['Customer Service', 'Customer Experience', 'Support', 'Client Relations'],
            'Diversity, Equity, and Inclusion (DEI)' => ['Diversity, Equity, and Inclusion', 'Diversity and Inclusion', 'DEI', 'Inclusive Leadership'],
            'Finance and Accounting' => ['Finance and Accounting', 'Finance', 'Accounting', 'Financial Analysis', 'Bookkeeping'],
            'Human Resources' => ['Human Resources', 'HR', 'Talent Management', 'Recruiting', 'People Management'],
            'Leadership and Management' => ['Leadership and Management', 'Leadership Skills', 'Management Skills', 'Executive Leadership', 'People Management'],
            'Marketing' => ['Marketing', 'Digital Marketing', 'Content Marketing', 'SEO', 'Branding'],
            'Professional Development' => ['Professional Development', 'Communication', 'Productivity', 'Presentation Skills', 'Time Management'],
            'Project Management' => ['Project Management', 'Agile', 'Scrum', 'Microsoft Project', 'Program Management'],
            'Sales' => ['Sales', 'Sales Management', 'Sales Strategy', 'Negotiation', 'Prospecting'],
            'Small Business and Entrepreneurship' => ['Small Business and Entrepreneurship', 'Entrepreneurship', 'Small Business', 'Startup', 'Business Plan'],
            'Training and Education' => ['Training and Education', 'Training', 'Education', 'Learning and Development', 'Instructional Design'],
            'Cloud Computing' => ['Cloud Computing', 'AWS', 'Azure', 'Google Cloud', 'Cloud Architecture', 'Cloud Security'],
            'Cybersecurity' => ['Cybersecurity', 'Security', 'Network Security', 'Information Security', 'Incident Response'],
            'Data Science' => ['Data Science', 'Machine Learning', 'Data Analysis', 'Python', 'Statistics', 'Data Visualization'],
            'Database Management' => ['Database Management', 'Database Administration', 'Databases', 'SQL', 'MySQL', 'PostgreSQL'],
            'DevOps' => ['DevOps', 'CI/CD', 'Docker', 'Kubernetes', 'Automation', 'Infrastructure'],
            'Hardware' => ['Hardware', 'Computer Hardware', 'PC', 'Troubleshooting'],
            'IT Help Desk' => ['IT Help Desk', 'IT Support', 'Technical Support', 'Troubleshooting'],
            'Mobile Development' => ['Mobile Development', 'Android', 'iOS', 'React Native', 'Flutter'],
            'Network and System Administration' => ['Network and System Administration', 'Network Administration', 'Systems Administration', 'Networking', 'Linux', 'Windows Server'],
            'Software Development' => ['Software Development', 'Programming', 'Python', 'JavaScript', 'Java', 'Software Engineering'],
            'Web Development' => ['Web Development', 'HTML', 'CSS', 'JavaScript', 'React', 'Laravel', 'PHP'],
            'AEC' => ['AEC', 'Architecture', 'Engineering', 'Construction', 'AutoCAD', 'Revit'],
            'Animation and Illustration' => ['Animation and Illustration', 'Animation', 'Illustration', 'Drawing', 'Character Design'],
            'Audio and Music' => ['Audio and Music', 'Audio', 'Music', 'Sound Design', 'Podcasting'],
            'Graphic Design' => ['Graphic Design', 'Adobe Photoshop', 'Adobe Illustrator', 'Typography', 'Branding', 'Layout Design'],
            'Motion Graphics and VFX' => ['Motion Graphics and VFX', 'Motion Graphics', 'VFX', 'Visual Effects', 'After Effects'],
            'Photography' => ['Photography', 'Photo Editing', 'Lightroom', 'Portrait Photography', 'Camera'],
            'Product and Manufacturing' => ['Product and Manufacturing', 'Product Design', 'Manufacturing', 'CAD', '3D Modeling'],
            'User Experience' => ['User Experience', 'UX Design', 'User Experience Design', 'Figma', 'Usability'],
            'Video' => ['Video', 'Video Editing', 'Premiere Pro', 'Final Cut Pro', 'Production'],
            'Visualization and Real-Time' => ['Visualization and Real-Time', 'Visualization', 'Real-Time', 'Unreal Engine', '3D Visualization'],
            'Web Design' => ['Web Design', 'Responsive Design', 'HTML', 'CSS', 'Figma', 'UI Design'],
            default => [$topic, $base],
        };

        return array_values(array_unique(array_filter(array_merge([$topic, $base], $aliases))));
    }

    public static function subtopics(string $topic): array
    {
        return match ($topic) {
            'Artificial Intelligence for Business' => [
                'AI Productivity Tools',
                'AI for Business Analysis',
                'AI for Business Foundations',
                'AI for HR',
                'AI for Project Management',
            ],
            'Business Software and Tools' => ['Microsoft Excel', 'Power BI', 'Microsoft 365', 'Microsoft Copilot', 'Salesforce'],
            'Leadership and Management' => ['People Management', 'Executive Leadership', 'Coaching', 'Decision-Making', 'Team Leadership'],
            'Marketing' => ['Digital Marketing', 'SEO', 'Content Marketing', 'Social Media Marketing', 'Brand Strategy'],
            'Project Management' => ['Agile Project Management', 'Scrum', 'Microsoft Project', 'Project Planning', 'Risk Management'],
            'Artificial Intelligence (AI)' => ['Generative AI', 'Prompt Engineering', 'Machine Learning', 'Responsible AI', 'AI Productivity Tools'],
            'Cloud Computing' => ['AWS', 'Microsoft Azure', 'Google Cloud', 'Cloud Security', 'Kubernetes'],
            'Cybersecurity' => ['Network Security', 'Security Monitoring', 'Incident Response', 'Cloud Security', 'Risk Management'],
            'Data Science' => ['Python', 'Machine Learning', 'Data Visualization', 'Statistics', 'SQL'],
            'Software Development' => ['Python', 'JavaScript', 'Software Engineering', 'Git', 'Object-Oriented Programming'],
            'Web Development' => ['HTML', 'CSS', 'JavaScript', 'React', 'PHP'],
            'Graphic Design' => ['Adobe Photoshop', 'Adobe Illustrator', 'Typography', 'Branding', 'Layout Design'],
            'User Experience' => ['UX Research', 'Figma', 'Wireframing', 'Prototyping', 'Usability Testing'],
            'Video' => ['Video Editing', 'Premiere Pro', 'Storytelling', 'Color Correction', 'Production'],
            default => array_slice(self::aliases($topic), 1, 5),
        };
    }

    public static function relatedTopics(string $type, string $currentSlug): array
    {
        return collect(self::topicsForType($type))
            ->map(fn (string $topic) => [
                'title' => $topic,
                'slug' => Str::slug($topic),
            ])
            ->reject(fn (array $topic) => $topic['slug'] === $currentSlug)
            ->take(6)
            ->values()
            ->all();
    }

    private static function summary(string $topic, string $typeLabel): string
    {
        return match ($topic) {
            'Artificial Intelligence for Business' => 'Artificial intelligence for business focuses on using AI tools, data, automation, and machine learning techniques to solve business problems, improve processes, and create better decisions across marketing, finance, operations, HR, and customer service.',
            'Artificial Intelligence (AI)' => 'Build practical AI fluency with courses on generative AI, machine learning, prompt engineering, responsible AI, and productivity tools for technical work.',
            'Cloud Computing' => 'Explore cloud platforms, architecture, deployment, security, and operations skills used to build and manage modern cloud-based systems.',
            'Graphic Design' => 'Develop visual communication skills across layout, typography, branding, and the creative software used by working designers.',
            default => "Explore {$topic} courses from the {$typeLabel} library, with learning content matched to related categories, skills, and course descriptions in your database.",
        };
    }
}
