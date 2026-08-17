<?php

namespace Database\Seeders;

use App\Models\VerticalConfig;
use Illuminate\Database\Seeder;

class VerticalConfigSeeder extends Seeder
{
    public function run(): void
    {
        $verticals = [
            [
                'vertical_type' => 'care', 'label' => 'Care & Support Services',
                'description' => 'Elderly care, dementia support, home care',
                'default_tone' => 'warm, professional, compassionate',
                'default_topics' => ['elderly care', 'dementia', 'wellness', 'health'],
                'default_hashtags' => ['#caregiver', '#homecare', '#elderly'],
                'lead_questions' => ['Type of care needed?', 'When do you need help?'],
            ],
            [
                'vertical_type' => 'cleaning', 'label' => 'Cleaning & Decluttering',
                'description' => 'House cleaning, office cleaning, decluttering',
                'default_tone' => 'friendly, energetic, practical',
                'default_topics' => ['spring cleaning', 'organization', 'decluttering'],
                'default_hashtags' => ['#cleaning', '#homeorg', '#declutter'],
                'lead_questions' => ['What area?', 'Size of property?'],
            ],
            [
                'vertical_type' => 'real_estate', 'label' => 'Property & Real Estate',
                'description' => 'Property sales, lettings, valuations',
                'default_tone' => 'professional, authoritative, helpful',
                'default_topics' => ['property tips', 'market trends', 'home buying'],
                'default_hashtags' => ['#realestate', '#property', '#homes'],
                'lead_questions' => ['Buying or selling?', 'Property type?'],
            ],
            [
                'vertical_type' => 'fitness', 'label' => 'Fitness & Wellness',
                'description' => 'Gyms, personal training, wellness',
                'default_tone' => 'energetic, motivational, supportive',
                'default_topics' => ['fitness tips', 'motivation', 'wellness'],
                'default_hashtags' => ['#fitness', '#gym', '#wellness'],
                'lead_questions' => ['Fitness level?', 'Goals?'],
            ],
            [
                'vertical_type' => 'trades', 'label' => 'Trades & Services',
                'description' => 'Plumbing, electrical, handyman services',
                'default_tone' => 'expert, reliable, straightforward',
                'default_topics' => ['DIY tips', 'maintenance', 'repairs'],
                'default_hashtags' => ['#plumber', '#handyman', '#repair'],
                'lead_questions' => ['Issue type?', 'Urgency?'],
            ],
            [
                'vertical_type' => 'beauty', 'label' => 'Beauty & Salon',
                'description' => 'Hair, nails, beauty services',
                'default_tone' => 'friendly, warm, trendy',
                'default_topics' => ['beauty tips', 'trends', 'self-care'],
                'default_hashtags' => ['#salon', '#beauty', '#selfcare'],
                'lead_questions' => ['Service type?', 'First time?'],
            ],
            [
                'vertical_type' => 'legal', 'label' => 'Legal Services',
                'description' => 'Law firms, legal advice',
                'default_tone' => 'professional, authoritative, trustworthy',
                'default_topics' => ['legal tips', 'compliance', 'rights'],
                'default_hashtags' => ['#legal', '#lawyer', '#advice'],
                'lead_questions' => ['Issue type?', 'Urgency?'],
            ],
            [
                'vertical_type' => 'automotive', 'label' => 'Automotive & Mechanics',
                'description' => 'Car repair, maintenance, sales',
                'default_tone' => 'expert, reliable, helpful',
                'default_topics' => ['car maintenance', 'tips', 'repairs'],
                'default_hashtags' => ['#mechanic', '#autorepair', '#car'],
                'lead_questions' => ['Vehicle type?', 'Issue?'],
            ],
        ];

        foreach ($verticals as $vertical) {
            VerticalConfig::updateOrCreate(
                ['vertical_type' => $vertical['vertical_type']],
                $vertical
            );
        }
    }
}
