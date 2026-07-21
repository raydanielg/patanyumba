import 'package:flutter/material.dart';
import 'package:toastification/toastification.dart';
import 'colors.dart';

class AppToast {
  static void show(
    BuildContext context, {
    required String title,
    String? description,
    ToastificationType type = ToastificationType.success,
  }) {
    toastification.show(
      context: context,
      type: type,
      style: ToastificationStyle.minimal,
      title: Text(
        title,
        style: const TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w800,
        ),
      ),
      description: description != null
          ? Text(
              description,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w500,
              ),
            )
          : null,
      alignment: Alignment.topCenter,
      autoClose: const Duration(seconds: 5),
      animationDuration: const Duration(milliseconds: 400),
      animationBuilder: (context, animation, alignment, child) {
        return FadeTransition(
          opacity: animation,
          child: SlideTransition(
            position: Tween<Offset>(
              begin: const Offset(0, -1),
              end: Offset.zero,
            ).animate(CurvedAnimation(
              parent: animation,
              curve: Curves.easeOut,
            )),
            child: child,
          ),
        );
      },
      icon: type == ToastificationType.success
          ? const Icon(Icons.check_circle, color: AppColors.lightGreen)
          : type == ToastificationType.error
              ? const Icon(Icons.error, color: AppColors.error)
              : type == ToastificationType.warning
                  ? const Icon(Icons.warning, color: AppColors.warning)
                  : const Icon(Icons.info, color: Colors.blue),
      borderRadius: BorderRadius.circular(12),
      boxShadow: [
        BoxShadow(
          color: Colors.black.withValues(alpha: 0.1),
          blurRadius: 20,
          offset: const Offset(0, 8),
        ),
      ],
    );
  }

  static void success(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastificationType.success);
  }

  static void error(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastificationType.error);
  }

  static void warning(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastificationType.warning);
  }

  static void info(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastificationType.info);
  }
}
