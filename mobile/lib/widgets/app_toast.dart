import 'package:flutter/material.dart';
import '../constants/colors.dart';

class AppToast {
  static void show(
    BuildContext context, {
    required String title,
    String? description,
    ToastType type = ToastType.success,
  }) {
    final overlay = Overlay.of(context);
    late OverlayEntry entry;
    late AnimationController controller;
    late Animation<double> animation;

    controller = AnimationController(
      duration: const Duration(milliseconds: 250),
      reverseDuration: const Duration(milliseconds: 200),
      vsync: overlay,
    );
    animation = CurvedAnimation(parent: controller, curve: Curves.easeOut);

    entry = OverlayEntry(
      builder: (context) => _ToastWidget(
        title: title,
        description: description,
        type: type,
        animation: animation,
        onDismiss: () {
          controller.reverse().then((_) {
            entry.remove();
            controller.dispose();
          });
        },
      ),
    );

    overlay.insert(entry);
    controller.forward();

    Future.delayed(const Duration(seconds: 3), () {
      if (entry.mounted) {
        controller.reverse().then((_) {
          entry.remove();
          controller.dispose();
        });
      }
    });
  }

  static void success(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastType.success);
  }

  static void error(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastType.error);
  }

  static void warning(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastType.warning);
  }

  static void info(BuildContext context, String title, [String? description]) {
    show(context, title: title, description: description, type: ToastType.info);
  }
}

enum ToastType { success, error, warning, info }

class _ToastWidget extends StatelessWidget {
  final String title;
  final String? description;
  final ToastType type;
  final Animation<double> animation;
  final VoidCallback onDismiss;

  const _ToastWidget({
    required this.title,
    this.description,
    required this.type,
    required this.animation,
    required this.onDismiss,
  });

  Color get _bgColor => switch (type) {
        ToastType.success => const Color(0xFF0A0A0A),
        ToastType.error => const Color(0xFFDC2626),
        ToastType.warning => const Color(0xFFD97706),
        ToastType.info => const Color(0xFF2563EB),
      };

  IconData get _icon => switch (type) {
        ToastType.success => Icons.check_rounded,
        ToastType.error => Icons.close_rounded,
        ToastType.warning => Icons.warning_amber_rounded,
        ToastType.info => Icons.info_rounded,
      };

  @override
  Widget build(BuildContext context) {
    return Positioned(
      top: 50,
      left: 0,
      right: 0,
      child: Center(
        child: AnimatedBuilder(
          animation: animation,
          builder: (context, child) {
            return Transform.translate(
              offset: Offset(0, (1 - animation.value) * -30),
              child: Opacity(
                opacity: animation.value,
                child: child,
              ),
            );
          },
          child: Material(
            color: Colors.transparent,
            child: Container(
              constraints: const BoxConstraints(maxWidth: 360),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                color: _bgColor,
                borderRadius: BorderRadius.circular(10),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.25),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(_icon, color: Colors.white, size: 16),
                  const SizedBox(width: 8),
                  Flexible(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          title,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            height: 1.2,
                          ),
                        ),
                        if (description != null) ...[
                          const SizedBox(height: 2),
                          Text(
                            description!,
                            style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.7),
                              fontSize: 11,
                              fontWeight: FontWeight.w400,
                              height: 1.2,
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  GestureDetector(
                    onTap: onDismiss,
                    child: Icon(
                      Icons.close,
                      color: Colors.white.withValues(alpha: 0.5),
                      size: 14,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
