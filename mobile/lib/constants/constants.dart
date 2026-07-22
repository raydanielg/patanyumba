class AppConstants {
  static const String appName = 'Patanyumba';
  static const String tagline = 'Find. Rent. Move In.';
  static const String appVersion = '1.0.0';

  // API
  // Use 10.0.2.2 for Android emulator (maps to host localhost)
  // Use localhost for iOS simulator or web
  // Use your actual server IP for physical devices
  static const String baseUrl = 'http://192.168.1.136:8081/api';
  static const Duration apiTimeout = Duration(seconds: 30);

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String onboardingKey = 'onboarding_complete';
  static const String themeKey = 'theme_mode'; // light, dark, system
  static const String languageKey = 'app_language'; // en, sw
  static const String regionKey = 'preferred_region';
  static const String notifPushKey = 'notif_push_enabled';
  static const String notifEmailKey = 'notif_email_enabled';
  static const String notifSmsKey = 'notif_sms_enabled';
  static const String notifNewPropKey = 'notif_new_props';
  static const String notifPriceDropKey = 'notif_price_drop';
  static const String notifKycKey = 'notif_kyc_updates';
  static const String notifSubKey = 'notif_subscription';
  static const String cacheKey = 'clear_cache_flag';

  // Animation
  static const Duration splashDuration = Duration(seconds: 3);
  static const Duration pageTransition = Duration(milliseconds: 300);
}
