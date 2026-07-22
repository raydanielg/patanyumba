import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';

class AboutScreen extends StatefulWidget {
  const AboutScreen({super.key});

  @override
  State<AboutScreen> createState() => _AboutScreenState();
}

class _AboutScreenState extends State<AboutScreen> {
  List<Map<String, dynamic>> _sections = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchAbout();
  }

  Future<void> _fetchAbout() async {
    setState(() => _isLoading = true);
    try {
      final data = await ApiService().get('about');
      final List<dynamic> grouped = data['data'] ?? [];
      setState(() {
        _sections = grouped.cast<Map<String, dynamic>>();
        _isLoading = false;
      });
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  IconData _getIcon(String? iconName) {
    switch (iconName) {
      case 'home':
        return Icons.home_outlined;
      case 'target':
        return Icons.track_changes_outlined;
      case 'eye':
        return Icons.visibility_outlined;
      case 'shield':
        return Icons.shield_outlined;
      case 'globe':
        return Icons.public_outlined;
      case 'lock':
        return Icons.lock_outline;
      case 'lightbulb':
        return Icons.lightbulb_outlined;
      case 'chart':
        return Icons.bar_chart_outlined;
      case 'info':
        return Icons.info_outline;
      case 'search':
        return Icons.search_outlined;
      case 'building':
        return Icons.apartment_outlined;
      case 'mail':
      case 'email':
        return Icons.email_outlined;
      case 'phone':
        return Icons.phone_outlined;
      case 'location':
        return Icons.location_on_outlined;
      case 'clock':
        return Icons.access_time_outlined;
      case 'users':
        return Icons.group_outlined;
      case 'map':
        return Icons.map_outlined;
      case 'check':
        return Icons.check_circle_outline;
      default:
        return Icons.info_outline;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('About ${AppConstants.appName}', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.tealGreen))
          : _sections.isEmpty
              ? _buildEmptyState()
              : RefreshIndicator(
                  onRefresh: _fetchAbout,
                  color: AppColors.tealGreen,
                  child: ListView.builder(
                    padding: const EdgeInsets.only(bottom: 40),
                    itemCount: _sections.length,
                    itemBuilder: (context, sectionIndex) {
                      final section = _sections[sectionIndex];
                      final items = (section['items'] as List<dynamic>?) ?? [];
                      final sectionName = section['section'] ?? '';

                      return _buildSection(sectionName, items.cast<Map<String, dynamic>>(), sectionIndex);
                    },
                  ),
                ),
    );
  }

  Widget _buildSection(String sectionName, List<Map<String, dynamic>> items, int sectionIndex) {
    if (items.isEmpty) return const SizedBox.shrink();

    // Stats section gets special treatment
    if (sectionName == 'stats') {
      return _buildStatsSection(items, sectionIndex);
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (sectionIndex == 0) _buildHeroSection(items.first),
        ...items.asMap().entries.map((entry) {
          final item = entry.value;
          final isMain = sectionName == 'main' && entry.key == 0;

          if (isMain) return const SizedBox.shrink();

          return _buildContentCard(item, sectionName);
        }),
      ],
    );
  }

  Widget _buildHeroSection(Map<String, dynamic> item) {
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [AppColors.tealGreen, AppColors.darkTealGreen],
        ),
      ),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 30, 24, 30),
            child: Column(
              children: [
                Container(
                  width: 72,
                  height: 72,
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(18),
                  ),
                  child: const Icon(Icons.home, size: 40, color: Colors.white),
                ),
                const SizedBox(height: 16),
                Text(
                  AppConstants.appName,
                  style: GoogleFonts.nunito(
                    fontSize: 28,
                    fontWeight: FontWeight.w900,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  AppConstants.tagline,
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    color: Colors.white.withValues(alpha: 0.8),
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 20),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    'Version ${AppConstants.appVersion}',
                    style: GoogleFonts.nunito(
                      fontSize: 11,
                      color: Colors.white.withValues(alpha: 0.9),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                if (item['content'] != null)
                  Text(
                    item['content'].toString(),
                    textAlign: TextAlign.center,
                    style: GoogleFonts.nunito(
                      fontSize: 13,
                      color: Colors.white.withValues(alpha: 0.9),
                      height: 1.6,
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildContentCard(Map<String, dynamic> item, String sectionName) {
    final title = item['title'] ?? '';
    final content = item['content'] ?? '';
    final icon = item['icon'] as String?;

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: AppColors.tealGreen50,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(_getIcon(icon), size: 22, color: AppColors.tealGreen),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  title,
                  style: GoogleFonts.nunito(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
            ],
          ),
          if (content.isNotEmpty) ...[
            const SizedBox(height: 12),
            Text(
              content.toString().replaceAll('\\n', '\n'),
              style: GoogleFonts.nunito(
                fontSize: 13,
                color: AppColors.textSecondary,
                height: 1.7,
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildStatsSection(List<Map<String, dynamic>> items, int sectionIndex) {
    final statsItem = items.firstWhere(
      (item) => item['stats'] != null,
      orElse: () => items.isNotEmpty ? items.first : <String, dynamic>{},
    );

    final stats = (statsItem['stats'] as List<dynamic>?) ?? [];

    if (stats.isEmpty) return const SizedBox.shrink();

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (statsItem['title'] != null)
            Padding(
              padding: const EdgeInsets.only(left: 4, bottom: 12),
              child: Text(
                statsItem['title'].toString(),
                style: GoogleFonts.nunito(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: AppColors.textPrimary,
                ),
              ),
            ),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              childAspectRatio: 1.5,
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
            ),
            itemCount: stats.length,
            itemBuilder: (context, index) {
              final stat = stats[index] as Map<String, dynamic>;
              return _buildStatCard(stat);
            },
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(Map<String, dynamic> stat) {
    final label = stat['label'] ?? '';
    final value = stat['value'] ?? '';
    final icon = stat['icon'] as String?;

    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [AppColors.tealGreen, AppColors.darkTealGreen],
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppColors.tealGreen.withValues(alpha: 0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(_getIcon(icon), size: 24, color: Colors.white.withValues(alpha: 0.9)),
          const SizedBox(height: 6),
          Text(
            value.toString(),
            style: GoogleFonts.nunito(
              fontSize: 20,
              fontWeight: FontWeight.w900,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label.toString(),
            style: GoogleFonts.nunito(
              fontSize: 11,
              color: Colors.white.withValues(alpha: 0.8),
              fontWeight: FontWeight.w600,
            ),
            textAlign: TextAlign.center,
          ),
        ],
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
            child: const Icon(Icons.info_outline, size: 40, color: AppColors.tealGreen),
          ),
          const SizedBox(height: 16),
          Text(
            'No content available',
            style: GoogleFonts.nunito(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 8),
          Text(
            'About content will appear here when available.',
            style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: _fetchAbout,
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
