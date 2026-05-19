<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RoleGuideController extends Controller
{
    public function show(string $role, Request $request)
    {
        $roleData = $this->roleCatalog()[$role] ?? null;
        abort_unless($roleData, 404);

        $activeSkill = (string) $request->query('skill', $roleData['skills'][0]);
        if (! in_array($activeSkill, $roleData['skills'], true)) {
            $activeSkill = $roleData['skills'][0];
        }

        return view('roles.marketing-manager', [
            'role' => $roleData + ['eyebrow' => 'Role Guide'],
            'skills' => $roleData['skills'],
            'activeSkill' => $activeSkill,
            'shortVideos' => $this->shortVideos($roleData),
            'skillCourses' => $this->coursesForSkill($activeSkill, $roleData),
        ]);
    }

    private function roleCatalog(): array
    {
        $images = [
            'marketing' => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1400&q=85',
            'operations' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1400&q=85',
            'product' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1400&q=85',
            'finance' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1400&q=85',
            'sales' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1400&q=85',
            'people' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1400&q=85',
            'analysis' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1400&q=85',
            'service' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1400&q=85',
            'engineering' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1400&q=85',
            'cloud' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1400&q=85',
            'security' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1400&q=85',
            'design' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=1400&q=85',
            'media' => 'https://images.unsplash.com/photo-1492619375914-88005aa9e8fb?auto=format&fit=crop&w=1400&q=85',
            'creative' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1400&q=85',
        ];

        $roles = [
            'Marketing Manager' => [
                'image' => $images['marketing'],
                'summary' => 'Marketing managers connect products with the right audience through strategy, messaging, campaigns, and measurement. Build the practical skills to plan campaigns, lead teams, and read marketing performance with confidence.',
                'skills' => ['Marketing', 'Marketing Strategy', 'Digital Marketing', 'Social Media Marketing', 'Search Engine Optimization (SEO)', 'Email Marketing', 'Customer Experience', 'Team Management', 'Google Analytics', 'Customer Relationship Management (CRM)', 'AI for Business'],
            ],
            'Operations Manager' => [
                'image' => $images['operations'],
                'summary' => 'Operations managers improve how teams, processes, and resources work together. Build skills in process improvement, analytics, planning, and team execution.',
                'skills' => ['Operations Management', 'Process Improvement', 'Project Management', 'Supply Chain Management', 'Business Analytics', 'Leadership', 'Team Management', 'Strategic Planning', 'Lean Six Sigma', 'Data Analysis'],
            ],
            'Program Manager' => [
                'image' => $images['operations'],
                'summary' => 'Program managers coordinate related projects so teams can deliver larger business outcomes. Strengthen planning, stakeholder management, risk management, and communication skills.',
                'skills' => ['Program Management', 'Project Management', 'Stakeholder Management', 'Risk Management', 'Agile Project Management', 'Strategic Planning', 'Communication', 'Leadership', 'Change Management', 'Business Analysis'],
            ],
            'Product Manager' => [
                'image' => $images['product'],
                'summary' => 'Product managers define customer problems, align stakeholders, and guide product decisions from discovery to launch. Build skills in strategy, research, analytics, and agile delivery.',
                'skills' => ['Product Management', 'Product Strategy', 'User Experience (UX)', 'Market Research', 'Agile Project Management', 'Data Analysis', 'Business Strategy', 'Roadmapping', 'Customer Experience', 'Leadership'],
            ],
            'Project Manager' => [
                'image' => $images['operations'],
                'summary' => 'Project managers help teams deliver work on time, on budget, and with clear communication. Develop planning, scheduling, risk, and agile execution skills.',
                'skills' => ['Project Management', 'Agile Project Management', 'Risk Management', 'Scheduling', 'Stakeholder Management', 'Communication', 'Leadership', 'Microsoft Project', 'Scrum', 'Change Management'],
            ],
            'Financial Analyst' => [
                'image' => $images['finance'],
                'summary' => 'Financial analysts translate numbers into business decisions. Build skills in Excel, forecasting, financial modeling, accounting, and data visualization.',
                'skills' => ['Financial Analysis', 'Financial Modeling', 'Microsoft Excel', 'Accounting', 'Forecasting', 'Data Analysis', 'Business Analytics', 'Budgeting', 'Power BI', 'Finance'],
            ],
            'Sales Manager' => [
                'image' => $images['sales'],
                'summary' => 'Sales managers coach teams, manage pipeline, and turn customer insight into revenue. Build skills in sales strategy, CRM, negotiation, and team leadership.',
                'skills' => ['Sales Management', 'Sales Strategy', 'Customer Relationship Management (CRM)', 'Salesforce', 'Negotiation', 'Leadership', 'Team Management', 'Communication', 'Business Development', 'Customer Experience'],
            ],
            'Business Development Manager' => [
                'image' => $images['sales'],
                'summary' => 'Business development managers identify growth opportunities, build partnerships, and open new markets. Strengthen sales, strategy, research, and relationship-building skills.',
                'skills' => ['Business Development', 'Sales Strategy', 'Market Research', 'Negotiation', 'Strategic Partnerships', 'Customer Relationship Management (CRM)', 'Communication', 'Lead Generation', 'Business Strategy', 'Sales'],
            ],
            'Accountant' => [
                'image' => $images['finance'],
                'summary' => 'Accountants organize financial records, support reporting, and help businesses maintain trust in their numbers. Build accounting, Excel, tax, and analysis skills.',
                'skills' => ['Accounting', 'Bookkeeping', 'Financial Reporting', 'Microsoft Excel', 'Tax Accounting', 'Auditing', 'QuickBooks', 'Financial Analysis', 'Budgeting', 'Data Analysis'],
            ],
            'Salesperson' => [
                'image' => $images['sales'],
                'summary' => 'Sales professionals create trust, understand customer needs, and guide prospects toward the right solution. Build prospecting, negotiation, CRM, and communication skills.',
                'skills' => ['Sales', 'Prospecting', 'Negotiation', 'Customer Relationship Management (CRM)', 'Communication', 'Lead Generation', 'Sales Strategy', 'Customer Experience', 'Presentation Skills', 'Persuasion'],
            ],
            'Recruiter' => [
                'image' => $images['people'],
                'summary' => 'Recruiters connect talent with opportunity through sourcing, interviewing, and candidate experience. Build skills in talent acquisition, interviewing, communication, and HR tools.',
                'skills' => ['Recruiting', 'Talent Acquisition', 'Interviewing', 'Human Resources', 'Sourcing', 'Communication', 'LinkedIn Recruiter', 'Candidate Experience', 'Employer Branding', 'Diversity Recruiting'],
            ],
            'Marketing Specialist' => [
                'image' => $images['marketing'],
                'summary' => 'Marketing specialists execute campaigns, create content, and analyze channel performance. Build strong foundations in digital marketing, content, analytics, and SEO.',
                'skills' => ['Marketing', 'Digital Marketing', 'Content Marketing', 'Social Media Marketing', 'Search Engine Optimization (SEO)', 'Email Marketing', 'Google Analytics', 'Copywriting', 'Branding', 'Marketing Strategy'],
            ],
            'Human Resources Specialist' => [
                'image' => $images['people'],
                'summary' => 'HR specialists support employee programs, compliance, recruiting, and workplace culture. Build skills in HR operations, communication, employee relations, and talent practices.',
                'skills' => ['Human Resources', 'Employee Relations', 'Recruiting', 'Talent Management', 'Performance Management', 'Communication', 'HR Compliance', 'Onboarding', 'Diversity and Inclusion', 'Learning and Development'],
            ],
            'Supply Chain Specialist' => [
                'image' => $images['operations'],
                'summary' => 'Supply chain specialists coordinate the flow of materials, information, and suppliers. Build planning, logistics, procurement, analytics, and operations skills.',
                'skills' => ['Supply Chain Management', 'Logistics', 'Procurement', 'Inventory Management', 'Operations Management', 'Data Analysis', 'Forecasting', 'Process Improvement', 'Vendor Management', 'Microsoft Excel'],
            ],
            'Social Media Manager' => [
                'image' => $images['marketing'],
                'summary' => 'Social media managers build audiences, plan content, and measure channel performance. Strengthen content strategy, analytics, community management, and paid social skills.',
                'skills' => ['Social Media Marketing', 'Content Marketing', 'Digital Marketing', 'Branding', 'Copywriting', 'Marketing Strategy', 'Community Management', 'Social Media Advertising', 'Google Analytics', 'AI for Business'],
            ],
            'People Manager' => [
                'image' => $images['people'],
                'summary' => 'People managers help teams perform, grow, and collaborate. Build leadership, coaching, feedback, communication, and performance management skills.',
                'skills' => ['Leadership', 'Team Management', 'Coaching', 'Communication', 'Performance Management', 'Emotional Intelligence', 'Conflict Resolution', 'Feedback', 'Change Management', 'Employee Engagement'],
            ],
            'Human Resources Manager' => [
                'image' => $images['people'],
                'summary' => 'HR managers lead people programs that support culture, performance, and organizational growth. Build skills in HR strategy, talent management, employee relations, and analytics.',
                'skills' => ['Human Resources', 'HR Strategy', 'Talent Management', 'Employee Relations', 'Performance Management', 'Leadership', 'Diversity and Inclusion', 'Learning and Development', 'People Analytics', 'Change Management'],
            ],
            'HR Manager' => [
                'image' => $images['people'],
                'summary' => 'HR managers lead people programs that support culture, performance, and organizational growth. Build skills in HR strategy, talent management, employee relations, and analytics.',
                'skills' => ['Human Resources', 'HR Strategy', 'Talent Management', 'Employee Relations', 'Performance Management', 'Leadership', 'Diversity and Inclusion', 'Learning and Development', 'People Analytics', 'Change Management'],
            ],
            'Customer Service Manager' => [
                'image' => $images['service'],
                'summary' => 'Customer service managers lead support teams and improve the customer experience. Build skills in service operations, coaching, communication, and customer success.',
                'skills' => ['Customer Service', 'Customer Experience', 'Team Management', 'Leadership', 'Communication', 'Conflict Resolution', 'Customer Success', 'Service Operations', 'Coaching', 'Customer Relationship Management (CRM)'],
            ],
            'Customer Service Representative' => [
                'image' => $images['service'],
                'summary' => 'Customer service representatives solve problems, communicate clearly, and create positive customer moments. Build skills in service, empathy, communication, and CRM tools.',
                'skills' => ['Customer Service', 'Communication', 'Problem Solving', 'Customer Experience', 'Conflict Resolution', 'Active Listening', 'Customer Relationship Management (CRM)', 'Time Management', 'Empathy', 'Service Operations'],
            ],
            'Business Analyst' => [
                'image' => $images['analysis'],
                'summary' => 'Business analysts clarify problems, analyze data, and translate needs into useful requirements. Build skills in analysis, process modeling, SQL, Excel, and stakeholder communication.',
                'skills' => ['Business Analysis', 'Requirements Gathering', 'Data Analysis', 'Business Analytics', 'SQL', 'Microsoft Excel', 'Process Improvement', 'Stakeholder Management', 'Data Visualization', 'Communication'],
            ],
            'Account Executive' => [
                'image' => $images['sales'],
                'summary' => 'Account executives manage opportunities, build relationships, and close deals. Build sales, negotiation, presentation, CRM, and account planning skills.',
                'skills' => ['Sales', 'Account Management', 'Negotiation', 'Customer Relationship Management (CRM)', 'Sales Strategy', 'Presentation Skills', 'Prospecting', 'Communication', 'Business Development', 'Customer Experience'],
            ],
            'Data Analyst' => [
                'image' => $images['analysis'],
                'summary' => 'Data analysts turn raw information into insight. Build skills in SQL, Excel, visualization, statistics, dashboards, and business analysis.',
                'skills' => ['Data Analysis', 'SQL', 'Microsoft Excel', 'Data Visualization', 'Business Analytics', 'Statistics', 'Power BI', 'Tableau', 'Python', 'Data Cleaning'],
            ],
            'Chief of Staff' => [
                'image' => $images['operations'],
                'summary' => 'Chiefs of staff help leaders turn strategy into coordinated action. Build skills in strategic planning, communication, operations, analytics, and stakeholder management.',
                'skills' => ['Strategic Planning', 'Leadership', 'Business Strategy', 'Communication', 'Operations Management', 'Stakeholder Management', 'Project Management', 'Change Management', 'Business Analytics', 'Executive Presence'],
            ],
            'Strategy Manager' => [
                'image' => $images['analysis'],
                'summary' => 'Strategy managers evaluate markets, shape priorities, and guide business decisions. Build skills in strategy, analytics, competitive intelligence, and executive communication.',
                'skills' => ['Business Strategy', 'Strategic Planning', 'Competitive Intelligence', 'Market Research', 'Business Analytics', 'Financial Analysis', 'Data Analysis', 'Leadership', 'Communication', 'Change Management'],
            ],
            'Software Engineer' => [
                'image' => $images['engineering'],
                'summary' => 'Software engineers design, build, test, and maintain applications. Build skills in programming, architecture, debugging, collaboration, and reliable software delivery.',
                'skills' => ['Software Development', 'Programming', 'JavaScript', 'Python', 'Object-Oriented Programming', 'Algorithms', 'GitHub', 'Debugging', 'Software Architecture', 'Testing'],
            ],
            'Data Scientist' => [
                'image' => $images['analysis'],
                'summary' => 'Data scientists use statistics, programming, and machine learning to turn data into predictions and decisions. Build skills in Python, modeling, analytics, and visualization.',
                'skills' => ['Data Science', 'Python', 'Machine Learning', 'Statistics', 'Data Analysis', 'Data Visualization', 'SQL', 'Artificial Intelligence', 'Deep Learning', 'Predictive Analytics'],
            ],
            'Web Developer' => [
                'image' => $images['engineering'],
                'summary' => 'Web developers build browser-based experiences with strong foundations in HTML, CSS, JavaScript, accessibility, and deployment workflows.',
                'skills' => ['Web Development', 'HTML', 'CSS', 'JavaScript', 'Responsive Design', 'React', 'GitHub', 'Accessibility', 'APIs', 'Testing'],
            ],
            'Full-Stack Developer' => [
                'image' => $images['engineering'],
                'summary' => 'Full-stack developers work across front-end interfaces, back-end services, databases, and deployment. Build broad engineering fluency from UI to APIs.',
                'skills' => ['Full-Stack Development', 'JavaScript', 'React', 'Node.js', 'APIs', 'SQL', 'Database Design', 'GitHub', 'Cloud Computing', 'Testing'],
            ],
            'Front-End Developer' => [
                'image' => $images['engineering'],
                'summary' => 'Front-end developers craft user interfaces that are fast, accessible, and maintainable. Build skills in HTML, CSS, JavaScript, frameworks, and design collaboration.',
                'skills' => ['Front-End Development', 'HTML', 'CSS', 'JavaScript', 'React', 'Responsive Design', 'Accessibility', 'User Experience (UX)', 'GitHub', 'Testing'],
            ],
            'Back-End Developer' => [
                'image' => $images['engineering'],
                'summary' => 'Back-end developers build the services, APIs, and data layers that power applications. Build skills in databases, architecture, security, and cloud deployment.',
                'skills' => ['Back-End Development', 'APIs', 'Node.js', 'Python', 'SQL', 'Database Design', 'Cloud Computing', 'Security', 'Software Architecture', 'Testing'],
            ],
            'Cloud Engineer' => [
                'image' => $images['cloud'],
                'summary' => 'Cloud engineers design and operate scalable systems in cloud environments. Build skills in AWS, Azure, networking, automation, and monitoring.',
                'skills' => ['Cloud Computing', 'Amazon Web Services (AWS)', 'Microsoft Azure', 'Networking', 'DevOps', 'Linux', 'Security', 'Automation', 'Kubernetes', 'Monitoring'],
            ],
            'DevOps Engineer' => [
                'image' => $images['cloud'],
                'summary' => 'DevOps engineers improve software delivery through automation, CI/CD, infrastructure, monitoring, and collaborative operations practices.',
                'skills' => ['DevOps', 'CI/CD', 'Docker', 'Kubernetes', 'Linux', 'Cloud Computing', 'Automation', 'GitHub', 'Monitoring', 'Infrastructure as Code'],
            ],
            'Cybersecurity Analyst' => [
                'image' => $images['security'],
                'summary' => 'Cybersecurity analysts monitor threats, protect systems, and respond to incidents. Build skills in security fundamentals, risk, networking, and threat detection.',
                'skills' => ['Cybersecurity', 'Information Security', 'Network Security', 'Security Monitoring', 'Risk Management', 'Incident Response', 'Linux', 'Cloud Security', 'Threat Hunting', 'SIEM'],
            ],
            'IT Support Specialist' => [
                'image' => $images['engineering'],
                'summary' => 'IT support specialists help users, troubleshoot systems, and keep workplace technology running. Build skills in hardware, operating systems, networking, and customer service.',
                'skills' => ['IT Support', 'Troubleshooting', 'Windows', 'Networking', 'Customer Service', 'Technical Support', 'Linux', 'Cybersecurity', 'Microsoft 365', 'Hardware'],
            ],
            'Network Administrator' => [
                'image' => $images['cloud'],
                'summary' => 'Network administrators maintain reliable connectivity and secure infrastructure. Build skills in routing, switching, troubleshooting, security, and monitoring.',
                'skills' => ['Networking', 'Network Administration', 'Network Security', 'Cisco', 'Troubleshooting', 'Linux', 'Cloud Computing', 'Security Monitoring', 'TCP/IP', 'Firewall'],
            ],
            'Database Administrator' => [
                'image' => $images['analysis'],
                'summary' => 'Database administrators keep data platforms reliable, secure, and performant. Build skills in SQL, backup, tuning, database design, and administration.',
                'skills' => ['Database Administration', 'SQL', 'Database Design', 'MySQL', 'Microsoft SQL Server', 'Performance Tuning', 'Data Management', 'Security', 'Backup and Recovery', 'Cloud Databases'],
            ],
            'Machine Learning Engineer' => [
                'image' => $images['analysis'],
                'summary' => 'Machine learning engineers build, evaluate, and deploy models into production systems. Build skills in Python, ML workflows, cloud, and model operations.',
                'skills' => ['Machine Learning', 'Python', 'Artificial Intelligence', 'Deep Learning', 'Data Science', 'MLOps', 'Cloud Computing', 'TensorFlow', 'PyTorch', 'Data Engineering'],
            ],
            'AI Engineer' => [
                'image' => $images['analysis'],
                'summary' => 'AI engineers build intelligent systems using models, prompts, APIs, and data pipelines. Build skills in generative AI, Python, machine learning, and responsible AI.',
                'skills' => ['Artificial Intelligence', 'Generative AI', 'Python', 'Machine Learning', 'Prompt Engineering', 'APIs', 'Data Science', 'Responsible AI', 'Cloud Computing', 'Automation'],
            ],
            'Systems Administrator' => [
                'image' => $images['engineering'],
                'summary' => 'Systems administrators manage servers, operating systems, access, automation, and reliability. Build practical skills across Linux, Windows, security, and cloud tools.',
                'skills' => ['Systems Administration', 'Linux', 'Windows Server', 'Networking', 'Security', 'Automation', 'Cloud Computing', 'Troubleshooting', 'PowerShell', 'Monitoring'],
            ],
            'QA Engineer' => [
                'image' => $images['engineering'],
                'summary' => 'QA engineers improve software quality with testing strategy, automation, and clear defect reporting. Build skills in test design, Selenium, debugging, and CI/CD.',
                'skills' => ['Quality Assurance', 'Software Testing', 'Test Automation', 'Selenium', 'Debugging', 'Agile Project Management', 'JavaScript', 'Python', 'CI/CD', 'API Testing'],
            ],
            'Mobile Developer' => [
                'image' => $images['engineering'],
                'summary' => 'Mobile developers build apps for phones and tablets with attention to performance, usability, APIs, and release workflows.',
                'skills' => ['Mobile Development', 'iOS Development', 'Android Development', 'Kotlin', 'Swift', 'React Native', 'APIs', 'User Experience (UX)', 'Testing', 'GitHub'],
            ],
            'Solutions Architect' => [
                'image' => $images['cloud'],
                'summary' => 'Solutions architects design systems that meet business, technical, security, and scale requirements. Build skills in architecture, cloud, integration, and communication.',
                'skills' => ['Solution Architecture', 'Cloud Computing', 'Amazon Web Services (AWS)', 'Microsoft Azure', 'Software Architecture', 'APIs', 'Security', 'Networking', 'Business Analysis', 'Communication'],
            ],
            'Data Engineer' => [
                'image' => $images['analysis'],
                'summary' => 'Data engineers build pipelines, models, and platforms that make data usable. Build skills in SQL, Python, databases, cloud, and data warehousing.',
                'skills' => ['Data Engineering', 'SQL', 'Python', 'ETL', 'Data Warehousing', 'Cloud Computing', 'Databases', 'Apache Spark', 'Data Modeling', 'Automation'],
            ],
            'Security Engineer' => [
                'image' => $images['security'],
                'summary' => 'Security engineers design controls, harden systems, and automate protection across applications, networks, and cloud environments.',
                'skills' => ['Cybersecurity', 'Cloud Security', 'Network Security', 'Application Security', 'Security Engineering', 'DevSecOps', 'Risk Management', 'Linux', 'Automation', 'Incident Response'],
            ],
            'Technical Program Manager' => [
                'image' => $images['operations'],
                'summary' => 'Technical program managers coordinate complex engineering initiatives across teams. Build skills in technical planning, execution, communication, and risk management.',
                'skills' => ['Technical Program Management', 'Program Management', 'Project Management', 'Agile Project Management', 'Stakeholder Management', 'Software Development', 'Risk Management', 'Communication', 'Cloud Computing', 'Leadership'],
            ],
            'Graphic Designer' => [
                'image' => $images['design'],
                'summary' => 'Graphic designers communicate ideas visually through layout, typography, color, and brand systems. Build skills in design principles and creative tools.',
                'skills' => ['Graphic Design', 'Adobe Photoshop', 'Adobe Illustrator', 'Typography', 'Layout Design', 'Branding', 'Color Theory', 'Visual Design', 'Creative Thinking', 'Design Principles'],
            ],
            'UX Designer' => [
                'image' => $images['design'],
                'summary' => 'UX designers understand user needs and shape products that are clear, useful, and accessible. Build skills in research, prototyping, usability, and interaction design.',
                'skills' => ['User Experience (UX)', 'User Research', 'Wireframing', 'Prototyping', 'Interaction Design', 'Usability Testing', 'Figma', 'Accessibility', 'Product Design', 'Design Thinking'],
            ],
            'UI Designer' => [
                'image' => $images['design'],
                'summary' => 'UI designers create polished interfaces with strong visual hierarchy, components, and interaction patterns. Build skills in visual design, Figma, and design systems.',
                'skills' => ['User Interface Design', 'Visual Design', 'Figma', 'Design Systems', 'Typography', 'Color Theory', 'Interaction Design', 'Prototyping', 'Web Design', 'Accessibility'],
            ],
            'Web Designer' => [
                'image' => $images['design'],
                'summary' => 'Web designers combine visual design, layout, content, and responsive thinking to create effective websites.',
                'skills' => ['Web Design', 'HTML', 'CSS', 'Responsive Design', 'User Experience (UX)', 'Graphic Design', 'Figma', 'Typography', 'Accessibility', 'Branding'],
            ],
            'Video Editor' => [
                'image' => $images['media'],
                'summary' => 'Video editors shape raw footage into clear stories through pacing, sound, color, and post-production workflows.',
                'skills' => ['Video Editing', 'Adobe Premiere Pro', 'Post-Production', 'Storytelling', 'Color Correction', 'Audio Editing', 'Motion Graphics', 'DaVinci Resolve', 'YouTube', 'Creative Thinking'],
            ],
            'Motion Graphics Designer' => [
                'image' => $images['media'],
                'summary' => 'Motion graphics designers bring visual ideas to life with animation, timing, typography, and compositing.',
                'skills' => ['Motion Graphics', 'Adobe After Effects', 'Animation', 'Visual Design', 'Typography', 'Video Editing', 'Storyboarding', 'Adobe Illustrator', 'Creative Thinking', '3D Design'],
            ],
            'Photographer' => [
                'image' => $images['media'],
                'summary' => 'Photographers use light, composition, editing, and visual storytelling to create compelling images.',
                'skills' => ['Photography', 'Photo Editing', 'Adobe Lightroom', 'Adobe Photoshop', 'Composition', 'Lighting', 'Portrait Photography', 'Color Correction', 'Visual Storytelling', 'Creative Thinking'],
            ],
            'Illustrator' => [
                'image' => $images['creative'],
                'summary' => 'Illustrators create visual artwork for stories, products, brands, and publications. Build skills in drawing, composition, tools, and style development.',
                'skills' => ['Illustration', 'Drawing', 'Adobe Illustrator', 'Adobe Photoshop', 'Digital Illustration', 'Composition', 'Color Theory', 'Storytelling', 'Creative Thinking', 'Visual Design'],
            ],
            '3D Artist' => [
                'image' => $images['creative'],
                'summary' => '3D artists model, texture, light, and render digital objects and environments for design, media, games, and visualization.',
                'skills' => ['3D Design', 'Blender', '3D Modeling', 'Rendering', 'Animation', 'Texturing', 'Lighting', 'Visual Design', 'Creative Thinking', 'Game Design'],
            ],
            'Content Creator' => [
                'image' => $images['media'],
                'summary' => 'Content creators plan, produce, and publish engaging media across channels. Build skills in content strategy, video, social media, and storytelling.',
                'skills' => ['Content Creation', 'Content Marketing', 'Social Media Marketing', 'Video Editing', 'Storytelling', 'Copywriting', 'YouTube', 'Branding', 'Digital Marketing', 'Creative Thinking'],
            ],
            'Copywriter' => [
                'image' => $images['creative'],
                'summary' => 'Copywriters use words to clarify value, persuade audiences, and support campaigns. Build skills in writing, messaging, editing, and brand voice.',
                'skills' => ['Copywriting', 'Writing', 'Content Marketing', 'Branding', 'Marketing Strategy', 'Storytelling', 'Editing', 'Search Engine Optimization (SEO)', 'Email Marketing', 'Creative Thinking'],
            ],
            'Creative Director' => [
                'image' => $images['creative'],
                'summary' => 'Creative directors shape concepts, guide teams, and protect brand vision across campaigns and experiences.',
                'skills' => ['Creative Direction', 'Branding', 'Leadership', 'Graphic Design', 'Marketing Strategy', 'Storytelling', 'Art Direction', 'Creative Thinking', 'Communication', 'Design Thinking'],
            ],
            'Brand Designer' => [
                'image' => $images['design'],
                'summary' => 'Brand designers create visual systems that make organizations recognizable and consistent across touchpoints.',
                'skills' => ['Branding', 'Graphic Design', 'Logo Design', 'Typography', 'Color Theory', 'Visual Design', 'Adobe Illustrator', 'Design Systems', 'Creative Thinking', 'Marketing Strategy'],
            ],
            'Art Director' => [
                'image' => $images['creative'],
                'summary' => 'Art directors guide the visual expression of campaigns, publications, products, and brands.',
                'skills' => ['Art Direction', 'Creative Direction', 'Graphic Design', 'Visual Design', 'Typography', 'Branding', 'Photography', 'Storytelling', 'Leadership', 'Creative Thinking'],
            ],
            'Instructional Designer' => [
                'image' => $images['design'],
                'summary' => 'Instructional designers create learning experiences that help people build knowledge and skills effectively.',
                'skills' => ['Instructional Design', 'E-Learning', 'Learning and Development', 'Curriculum Design', 'Storyboarding', 'Adult Learning', 'Learning Management Systems', 'Visual Design', 'Communication', 'Assessment Design'],
            ],
            'Digital Illustrator' => [
                'image' => $images['creative'],
                'summary' => 'Digital illustrators use drawing tools and visual storytelling to create artwork for digital products, media, and brands.',
                'skills' => ['Digital Illustration', 'Illustration', 'Drawing', 'Adobe Illustrator', 'Adobe Photoshop', 'Procreate', 'Composition', 'Color Theory', 'Creative Thinking', 'Visual Design'],
            ],
            'Animation Artist' => [
                'image' => $images['media'],
                'summary' => 'Animation artists use motion, timing, and storytelling to bring characters, interfaces, and concepts to life.',
                'skills' => ['Animation', 'Motion Graphics', 'Adobe After Effects', 'Storyboarding', '3D Design', 'Character Animation', 'Video Editing', 'Creative Thinking', 'Visual Design', 'Typography'],
            ],
            'Presentation Designer' => [
                'image' => $images['design'],
                'summary' => 'Presentation designers turn ideas into clear, persuasive visual stories for meetings, pitches, and executive communication.',
                'skills' => ['Presentation Design', 'Microsoft PowerPoint', 'Visual Design', 'Storytelling', 'Graphic Design', 'Data Visualization', 'Typography', 'Branding', 'Communication', 'Design Principles'],
            ],
            'Design Systems Designer' => [
                'image' => $images['design'],
                'summary' => 'Design systems designers create reusable components, patterns, and guidelines that help teams build coherent products.',
                'skills' => ['Design Systems', 'User Interface Design', 'Figma', 'Interaction Design', 'Accessibility', 'Front-End Development', 'User Experience (UX)', 'Documentation', 'Visual Design', 'Product Design'],
            ],
            'Social Media Designer' => [
                'image' => $images['media'],
                'summary' => 'Social media designers create visual content tailored for fast-moving channels, campaigns, and communities.',
                'skills' => ['Social Media Design', 'Graphic Design', 'Social Media Marketing', 'Adobe Photoshop', 'Canva', 'Branding', 'Content Creation', 'Motion Graphics', 'Copywriting', 'Digital Marketing'],
            ],
        ];

        return collect($roles)->mapWithKeys(function (array $data, string $title) {
            $slug = 'role-'.Str::slug($title);

            return [$slug => [
                'slug' => $slug,
                'title' => $title,
                'summary' => $data['summary'],
                'skills' => $data['skills'],
                'hero_image' => $data['image'],
                'browse_query' => $title,
            ]];
        })->all();
    }

    private function shortVideos(array $role): Collection
    {
        $terms = $this->roleTerms($role);

        $videos = Video::query()
            ->with('course')
            ->where('tipe', 'video')
            ->whereHas('course', fn ($query) => $this->applyCourseTerms($query->published(), $terms))
            ->where('durasi_detik', '>', 0)
            ->orderBy('durasi_detik')
            ->limit(4)
            ->get();

        if ($videos->isEmpty()) {
            return $this->fallbackShortVideos($role);
        }

        return $videos->map(fn (Video $video) => [
            'type' => 'Video',
            'title' => $video->title,
            'duration' => $video->durasi ?: $video->durasi_format,
            'author' => $video->course?->instructor_name ?: 'LinkedIn Learning',
            'description' => $video->course?->description,
            'learners' => $video->course?->jumlah_learner,
            'url' => $video->course ? route('course.show', $video->course) : '#',
            'thumbnail' => $video->course?->thumbnail,
        ]);
    }

    private function coursesForSkill(string $skill, array $role): Collection
    {
        $terms = $this->termsForSkill($skill);

        $courses = $this->baseCourseQuery()
            ->whereHas('skills', function ($query) use ($terms) {
                $query->where(function ($skillQuery) use ($terms) {
                    foreach ($terms as $term) {
                        $skillQuery
                            ->orWhere('skills.name', 'like', '%'.$term.'%')
                            ->orWhere('skills.slug', 'like', '%'.Str::slug($term).'%');
                    }
                });
            })
            ->orderByDesc('jumlah_learner')
            ->limit(8)
            ->get();

        if ($courses->isEmpty()) {
            $courses = $this->baseCourseQuery()
                ->where(fn ($query) => $this->applyCourseTerms($query, $terms))
                ->orderByDesc('jumlah_learner')
                ->limit(8)
                ->get();
        }

        if ($courses->isEmpty()) {
            return $this->fallbackCourses($skill, $role);
        }

        return $courses->map(fn (Course $course) => $this->mapCourse($course));
    }

    private function baseCourseQuery()
    {
        return Course::published()->withLearningMetrics();
    }

    private function roleTerms(array $role): array
    {
        return array_values(array_unique(array_merge(
            [$role['title']],
            array_slice($role['skills'], 0, 5),
            collect($role['skills'])->flatMap(fn ($skill) => $this->termsForSkill($skill))->take(8)->all(),
        )));
    }

    private function applyCourseTerms($query, array $terms)
    {
        return $query->where(function ($courseQuery) use ($terms) {
            foreach ($terms as $term) {
                $courseQuery
                    ->orWhere('title', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            }
        });
    }

    private function termsForSkill(string $skill): array
    {
        $terms = [$skill];
        $clean = trim(preg_replace('/\s*\([^)]*\)/', '', $skill) ?? $skill);
        if ($clean !== $skill) {
            $terms[] = $clean;
        }

        $extra = match ($skill) {
            'Marketing Strategy' => ['competitive intelligence', 'brand strategy'],
            'Digital Marketing' => ['online marketing', 'conversions'],
            'Social Media Marketing' => ['social media', 'social marketing'],
            'Search Engine Optimization (SEO)' => ['SEO', 'search engine optimization'],
            'Customer Relationship Management (CRM)' => ['CRM', 'customer relationship', 'salesforce'],
            'AI for Business' => ['generative AI', 'artificial intelligence'],
            'Human Resources' => ['HR', 'people operations'],
            'Microsoft Excel' => ['Excel'],
            'Agile Project Management' => ['agile', 'scrum'],
            'Supply Chain Management' => ['supply chain', 'logistics'],
            'Business Analytics' => ['analytics'],
            'Customer Service' => ['customer support'],
            'Data Visualization' => ['visualization', 'dashboard'],
            default => [],
        };

        return array_values(array_unique(array_filter(array_merge($terms, $extra))));
    }

    private function mapCourse(Course $course): array
    {
        return [
            'type' => 'Course',
            'title' => $course->title,
            'duration' => $course->durasi ?: $course->durasi_format,
            'author' => $course->instructor_name ?: 'LinkedIn Learning',
            'date' => $course->release_date?->format('M j, Y'),
            'description' => $course->description,
            'learners' => $course->display_learner_count,
            'url' => route('course.show', $course),
            'thumbnail' => $course->thumbnail,
        ];
    }

    private function fallbackShortVideos(array $role): Collection
    {
        return collect([
            [
                'type' => 'Video',
                'title' => 'Overview of '.$role['title'].' roles',
                'duration' => '2m 41s',
                'author' => 'LinkedIn Learning',
                'description' => 'A quick primer on responsibilities, common workflows, and expectations for this role.',
                'learners' => 0,
                'url' => route('browse', ['q' => $role['browse_query']]),
                'thumbnail' => null,
            ],
        ]);
    }

    private function fallbackCourses(string $skill, array $role): Collection
    {
        return collect([
            [
                'type' => 'Course',
                'title' => $skill.' Foundations',
                'duration' => '1h 15m',
                'author' => 'LinkedIn Learning',
                'date' => null,
                'description' => 'Build the core concepts and practical habits needed to apply this skill in a '.$role['title'].' role.',
                'learners' => 0,
                'url' => route('browse', ['q' => $skill]),
                'thumbnail' => null,
            ],
        ]);
    }
}
