import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import '../widgets/app_toast.dart';
import 'home_screen.dart';
import 'kyc_status_screen.dart';

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

  int _currentStep = 0;
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
      backgroundColor: const Color(0xFFF5F7FA),
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      if (_currentStep == 0) ..._buildStep0(),
                      if (_currentStep == 1) ..._buildStep1(),
                    ],
                  ),
                ),
              ),
            ),
            _buildBottomBar(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [AppColors.darkTealGreen, AppColors.tealGreen],
        ),
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
      ),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: Row(
              children: [
                GestureDetector(
                  onTap: () {
                    if (_currentStep > 0) {
                      setState(() => _currentStep = 0);
                    } else {
                      Navigator.of(context).pop();
                    }
                  },
                  child: Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.arrow_back, color: Colors.white, size: 20),
                  ),
                ),
                const SizedBox(width: 12),
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: Colors.white.withValues(alpha: 0.15),
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(10),
                    child: Image.asset('assets/logo/whitelogo.png', fit: BoxFit.contain),
                  ),
                ),
                const SizedBox(width: 10),
                Text(
                  AppConstants.appName,
                  style: GoogleFonts.nunito(
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    color: Colors.white,
                    letterSpacing: 0.3,
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 24),
            child: Align(
              alignment: Alignment.centerLeft,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Join ${AppConstants.appName}',
                    style: GoogleFonts.nunito(
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _currentStep == 0
                        ? 'Tell us who you are to get started'
                        : 'Enter your details to create your account',
                    style: GoogleFonts.nunito(
                      fontSize: 13,
                      color: Colors.white.withValues(alpha: 0.8),
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

  List<Widget> _buildStep0() {
    return [
      _buildStepIndicator(),
      const SizedBox(height: 28),
      Text(
        'I am a...',
        style: GoogleFonts.nunito(
          fontSize: 18,
          fontWeight: FontWeight.w800,
          color: AppColors.textPrimary,
        ),
      ),
      const SizedBox(height: 20),
      _buildRoleCard(
        icon: Icons.person_outline,
        title: 'Tenant',
        subtitle: 'I\'m looking for a place to rent',
        isSelected: _selectedRole == 'tenant',
        onTap: () => setState(() => _selectedRole = 'tenant'),
      ),
      const SizedBox(height: 12),
      _buildRoleCard(
        icon: Icons.home_outlined,
        title: 'Landlord',
        subtitle: 'I own properties and want to list them',
        isSelected: _selectedRole == 'landlord',
        onTap: () => setState(() => _selectedRole = 'landlord'),
      ),
      const SizedBox(height: 12),
      _buildRoleCard(
        icon: Icons.business_outlined,
        title: 'Agent',
        subtitle: 'I manage properties on behalf of owners',
        isSelected: _selectedRole == 'agent',
        onTap: () => setState(() => _selectedRole = 'agent'),
      ),
      const SizedBox(height: 24),
      if (_selectedRole != 'tenant')
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: AppColors.warning.withValues(alpha: 0.08),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.warning.withValues(alpha: 0.2)),
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(Icons.info_outline, color: AppColors.warning, size: 18),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  'As a ${_selectedRole == 'agent' ? 'agent' : 'landlord'}, you\'ll need to complete KYC verification after registration.',
                  style: GoogleFonts.nunito(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                    height: 1.5,
                  ),
                ),
              ),
            ],
          ),
        ),
      const SizedBox(height: 20),
      Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            'Already have an account? ',
            style: GoogleFonts.nunito(color: AppColors.textSecondary, fontSize: 13),
          ),
          GestureDetector(
            onTap: () => Navigator.of(context).pop(),
            child: Text(
              'Sign in',
              style: GoogleFonts.nunito(
                color: AppColors.tealGreen,
                fontWeight: FontWeight.w700,
                fontSize: 13,
              ),
            ),
          ),
        ],
      ),
      const SizedBox(height: 20),
    ];
  }

  Widget _buildRoleCard({
    required IconData icon,
    required String title,
    required String subtitle,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.tealGreen.withValues(alpha: 0.05) : Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected ? AppColors.tealGreen : AppColors.inputBorder,
            width: isSelected ? 2 : 1,
          ),
          boxShadow: [
            BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 6, offset: const Offset(0, 2)),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: isSelected ? AppColors.tealGreen : AppColors.tealGreen50,
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(icon, size: 24, color: isSelected ? Colors.white : AppColors.tealGreen),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.nunito(
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: GoogleFonts.nunito(fontSize: 12, color: AppColors.textSecondary),
                  ),
                ],
              ),
            ),
            if (isSelected)
              const Icon(Icons.check_circle, color: AppColors.tealGreen, size: 24),
          ],
        ),
      ),
    );
  }

  List<Widget> _buildStep1() {
    return [
      _buildStepIndicator(),
      const SizedBox(height: 24),
      Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: AppColors.tealGreen50,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: AppColors.tealGreen100),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              _selectedRole == 'tenant'
                  ? Icons.person_outline
                  : _selectedRole == 'landlord'
                      ? Icons.home_outlined
                      : Icons.business_outlined,
              size: 16,
              color: AppColors.tealGreen,
            ),
            const SizedBox(width: 6),
            Text(
              'Registering as ${_selectedRole == 'tenant' ? 'Tenant' : _selectedRole == 'landlord' ? 'Landlord' : 'Agent'}',
              style: GoogleFonts.nunito(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.tealGreen),
            ),
          ],
        ),
      ),
      const SizedBox(height: 20),
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
      if (_selectedRole != 'tenant') ...[
        const SizedBox(height: 24),
        _buildSectionDivider('Business Details'),
        const SizedBox(height: 16),
        TextFormField(
          controller: _businessNameController,
          decoration: InputDecoration(
            labelText: _selectedRole == 'agent' ? 'Agency Name (optional)' : 'Business Name (optional)',
            hintText: _selectedRole == 'agent' ? 'e.g. PataNyumba Real Estate' : 'e.g. My Properties Ltd',
            prefixIcon: const Icon(Icons.business_outlined, color: AppColors.textHint),
          ),
        ),
        const SizedBox(height: 16),
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
        TextFormField(
          controller: _addressController,
          maxLines: 2,
          decoration: const InputDecoration(
            labelText: 'Street Address (optional)',
            hintText: 'e.g. Mlimani City, Sam Nujoma Road',
            prefixIcon: Icon(Icons.location_on_outlined, color: AppColors.textHint),
          ),
        ),
      ],
      const SizedBox(height: 16),
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
      const SizedBox(height: 20),
    ];
  }

  Widget _buildStepIndicator() {
    final steps = ['Role', 'Details'];
    return Padding(
      padding: const EdgeInsets.only(top: 20),
      child: Row(
        children: List.generate(steps.length * 2 - 1, (index) {
          if (index.isOdd) {
            final isCompleted = _currentStep > index ~/ 2;
            return Expanded(
              child: Container(
                height: 3,
                margin: const EdgeInsets.symmetric(horizontal: 4),
                decoration: BoxDecoration(
                  color: isCompleted ? AppColors.tealGreen : AppColors.tealGreen100,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            );
          }
          final stepIndex = index ~/ 2;
          final isActive = _currentStep == stepIndex;
          final isCompleted = _currentStep > stepIndex;
          return Column(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: isCompleted ? AppColors.tealGreen : (isActive ? AppColors.tealGreen : AppColors.tealGreen50),
                  shape: BoxShape.circle,
                  border: isActive ? Border.all(color: AppColors.tealGreen, width: 2) : null,
                ),
                child: isCompleted
                    ? const Icon(Icons.check, size: 18, color: Colors.white)
                    : Text(
                        '${stepIndex + 1}',
                        style: GoogleFonts.nunito(
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                          color: isActive ? Colors.white : AppColors.tealGreen,
                        ),
                      ),
              ),
              const SizedBox(height: 4),
              Text(
                steps[stepIndex],
                style: GoogleFonts.nunito(
                  fontSize: 10,
                  fontWeight: FontWeight.w700,
                  color: isActive ? AppColors.tealGreen : AppColors.textHint,
                ),
              ),
            ],
          );
        }),
      ),
    );
  }

  Widget _buildBottomBar() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: AppColors.inputBorder)),
      ),
      child: Row(
        children: [
          if (_currentStep > 0)
            Expanded(
              child: OutlinedButton(
                onPressed: () => setState(() => _currentStep = 0),
                style: OutlinedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  side: const BorderSide(color: AppColors.tealGreen100),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text('Back', style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textSecondary)),
              ),
            ),
          if (_currentStep > 0) const SizedBox(width: 12),
          Expanded(
            child: ElevatedButton(
              onPressed: _isLoading
                  ? null
                  : () {
                      if (_currentStep == 0) {
                        setState(() => _currentStep = 1);
                      } else {
                        _register();
                      }
                    },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.tealGreen,
                foregroundColor: Colors.white,
                disabledBackgroundColor: AppColors.tealGreen100,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: _isLoading
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                    )
                  : Text(
                      _currentStep == 0 ? 'Next' : 'Create Account',
                      style: GoogleFonts.nunito(fontWeight: FontWeight.w800, fontSize: 14),
                    ),
            ),
          ),
        ],
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
            style: GoogleFonts.nunito(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textHint),
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
