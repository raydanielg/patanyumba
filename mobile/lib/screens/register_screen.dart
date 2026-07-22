import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import '../widgets/app_toast.dart';
import 'home_screen.dart';
import 'kyc_status_screen.dart';
import 'login_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  final _businessNameController = TextEditingController();
  final _addressController = TextEditingController();
  bool _obscurePassword = true;
  bool _obscureConfirm = true;
  bool _isLoading = false;

  String _selectedRole = 'tenant';
  String? _selectedRegion;
  String? _selectedDistrict;
  int? _selectedRegionId;
  List<Map<String, dynamic>> _regions = [];
  List<Map<String, dynamic>> _districts = [];
  bool _loadingRegions = false;

  @override
  void initState() {
    super.initState();
    _fetchRegions();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    _businessNameController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _fetchRegions() async {
    setState(() => _loadingRegions = true);
    try {
      final data = await ApiService().get('all-regions');
      final List<dynamic> list = data['data'] ?? [];
      setState(() {
        _regions = list.cast<Map<String, dynamic>>();
        _loadingRegions = false;
      });
    } catch (_) {
      setState(() => _loadingRegions = false);
    }
  }

  Future<void> _fetchDistricts(int regionId) async {
    setState(() {
      _selectedDistrict = null;
      _districts = [];
    });
    try {
      final data = await ApiService().get('regions/$regionId/districts');
      final List<dynamic> list = data['data'] ?? [];
      setState(() {
        _districts = list.cast<Map<String, dynamic>>();
      });
    } catch (_) {}
  }

  Future<void> _register() async {
    if (_formKey.currentState!.validate()) {
      if (_selectedRole != 'tenant') {
        if (_selectedRegion == null || _selectedDistrict == null) {
          AppToast.warning(context, 'Missing Info', 'Please select your region and district');
          return;
        }
      }

      setState(() => _isLoading = true);

      try {
        final success = await AuthService().register(
          _nameController.text.trim(),
          _emailController.text.trim(),
          _passwordController.text,
          phone: _phoneController.text.trim(),
          role: _selectedRole,
          businessName: _selectedRole != 'tenant' ? _businessNameController.text.trim() : null,
          region: _selectedRole != 'tenant' ? _selectedRegion : null,
          district: _selectedRole != 'tenant' ? _selectedDistrict : null,
          address: _selectedRole != 'tenant' ? _addressController.text.trim() : null,
        );

        if (success && mounted) {
          if (_selectedRole != 'tenant') {
            AppToast.success(context, 'Account Created!', 'Please complete KYC verification');
            Navigator.of(context).pushAndRemoveUntil(
              MaterialPageRoute(builder: (context) => const KycStatusScreen(justRegistered: true)),
              (route) => false,
            );
          } else {
            AppToast.success(context, 'Account created!', 'Welcome to ${AppConstants.appName}');
            Navigator.of(context).pushAndRemoveUntil(
              MaterialPageRoute(builder: (context) => const HomeScreen()),
              (route) => false,
            );
          }
        }
      } catch (e) {
        if (mounted) {
          AppToast.error(context, 'Registration Failed', e.toString());
        }
      } finally {
        if (mounted) setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.overlayBg,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  const SizedBox(height: 50),
                  // Back button
                  Align(
                    alignment: Alignment.centerLeft,
                    child: GestureDetector(
                      onTap: () => Navigator.of(context).pop(),
                      child: Container(
                        width: 40,
                        height: 40,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppColors.inputBorder),
                        ),
                        child: const Icon(Icons.arrow_back, color: AppColors.tealGreen, size: 20),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  // Logo
                  Container(
                    width: 72,
                    height: 72,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(18),
                      gradient: const LinearGradient(
                        colors: [AppColors.darkTealGreen, AppColors.tealGreen],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.tealGreen.withValues(alpha: 0.3),
                          blurRadius: 16,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(18),
                      child: Image.asset('assets/logo/whitelogo.png', fit: BoxFit.contain),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    'Create Account',
                    style: GoogleFonts.nunito(
                      fontSize: 26,
                      fontWeight: FontWeight.w800,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'Join ${AppConstants.appName} today',
                    style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    AppConstants.tagline,
                    style: GoogleFonts.nunito(
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                      color: AppColors.tealGreen,
                      letterSpacing: 0.5,
                    ),
                  ),
                  const SizedBox(height: 28),
                  // Account type toggle
                  _buildAccountTypeToggle(),
                  const SizedBox(height: 24),
                  // Name
                  TextFormField(
                    controller: _nameController,
                    decoration: const InputDecoration(
                      labelText: 'Full Name',
                      hintText: 'John Doe',
                      prefixIcon: Icon(Icons.person_outline, color: AppColors.textHint),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter your name';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  // Phone
                  TextFormField(
                    controller: _phoneController,
                    keyboardType: TextInputType.phone,
                    maxLength: 10,
                    decoration: const InputDecoration(
                      labelText: 'Phone Number',
                      hintText: '06XXXXXXXX',
                      prefixIcon: Icon(Icons.phone_outlined, color: AppColors.textHint),
                      counterText: '',
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter your phone number';
                      }
                      if (!RegExp(r'^0[6-7]\d{8}$').hasMatch(value)) {
                        return 'Enter valid number (e.g. 06XXXXXXXX)';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  // Email
                  TextFormField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: const InputDecoration(
                      labelText: 'Email Address',
                      hintText: 'name@example.com',
                      prefixIcon: Icon(Icons.email_outlined, color: AppColors.textHint),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter your email';
                      }
                      if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
                        return 'Please enter a valid email';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  // Password
                  TextFormField(
                    controller: _passwordController,
                    obscureText: _obscurePassword,
                    decoration: InputDecoration(
                      labelText: 'Password',
                      hintText: 'Min. 8 characters',
                      prefixIcon: const Icon(Icons.lock_outline, color: AppColors.textHint),
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                          color: AppColors.textHint,
                        ),
                        onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                      ),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter a password';
                      }
                      if (value.length < 8) {
                        return 'Password must be at least 8 characters';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  // Confirm Password
                  TextFormField(
                    controller: _confirmPasswordController,
                    obscureText: _obscureConfirm,
                    decoration: InputDecoration(
                      labelText: 'Confirm Password',
                      hintText: 'Re-enter your password',
                      prefixIcon: const Icon(Icons.lock_outline, color: AppColors.textHint),
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscureConfirm ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                          color: AppColors.textHint,
                        ),
                        onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                      ),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please confirm your password';
                      }
                      if (value != _passwordController.text) {
                        return 'Passwords do not match';
                      }
                      return null;
                    },
                  ),
                  // Landlord/Agent extra fields
                  if (_selectedRole != 'tenant') ...[
                    const SizedBox(height: 24),
                    _buildSectionDivider('Business Details'),
                    const SizedBox(height: 16),
                    // Business name
                    TextFormField(
                      controller: _businessNameController,
                      decoration: InputDecoration(
                        labelText: _selectedRole == 'agent' ? 'Agency Name (optional)' : 'Business Name (optional)',
                        hintText: _selectedRole == 'agent' ? 'e.g. PataNyumba Real Estate' : 'e.g. My Properties Ltd',
                        prefixIcon: const Icon(Icons.business_outlined, color: AppColors.textHint),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // Region dropdown
                    _buildDropdownField(
                      label: 'Region',
                      value: _selectedRegion,
                      hint: _loadingRegions ? 'Loading regions...' : 'Select your region',
                      items: _regions.map((r) => r['name'] as String).toList(),
                      onChanged: (val) {
                        setState(() {
                          _selectedRegion = val;
                          _selectedRegionId = _regions.firstWhere((r) => r['name'] == val)['id'];
                        });
                        _fetchDistricts(_selectedRegionId!);
                      },
                    ),
                    const SizedBox(height: 16),
                    // District dropdown
                    _buildDropdownField(
                      label: 'District',
                      value: _selectedDistrict,
                      hint: _selectedRegion == null ? 'Select region first' : 'Select your district',
                      items: _districts.map((d) => d['name'] as String).toList(),
                      onChanged: _selectedRegion == null
                          ? null
                          : (val) => setState(() => _selectedDistrict = val),
                    ),
                    const SizedBox(height: 16),
                    // Address
                    TextFormField(
                      controller: _addressController,
                      maxLines: 2,
                      decoration: const InputDecoration(
                        labelText: 'Street Address (optional)',
                        hintText: 'e.g. Mlimani City, Sam Nujoma Road',
                        prefixIcon: Icon(Icons.location_on_outlined, color: AppColors.textHint),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // KYC info note
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: AppColors.warning.withValues(alpha: 0.08),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppColors.warning.withValues(alpha: 0.2), width: 1),
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Icon(Icons.info_outline, color: AppColors.warning, size: 18),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'As a ${_selectedRole == 'agent' ? 'agent' : 'landlord'}, you\'ll need to complete KYC verification after registration before listing properties.',
                              style: GoogleFonts.nunito(
                                fontSize: 12,
                                color: AppColors.textSecondary,
                                height: 1.4,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                  const SizedBox(height: 16),
                  // Terms note
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppColors.tealGreen50,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: AppColors.tealGreen100, width: 1),
                    ),
                    child: const Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(Icons.info_outline, color: AppColors.tealGreen, size: 18),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'By creating an account, you agree to our Terms of Service and Privacy Policy.',
                            style: TextStyle(fontSize: 12, color: AppColors.tealGreen600, height: 1.4),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  // Register button
                  SizedBox(
                    width: double.infinity,
                    height: 54,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _register,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.tealGreen,
                        foregroundColor: Colors.white,
                        elevation: 2,
                        shadowColor: AppColors.tealGreen.withValues(alpha: 0.3),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: _isLoading
                          ? const SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(
                                strokeWidth: 2.5,
                                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                              ),
                            )
                          : Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(_selectedRole == 'tenant' ? Icons.person_add : Icons.real_estate_agent, size: 20),
                                const SizedBox(width: 8),
                                Text(
                                  _selectedRole == 'tenant'
                                      ? 'Create Account'
                                      : 'Register as ${_selectedRole == 'agent' ? 'Agent' : 'Landlord'}',
                                  style: GoogleFonts.nunito(fontSize: 16, fontWeight: FontWeight.w700),
                                ),
                              ],
                            ),
                    ),
                  ),
                  const SizedBox(height: 24),
                  // Divider
                  Row(
                    children: [
                      const Expanded(child: Divider(color: AppColors.inputBorder)),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: Text('or', style: GoogleFonts.nunito(color: AppColors.textHint, fontSize: 14)),
                      ),
                      const Expanded(child: Divider(color: AppColors.inputBorder)),
                    ],
                  ),
                  const SizedBox(height: 20),
                  // Login link
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        'Already have an account? ',
                        style: GoogleFonts.nunito(color: AppColors.textSecondary, fontSize: 14),
                      ),
                      GestureDetector(
                        onTap: () => Navigator.of(context).pop(),
                        child: Text(
                          'Sign in',
                          style: GoogleFonts.nunito(
                            color: AppColors.tealGreen,
                            fontWeight: FontWeight.w700,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 30),
                  Text(
                    '\u00a9 2025 ${AppConstants.appName}. All rights reserved.',
                    style: GoogleFonts.nunito(fontSize: 12, color: AppColors.textHint),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildAccountTypeToggle() {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.inputBorder),
      ),
      child: Row(
        children: [
          Expanded(
            child: _buildToggleOption(
              icon: Icons.person_outline,
              label: 'Tenant',
              isSelected: _selectedRole == 'tenant',
              onTap: () => setState(() => _selectedRole = 'tenant'),
            ),
          ),
          Expanded(
            child: _buildToggleOption(
              icon: Icons.home_outlined,
              label: 'Landlord',
              isSelected: _selectedRole == 'landlord',
              onTap: () => setState(() => _selectedRole = 'landlord'),
            ),
          ),
          Expanded(
            child: _buildToggleOption(
              icon: Icons.business_outlined,
              label: 'Agent',
              isSelected: _selectedRole == 'agent',
              onTap: () => setState(() => _selectedRole = 'agent'),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildToggleOption({
    required IconData icon,
    required String label,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.tealGreen : Colors.transparent,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          children: [
            Icon(icon, size: 22, color: isSelected ? Colors.white : AppColors.textHint),
            const SizedBox(height: 4),
            Text(
              label,
              style: GoogleFonts.nunito(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: isSelected ? Colors.white : AppColors.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionDivider(String title) {
    return Row(
      children: [
        const Expanded(child: Divider(color: AppColors.inputBorder)),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          child: Text(
            title,
            style: GoogleFonts.nunito(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: AppColors.textHint,
            ),
          ),
        ),
        const Expanded(child: Divider(color: AppColors.inputBorder)),
      ],
    );
  }

  Widget _buildDropdownField({
    required String label,
    required String? value,
    required String hint,
    required List<String> items,
    required ValueChanged<String?>? onChanged,
  }) {
    return DropdownButtonFormField<String>(
      value: value,
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        hintStyle: GoogleFonts.nunito(fontSize: 14, color: AppColors.textHint),
        prefixIcon: const Icon(Icons.location_on_outlined, color: AppColors.textHint),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.inputBorder),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.inputBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.tealGreen, width: 2),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      ),
      items: items
          .map((item) => DropdownMenuItem(
                value: item,
                child: Text(item, style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textPrimary)),
              ))
          .toList(),
      onChanged: onChanged,
      icon: const Icon(Icons.keyboard_arrow_down, color: AppColors.textHint),
      isExpanded: true,
    );
  }
}
