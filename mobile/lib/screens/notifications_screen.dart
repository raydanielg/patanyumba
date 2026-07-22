import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../services/api_service.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<Map<String, dynamic>> _notifications = [];
  int _unreadCount = 0;
  bool _isLoading = true;
  bool _isLoadingMore = false;
  int _currentPage = 1;
  int _lastPage = 1;
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _fetchNotifications();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200 &&
        !_isLoadingMore &&
        _currentPage < _lastPage) {
      _loadMore();
    }
  }

  Future<void> _fetchNotifications() async {
    setState(() => _isLoading = true);
    _currentPage = 1;
    try {
      final data = await ApiService().get('notifications?page=1');
      final notifData = data['data'] as Map<String, dynamic>?;
      final List<dynamic> notifList = notifData?['data'] ?? [];
      setState(() {
        _notifications = notifList.cast<Map<String, dynamic>>();
        _unreadCount = data['unread_count'] ?? 0;
        _currentPage = notifData?['current_page'] ?? 1;
        _lastPage = notifData?['last_page'] ?? 1;
        _isLoading = false;
      });
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _loadMore() async {
    setState(() => _isLoadingMore = true);
    try {
      final nextPage = _currentPage + 1;
      final data = await ApiService().get('notifications?page=$nextPage');
      final notifData = data['data'] as Map<String, dynamic>?;
      final List<dynamic> notifList = notifData?['data'] ?? [];
      setState(() {
        _notifications.addAll(notifList.cast<Map<String, dynamic>>());
        _currentPage = notifData?['current_page'] ?? nextPage;
        _lastPage = notifData?['last_page'] ?? _lastPage;
        _isLoadingMore = false;
      });
    } catch (_) {
      setState(() => _isLoadingMore = false);
    }
  }

  Future<void> _markAsRead(Map<String, dynamic> notification) async {
    final id = notification['id'];
    final wasRead = notification['is_read'] == true;
    if (wasRead) return;

    setState(() {
      notification['is_read'] = true;
      _unreadCount = (_unreadCount - 1).clamp(0, _unreadCount);
    });

    try {
      await ApiService().post('notifications/$id/read');
    } catch (_) {
      setState(() {
        notification['is_read'] = false;
        _unreadCount++;
      });
    }
  }

  Future<void> _markAllAsRead() async {
    if (_unreadCount == 0) return;

    final prevUnread = _unreadCount;
    setState(() {
      for (var n in _notifications) {
        n['is_read'] = true;
      }
      _unreadCount = 0;
    });

    try {
      await ApiService().post('notifications/read-all');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('All notifications marked as read'),
            backgroundColor: AppColors.tealGreen,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
        );
      }
    } catch (_) {
      setState(() {
        for (var n in _notifications) {
          n['is_read'] = false;
        }
        _unreadCount = prevUnread;
      });
    }
  }

  Future<void> _deleteNotification(int index, int id) async {
    final notification = _notifications[index];
    setState(() {
      _notifications.removeAt(index);
      if (notification['is_read'] != true) {
        _unreadCount = (_unreadCount - 1).clamp(0, _unreadCount);
      }
    });

    try {
      await ApiService().delete('notifications/$id');
    } catch (_) {
      setState(() {
        _notifications.insert(index, notification);
        if (notification['is_read'] != true) {
          _unreadCount++;
        }
      });
    }
  }

  Future<void> _clearAll() async {
    final prevNotifications = List<Map<String, dynamic>>.from(_notifications);
    final prevUnread = _unreadCount;

    setState(() {
      _notifications.clear();
      _unreadCount = 0;
    });

    try {
      await ApiService().delete('notifications');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('All notifications cleared'),
            backgroundColor: AppColors.tealGreen,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
        );
      }
    } catch (_) {
      setState(() {
        _notifications = prevNotifications;
        _unreadCount = prevUnread;
      });
    }
  }

  void _confirmClearAll() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text('Clear All Notifications', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        content: Text(
          'Are you sure you want to delete all notifications? This action cannot be undone.',
          style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textSecondary),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              _clearAll();
            },
            child: const Text('Clear All', style: TextStyle(color: AppColors.error)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('Notifications', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          if (_unreadCount > 0)
            TextButton(
              onPressed: _markAllAsRead,
              child: Text(
                'Mark all read',
                style: GoogleFonts.nunito(
                  color: Colors.white,
                  fontWeight: FontWeight.w600,
                  fontSize: 13,
                ),
              ),
            ),
          if (_notifications.isNotEmpty)
            IconButton(
              icon: const Icon(Icons.delete_sweep_outlined, color: Colors.white),
              onPressed: _confirmClearAll,
            ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.tealGreen))
          : _notifications.isEmpty
              ? _buildEmptyState()
              : RefreshIndicator(
                  onRefresh: _fetchNotifications,
                  color: AppColors.tealGreen,
                  child: ListView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.all(16),
                    itemCount: _notifications.length + (_isLoadingMore ? 1 : 0),
                    itemBuilder: (context, index) {
                      if (index == _notifications.length) {
                        return const Padding(
                          padding: EdgeInsets.all(16),
                          child: Center(child: CircularProgressIndicator(color: AppColors.tealGreen)),
                        );
                      }
                      final notification = _notifications[index];
                      return _buildNotificationCard(index, notification);
                    },
                  ),
                ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: AppColors.tealGreen50,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.notifications_off_outlined, size: 40, color: AppColors.tealGreen),
          ),
          const SizedBox(height: 16),
          Text(
            'No notifications yet',
            style: GoogleFonts.nunito(
              fontSize: 18,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'You will see notifications here when\nthere are updates about properties and your account.',
            textAlign: TextAlign.center,
            style: GoogleFonts.nunito(
              fontSize: 13,
              color: AppColors.textHint,
            ),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: _fetchNotifications,
            icon: const Icon(Icons.refresh, size: 18),
            label: Text(
              'Refresh',
              style: GoogleFonts.nunito(fontWeight: FontWeight.w600),
            ),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.tealGreen,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNotificationCard(int index, Map<String, dynamic> notification) {
    final isRead = notification['is_read'] == true;
    final type = notification['type'] ?? 'info';
    final title = notification['title'] ?? '';
    final body = notification['body'] ?? '';
    final createdAt = notification['created_at'];
    final link = notification['link'];

    final typeConfig = _getTypeConfig(type);

    return Dismissible(
      key: Key('notif_${notification['id']}'),
      direction: DismissDirection.endToStart,
      background: Container(
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 20),
        decoration: BoxDecoration(
          color: Colors.red,
          borderRadius: BorderRadius.circular(14),
        ),
        child: const Icon(Icons.delete_outline, color: Colors.white),
      ),
      onDismissed: (direction) {
        _deleteNotification(index, notification['id'] as int);
      },
      child: GestureDetector(
        onTap: () {
          if (!isRead) {
            _markAsRead(notification);
          }
          if (link != null && link.toString().isNotEmpty) {
            // Navigate to link if available
          }
        },
        child: Container(
          margin: const EdgeInsets.only(bottom: 10),
          decoration: BoxDecoration(
            color: isRead ? Colors.white : typeConfig['bgColor'].withValues(alpha: 0.3),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: isRead ? AppColors.tealGreen100.withValues(alpha: 0.5) : typeConfig['color'].withValues(alpha: 0.2),
              width: 1,
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.03),
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: typeConfig['bgColor'],
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(typeConfig['icon'], size: 20, color: typeConfig['color']),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              title,
                              style: GoogleFonts.nunito(
                                fontSize: 14,
                                fontWeight: isRead ? FontWeight.w700 : FontWeight.w800,
                                color: AppColors.textPrimary,
                              ),
                            ),
                          ),
                          if (!isRead)
                            Container(
                              width: 8,
                              height: 8,
                              decoration: const BoxDecoration(
                                color: AppColors.tealGreen,
                                shape: BoxShape.circle,
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        body,
                        style: GoogleFonts.nunito(
                          fontSize: 12,
                          color: AppColors.textSecondary,
                          height: 1.4,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Icon(Icons.access_time, size: 12, color: AppColors.textHint),
                          const SizedBox(width: 4),
                          Text(
                            _formatTime(createdAt),
                            style: GoogleFonts.nunito(
                              fontSize: 11,
                              color: AppColors.textHint,
                            ),
                          ),
                          if (link != null && link.toString().isNotEmpty) ...[
                            const SizedBox(width: 12),
                            Icon(Icons.open_in_new, size: 12, color: AppColors.tealGreen),
                            const SizedBox(width: 2),
                            Text(
                              'View',
                              style: GoogleFonts.nunito(
                                fontSize: 11,
                                color: AppColors.tealGreen,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Map<String, dynamic> _getTypeConfig(String type) {
    switch (type) {
      case 'success':
        return {
          'icon': Icons.check_circle_outline,
          'color': const Color(0xFF2e7d32),
          'bgColor': const Color(0xFFe8f5e9),
        };
      case 'warning':
        return {
          'icon': Icons.warning_amber_outlined,
          'color': const Color(0xFFf57c00),
          'bgColor': const Color(0xFFfff3e0),
        };
      case 'danger':
        return {
          'icon': Icons.error_outline,
          'color': const Color(0xFFc62828),
          'bgColor': const Color(0xFFffebee),
        };
      default:
        return {
          'icon': Icons.info_outline,
          'color': AppColors.tealGreen,
          'bgColor': AppColors.tealGreen50,
        };
    }
  }

  String _formatTime(dynamic createdAt) {
    if (createdAt == null) return '';
    try {
      final dt = DateTime.parse(createdAt.toString()).toLocal();
      final now = DateTime.now();
      final diff = now.difference(dt);

      if (diff.inMinutes < 1) return 'Just now';
      if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
      if (diff.inHours < 24) return '${diff.inHours}h ago';
      if (diff.inDays == 1) return 'Yesterday';
      if (diff.inDays < 7) return '${diff.inDays}d ago';
      return '${dt.day}/${dt.month}/${dt.year}';
    } catch (_) {
      return '';
    }
  }
}
