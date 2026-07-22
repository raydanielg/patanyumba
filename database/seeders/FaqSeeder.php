<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'general',
                'question' => 'What is PataNyumba?',
                'answer' => 'PataNyumba is a property rental platform that connects tenants and landlords across Tanzania. You can browse, search, and contact property owners directly through the app.',
                'sort_order' => 1,
            ],
            [
                'category' => 'general',
                'question' => 'How do I create an account?',
                'answer' => 'Tap on "Register" on the login screen, fill in your name, email, phone number, and password. You will receive a verification email to activate your account.',
                'sort_order' => 2,
            ],
            [
                'category' => 'general',
                'question' => 'Is PataNyumba free to use?',
                'answer' => 'Yes, creating an account and browsing properties is free. However, unlocking contact details of property owners may require a subscription plan.',
                'sort_order' => 3,
            ],
            [
                'category' => 'properties',
                'question' => 'How do I search for properties?',
                'answer' => 'Use the Search tab to filter properties by region, district, price range, number of bedrooms, bathrooms, and property type. You can also browse by category from the Home screen.',
                'sort_order' => 1,
            ],
            [
                'category' => 'properties',
                'question' => 'How do I contact a property owner?',
                'answer' => 'Tap "Get Contact" on the property detail screen. You can choose to call online (in-app call) or call offline (using your phone dialer). Online calls are logged in the system.',
                'sort_order' => 2,
            ],
            [
                'category' => 'properties',
                'question' => 'Can I save properties to view later?',
                'answer' => 'Yes, tap the heart icon on any property to save it. You can view all saved properties in the "Saved" tab at the bottom of the screen.',
                'sort_order' => 3,
            ],
            [
                'category' => 'properties',
                'question' => 'How do I list my property?',
                'answer' => 'If you are a landlord, go to your Profile > Edit Profile and ensure your role is set to landlord. Then use the "Add Property" feature to list your property with photos, videos, and details.',
                'sort_order' => 4,
            ],
            [
                'category' => 'subscription',
                'question' => 'What are subscription plans?',
                'answer' => 'Subscription plans give you premium features such as unlimited contact unlocks, featured listings, and priority support. You can view available plans in the Subscription section.',
                'sort_order' => 1,
            ],
            [
                'category' => 'subscription',
                'question' => 'How do I pay for a subscription?',
                'answer' => 'We support multiple payment methods including M-Pesa, Airtel Money, Halopesa, T-Pesa, Visa, and Mastercard. Select your preferred plan and follow the payment instructions.',
                'sort_order' => 2,
            ],
            [
                'category' => 'subscription',
                'question' => 'Can I cancel my subscription?',
                'answer' => 'Yes, you can cancel your subscription at any time from the Subscription section. Your benefits will remain active until the end of your current billing period.',
                'sort_order' => 3,
            ],
            [
                'category' => 'kyc',
                'question' => 'What is KYC verification?',
                'answer' => 'KYC (Know Your Customer) is a verification process where you submit identification documents to verify your identity. This helps build trust in the PataNyumba community.',
                'sort_order' => 1,
            ],
            [
                'category' => 'kyc',
                'question' => 'What documents do I need for KYC?',
                'answer' => 'You need a valid government-issued ID such as a National ID (NIDA), passport, or driver\'s license. Upload clear photos of both the front and back of your document.',
                'sort_order' => 2,
            ],
            [
                'category' => 'kyc',
                'question' => 'How long does KYC verification take?',
                'answer' => 'KYC verification typically takes 24-48 hours. You will receive a notification once your documents have been reviewed and approved or if additional information is needed.',
                'sort_order' => 3,
            ],
            [
                'category' => 'security',
                'question' => 'How do I change my password?',
                'answer' => 'Go to Profile > Change Password. Enter your current password and your new password. Your new password must be at least 8 characters long.',
                'sort_order' => 1,
            ],
            [
                'category' => 'security',
                'question' => 'Is my personal information safe?',
                'answer' => 'Yes, we take your privacy seriously. Your personal information is encrypted and never shared with third parties without your consent. Read our Privacy Policy for more details.',
                'sort_order' => 2,
            ],
            [
                'category' => 'security',
                'question' => 'What should I do if I suspect fraud?',
                'answer' => 'If you encounter a suspicious listing or user, use the "Report" feature on the property or contact our support team immediately through the Help & Support section.',
                'sort_order' => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create(array_merge($faq, ['is_active' => true]));
        }
    }
}
