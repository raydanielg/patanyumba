import 'package:flutter/material.dart';

enum AppLanguage { english, swahili }

class AppStrings {
  final AppLanguage language;

  AppStrings(this.language);

  static AppStrings of(BuildContext context) {
    final provider = LanguageProvider.of(context);
    return AppStrings(provider.language);
  }

  static const _en = {
    // ─── Auth ───
    'welcome_back': 'Welcome Back',
    'sign_in_subtitle': 'Sign in to your PataNyumba account',
    'email_address': 'Email Address',
    'email_hint': 'you@example.com',
    'enter_email': 'Please enter your email',
    'valid_email': 'Please enter a valid email',
    'password': 'Password',
    'enter_password': 'Enter your password',
    'please_password': 'Please enter your password',
    'password_min': 'Password must be at least 6 characters',
    'remember_me': 'Remember me',
    'forgot_password': 'Forgot password?',
    'sign_in': 'Sign In',
    'or': 'or',
    'no_account': "Don't have an account? ",
    'create_account_link': 'Create account',
    'create_account': 'Create Account',
    'join_app': 'Join PataNyumba',
    'join_today': 'Join PataNyumba today',
    'step0_subtitle': 'Tell us who you are to get started',
    'step1_subtitle': 'Enter your details to create your account',
    'i_am_a': 'I am a...',
    'tenant': 'Tenant',
    'tenant_desc': "I'm looking for a place to rent",
    'landlord': 'Landlord',
    'landlord_desc': 'I own properties and want to list them',
    'agent': 'Agent',
    'agent_desc': 'I manage properties on behalf of owners',
    'kyc_warning_landlord': "As a landlord, you'll need to complete KYC verification after registration.",
    'kyc_warning_agent': "As an agent, you'll need to complete KYC verification after registration.",
    'already_account': 'Already have an account? ',
    'sign_in_link': 'Sign in',
    'full_name': 'Full Name',
    'name_hint': 'John Doe',
    'enter_name': 'Please enter your name',
    'phone_number': 'Phone Number',
    'phone_hint': '06XXXXXXXX',
    'enter_phone': 'Please enter your phone number',
    'valid_phone': 'Enter valid number (e.g. 06XXXXXXXX)',
    'confirm_password': 'Confirm Password',
    'reenter_password': 'Re-enter your password',
    'please_confirm': 'Please confirm your password',
    'passwords_nomatch': 'Passwords do not match',
    'password_min_8': 'Password must be at least 8 characters',
    'business_details': 'Business Details',
    'business_name_optional': 'Business Name (optional)',
    'agency_name_optional': 'Agency Name (optional)',
    'business_hint': 'e.g. My Properties Ltd',
    'agency_hint': 'e.g. PataNyumba Real Estate',
    'region': 'Region',
    'select_region': 'Select your region',
    'loading_regions': 'Loading regions...',
    'district': 'District',
    'select_district': 'Select your district',
    'select_region_first': 'Select region first',
    'street_address': 'Street Address (optional)',
    'address_hint': 'e.g. Mlimani City, Sam Nujoma Road',
    'terms_note': 'By creating an account, you agree to our Terms of Service and Privacy Policy.',
    'next': 'Next',
    'back': 'Back',
    'registering_as': 'Registering as',
    'role': 'Role',
    'details': 'Details',
    'account_created': 'Account created!',
    'welcome_app': 'Welcome to PataNyumba',
    'account_created_kyc': 'Account Created!',
    'complete_kyc': 'Please complete KYC verification',
    'registration_failed': 'Registration Failed',
    'login_failed': 'Login Failed',
    'welcome_back_toast': 'Welcome back!',
    'login_successful': 'Login successful',
    'missing_info': 'Missing Info',
    'select_region_district': 'Please select your region and district',

    // ─── KYC Status ───
    'verification_status': 'Verification Status',
    'verified': 'Verified!',
    'verified_msg': 'Congratulations! Your account is verified. You can now list properties on PataNyumba.',
    'not_verified': 'Not Verified',
    'not_verified_msg': 'Your verification was not approved. Please review your documents and resubmit.',
    'pending_verification': 'Pending Verification',
    'pending_msg': 'Your documents are being reviewed. This usually takes 24-48 hours. We\'ll notify you once verified.',
    'progress_tracker': 'Progress Tracker',
    'account_created_step': 'Account Created',
    'account_created_desc': 'Your account has been set up',
    'documents_submitted': 'Documents Submitted',
    'documents_submitted_yes': 'KYC documents uploaded',
    'documents_submitted_no': 'Upload your KYC documents',
    'under_review': 'Under Review',
    'under_review_desc': 'Admin reviewing your documents',
    'verification_complete': 'Verification Complete',
    'verification_complete_yes': 'You are verified!',
    'verification_complete_no': 'Awaiting verification',
    'no_kyc_docs': 'No KYC Documents Yet',
    'no_kyc_docs_desc': 'Upload your documents to start verification',
    'your_documents': 'Your Documents',
    'upload_kyc': 'Upload KYC Documents',
    'upload_more': 'Upload More Documents',
    'go_home': 'Go to Home',
    'start_listing': 'Start Listing Properties',
    'continue_home': 'Continue to Home',
    'could_not_load': 'Could not load status',
    'check_connection': 'Please check your connection and try again',
    'retry': 'Retry',
    'level': 'Level',
  };

  static const _sw = {
    // ─── Auth ───
    'welcome_back': 'Karibu Tena',
    'sign_in_subtitle': 'Ingia kwenye akaunti yako ya PataNyumba',
    'email_address': 'Barua Pepe',
    'email_hint': 'you@example.com',
    'enter_email': 'Tafadhali weka barua pepe yako',
    'valid_email': 'Tafadhali weka barua pepe sahihi',
    'password': 'Nenosiri',
    'enter_password': 'Weka nenosiri lako',
    'please_password': 'Tafadhali weka nenosiri',
    'password_min': 'Nenosiri lazima liwe na herufi 6 zaidi',
    'remember_me': 'Nikumbuke',
    'forgot_password': 'Umesahau nenosiri?',
    'sign_in': 'Ingia',
    'or': 'au',
    'no_account': 'Huna akaunti? ',
    'create_account_link': 'Tengeneza akaunti',
    'create_account': 'Tengeneza Akaunti',
    'join_app': 'Jiunge na PataNyumba',
    'join_today': 'Jiunge na PataNyumba leo',
    'step0_subtitle': 'Tuambie wewe ni nani kuanzia',
    'step1_subtitle': 'Weka maelezo yako kutengeneza akaunti',
    'i_am_a': 'Mimi ni...',
    'tenant': 'Mpangaji',
    'tenant_desc': 'Natafuta nyumba ya kupanga',
    'landlord': 'Mmiliki wa Nyumba',
    'landlord_desc': 'Nina nyumba nazitaka kuweka kwenye orodha',
    'agent': 'Wakala',
    'agent_desc': 'Ninadhibiti nyumba kwa niaba ya wamiliki',
    'kyc_warning_landlord': 'Kama mmiliki wa nyumba, utahitaji kukamilisha uthibitisho wa KYC baada ya usajili.',
    'kyc_warning_agent': 'Kama wakala, utahitaji kukamilisha uthibitisho wa KYC baada ya usajili.',
    'already_account': 'Tayari una akaunti? ',
    'sign_in_link': 'Ingia',
    'full_name': 'Jina Kamili',
    'name_hint': 'John Doe',
    'enter_name': 'Tafadhali weka jina lako',
    'phone_number': 'Nambari ya Simu',
    'phone_hint': '06XXXXXXXX',
    'enter_phone': 'Tafadhali weka nambari ya simu',
    'valid_phone': 'Weka nambari sahihi (mfano 06XXXXXXXX)',
    'confirm_password': 'Thibitisha Nenosiri',
    'reenter_password': 'Weka tena nenosiri lako',
    'please_confirm': 'Tafadhali thibitisha nenosiri',
    'passwords_nomatch': 'Nenosiri halilingani',
    'password_min_8': 'Nenosiri lazima liwe na herufi 8 zaidi',
    'business_details': 'Maelezo ya Biashara',
    'business_name_optional': 'Jina la Biashara (hiari)',
    'agency_name_optional': 'Jina la Wakala (hiari)',
    'business_hint': 'mfano. My Properties Ltd',
    'agency_hint': 'mfano. PataNyumba Real Estate',
    'region': 'Mkoa',
    'select_region': 'Chagua mkoa wako',
    'loading_regions': 'Inapakia mikoa...',
    'district': 'Wilaya',
    'select_district': 'Chagua wilaya yako',
    'select_region_first': 'Chagua mkoa kwanza',
    'street_address': 'Anwani ya Mtaa (hiari)',
    'address_hint': 'mfano. Mlimani City, Sam Nujoma Road',
    'terms_note': 'Kwa kutengeneza akaunti, unakubaliana na Masharti yetu na Sera ya Faragha.',
    'next': 'Endelea',
    'back': 'Rudi',
    'registering_as': 'Unasajiliwa kama',
    'role': 'Aina',
    'details': 'Maelezo',
    'account_created': 'Akaunti imeundwa!',
    'welcome_app': 'Karibu PataNyumba',
    'account_created_kyc': 'Akaunti Imeundwa!',
    'complete_kyc': 'Tafadhali kamilisha uthibitisho wa KYC',
    'registration_failed': 'Usajili Umeshindwa',
    'login_failed': 'Kuingia Kumeshindwa',
    'welcome_back_toast': 'Karibu tena!',
    'login_successful': 'Umefanikiwa kuingia',
    'missing_info': 'Taarifa Inakosekana',
    'select_region_district': 'Tafadhali chagua mkoa na wilaya yako',

    // ─── KYC Status ───
    'verification_status': 'Hali ya Uthibitisho',
    'verified': 'Imethibitishwa!',
    'verified_msg': 'Hongera! Akaunti yako imethibitishwa. Sasa unaweza kuweka nyumba kwenye PataNyumba.',
    'not_verified': 'Haijathibitishwa',
    'not_verified_msg': 'Uthibitisho wako haukukubaliwa. Tafadhali kagua hati zako na uwasilishe tena.',
    'pending_verification': 'Inasubiri Uthibitisho',
    'pending_msg': 'Hati zako zinakaguliwa. Hii kawaida inachukua masaa 24-48. Tutakujulisha unapothibitishwa.',
    'progress_tracker': 'Kufuatilia Maendeleo',
    'account_created_step': 'Akaunti Imeundwa',
    'account_created_desc': 'Akaunti yako imewekwa',
    'documents_submitted': 'Hati Zilizowasilishwa',
    'documents_submitted_yes': 'Hati za KYC zimepakiwa',
    'documents_submitted_no': 'Pakia hati zako za KYC',
    'under_review': 'Inakaguliwa',
    'under_review_desc': 'Msimamizi anakagua hati zako',
    'verification_complete': 'Uthibitisho Umekamilika',
    'verification_complete_yes': 'Umethibitishwa!',
    'verification_complete_no': 'Inasubiri uthibitisho',
    'no_kyc_docs': 'Hakuna Hati za KYC Bado',
    'no_kyc_docs_desc': 'Pakia hati zako kuanza uthibitisho',
    'your_documents': 'Hati Zako',
    'upload_kyc': 'Pakia Hati za KYC',
    'upload_more': 'Pakia Hati Zaidi',
    'go_home': 'Nenda Nyumbani',
    'start_listing': 'Anza Kuweka Nyumba',
    'continue_home': 'Endelea kwa Nyumbani',
    'could_not_load': 'Imeshindwa kupakia hali',
    'check_connection': 'Tafadhali kagua muunganisho wako na ujaribu tena',
    'retry': 'Jaribu Tena',
    'level': 'Kiwango',
  };

  String get(String key) {
    final map = language == AppLanguage.english ? _en : _sw;
    return map[key] ?? _en[key] ?? key;
  }

  // Convenience getters
  String get welcomeBack => get('welcome_back');
  String get signInSubtitle => get('sign_in_subtitle');
  String get emailAddress => get('email_address');
  String get emailHint => get('email_hint');
  String get enterEmail => get('enter_email');
  String get validEmail => get('valid_email');
  String get password => get('password');
  String get enterPassword => get('enter_password');
  String get pleasePassword => get('please_password');
  String get passwordMin => get('password_min');
  String get rememberMe => get('remember_me');
  String get forgotPassword => get('forgot_password');
  String get signIn => get('sign_in');
  String get or => get('or');
  String get noAccount => get('no_account');
  String get createAccountLink => get('create_account_link');
  String get createAccount => get('create_account');
  String get joinApp => get('join_app');
  String get joinToday => get('join_today');
  String get step0Subtitle => get('step0_subtitle');
  String get step1Subtitle => get('step1_subtitle');
  String get iAmA => get('i_am_a');
  String get tenant => get('tenant');
  String get tenantDesc => get('tenant_desc');
  String get landlord => get('landlord');
  String get landlordDesc => get('landlord_desc');
  String get agent => get('agent');
  String get agentDesc => get('agent_desc');
  String get alreadyAccount => get('already_account');
  String get signInLink => get('sign_in_link');
  String get fullName => get('full_name');
  String get nameHint => get('name_hint');
  String get enterName => get('enter_name');
  String get phoneNumber => get('phone_number');
  String get phoneHint => get('phone_hint');
  String get enterPhone => get('enter_phone');
  String get validPhone => get('valid_phone');
  String get confirmPassword => get('confirm_password');
  String get reenterPassword => get('reenter_password');
  String get pleaseConfirm => get('please_confirm');
  String get passwordsNoMatch => get('passwords_nomatch');
  String get passwordMin8 => get('password_min_8');
  String get businessDetails => get('business_details');
  String get businessNameOptional => get('business_name_optional');
  String get agencyNameOptional => get('agency_name_optional');
  String get businessHint => get('business_hint');
  String get agencyHint => get('agency_hint');
  String get region => get('region');
  String get selectRegion => get('select_region');
  String get loadingRegions => get('loading_regions');
  String get district => get('district');
  String get selectDistrict => get('select_district');
  String get selectRegionFirst => get('select_region_first');
  String get streetAddress => get('street_address');
  String get addressHint => get('address_hint');
  String get termsNote => get('terms_note');
  String get next => get('next');
  String get back => get('back');
  String get registeringAs => get('registering_as');
  String get roleLabel => get('role');
  String get details => get('details');
  String get accountCreated => get('account_created');
  String get welcomeApp => get('welcome_app');
  String get accountCreatedKyc => get('account_created_kyc');
  String get completeKyc => get('complete_kyc');
  String get registrationFailed => get('registration_failed');
  String get loginFailed => get('login_failed');
  String get welcomeBackToast => get('welcome_back_toast');
  String get loginSuccessful => get('login_successful');
  String get missingInfo => get('missing_info');
  String get selectRegionDistrict => get('select_region_district');
  String get verificationStatus => get('verification_status');
  String get verified => get('verified');
  String get verifiedMsg => get('verified_msg');
  String get notVerified => get('not_verified');
  String get notVerifiedMsg => get('not_verified_msg');
  String get pendingVerification => get('pending_verification');
  String get pendingMsg => get('pending_msg');
  String get progressTracker => get('progress_tracker');
  String get accountCreatedStep => get('account_created_step');
  String get accountCreatedDesc => get('account_created_desc');
  String get documentsSubmitted => get('documents_submitted');
  String get documentsSubmittedYes => get('documents_submitted_yes');
  String get documentsSubmittedNo => get('documents_submitted_no');
  String get underReview => get('under_review');
  String get underReviewDesc => get('under_review_desc');
  String get verificationComplete => get('verification_complete');
  String get verificationCompleteYes => get('verification_complete_yes');
  String get verificationCompleteNo => get('verification_complete_no');
  String get noKycDocs => get('no_kyc_docs');
  String get noKycDocsDesc => get('no_kyc_docs_desc');
  String get yourDocuments => get('your_documents');
  String get uploadKyc => get('upload_kyc');
  String get uploadMore => get('upload_more');
  String get goHome => get('go_home');
  String get startListing => get('start_listing');
  String get continueHome => get('continue_home');
  String get couldNotLoad => get('could_not_load');
  String get checkConnection => get('check_connection');
  String get retry => get('retry');
  String get level => get('level');
}

class LanguageProvider extends ChangeNotifier {
  AppLanguage _language = AppLanguage.english;

  AppLanguage get language => _language;

  static LanguageProvider? _instance;

  static LanguageProvider of(BuildContext context) {
    _instance ??= LanguageProvider();
    return _instance!;
  }

  void setLanguage(AppLanguage lang) {
    _language = lang;
    notifyListeners();
  }

  void toggleLanguage() {
    _language = _language == AppLanguage.english ? AppLanguage.swahili : AppLanguage.english;
    notifyListeners();
  }
}
