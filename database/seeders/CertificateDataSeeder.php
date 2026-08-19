<?php


namespace Database\Seeders;

use App\Enums\AttachmentStorageEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CertificateProvider;
use App\Models\Certificate;
use App\Models\Attachment;
use App\Enums\AttachmentDocumentTypeEnum;

class CertificateDataSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            'Google' => [
                'url' => 'https://google.com',
                'path' => 'certificate_provider_logos/Google.png.png',
                'certificates' => [
                    'Google IT Support Professional Certificate',
                    'Google Data Analytics Professional Certificate',
                    'Google UX Design Professional Certificate',
                    'Google Project Management Professional Certificate',
                    'Google Digital Marketing & E-commerce Professional Certificate',
                    'Google Cybersecurity Professional Certificate',
                    'Google IT Automation with Python Professional Certificate',
                    'Google Advanced Data Analytics Professional Certificate',
                    'Google Business Intelligence Professional Certificate',
                    'Foundations of Project Management',
                    'Foundations of User Experience (UX) Design',
                    'Technical Support Fundamentals',
                ],
            ],
            'Coursera' => [
                'url' => 'https://coursera.org',
                'path' => 'certificate_provider_logos/Coursera.png.png',
                'certificates' => [
                    'The Science of Well-Being – Yale University',
                    'Financial Markets – Yale University',
                    'Introduction to Psychology – Yale University',
                    'Python for Everybody – University of Michigan',
                    'Programming for Everybody (Getting Started with Python)',
                    'Applied Data Science with Python Specialization',
                    'Machine Learning – Stanford University',
                    'English for Career Development – University of Pennsylvania',
                    'Business Foundations Specialization – University of Pennsylvania',
                    'Gamification – University of Pennsylvania',
                    'Data Science Specialization – Johns Hopkins University',
                    'Genomic Data Science – Johns Hopkins University',
                    'Psychological First Aid – Johns Hopkins University',
                    'Digital Marketing Specialization – University of Illinois',
                    'Strategic Leadership and Management Specialization – University of Illinois',
                    'Data Visualization with Tableau – UC Davis',
                    'Learn SQL Basics for Data Science – UC Davis',
                    'Geographic Information Systems (GIS) – UC Davis',
                    'Search Engine Optimization (SEO) – UC Davis',
                    'Excel to MySQL: Analytic Techniques for Business – Duke University',
                    'Oil & Gas Industry Operations and Markets – Duke University',
                    'Fundamentals of Project Planning – University of Virginia',
                    'Social Media Marketing – Northwestern University',
                    'Investment Management – University of Geneva',
                    'Excel Skills for Business – Macquarie University',
                    'Investment Management with Python – EDHEC Business School',
                    'Supply Chain Management – Rutgers University',
                    'Supply Chain Logistics – Rutgers University',
                    'HR Management – University of Minnesota',
                    'Career Success Specialization – UC Irvine',
                    'Effective Communication – University of Colorado Boulder',
                    'Mind Control – University of Toronto',
                    'Self-Driving Cars – University of Toronto',
                    'First Step Korean – Yonsei University',
                ],
            ],
            'DeepLearning.AI' => [
                'url' => 'https://www.deeplearning.ai',
                'path' => 'certificate_provider_logos/DeepLearning.AI.png.png',
                'certificates' => [
                    'Neural Networks and Deep Learning',
                    'Introduction to TensorFlow for Artificial Intelligence, Machine Learning, and Deep Learning',
                ],
            ],
            'IBM' => [
                'url' => 'https://www.ibm.com',
                'path' => 'certificate_provider_logos/DeepLearning.AI.png.png',
                'certificates' => [
                    'IBM Data Science Professional Certificate',
                    'Data Analysis with Python',
                ],
            ],
            'Meta' => [
                'url' => 'https://about.meta.com',
                'path' => 'certificate_provider_logos/Meta.png.png',
                'certificates' => [
                    'Facebook Social Media Marketing',
                ],
            ],
            'PwC' => [
                'url' => 'https://www.pwc.com',
                'path' => 'certificate_provider_logos/pwc.png.png',
                'certificates' => [
                    'Data Analysis and Presentation Skills: the PwC Approach',
                ],
            ],
            'VMware' => [
                'url' => 'https://www.vmware.com',
                'path' => 'certificate_provider_logos/VMware.png.png',
                'certificates' => [
                    'Networking and Security Architecture with VMware NSX',
                ],
            ],
        ];

        foreach ($providers as $providerName => $data) {
            $provider = CertificateProvider::updateOrCreate(
                ['title_en' => $providerName],
                [
                    'uuid' => Str::uuid(),
                    'title_ar' => $providerName,
                    'slug' => Str::slug($providerName),
                    'description' => "$providerName Certificate Provider",
                    'website_url' => $data['url'],
                    'is_active' => true,
                ]
            );

            Attachment::updateOrCreate(
                [
                    'attachable_type' => CertificateProvider::class,
                    'attachable_id' => $provider->id,
                    'document_type' => AttachmentDocumentTypeEnum::LOGO->value,
                ],
                [
                    'user_id' => 1,
                    'path' => $data['path'],
                    'disk' => AttachmentStorageEnum::BLOB->value,
                ]
            );

            foreach ($data['certificates'] as $certTitle) {
                Certificate::updateOrCreate(
                    [
                        'certificate_provider_id' => $provider->id,
                        'title_en' => $certTitle,
                    ],
                    [
                        'title_ar' => $certTitle,
                        'slug' => Str::slug($certTitle),
                        'description' => $certTitle,
                        'level' => 'beginner',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
