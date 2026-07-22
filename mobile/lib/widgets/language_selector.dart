import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../l10n/app_strings.dart';

class LanguageSelector extends StatelessWidget {
  final bool isOnGradient;

  const LanguageSelector({super.key, this.isOnGradient = true});

  @override
  Widget build(BuildContext context) {
    final provider = LanguageProvider.of(context);
    final isEnglish = provider.language == AppLanguage.english;

    return GestureDetector(
      onTap: () => provider.toggleLanguage(),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: isOnGradient ? Colors.white.withValues(alpha: 0.2) : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isOnGradient ? Colors.white.withValues(alpha: 0.3) : AppColors.inputBorder,
            width: 1,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'EN',
              style: GoogleFonts.nunito(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                color: isEnglish
                    ? (isOnGradient ? Colors.white : AppColors.tealGreen)
                    : (isOnGradient ? Colors.white.withValues(alpha: 0.5) : AppColors.textHint),
              ),
            ),
            Container(
              width: 28,
              height: 16,
              margin: const EdgeInsets.symmetric(horizontal: 6),
              decoration: BoxDecoration(
                color: isOnGradient ? Colors.white.withValues(alpha: 0.25) : AppColors.tealGreen50,
                borderRadius: BorderRadius.circular(10),
              ),
              child: AnimatedAlign(
                duration: const Duration(milliseconds: 200),
                alignment: isEnglish ? Alignment.centerLeft : Alignment.centerRight,
                child: Container(
                  width: 12,
                  height: 12,
                  margin: const EdgeInsets.all(2),
                  decoration: BoxDecoration(
                    color: isOnGradient ? Colors.white : AppColors.tealGreen,
                    shape: BoxShape.circle,
                  ),
                ),
              ),
            ),
            Text(
              'SW',
              style: GoogleFonts.nunito(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                color: !isEnglish
                    ? (isOnGradient ? Colors.white : AppColors.tealGreen)
                    : (isOnGradient ? Colors.white.withValues(alpha: 0.5) : AppColors.textHint),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
