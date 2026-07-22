class AppConstants {
  static const String appName = 'Patanyumba';
  static const String tagline = 'Find. Rent. Move In.';
  static const String appVersion = '1.0.0';

  // API
  // Use 10.0.2.2 for Android emulator (maps to host localhost)
  // Use localhost for iOS simulator or web
  // Use your actual server IP for physical devices
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  static const Duration apiTimeout = Duration(seconds: 30);

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String onboardingKey = 'onboarding_complete';

  // Animation
  static const Duration splashDuration = Duration(seconds: 3);
  static const Duration pageTransition = Duration(milliseconds: 300);
}
