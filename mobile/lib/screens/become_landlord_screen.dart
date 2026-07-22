import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';

class BecomeLandlordScreen extends StatefulWidget {
  const BecomeLandlordScreen({super.key});

  @override
  State<BecomeLandlordScreen> createState() => _BecomeLandlordScreenState();
}

class _BecomeLandlordScreenState extends State<BecomeLandlordScreen> {
  int _currentStep = 0;
  bool _isSubmitting = false;

  // Form data
  String _selectedRole = 'landlord';
  final _businessNameController = TextEditingController();
  final _addressController = TextEditingController();
  final _phoneController = TextEditingController();
  String? _selectedRegion;
  String? _selectedDistrict;
  int? _selectedRegionId;

  List<Map<String, dynamic>> _regions = [];
  List<Map<String, dynamic>> _districts = [];
  bool _loadingRegions = true;

  @override
  void initState() {
    super.initState();
    _fetchRegions();
  }

  @override
  void dispose() {
    _businessNameController.dispose();
    _addressController.dispose();
    _phoneController.dispose();
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

  Future<void> _submit() async {
    if (_selectedRegion == null || _selectedDistrict == null) {
      _showSnackBar('Please select your region and district', isError: true);
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final data = await ApiService().post('user/become-landlord', body: {
        'role': _selectedRole,
        'business_name': _businessNameController.text.trim(),
        'region': _selectedRegion,
        'district': _selectedDistrict,
        'address': _addressController.text.trim(),
        'phone': _phoneController.text.trim(),
      });

      if (data['success'] == true) {
        // Update local user data
        final user = data['user'] as Map<String, dynamic>;
        await AuthService().updateUser(user);

        _showSuccessDialog(data['message'] ?? 'Successfully became a landlord!');
      } else {
        _showSnackBar(data['message'] ?? 'Failed to submit', isError: true);
      }
    } catch (e) {
      _showSnackBar('Network error. Please try again.', isError: true);
    }

    setState(() => _isSubmitting = false);
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

  void _showSuccessDialog(String message) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: const BoxDecoration(
                color: AppColors.tealGreen50,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.check_circle, size: 40, color: AppColors.tealGreen),
            ),
            const SizedBox(height: 16),
            Text(
              'Congratulations!',
              style: GoogleFonts.nunito(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 8),
            Text(
              message,
              textAlign: TextAlign.center,
              style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary, height: 1.5),
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {
                Navigator.of(ctx).pop();
                Navigator.of(context).pop(true);
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.tealGreen,
                foregroundColor: Colors.white,
                minimumSize: const Size(double.infinity, 48),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: Text('Continue', style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14)),
            ),
          ],
        ),
      ),
    );
  }

  bool get _canProceed {
    switch (_currentStep) {
      case 0:
        return true;
      case 1:
        return _selectedRegion != null && _selectedDistrict != null;
      case 2:
        return true;
      default:
        return false;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('Become a Landlord', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          _buildStepIndicator(),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: _buildStepContent(),
            ),
          ),
          _buildBottomBar(),
        ],
      ),
    );
  }

  Widget _buildStepIndicator() {
    final steps = ['Role', 'Location', 'Details'];
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(16)),
        boxShadow: [
          BoxShadow(color: Colors.black12, blurRadius: 4, offset: Offset(0, 2)),
        ],
      ),
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

  Widget _buildStepContent() {
    switch (_currentStep) {
      case 0:
        return _buildRoleStep();
      case 1:
        return _buildLocationStep();
      case 2:
        return _buildDetailsStep();
      default:
        return const SizedBox.shrink();
    }
  }

  Widget _buildRoleStep() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Choose Your Role',
          style: GoogleFonts.nunito(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 6),
        Text(
          'Select how you want to participate in PataNyumba',
          style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary),
        ),
        const SizedBox(height: 24),
        _buildRoleCard(
          icon: Icons.home_outlined,
          title: 'Landlord',
          subtitle: 'I own properties and want to list them for rent',
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
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.tealGreen50,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppColors.tealGreen100),
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(Icons.info_outline, size: 20, color: AppColors.tealGreen),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  'After registration, you\'ll need to complete KYC verification to start listing properties. This ensures trust and safety for all users.',
                  style: GoogleFonts.nunito(fontSize: 12, color: AppColors.textSecondary, height: 1.6),
                ),
              ),
            ],
          ),
        ),
      ],
    );
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

  Widget _buildLocationStep() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Your Location',
          style: GoogleFonts.nunito(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 6),
        Text(
          'Where are your properties located?',
          style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary),
        ),
        const SizedBox(height: 24),
        // Region dropdown
        Text(
          'Region',
          style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 8),
        _buildDropdown(
          value: _selectedRegion,
          hint: _loadingRegions ? 'Loading regions...' : 'Select region',
          items: _regions.map((r) => r['name'] as String).toList(),
          onChanged: (val) {
            setState(() {
              _selectedRegion = val;
              _selectedRegionId = _regions.firstWhere((r) => r['name'] == val)['id'];
            });
            _fetchDistricts(_selectedRegionId!);
          },
        ),
        const SizedBox(height: 20),
        // District dropdown
        Text(
          'District',
          style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 8),
        _buildDropdown(
          value: _selectedDistrict,
          hint: _selectedRegion == null ? 'Select region first' : 'Select district',
          items: _districts.map((d) => d['name'] as String).toList(),
          onChanged: _selectedRegion == null
              ? null
              : (val) => setState(() => _selectedDistrict = val),
        ),
        const SizedBox(height: 20),
        // Address
        Text(
          'Street Address (optional)',
          style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 8),
        TextField(
          controller: _addressController,
          maxLines: 2,
          decoration: InputDecoration(
            hintText: 'e.g. Mlimani City, Sam Nujoma Road',
            hintStyle: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
            filled: true,
            fillColor: Colors.white,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(color: AppColors.inputBorder),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(color: AppColors.inputBorder),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.tealGreen, width: 2),
            ),
            contentPadding: const EdgeInsets.all(14),
          ),
          style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textPrimary),
        ),
      ],
    );
  }

  Widget _buildDropdown({
    required String? value,
    required String hint,
    required List<String> items,
    required ValueChanged<String?>? onChanged,
  }) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 4, offset: const Offset(0, 1)),
        ],
      ),
      child: DropdownButtonFormField<String>(
        value: value,
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: BorderSide(color: AppColors.inputBorder),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: BorderSide(color: AppColors.inputBorder),
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
                  child: Text(item, style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textPrimary)),
                ))
            .toList(),
        onChanged: onChanged,
        icon: const Icon(Icons.keyboard_arrow_down, color: AppColors.textHint),
        isExpanded: true,
      ),
    );
  }

  Widget _buildDetailsStep() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Additional Details',
          style: GoogleFonts.nunito(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 6),
        Text(
          'Help us know you better',
          style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary),
        ),
        const SizedBox(height: 24),
        // Business name
        Text(
          _selectedRole == 'agent' ? 'Agency Name (optional)' : 'Business Name (optional)',
          style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 8),
        _buildTextField(
          controller: _businessNameController,
          hint: _selectedRole == 'agent' ? 'e.g. PataNyumba Real Estate Ltd' : 'e.g. My Properties Ltd',
        ),
        const SizedBox(height: 20),
        // Phone
        Text(
          'Phone Number',
          style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
        ),
        const SizedBox(height: 8),
        _buildTextField(
          controller: _phoneController,
          hint: 'e.g. +255 712 345 678',
          keyboardType: TextInputType.phone,
        ),
        const SizedBox(height: 24),
        // Summary
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.tealGreen50,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppColors.tealGreen100),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.summarize_outlined, size: 18, color: AppColors.tealGreen),
                  const SizedBox(width: 8),
                  Text(
                    'Summary',
                    style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w800, color: AppColors.tealGreen),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              _buildSummaryRow('Role', _selectedRole == 'landlord' ? 'Landlord' : 'Agent'),
              _buildSummaryRow('Region', _selectedRegion ?? '-'),
              _buildSummaryRow('District', _selectedDistrict ?? '-'),
              if (_businessNameController.text.isNotEmpty)
                _buildSummaryRow('Business', _businessNameController.text),
            ],
          ),
        ),
        const SizedBox(height: 16),
        Text(
          'After submitting, you\'ll be redirected to complete KYC verification. Once verified, you can start listing your properties.',
          style: GoogleFonts.nunito(fontSize: 11, color: AppColors.textHint, height: 1.6),
        ),
      ],
    );
  }

  Widget _buildSummaryRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        children: [
          Text(
            '$label: ',
            style: GoogleFonts.nunito(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textSecondary),
          ),
          Expanded(
            child: Text(
              value,
              style: GoogleFonts.nunito(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String hint,
    TextInputType keyboardType = TextInputType.text,
  }) {
    return TextField(
      controller: controller,
      keyboardType: keyboardType,
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: AppColors.inputBorder),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: AppColors.inputBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.tealGreen, width: 2),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      ),
      style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textPrimary),
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
                onPressed: () => setState(() => _currentStep--),
                style: OutlinedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  side: BorderSide(color: AppColors.tealGreen100),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text('Back', style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textSecondary)),
              ),
            ),
          if (_currentStep > 0) const SizedBox(width: 12),
          Expanded(
            child: ElevatedButton(
              onPressed: !_canProceed || _isSubmitting
                  ? null
                  : () {
                      if (_currentStep < 2) {
                        setState(() => _currentStep++);
                      } else {
                        _submit();
                      }
                    },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.tealGreen,
                foregroundColor: Colors.white,
                disabledBackgroundColor: AppColors.tealGreen100,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: _isSubmitting
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                    )
                  : Text(
                      _currentStep < 2 ? 'Continue' : 'Submit',
                      style: GoogleFonts.nunito(fontWeight: FontWeight.w800, fontSize: 14),
                    ),
            ),
          ),
        ],
      ),
    );
  }
}
