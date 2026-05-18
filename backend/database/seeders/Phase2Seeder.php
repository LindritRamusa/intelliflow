<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Candidate;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Phase2Seeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::first();
        $user = User::first();

        if (!$org || !$user) {
            $this->command->warn('No organization or user found. Register first, then run the seeder.');
            return;
        }

        $this->seedCandidates($org->id);
        $this->seedArticles($org->id, $user->id);

        $this->command->info('Phase 2 seed data created successfully.');
    }

    private function seedCandidates(int $orgId): void
    {
        $candidates = [
            [
                'name' => 'Sarah Mitchell',
                'email' => 'sarah.mitchell@example.com',
                'phone' => '+1 555 201 3344',
                'applied_role' => 'Senior Software Engineer',
                'source' => 'LinkedIn',
                'status' => 'shortlisted',
                'ai_score' => 87,
                'ai_analysis' => 'Highly qualified candidate with strong full-stack background and leadership experience.',
                'ai_strengths' => ['8+ years TypeScript/React experience', 'Led teams of 5+ engineers', 'Strong system design skills'],
                'ai_weaknesses' => ['Limited mobile development experience', 'No public cloud certifications'],
                'skills' => ['TypeScript', 'React', 'Node.js', 'PostgreSQL', 'AWS', 'Docker', 'System Design'],
                'cv_text' => "Sarah Mitchell — Senior Software Engineer\nEmail: sarah.mitchell@example.com\n\nSUMMARY\n8+ years building scalable web applications. Led multiple cross-functional teams. Expert in TypeScript, React, and Node.js. Strong background in distributed systems and microservices architecture.\n\nEXPERIENCE\nStaff Engineer — TechCorp Inc (2021–Present)\n- Led architecture of real-time analytics platform handling 50M events/day\n- Mentored team of 6 engineers, ran weekly design reviews\n- Reduced API latency by 40% through query optimization\n\nSenior Engineer — StartupXYZ (2018–2021)\n- Built full-stack SaaS product from 0 to 100k users\n- Implemented CI/CD pipelines with GitHub Actions and Docker\n\nEDUCATION\nB.Sc. Computer Science — MIT (2014–2018)\n\nSKILLS\nTypeScript, React, Node.js, PostgreSQL, Redis, AWS, Docker, Kubernetes, GraphQL",
                'notes' => 'Strong portfolio. Recommended by internal engineer.',
            ],
            [
                'name' => 'James Okonkwo',
                'email' => 'james.okonkwo@example.com',
                'phone' => '+44 7700 123456',
                'applied_role' => 'Product Manager',
                'source' => 'Referral',
                'status' => 'interview',
                'ai_score' => 74,
                'ai_analysis' => 'Good PM candidate with solid B2B SaaS background. Some gaps in technical depth but strong on execution.',
                'ai_strengths' => ['3 successful product launches', 'Strong stakeholder management', 'Data-driven approach with SQL skills'],
                'ai_weaknesses' => ['Limited enterprise sales exposure', 'No experience with AI/ML products'],
                'skills' => ['Product Strategy', 'Roadmapping', 'A/B Testing', 'SQL', 'Figma', 'JIRA', 'User Research'],
                'cv_text' => "James Okonkwo — Product Manager\n\nSUMMARY\n5 years in B2B SaaS product management. Launched 3 major products from 0 to GA. Strong mix of strategic thinking and execution. Data-driven with hands-on SQL skills.\n\nEXPERIENCE\nProduct Manager — GrowthBase (2022–Present)\n- Owned end-to-end roadmap for analytics module (€2M ARR)\n- Ran 20+ user interviews and weekly stakeholder syncs\n- Increased activation rate by 28% through onboarding redesign\n\nAssociate PM — SaasFlow (2020–2022)\n- Delivered 3 feature releases on schedule\n- Created PRDs and coordinated with engineering and design\n\nEDUCATION\nMBA — London Business School (2019)\nB.A. Economics — University of Lagos (2015)\n\nSKILLS\nProduct strategy, roadmapping, user research, SQL, Figma, JIRA, A/B testing",
                'notes' => 'Good culture fit. Technical round scheduled.',
            ],
            [
                'name' => 'Anika Patel',
                'email' => 'anika.patel@example.com',
                'phone' => '+1 415 987 6543',
                'applied_role' => 'UX Designer',
                'source' => 'Indeed',
                'status' => 'review',
                'ai_score' => 68,
                'ai_analysis' => 'Solid UX foundation with good portfolio. Needs more enterprise-scale design systems experience.',
                'ai_strengths' => ['Strong visual design skills', 'Proficient in Figma and prototyping', 'User research background'],
                'ai_weaknesses' => ['Limited design system work at scale', 'No native mobile design in portfolio'],
                'skills' => ['Figma', 'User Research', 'Prototyping', 'Wireframing', 'Design Systems', 'Usability Testing'],
                'cv_text' => "Anika Patel — UX Designer\n\nSUMMARY\n4 years creating user-centered digital experiences. Proficient in Figma, design systems, and user research. Worked across SaaS, fintech, and e-commerce.\n\nEXPERIENCE\nUX Designer — FinanceApp (2022–Present)\n- Redesigned onboarding flow, improving completion by 35%\n- Built component library with 80+ reusable elements\n- Conducted 15 usability tests per quarter\n\nJunior Designer — AgencyXYZ (2020–2022)\n- Delivered UI/UX for 10+ client projects\n- Created high-fidelity prototypes and design specs\n\nEDUCATION\nB.Des. Interaction Design — Parsons School of Design (2020)\n\nSKILLS\nFigma, Sketch, Prototyping, User Research, Design Systems, Usability Testing",
                'notes' => 'Portfolio looks clean. Needs design system deep dive in interview.',
            ],
            [
                'name' => 'Carlos Rivera',
                'email' => 'carlos.rivera@example.com',
                'phone' => '+34 612 345 678',
                'applied_role' => 'DevOps Engineer',
                'source' => 'LinkedIn',
                'status' => 'pending',
                'ai_score' => null,
                'ai_analysis' => null,
                'ai_strengths' => null,
                'ai_weaknesses' => null,
                'skills' => ['Kubernetes', 'Terraform', 'AWS', 'CI/CD'],
                'cv_text' => "Carlos Rivera — DevOps Engineer\n\nSUMMARY\n6 years in infrastructure and DevOps. Expert in Kubernetes, Terraform, and AWS. Passionate about reliability engineering and automation.\n\nEXPERIENCE\nSenior DevOps Engineer — CloudSystems (2021–Present)\n- Managed 200+ node Kubernetes clusters across 3 regions\n- Reduced deployment time from 45 min to 8 min with GitOps\n- Implemented SLO-based alerting using Prometheus + Grafana\n\nDevOps Engineer — WebScale (2019–2021)\n- Migrated monolith to microservices on AWS EKS\n- Built Terraform modules used across 4 product teams\n\nEDUCATION\nB.Eng. Computer Engineering — Universidad Politécnica de Madrid (2018)\n\nSKILLS\nKubernetes, Terraform, AWS, Docker, Helm, CI/CD, Prometheus, Grafana, Python, Bash",
                'notes' => 'Applied directly. CV not analyzed yet.',
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@example.com',
                'phone' => '+91 98765 43210',
                'applied_role' => 'Data Analyst',
                'source' => 'Company Website',
                'status' => 'rejected',
                'ai_score' => 42,
                'ai_analysis' => 'Candidate has basic analytical skills but lacks the SQL depth and business intelligence tooling experience required for the role.',
                'ai_strengths' => ['Excel and basic data visualization', 'Positive attitude noted in cover letter'],
                'ai_weaknesses' => ['No advanced SQL experience', 'No experience with BI tools (Tableau, Power BI)', 'Very limited data pipeline knowledge'],
                'skills' => ['Excel', 'Google Sheets', 'Basic SQL'],
                'cv_text' => "Priya Sharma — Data Analyst\n\nSUMMARY\n2 years working with data in operational roles. Comfortable with Excel and basic SQL. Looking to transition into a dedicated analyst role.\n\nEXPERIENCE\nOperations Coordinator — RetailCo (2022–Present)\n- Created weekly Excel dashboards for store performance\n- Ran basic SQL queries for inventory reports\n\nEDUCATION\nB.Com — Delhi University (2022)\n\nSKILLS\nExcel, Google Sheets, SQL (basic), PowerPoint",
                'notes' => 'Lacks required SQL depth.',
            ],
            [
                'name' => 'Tom Hendricks',
                'email' => 'tom.hendricks@example.com',
                'phone' => '+1 646 555 7890',
                'applied_role' => 'Backend Engineer',
                'source' => 'LinkedIn',
                'status' => 'hired',
                'ai_score' => 91,
                'ai_analysis' => 'Exceptional backend engineer. Strong distributed systems background with proven track record at scale.',
                'ai_strengths' => ['Expert in Go and distributed systems', '10+ years of backend experience', 'Open source contributions to major projects'],
                'ai_weaknesses' => ['Prefers remote only (potential timezone challenge)'],
                'skills' => ['Go', 'Rust', 'gRPC', 'Kafka', 'PostgreSQL', 'Redis', 'Distributed Systems', 'AWS'],
                'cv_text' => "Tom Hendricks — Staff Backend Engineer\n\nSUMMARY\n10 years building high-performance distributed systems. Core maintainer of 2 open-source projects with 8k+ GitHub stars.\n\nEXPERIENCE\nStaff Engineer — MegaScale (2020–Present)\n- Designed event-driven architecture processing 1B+ messages/day using Kafka\n- Built multi-region replication layer in Go with <10ms p99 latency\n- Led adoption of gRPC across 12 internal services\n\nSenior Backend Engineer — StreamTech (2016–2020)\n- Rewrote core pipeline in Go (from Python), 8x throughput improvement\n- Authored company-wide PostgreSQL best practices guide\n\nEDUCATION\nM.Sc. Computer Science — Carnegie Mellon (2014)\n\nSKILLS\nGo, Rust, gRPC, Kafka, PostgreSQL, Redis, AWS, Distributed Systems",
                'notes' => 'Offer accepted. Starting next month.',
            ],
        ];

        foreach ($candidates as $data) {
            Candidate::create([...$data, 'organization_id' => $orgId]);
        }

        $this->command->info('Created ' . count($candidates) . ' sample candidates.');
    }

    private function seedArticles(int $orgId, int $userId): void
    {
        $articles = [
            [
                'title' => 'Getting Started with IntelliFlow Workflows',
                'category' => 'Engineering',
                'excerpt' => 'Learn how to create and configure your first automation workflow in IntelliFlow.',
                'published' => true,
                'content' => "# Getting Started with Workflows\n\nWorkflows are the core building blocks of IntelliFlow. This guide walks you through creating your first automated workflow.\n\n## What is a Workflow?\n\nA workflow is a sequence of automated steps triggered by a specific event. Each workflow can contain multiple automations that run conditionally based on rules you define.\n\n## Creating Your First Workflow\n\n1. Navigate to **Workflows** in the sidebar\n2. Click **New Workflow**\n3. Give it a descriptive name (e.g. \"Lead Nurture Flow\")\n4. Select a trigger type — this is the event that starts the workflow\n5. Set the initial status to Active\n\n## Connecting Automations\n\nOnce your workflow exists, you can attach automations to it from the Automations page. Each automation can have its own rule set defining when it fires within the workflow.\n\n## Best Practices\n\n- Keep workflows focused on a single business process\n- Use descriptive names so your team understands the purpose\n- Always test with the Manual trigger before going live\n- Monitor the run count to ensure automations are firing as expected",
            ],
            [
                'title' => 'AI Recruitment: How CV Scoring Works',
                'category' => 'HR',
                'excerpt' => 'Understanding the AI scoring algorithm behind candidate analysis in IntelliFlow Recruitment.',
                'published' => true,
                'content' => "# How AI CV Scoring Works\n\nIntelliFlow uses a large language model to analyze candidate CVs and produce structured scores and insights.\n\n## The Scoring Scale\n\n- **80–100**: Strong match — automatically shortlisted for review\n- **60–79**: Good potential — recommended for closer review\n- **0–59**: Below threshold — flagged for rejection\n\n## What Gets Analyzed\n\nThe AI evaluates:\n- Relevant experience and years in role\n- Technical and soft skills alignment\n- Career progression trajectory\n- Education and certifications\n- Clarity and quality of the CV itself\n\n## How to Get the Best Results\n\nPaste the full plain-text CV. The more content the model has, the more accurate the analysis. Avoid submitting only a name and job title.\n\n## Limitations\n\nThe AI score is a screening aid, not a final decision. Always review the strengths/weaknesses notes and conduct human interviews before making hiring decisions.\n\n## Privacy\n\nCV text is sent to the OpenRouter API for processing and stored in your organization's database. Do not submit CVs that haven't been consented to for automated processing.",
            ],
            [
                'title' => 'Automation Rule Builder Reference',
                'category' => 'Engineering',
                'excerpt' => 'Complete reference for all available conditions and actions in the visual rule builder.',
                'published' => true,
                'content' => "# Rule Builder Reference\n\nThe Rule Builder lets you define IF-THEN logic for each automation without writing code.\n\n## Conditions\n\nConditions filter when an automation fires. You can combine multiple conditions with AND (all must match) or OR (any must match).\n\n### Available Fields\n- **trigger.type** — The type of event that fired\n- **workflow.status** — Current status of the parent workflow\n- **execution.count** — How many times this automation has run\n- **time.hour** — Current hour (0–23)\n- **time.dayOfWeek** — Day of week (0=Sunday, 6=Saturday)\n\n### Operators\n- equals / does not equal\n- contains\n- is greater than / is less than\n- is empty / is not empty\n\n## Actions\n\nActions define what happens when conditions are met.\n\n| Action | Description |\n|--------|-------------|\n| Send Notification | Creates a system notification for your team |\n| Send Email | Sends an email to a specified address |\n| Trigger Webhook | POSTs a payload to an external URL |\n| Update Field | Modifies a data field value |\n| Create Task | Creates a task item |\n| Add Tag | Attaches a tag to the triggering record |\n\n## Tips\n- Start simple — one condition and one action\n- Use Send Notification to verify your automation fires before wiring real side effects\n- Combine multiple actions in one automation for complex flows",
            ],
            [
                'title' => 'Team Onboarding Checklist',
                'category' => 'HR',
                'excerpt' => 'Standard checklist for onboarding new team members to IntelliFlow and internal tools.',
                'published' => false,
                'content' => "# New Team Member Onboarding Checklist\n\nUse this checklist when onboarding any new hire to ensure they have access to everything they need.\n\n## Day 1\n\n- [ ] Send welcome email with login credentials\n- [ ] Create IntelliFlow account (company_admin role)\n- [ ] Set up Slack and invite to relevant channels\n- [ ] Schedule intro call with direct manager\n- [ ] Share this Knowledge Base link\n\n## Week 1\n\n- [ ] Complete security awareness training\n- [ ] Review the team's active workflows in IntelliFlow\n- [ ] Set up local development environment (see Engineering article)\n- [ ] Meet with each team lead (15-min intros)\n\n## Week 2\n\n- [ ] First solo task assigned and completed\n- [ ] Access to production read-only environment granted\n- [ ] Feedback session scheduled for end of month\n\n## Notes\n\nThis document is a draft. Publish once the HR team reviews and approves.",
            ],
            [
                'title' => 'Notification Types and When to Use Them',
                'category' => 'General',
                'excerpt' => 'Guide to the four notification types — info, success, warning, and error — and best practices.',
                'published' => true,
                'content' => "# Notification Types\n\nIntelliFlow generates four types of system notifications. Understanding each helps you quickly triage what needs attention.\n\n## Info (Blue)\n\nGeneral informational updates. No action required.\n\nExamples:\n- A new article was published to the Knowledge Base\n- A candidate CV analysis was completed\n- An automation ran successfully\n\n## Success (Green)\n\nPositive outcomes — something completed as intended.\n\nExamples:\n- A workflow was activated\n- A candidate was hired\n- A bulk operation completed\n\n## Warning (Amber)\n\nSomething needs your attention but isn't broken.\n\nExamples:\n- An automation has been disabled for 30+ days\n- A workflow has not run in over a week\n- A draft article has been unpublished for 14 days\n\n## Error (Red)\n\nSomething failed and may need intervention.\n\nExamples:\n- A webhook action returned a non-200 response\n- An automation failed to execute\n- A scheduled trigger was missed\n\n## Filtering\n\nUse the filter tabs on the Notifications page to isolate by type or view only unread items.",
            ],
        ];

        foreach ($articles as $data) {
            $content = $data['content'];
            $published = $data['published'];
            unset($data['content'], $data['published']);

            $baseSlug = Str::slug($data['title']);
            $slug = $baseSlug;
            $count = 1;
            while (Article::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            Article::create([
                ...$data,
                'slug' => $slug,
                'content' => $content,
                'organization_id' => $orgId,
                'author_id' => $userId,
                'published_at' => $published ? now()->subDays(rand(1, 30)) : null,
                'views' => rand(0, 150),
            ]);
        }

        $this->command->info('Created ' . count($articles) . ' sample articles.');
    }
}
