class AppConstants {
  static const String appName = 'Patanyumba';
  static const String tagline = 'Find. Rent. Move In.';
  static const String appVersion = '1.0.0';

  // API
  static const String baseUrl = 'http://localhost:8000/api';
  static const Duration apiTimeout = Duration(seconds: 30);

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';

  // Animation
  static const Duration splashDuration = Duration(seconds: 3);
  static const Duration pageTransition = Duration(milliseconds: 300);
}
