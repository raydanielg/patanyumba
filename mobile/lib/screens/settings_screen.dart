import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';
import 'help_support_screen.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  // Theme
  String _themeMode = 'system'; // light, dark, system

  // Language
  String _language = 'en'; // en, sw

  // Region
  String? _preferredRegion;
  List<String> _regions = [];

  // Notification toggles
  bool _pushEnabled = true;
  bool _emailEnabled = true;
  bool _smsEnabled = false;
  bool _newPropsEnabled = true;
  bool _priceDropEnabled = true;
  bool _kycUpdatesEnabled = true;
  bool _subEnabled = true;

  // Other
  bool _isLoadingRegions = true;

  @override
  void initState() {
    super.initState();
    _loadSettings();
    _fetchRegions();
  }

  Future<void> _loadSettings() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _themeMode = prefs.getString(AppConstants.themeKey) ?? 'system';
      _language = prefs.getString(AppConstants.languageKey) ?? 'en';
      _preferredRegion = prefs.getString(AppConstants.regionKey);
      _pushEnabled = prefs.getBool(AppConstants.notifPushKey) ?? true;
      _emailEnabled = prefs.getBool(AppConstants.notifEmailKey) ?? true;
      _smsEnabled = prefs.getBool(AppConstants.notifSmsKey) ?? false;
      _newPropsEnabled = prefs.getBool(AppConstants.notifNewPropKey) ?? true;
      _priceDropEnabled = prefs.getBool(AppConstants.notifPriceDropKey) ?? true;
      _kycUpdatesEnabled = prefs.getBool(AppConstants.notifKycKey) ?? true;
      _subEnabled = prefs.getBool(AppConstants.notifSubKey) ?? true;
    });
  }

  Future<void> _fetchRegions() async {
    try {
      final data = await ApiService().get('regions');
      final list = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _regions = list.map((r) => r.toString()).toList();
        _isLoadingRegions = false;
      });
    } catch (_) {
      setState(() => _isLoadingRegions = false);
    }
  }

  Future<void> _saveSetting(String key, dynamic value) async {
    final prefs = await SharedPreferences.getInstance();
    if (value is bool) {
      await prefs.setBool(key, value);
    } else if (value is String) {
      await prefs.setString(key, value);
    }
  }

  void _showSnackBar(String message, {Color? color}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: color ?? AppColors.tealGreen,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        duration: const Duration(seconds: 2),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('Settings', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _buildSectionHeader('Appearance'),
          _buildSection([
            _buildThemeSelector(),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('Language & Region'),
          _buildSection([
            _buildLanguageSelector(),
            _buildDivider(),
            _buildRegionSelector(),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('Push Notifications'),
          _buildSection([
            _buildSwitchTile(
              icon: Icons.notifications_active_outlined,
              title: 'Push Notifications',
              subtitle: 'Receive push notifications on your device',
              value: _pushEnabled,
              onChanged: (v) {
                setState(() => _pushEnabled = v);
                _saveSetting(AppConstants.notifPushKey, v);
              },
            ),
            _buildDivider(),
            _buildSwitchTile(
              icon: Icons.email_outlined,
              title: 'Email Notifications',
              subtitle: 'Get updates via email',
              value: _emailEnabled,
              onChanged: (v) {
                setState(() => _emailEnabled = v);
                _saveSetting(AppConstants.notifEmailKey, v);
              },
            ),
            _buildDivider(),
            _buildSwitchTile(
              icon: Icons.sms_outlined,
              title: 'SMS Notifications',
              subtitle: 'Get updates via SMS',
              value: _smsEnabled,
              onChanged: (v) {
                setState(() => _smsEnabled = v);
                _saveSetting(AppConstants.notifSmsKey, v);
              },
            ),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('Notification Preferences'),
          _buildSection([
            _buildSwitchTile(
              icon: Icons.home_work_outlined,
              title: 'New Properties',
              subtitle: 'Notify when new properties are listed',
              value: _newPropsEnabled,
              onChanged: (v) {
                setState(() => _newPropsEnabled = v);
                _saveSetting(AppConstants.notifNewPropKey, v);
              },
            ),
            _buildDivider(),
            _buildSwitchTile(
              icon: Icons.trending_down_outlined,
              title: 'Price Drops',
              subtitle: 'Notify when property prices decrease',
              value: _priceDropEnabled,
              onChanged: (v) {
                setState(() => _priceDropEnabled = v);
                _saveSetting(AppConstants.notifPriceDropKey, v);
              },
            ),
            _buildDivider(),
            _buildSwitchTile(
              icon: Icons.verified_user_outlined,
              title: 'KYC Updates',
              subtitle: 'Notifications about your KYC status',
              value: _kycUpdatesEnabled,
              onChanged: (v) {
                setState(() => _kycUpdatesEnabled = v);
                _saveSetting(AppConstants.notifKycKey, v);
              },
            ),
            _buildDivider(),
            _buildSwitchTile(
              icon: Icons.card_membership_outlined,
              title: 'Subscription Alerts',
              subtitle: 'Reminders about your subscription',
              value: _subEnabled,
              onChanged: (v) {
                setState(() => _subEnabled = v);
                _saveSetting(AppConstants.notifSubKey, v);
              },
            ),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('Privacy & Security'),
          _buildSection([
            _buildNavTile(
              icon: Icons.lock_person_outlined,
              title: 'Change Password',
              subtitle: 'Update your account password',
              onTap: () => _showComingSoon('Change Password'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.privacy_tip_outlined,
              title: 'Privacy Policy',
              subtitle: 'Read our privacy policy',
              onTap: () => _showComingSoon('Privacy Policy'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.description_outlined,
              title: 'Terms & Conditions',
              subtitle: 'Read our terms of service',
              onTap: () => _showComingSoon('Terms & Conditions'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.block_outlined,
              title: 'Blocked Users',
              subtitle: 'Manage blocked accounts',
              onTap: () => _showComingSoon('Blocked Users'),
            ),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('Data & Storage'),
          _buildSection([
            _buildNavTile(
              icon: Icons.cleaning_services_outlined,
              title: 'Clear Cache',
              subtitle: 'Free up storage space',
              onTap: () {
                _showSnackBar('Cache cleared successfully');
              },
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.download_outlined,
              title: 'Download My Data',
              subtitle: 'Request a copy of your data',
              onTap: () => _showComingSoon('Download My Data'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.sync_outlined,
              title: 'Sync Data',
              subtitle: 'Manually sync your data with server',
              onTap: () {
                _showSnackBar('Data synced successfully');
              },
            ),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('Support'),
          _buildSection([
            _buildNavTile(
              icon: Icons.help_outline,
              title: 'Help Center',
              subtitle: 'FAQs and guides',
              onTap: () => _showComingSoon('Help Center'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.support_agent_outlined,
              title: 'Contact Support',
              subtitle: 'Get help from our team',
              onTap: () => _showComingSoon('Contact Support'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.bug_report_outlined,
              title: 'Report a Bug',
              subtitle: 'Help us improve the app',
              onTap: () => _showComingSoon('Report a Bug'),
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.star_outline,
              title: 'Rate PataNyumba',
              subtitle: 'Rate us on the app store',
              onTap: () => _showComingSoon('Rate App'),
            ),
          ]),
          const SizedBox(height: 20),

          _buildSectionHeader('About'),
          _buildSection([
            _buildInfoTile(
              icon: Icons.info_outline,
              title: 'App Version',
              value: '${AppConstants.appVersion}',
            ),
            _buildDivider(),
            _buildInfoTile(
              icon: Icons.build_outlined,
              title: 'Build Number',
              value: '2026.07.22',
            ),
            _buildDivider(),
            _buildNavTile(
              icon: Icons.groups_outlined,
              title: 'About PataNyumba',
              subtitle: 'Learn more about us',
              onTap: () => _showAboutDialog(),
            ),
          ]),
          const SizedBox(height: 32),

          // Reset button
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 4),
            child: OutlinedButton.icon(
              onPressed: () => _showResetConfirm(),
              icon: const Icon(Icons.restart_alt, color: AppColors.error, size: 20),
              label: Text(
                'Reset All Settings',
                style: GoogleFonts.nunito(
                  color: AppColors.error,
                  fontWeight: FontWeight.w700,
                  fontSize: 14,
                ),
              ),
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: AppColors.error, width: 1.5),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                padding: const EdgeInsets.symmetric(vertical: 14),
                backgroundColor: Colors.white,
              ),
            ),
          ),
          const SizedBox(height: 40),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 10),
      child: Text(
        title,
        style: GoogleFonts.nunito(
          fontSize: 13,
          fontWeight: FontWeight.w800,
          color: AppColors.tealGreen,
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Widget _buildSection(List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(children: children),
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Divider(height: 1, color: AppColors.tealGreen100.withValues(alpha: 0.5)),
    );
  }

  Widget _buildThemeSelector() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.tealGreen50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(Icons.palette_outlined, size: 18, color: AppColors.tealGreen),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  'Theme Mode',
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: _buildThemeOption(
                  icon: Icons.light_mode_outlined,
                  label: 'Light',
                  isSelected: _themeMode == 'light',
                  onTap: () {
                    setState(() => _themeMode = 'light');
                    _saveSetting(AppConstants.themeKey, 'light');
                    _showSnackBar('Theme set to Light mode');
                  },
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildThemeOption(
                  icon: Icons.dark_mode_outlined,
                  label: 'Dark',
                  isSelected: _themeMode == 'dark',
                  onTap: () {
                    setState(() => _themeMode = 'dark');
                    _saveSetting(AppConstants.themeKey, 'dark');
                    _showSnackBar('Theme set to Dark mode');
                  },
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildThemeOption(
                  icon: Icons.settings_brightness_outlined,
                  label: 'System',
                  isSelected: _themeMode == 'system',
                  onTap: () {
                    setState(() => _themeMode = 'system');
                    _saveSetting(AppConstants.themeKey, 'system');
                    _showSnackBar('Theme set to System default');
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildThemeOption({
    required IconData icon,
    required String label,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.tealGreen : AppColors.tealGreen50,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? AppColors.tealGreen : AppColors.tealGreen100,
            width: 1.5,
          ),
        ),
        child: Column(
          children: [
            Icon(icon, size: 22, color: isSelected ? Colors.white : AppColors.tealGreen),
            const SizedBox(height: 6),
            Text(
              label,
              style: GoogleFonts.nunito(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: isSelected ? Colors.white : AppColors.tealGreen,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLanguageSelector() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.tealGreen50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(Icons.language_outlined, size: 18, color: AppColors.tealGreen),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  'Language',
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: _buildLanguageOption(
                  flag: '🇬🇧',
                  label: 'English',
                  code: 'en',
                  isSelected: _language == 'en',
                  onTap: () {
                    setState(() => _language = 'en');
                    _saveSetting(AppConstants.languageKey, 'en');
                    _showSnackBar('Language set to English');
                  },
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildLanguageOption(
                  flag: '🇹🇿',
                  label: 'Kiswahili',
                  code: 'sw',
                  isSelected: _language == 'sw',
                  onTap: () {
                    setState(() => _language = 'sw');
                    _saveSetting(AppConstants.languageKey, 'sw');
                    _showSnackBar('Lugha imewekwa: Kiswahili');
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildLanguageOption({
    required String flag,
    required String label,
    required String code,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 12),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.tealGreen : AppColors.tealGreen50,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? AppColors.tealGreen : AppColors.tealGreen100,
            width: 1.5,
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(flag, style: const TextStyle(fontSize: 20)),
            const SizedBox(width: 8),
            Text(
              label,
              style: GoogleFonts.nunito(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: isSelected ? Colors.white : AppColors.tealGreen,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRegionSelector() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: AppColors.tealGreen50,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(Icons.location_on_outlined, size: 18, color: AppColors.tealGreen),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Preferred Region',
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  _preferredRegion ?? 'All regions',
                  style: GoogleFonts.nunito(
                    fontSize: 11,
                    color: AppColors.textHint,
                  ),
                ),
              ],
            ),
          ),
          GestureDetector(
            onTap: _showRegionPicker,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: AppColors.tealGreen50,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: AppColors.tealGreen100),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.edit, size: 14, color: AppColors.tealGreen),
                  const SizedBox(width: 4),
                  Text(
                    'Change',
                    style: GoogleFonts.nunito(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.tealGreen,
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

  void _showRegionPicker() {
    if (_regions.isEmpty && !_isLoadingRegions) {
      _showSnackBar('No regions available', color: Colors.orange);
      return;
    }

    String? selected = _preferredRegion;

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 34),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(
                    width: 40,
                    height: 4,
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                Text(
                  'Select Preferred Region',
                  style: GoogleFonts.nunito(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Choose your preferred region for property recommendations',
                  style: GoogleFonts.nunito(
                    fontSize: 13,
                    color: AppColors.textHint,
                  ),
                ),
                const SizedBox(height: 20),
                Flexible(
                  child: ListView(
                    shrinkWrap: true,
                    children: [
                      RadioListTile<String?>(
                        title: Text('All regions', style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w600)),
                        subtitle: Text('Show properties from all regions', style: GoogleFonts.nunito(fontSize: 11, color: AppColors.textHint)),
                        value: null,
                        groupValue: selected,
                        activeColor: AppColors.tealGreen,
                        onChanged: (v) => setModalState(() => selected = v),
                      ),
                      ..._regions.map((r) => RadioListTile<String?>(
                        title: Text(r, style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w600)),
                        value: r,
                        groupValue: selected,
                        activeColor: AppColors.tealGreen,
                        onChanged: (v) => setModalState(() => selected = v),
                      )),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        onPressed: () => Navigator.pop(ctx),
                        child: Text('Cancel', style: GoogleFonts.nunito(fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () {
                          setState(() => _preferredRegion = selected);
                          _saveSetting(AppConstants.regionKey, selected ?? '');
                          Navigator.pop(ctx);
                          _showSnackBar('Region updated');
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.tealGreen,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        child: Text('Save', style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14)),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSwitchTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required bool value,
    required ValueChanged<bool> onChanged,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: AppColors.tealGreen50,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 18, color: AppColors.tealGreen),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: GoogleFonts.nunito(
                    fontSize: 11,
                    color: AppColors.textHint,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Switch(
            value: value,
            onChanged: onChanged,
            activeColor: AppColors.tealGreen,
            inactiveThumbColor: AppColors.textHint,
            inactiveTrackColor: AppColors.tealGreen100,
          ),
        ],
      ),
    );
  }

  Widget _buildNavTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.tealGreen50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, size: 18, color: AppColors.tealGreen),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.nunito(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      style: GoogleFonts.nunito(
                        fontSize: 11,
                        color: AppColors.textHint,
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right, color: AppColors.textHint, size: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInfoTile({
    required IconData icon,
    required String title,
    required String value,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: AppColors.tealGreen50,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 18, color: AppColors.tealGreen),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Text(
              title,
              style: GoogleFonts.nunito(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
          ),
          Text(
            value,
            style: GoogleFonts.nunito(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  void _showComingSoon(String feature) {
    _showSnackBar('$feature coming soon!', color: Colors.orange);
  }

  void _showAboutDialog() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(
                color: AppColors.tealGreen,
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(Icons.home, size: 36, color: Colors.white),
            ),
            const SizedBox(height: 16),
            Text(
              AppConstants.appName,
              style: GoogleFonts.nunito(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 4),
            Text(
              AppConstants.tagline,
              style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 12),
            Text(
              'Version ${AppConstants.appVersion}',
              style: GoogleFonts.nunito(fontSize: 12, color: AppColors.textHint),
            ),
            const SizedBox(height: 16),
            Text(
              'PataNyumba is a property rental platform connecting tenants and landlords across Tanzania. Find your perfect home with ease.',
              textAlign: TextAlign.center,
              style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary, height: 1.5),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text('Close', style: GoogleFonts.nunito(fontWeight: FontWeight.w600, color: AppColors.tealGreen)),
          ),
        ],
      ),
    );
  }

  void _showResetConfirm() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text('Reset All Settings', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        content: Text(
          'This will reset all your preferences including theme, language, region, and notification settings. This action cannot be undone.',
          style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textSecondary),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          TextButton(
            onPressed: () async {
              Navigator.pop(ctx);
              final prefs = await SharedPreferences.getInstance();
              await prefs.remove(AppConstants.themeKey);
              await prefs.remove(AppConstants.languageKey);
              await prefs.remove(AppConstants.regionKey);
              await prefs.remove(AppConstants.notifPushKey);
              await prefs.remove(AppConstants.notifEmailKey);
              await prefs.remove(AppConstants.notifSmsKey);
              await prefs.remove(AppConstants.notifNewPropKey);
              await prefs.remove(AppConstants.notifPriceDropKey);
              await prefs.remove(AppConstants.notifKycKey);
              await prefs.remove(AppConstants.notifSubKey);
              await _loadSettings();
              _showSnackBar('All settings have been reset');
            },
            child: const Text('Reset', style: TextStyle(color: AppColors.error)),
          ),
        ],
      ),
    );
  }
}
