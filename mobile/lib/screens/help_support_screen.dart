import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';

class HelpSupportScreen extends StatefulWidget {
  const HelpSupportScreen({super.key});

  @override
  State<HelpSupportScreen> createState() => _HelpSupportScreenState();
}

class _HelpSupportScreenState extends State<HelpSupportScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('Help & Support', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          indicatorWeight: 3,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          labelStyle: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14),
          unselectedLabelStyle: GoogleFonts.nunito(fontWeight: FontWeight.w500, fontSize: 14),
          tabs: const [
            Tab(icon: Icon(Icons.quiz_outlined, size: 18), text: 'FAQ'),
            Tab(icon: Icon(Icons.chat_outlined, size: 18), text: 'Live Chat'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _FaqTab(),
          _ChatTab(),
        ],
      ),
    );
  }
}

// =================== FAQ TAB ===================

class _FaqTab extends StatefulWidget {
  @override
  State<_FaqTab> createState() => _FaqTabState();
}

class _FaqTabState extends State<_FaqTab> {
  List<Map<String, dynamic>> _categories = [];
  bool _isLoading = true;
  int _expandedIndex = -1;

  @override
  void initState() {
    super.initState();
    _fetchFaqs();
  }

  Future<void> _fetchFaqs() async {
    setState(() => _isLoading = true);
    try {
      final data = await ApiService().get('faqs');
      final List<dynamic> grouped = data['data'] ?? [];
      setState(() {
        _categories = grouped.cast<Map<String, dynamic>>();
        _isLoading = false;
      });
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.tealGreen));
    }

    if (_categories.isEmpty) {
      return _buildEmptyState();
    }

    return RefreshIndicator(
      onRefresh: _fetchFaqs,
      color: AppColors.tealGreen,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _categories.length,
        itemBuilder: (context, catIndex) {
          final category = _categories[catIndex];
          final items = (category['items'] as List<dynamic>?) ?? [];

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: EdgeInsets.only(left: 4, bottom: 10, top: catIndex == 0 ? 0 : 16),
                child: Row(
                  children: [
                    Container(
                      width: 4,
                      height: 18,
                      decoration: BoxDecoration(
                        color: AppColors.tealGreen,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      category['category'] ?? 'General',
                      style: GoogleFonts.nunito(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                        color: AppColors.tealGreen,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: AppColors.tealGreen50,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        '${items.length}',
                        style: GoogleFonts.nunito(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          color: AppColors.tealGreen,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              ...items.asMap().entries.map((entry) {
                final globalIndex = catIndex * 1000 + entry.key;
                final item = entry.value as Map<String, dynamic>;
                final isExpanded = _expandedIndex == globalIndex;

                return _buildFaqCard(globalIndex, item, isExpanded);
              }),
            ],
          );
        },
      ),
    );
  }

  Widget _buildFaqCard(int index, Map<String, dynamic> item, bool isExpanded) {
    final question = item['question'] ?? '';
    final answer = item['answer'] ?? '';

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isExpanded ? AppColors.tealGreen.withValues(alpha: 0.3) : AppColors.tealGreen100.withValues(alpha: 0.4),
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
      child: ClipRRect(
        borderRadius: BorderRadius.circular(14),
        child: Column(
          children: [
            InkWell(
              onTap: () {
                setState(() {
                  _expandedIndex = isExpanded ? -1 : index;
                });
              },
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                child: Row(
                  children: [
                    Container(
                      width: 30,
                      height: 30,
                      decoration: BoxDecoration(
                        color: isExpanded ? AppColors.tealGreen : AppColors.tealGreen50,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Icon(
                        isExpanded ? Icons.remove : Icons.add,
                        size: 18,
                        color: isExpanded ? Colors.white : AppColors.tealGreen,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        question,
                        style: GoogleFonts.nunito(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textPrimary,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            AnimatedCrossFade(
              firstChild: const SizedBox.shrink(),
              secondChild: Padding(
                padding: const EdgeInsets.fromLTRB(14, 0, 14, 14),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppColors.tealGreen50,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    answer,
                    style: GoogleFonts.nunito(
                      fontSize: 12,
                      color: AppColors.textSecondary,
                      height: 1.6,
                    ),
                  ),
                ),
              ),
              crossFadeState: isExpanded
                  ? CrossFadeState.showSecond
                  : CrossFadeState.showFirst,
              duration: const Duration(milliseconds: 200),
            ),
          ],
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
            decoration: const BoxDecoration(
              color: AppColors.tealGreen50,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.quiz_outlined, size: 40, color: AppColors.tealGreen),
          ),
          const SizedBox(height: 16),
          Text(
            'No FAQs available',
            style: GoogleFonts.nunito(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 8),
          Text(
            'FAQs will appear here when available.\nTry refreshing or contact support.',
            textAlign: TextAlign.center,
            style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: _fetchFaqs,
            icon: const Icon(Icons.refresh, size: 18),
            label: Text('Refresh', style: GoogleFonts.nunito(fontWeight: FontWeight.w600)),
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
}

// =================== CHAT TAB ===================

class _ChatTab extends StatefulWidget {
  @override
  State<_ChatTab> createState() => _ChatTabState();
}

class _ChatTabState extends State<_ChatTab> {
  Map<String, dynamic>? _chat;
  List<Map<String, dynamic>> _messages = [];
  bool _isLoading = true;
  bool _isSending = false;
  final _messageController = TextEditingController();
  final _scrollController = ScrollController();
  Timer? _pollTimer;

  @override
  void initState() {
    super.initState();
    _fetchChat();
  }

  @override
  void dispose() {
    _pollTimer?.cancel();
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _fetchChat() async {
    setState(() => _isLoading = true);
    try {
      final data = await ApiService().get('support/chat');
      if (data['success'] == true && data['data'] != null) {
        final chat = data['data'] as Map<String, dynamic>;
        final msgs = (chat['messages'] as List<dynamic>?) ?? [];
        setState(() {
          _chat = chat;
          _messages = msgs.cast<Map<String, dynamic>>();
          _isLoading = false;
        });
        _markAdminMessagesRead();
        _scrollToBottom();
        _startPolling();
      } else {
        setState(() => _isLoading = false);
      }
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  void _startPolling() {
    _pollTimer?.cancel();
    _pollTimer = Timer.periodic(const Duration(seconds: 10), (_) => _pollMessages());
  }

  Future<void> _pollMessages() async {
    if (_chat == null) return;
    try {
      final chatId = _chat!['id'];
      final data = await ApiService().get('support/chat/$chatId/messages');
      if (data['success'] == true && data['data'] != null) {
        final msgs = (data['data'] as List<dynamic>?) ?? [];
        final newMessages = msgs.cast<Map<String, dynamic>>();
        if (newMessages.length != _messages.length) {
          setState(() => _messages = newMessages);
          _markAdminMessagesRead();
          _scrollToBottom();
        }
      }
    } catch (_) {}
  }

  Future<void> _markAdminMessagesRead() async {
    if (_chat == null) return;
    try {
      await ApiService().post('support/chat/${_chat!['id']}/read');
    } catch (_) {}
  }

  Future<void> _sendMessage() async {
    final text = _messageController.text.trim();
    if (text.isEmpty || _chat == null || _isSending) return;

    _messageController.clear();
    final tempMsg = {
      'id': DateTime.now().millisecondsSinceEpoch,
      'sender_type': 'user',
      'message': text,
      'created_at': DateTime.now().toIso8601String(),
    };
    setState(() {
      _messages.add(tempMsg);
      _isSending = true;
    });
    _scrollToBottom();

    try {
      final data = await ApiService().post(
        'support/chat/${_chat!['id']}/send',
        body: {'message': text},
      );
      if (data['success'] == true && data['data'] != null) {
        final newMsg = data['data'] as Map<String, dynamic>;
        setState(() {
          _messages[_messages.length - 1] = newMsg;
        });
      }
    } catch (_) {
      _showSnackBar('Failed to send message', isError: true);
    }
    setState(() => _isSending = false);
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      }
    });
  }

  void _showSnackBar(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? Colors.red : AppColors.tealGreen,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.tealGreen));
    }

    final chatStatus = _chat?['status'] ?? 'open';
    final isClosed = chatStatus == 'closed';

    return Column(
      children: [
        // Chat header info
        Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          color: isClosed ? Colors.grey[100] : AppColors.tealGreen50,
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: isClosed ? Colors.grey[300] : AppColors.tealGreen,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  Icons.support_agent,
                  size: 20,
                  color: isClosed ? Colors.grey : Colors.white,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'PataNyumba Support',
                      style: GoogleFonts.nunito(
                        fontSize: 13,
                        fontWeight: FontWeight.w800,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    Text(
                      isClosed ? 'Chat closed' : 'Online · Typically replies in minutes',
                      style: GoogleFonts.nunito(
                        fontSize: 11,
                        color: isClosed ? AppColors.textHint : AppColors.tealGreen,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
              if (!isClosed)
                Container(
                  width: 8,
                  height: 8,
                  decoration: const BoxDecoration(
                    color: AppColors.success,
                    shape: BoxShape.circle,
                  ),
                ),
            ],
          ),
        ),
        // Messages
        Expanded(
          child: _messages.isEmpty
              ? _buildEmptyChat()
              : ListView.builder(
                  controller: _scrollController,
                  padding: const EdgeInsets.all(16),
                  itemCount: _messages.length,
                  itemBuilder: (context, index) {
                    final msg = _messages[index];
                    return _buildMessageBubble(msg);
                  },
                ),
        ),
        // Input
        if (!isClosed) _buildInputBar(),
        if (isClosed)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            color: Colors.white,
            child: ElevatedButton.icon(
              onPressed: _fetchChat,
              icon: const Icon(Icons.refresh, size: 18),
              label: Text('Start New Chat', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.tealGreen,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildMessageBubble(Map<String, dynamic> msg) {
    final isUser = msg['sender_type'] == 'user';
    final message = msg['message'] ?? '';
    final createdAt = msg['created_at'];

    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        mainAxisAlignment: isUser ? MainAxisAlignment.end : MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (!isUser) ...[
            Container(
              width: 28,
              height: 28,
              decoration: const BoxDecoration(
                color: AppColors.tealGreen,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.support_agent, size: 16, color: Colors.white),
            ),
            const SizedBox(width: 8),
          ],
          Flexible(
            child: Container(
              constraints: BoxConstraints(
                maxWidth: MediaQuery.of(context).size.width * 0.72,
              ),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                color: isUser ? AppColors.tealGreen : Colors.white,
                borderRadius: BorderRadius.only(
                  topLeft: const Radius.circular(16),
                  topRight: const Radius.circular(16),
                  bottomLeft: isUser ? const Radius.circular(16) : const Radius.circular(4),
                  bottomRight: isUser ? const Radius.circular(4) : const Radius.circular(16),
                ),
                border: isUser ? null : Border.all(color: AppColors.tealGreen100),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    message,
                    style: GoogleFonts.nunito(
                      fontSize: 13,
                      color: isUser ? Colors.white : AppColors.textPrimary,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _formatTime(createdAt),
                    style: GoogleFonts.nunito(
                      fontSize: 10,
                      color: isUser ? Colors.white70 : AppColors.textHint,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyChat() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 72,
            height: 72,
            decoration: const BoxDecoration(
              color: AppColors.tealGreen50,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.chat_bubble_outline, size: 36, color: AppColors.tealGreen),
          ),
          const SizedBox(height: 16),
          Text(
            'Start a conversation',
            style: GoogleFonts.nunito(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 8),
          Text(
            'Send a message below and our support\nteam will get back to you shortly.',
            textAlign: TextAlign.center,
            style: GoogleFonts.nunito(fontSize: 12, color: AppColors.textHint),
          ),
        ],
      ),
    );
  }

  Widget _buildInputBar() {
    return Container(
      padding: EdgeInsets.fromLTRB(12, 10, 12, 10 + MediaQuery.of(context).viewInsets.bottom),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: AppColors.tealGreen100.withValues(alpha: 0.5))),
      ),
      child: Row(
        children: [
          Expanded(
            child: Container(
              decoration: BoxDecoration(
                color: const Color(0xFFF5F7FA),
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: AppColors.tealGreen100),
              ),
              child: TextField(
                controller: _messageController,
                maxLines: null,
                textCapitalization: TextCapitalization.sentences,
                decoration: InputDecoration(
                  hintText: 'Type a message...',
                  hintStyle: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  border: InputBorder.none,
                ),
                style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textPrimary),
                onSubmitted: (_) => _sendMessage(),
              ),
            ),
          ),
          const SizedBox(width: 8),
          Container(
            width: 44,
            height: 44,
            decoration: const BoxDecoration(
              color: AppColors.tealGreen,
              shape: BoxShape.circle,
            ),
            child: IconButton(
              icon: _isSending
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                    )
                  : const Icon(Icons.send, size: 20, color: Colors.white),
              onPressed: _isSending ? null : _sendMessage,
            ),
          ),
        ],
      ),
    );
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
      return '${dt.day}/${dt.month} ${dt.hour.toString().padLeft(2, '0')}:${dt.minute.toString().padLeft(2, '0')}';
    } catch (_) {
      return '';
    }
  }
}
