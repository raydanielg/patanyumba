import 'package:flutter/material.dart';
import 'constants/theme.dart';
import 'l10n/app_strings.dart';
import 'screens/onboarding_screen.dart';
import 'screens/splash_screen.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'screens/forgot_password_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const PatanyumbaApp());
}

class PatanyumbaApp extends StatelessWidget {
  const PatanyumbaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ListenableBuilder(
      listenable: LanguageProvider._instance ??= LanguageProvider(),
      builder: (context, _) {
        return MaterialApp(
          title: 'PataNyumba',
          debugShowCheckedModeBanner: false,
          theme: AppTheme.lightTheme,
          initialRoute: '/splash',
          routes: {
            '/splash': (context) => const SplashScreen(),
            '/onboarding': (context) => const OnboardingScreen(),
            '/login': (context) => const LoginScreen(),
            '/register': (context) => const RegisterScreen(),
            '/forgot-password': (context) => const ForgotPasswordScreen(),
          },
        );
      },
    );
  }
}
